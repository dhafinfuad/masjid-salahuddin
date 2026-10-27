<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotulaAiService
{
    /**
     * Memeriksa apakah Gemini API Key sudah terkonfigurasi.
     */
    public function isAvailable(): bool
    {
        return !empty($this->getApiKey());
    }

    /**
     * Mendapatkan Gemini API Key dari konfigurasi atau .env.
     */
    protected function getApiKey(): ?string
    {
        return config('services.gemini.key') ?: env('GEMINI_API_KEY');
    }

    /**
     * Membersihkan teks mentah transkrip dari header YouTube dan baris kosong berulang.
     */
    public function cleanRawTranscript(string $rawTranscript): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $rawTranscript);
        $lines = explode("\n", $text);
        $cleanedLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }
            // Abaikan header bawaan YouTube
            if (preg_match('/^(?:transkrip|telusuri transkrip)$/i', $trimmed)) {
                continue;
            }
            $cleanedLines[] = $trimmed;
        }

        return implode("\n", $cleanedLines);
    }

    /**
     * Mengubah naskah mentah transkrip YouTube menjadi Dokumen Notula Eksekutif Kajian.
     *
     * @param string $rawTranscript Teks mentah transkrip YouTube (lengkap dengan timestamp detik)
     * @param array<string, mixed> $meta Informasi kajian (title, speaker_name, date, type)
     * @return string Naskah notula berformat Markdown resmi Masjid Salahuddin
     * @throws \RuntimeException Jika API tidak tersedia atau proses transformasi gagal
     */
    public function generateFromTranscript(string $rawTranscript, array $meta = []): string
    {
        $cleanedTranscript = $this->cleanRawTranscript($rawTranscript);
        if (mb_strlen($cleanedTranscript) < 50) {
            throw new \InvalidArgumentException('Teks transkrip terlalu pendek untuk diproses menjadi notula eksekutif.');
        }

        $apiKey = $this->getApiKey();
        if (empty($apiKey)) {
            throw new \RuntimeException('Google Gemini API Key belum terkonfigurasi pada file .env.');
        }

        $title = !empty($meta['title']) ? trim((string) $meta['title']) : 'Kajian Islam Tematik';
        $speaker = !empty($meta['speaker_name']) ? trim((string) $meta['speaker_name']) : 'Asatidz';
        $date = !empty($meta['date']) ? trim((string) $meta['date']) : Carbon::now()->translatedFormat('l, d F Y');
        $place = !empty($meta['place']) ? trim((string) $meta['place']) : 'Masjid Salahuddin KPP Madya Malang';

        $prompt = $this->buildPrompt($cleanedTranscript, [
            'title' => $title,
            'speaker' => $speaker,
            'date' => $date,
            'place' => $place,
        ]);

        return $this->executeGeminiPrompt($prompt);
    }

    /**
     * Mengonversi naskah notula lama menjadi format baru yang ringkas, terstruktur, dan padat.
     *
     * @param string $oldNotulaText Naskah notula lama yang ada di database
     * @param array<string, mixed> $fallbackMeta Informasi kajian cadangan jika di teks tidak tercantum
     * @return string Naskah notula baru berformat ringkas
     */
    public function reformatExistingNotula(string $oldNotulaText, array $fallbackMeta = []): string
    {
        $oldText = trim($oldNotulaText);
        if (mb_strlen($oldText) < 50) {
            throw new \InvalidArgumentException('Teks notula terlalu pendek untuk diproses reformat.');
        }

        $prompt = $this->buildReformatPrompt($oldText, $fallbackMeta);

        return $this->executeGeminiPrompt($prompt);
    }

    /**
     * Mengeksekusi prompt ke Google Gemini API dengan fallback beberapa model.
     */
    protected function executeGeminiPrompt(string $prompt, int $timeoutSeconds = 90): string
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) {
            throw new \RuntimeException('Google Gemini API Key belum terkonfigurasi pada file .env.');
        }

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 8192,
                'temperature' => 0.2,
                'thinkingConfig' => [
                    'thinkingBudget' => 0,
                ],
            ]
        ];

        $primaryModel = config('services.gemini.model') ?: 'gemini-3-flash-preview';
        $modelsToTry = array_unique([$primaryModel, 'gemini-3-flash-preview', 'gemini-3.1-flash-lite', 'gemini-2.0-flash', 'gemini-1.5-flash']);

        $generatedText = null;
        $lastError = 'Tidak ada respon dari server Gemini.';

        foreach ($modelsToTry as $modelName) {
            $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";
            $response = $this->callGeminiApi($apiUrl, $payload, $timeoutSeconds);

            if ($response['code'] === 200 && !empty($response['body'])) {
                $resData = json_decode($response['body'], true);
                $candidateText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!empty($candidateText)) {
                    $generatedText = $candidateText;
                    break;
                }
            } else {
                $lastError = "Model {$modelName} gagal (HTTP {$response['code']}): " . substr((string) ($response['body'] ?? ''), 0, 200);
                Log::warning("Gemini Notula AI: " . $lastError);
                if (in_array($response['code'], [429, 503], true)) {
                    // Jeda sejenak jika terjadi spike/rate limit sebelum mencoba model berikutnya
                    sleep(2);
                }
            }
        }

        if (empty($generatedText)) {
            Log::error("Gagal memproses notula dengan Gemini: {$lastError}");
            throw new \RuntimeException("Gagal memproses notula dengan Google Gemini AI: {$lastError}");
        }

        return $this->sanitizeGeneratedMarkdown($generatedText);
    }

    /**
     * Membangun prompt komprehensif untuk notula baru dari transkrip YouTube dengan format ringkas.
     */
    protected function buildPrompt(string $transcript, array $meta): string
    {
        $judulUpper = mb_strtoupper($meta['title'] ?? 'KAJIAN ISLAM');
        return <<<PROMPT
Anda adalah Sekretaris Keilmuan dan Ahli Notulensi Eksekutif Kajian Islam resmi Masjid Salahuddin yang sangat teliti, berwawasan luas dalam Al-Qur'an dan Sunnah, serta mahir merangkum ceramah lisan menjadi risalah formal berbobot tinggi, ringkas, dan padat.

TUGAS ANDA:
Ubahlah naskah transkrip mentah rekaman YouTube di bawah ini (yang memuat timestamp waktu seperti "0.000 detik...", dialek lisan sehari-hari, bahasa daerah/Jawa santai, kata pengisi lisan, dan kutipan ayat lisan yang belum terstruktur) menjadi sebuah Dokumen Notula Kajian Masjid Salahuddin yang bersih, berbobot, padat, dan langsung siap dipublikasikan/dicetak.

METADATA KAJIAN SAAT INI:
- Tema / Judul: {$meta['title']}
- Pemateri: {$meta['speaker']}
- Hari, Tanggal: {$meta['date']}
- Tempat: {$meta['place']}

ATURAN STRUKTUR DOKUMEN (WAJIB PERSIS IKUTI FORMAT BERIKUT TANPA MODIFIKASI STRUKTUR):

KAJIAN {$judulUpper}
Hari, Tanggal : {$meta['date']}
Tempat : {$meta['place']}
Pemateri : {$meta['speaker']}
Tema / Kitab : {$meta['title']}
Notulis : Tim Media & Notula Masjid Salahuddin

📌 RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)
[Tulis 2 paragraf ringkas, padat, dan berbobot yang merangkum esensi pokok kajian, latar belakang urgensi materi, serta benang merah bahasan].

📑 DAFTAR ISI PEMBAHASAN
[Judul Poin Pembahasan 1]
[Judul Poin Pembahasan 2]
[Judul Poin Pembahasan 3]
[Judul Poin Pembahasan 4]
(Tulis daftar judul pembahasan, satu baris per judul, tanpa nomor, tanpa bullet, tanpa strip)

📖 PEMBAHASAN LENGKAP
1. [Judul Poin Pembahasan 1]
[Uraian materi mengalir padat, jelas, menggunakan Bahasa Indonesia baku dan elegan tanpa sub-poin bersarang atau sub-header. Jika materi membahas dalil Al-Qur'an atau Hadits, cantumkan teks Arab berharakat lengkap dan terjemahan resmi dalam blok kutipan:
> [Teks Arab Berharakat]
> *"[Terjemahan resmi]"*
Lalu lanjutkan uraian hikmahnya secara langsung].

2. [Judul Poin Pembahasan 2]
[Uraian materi berikutnya...]

3. [Judul Poin Pembahasan 3]
...

💡 KESIMPULAN & PESAN AKSI
[Poin 1 Judul Tebal: Uraian ringkas padat kesimpulan dan amalan nyata]
[Poin 2 Judul Tebal: Uraian ringkas padat kesimpulan dan amalan nyata]
[Poin 3 Judul Tebal: Uraian ringkas padat kesimpulan dan amalan nyata]

PEDOMAN KETAT:
1. PEMBERSIHAN TOTAL:
   - Bersihkan SEMUA timestamp YouTube ("0.000 detik", "00:00", dll).
   - Ubah kosakata percakapan santai, dialek bahasa daerah (misal bahasa Jawa santai: "wis gelem zikir", "loro merer", "lingges kambang", dll), atau celetukan lisan menjadi kalimat bahasa Indonesia resmi, formal, dan berbobot tanpa mengurangi konteks analogi penceramah.
2. DILARANG KERAS MENGGUNAKAN:
   - Sub-header bertingkat (###, ####).
   - Sub-poin bersarang / nested bullets (daftar poin di dalam poin).
   - Catatan kaki / footnotes ([^1], [^2], dll). Semua penjelasan penting harus langsung disatukan ke dalam teks narasi.
3. OUTPUT HARUS BERSIH:
   - Berikan HANYA teks notula markdown di atas, langsung dimulai dari baris "KAJIAN {$judulUpper}".
   - DILARANG menambahkan kata pembuka ("Berikut adalah notula..."), penutup, atau tanda kurung kode markdown (```markdown).

TRANSKRIP MENTAH YOUTUBE YANG AKAN DIPROSES:
\"\"\"
{$transcript}
\"\"\"
PROMPT;
    }

    /**
     * Membangun prompt untuk mengonversi notula lama menjadi format ringkas standar baru.
     */
    protected function buildReformatPrompt(string $oldNotula, array $meta = []): string
    {
        $defaultPlace = !empty($meta['place']) ? $meta['place'] : 'Masjid Salahuddin KPP Madya Malang';
        $defaultSpeaker = !empty($meta['speaker_name']) ? $meta['speaker_name'] : '';
        $defaultTitle = !empty($meta['title']) ? $meta['title'] : '';
        $defaultDate = !empty($meta['date']) ? $meta['date'] : '';

        return <<<PROMPT
Anda adalah Sekretaris Keilmuan dan Ahli Notulensi Eksekutif Kajian Islam resmi Masjid Salahuddin yang sangat teliti, objektif, dan mahir merangkum risalah ilmiah menjadi format yang ringkas, elegan, dan padat.

TUGAS ANDA:
Ubah dan susun kembali naskah risalah/notula kajian lama di bawah ini (yang sebelumnya memiliki format berbelit, terlalu banyak sub-header, sub-poin bersarang, atau catatan kaki/footnotes) menjadi format standar notula baru Masjid Salahuddin yang bersih, elegan, dan padat.

ATURAN STRUKTUR DOKUMEN TARGET (WAJIB PERSIS IKUTI FORMAT BERIKUT):

KAJIAN [JUDUL/TEMA DALAM HURUF BESAR]
Hari, Tanggal : [Ambil hari dan tanggal dari dokumen lama, atau gunakan "{$defaultDate}"]
Tempat : {$defaultPlace}
Pemateri : [Ambil nama lengkap pemateri beserta gelar dari dokumen lama, atau gunakan "{$defaultSpeaker}"]
Tema / Kitab : [Ambil tema kajian atau kitab dari dokumen lama, atau gunakan "{$defaultTitle}"]
Notulis : Tim Media & Notula Masjid Salahuddin

📌 RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)
[Tulis 2 paragraf ringkas, padat, dan berbobot yang merangkum esensi kajian dan latar belakang urgensi materi].

📑 DAFTAR ISI PEMBAHASAN
[Judul Poin Pembahasan 1]
[Judul Poin Pembahasan 2]
[Judul Poin Pembahasan 3]
[Judul Poin Pembahasan 4]
(Daftar judul poin pembahasan, satu baris per judul, tanpa nomor dan tanpa bullet)

📖 PEMBAHASAN LENGKAP
1. [Judul Poin Pembahasan 1]
[Uraian materi yang mengalir secara naratif, padat, jelas, dan berbobot tanpa sub-poin bersarang atau sub-header. Jika dalam materi terdapat pembahasan dalil ayat Al-Qur'an atau Hadits, tetap wajib mencantumkan teks Arab berharakat lengkap dan terjemahan resminya dalam format blok kutipan:
> [Teks Arab Berharakat]
> *"[Terjemahan]"*
Lalu lanjutkan ulasan hikmahnya secara langsung tanpa membuat sub-poin nested atau sub-header tambahan].

2. [Judul Poin Pembahasan 2]
[Uraian materi berikutnya...]

3. [Judul Poin Pembahasan 3]
...

💡 KESIMPULAN & PESAN AKSI
[Poin 1 Judul Tebal: Uraian ringkas padat kesimpulan dan amalan aksi nyata]
[Poin 2 Judul Tebal: Uraian ringkas padat kesimpulan dan amalan aksi nyata]
[Poin 3 Judul Tebal: Uraian ringkas padat kesimpulan dan amalan aksi nyata]

PEDOMAN KETAT:
1. PANGKAS & RINGKAS:
   - Hilangkan semua sub-header (###, ####).
   - Hilangkan semua sub-poin bersarang (nested bullet points).
   - Hilangkan bagian Catatan Kaki (Footnotes [^1], [^2], dll). Jika ada takhrij hadits atau catatan istilah penting di footnote lama, leburkan keterangannya secara ringkas langsung ke dalam alur narasi di nomor pembahasan yang relevan.
2. TETAPKAN DALIL ARAB:
   - Jika dokumen lama memuat dalil Al-Qur'an atau Hadits (teks Arab), tetap pertahankan teks Arab berharakat dan terjemahannya dalam format blok kutipan (>).
3. OUTPUT HARUS BERSIH:
   - Berikan HANYA teks Markdown di atas, langsung dimulai dari baris "KAJIAN [JUDUL]".
   - DILARANG menambahkan kalimat pembuka atau penutup ("Berikut adalah...", dll) dan DILARANG membungkus dengan kode blok markdown (```markdown).

NASKAH NOTULA LAMA YANG AKAN DIUBAH:
\"\"\"
{$oldNotula}
\"\"\"
PROMPT;
    }

    /**
     * Membersihkan kode markdown hasil keluaran AI jika dibungkus code block.
     */
    protected function sanitizeGeneratedMarkdown(string $text): string
    {
        $cleaned = trim($text);

        // Jika terbungkus ```markdown ... ```
        if (str_starts_with($cleaned, '```')) {
            $cleaned = preg_replace('/^```(?:markdown)?\s*/i', '', $cleaned);
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);
            $cleaned = trim($cleaned);
        }

        return $cleaned;
    }

    /**
     * Memanggil HTTP Endpoint Google Gemini API via cURL.
     *
     * @param array<string, mixed> $payload
     * @return array{code: int, body: string|false}
     */
    protected function callGeminiApi(string $url, array $payload, int $timeoutSeconds = 90): array
    {
        $ch = curl_init($url);
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonPayload),
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutSeconds);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => (int) $code,
            'body' => $body,
        ];
    }
}

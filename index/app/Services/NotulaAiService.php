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
        $type = !empty($meta['type']) ? strtolower(trim((string) $meta['type'])) : 'tematik';
        $date = !empty($meta['date']) ? trim((string) $meta['date']) : Carbon::now()->translatedFormat('l, d F Y');

        $docTypeHeader = match ($type) {
            'jumat' => 'Dokumen Risalah Shalat Jumat',
            'pekanan' => 'Dokumen Risalah Kajian Pekanan Rutin',
            default => 'Dokumen Kajian Tafsir Tematik Al-Qur\'an',
        };

        $prompt = $this->buildPrompt($cleanedTranscript, [
            'doc_type' => $docTypeHeader,
            'title' => $title,
            'speaker' => $speaker,
            'date' => $date,
        ]);

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
        $modelsToTry = array_unique([$primaryModel, 'gemini-3-flash-preview', 'gemini-3.1-flash-lite', 'gemini-3.8-flash', 'gemini-3.5-flash', 'gemini-flash-latest']);

        $generatedText = null;
        $lastError = 'Tidak ada respon dari server Gemini.';

        foreach ($modelsToTry as $modelName) {
            $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";
            $response = $this->callGeminiApi($apiUrl, $payload, 90);

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
            }
        }

        if (empty($generatedText)) {
            Log::error("Gagal mentransformasi transkrip YouTube menjadi notula: {$lastError}");
            throw new \RuntimeException("Gagal memproses transkrip dengan Google Gemini AI: {$lastError}");
        }

        return $this->sanitizeGeneratedMarkdown($generatedText);
    }

    /**
     * Membangun prompt sistem komprehensif untuk notulensi eksekutif kajian Masjid Salahuddin.
     */
    protected function buildPrompt(string $transcript, array $meta): string
    {
        return <<<PROMPT
Anda adalah Sekretaris Keilmuan dan Ahli Notulensi Eksekutif Kajian Islam resmi Masjid Salahuddin yang sangat teliti, berwawasan luas dalam Al-Qur'an dan Sunnah, serta mahir merangkum ceramah lisan menjadi risalah formal berbobot tinggi.

TUGAS ANDA:
Ubahlah naskah transkrip mentah rekaman YouTube di bawah ini (yang memuat timestamp waktu seperti "0.000 detik...", dialek lisan sehari-hari, bahasa daerah/Jawa santai, kata pengisi lisan, dan kutipan ayat lisan yang belum terstruktur) menjadi sebuah "Dokumen Risalah / Notula Eksekutif Kajian" yang sangat rapi, mendalam, berwibawa, dan siap cetak.

METADATA KAJIAN SAAT INI:
- Format Dokumen: {$meta['doc_type']}
- Tema / Judul: {$meta['title']}
- Pemateri: {$meta['speaker']}
- Tanggal: {$meta['date']}

STANDAR ATURAN PENYUSUNAN (WAJIB DIIKUTI DENGAN SANGAT KETAT):
1. PEMBERSIHAN TOTAL:
   - Hilangkan SEMUA timestamp waktu ("0.000 detik", "1.441 detik", "00:00", dsb.).
   - Ubah gaya tutur lisan santai atau campuran bahasa daerah (misal bahasa Jawa: "wis gelem zikir", "loro merer gak ono", "sedino gorengan ek lim", "lingges kambang", dsb.) menjadi Bahasa Indonesia ragam baku, anggun, akademis, dan formal tanpa menghilangkan konteks analogi maupun pesan penceramah.

2. EKSTRAKSI AYAT AL-QUR'AN DAN HADITS:
   - Setiap kali penceramah melafalkan penggalan ayat Al-Qur'an atau hadits secara lisan (misal potongan doa Allahumma Antas Salam, ayat tentang surga surah Yunus, doa selamat fid din wa afiyatan fil jasad, dsb.), identifikasi rujukan aslinya secara presisi.
   - Wajib menyertakan:
     a. Teks Arab berharakat lengkap dalam format blok kutipan:
        > [Teks Arab Berharakat]
        > *"Terjemahan resmi Kemenag RI atau terjemahan hadits shahih..."*
     b. Nama surah dan nomor ayat yang jelas (contoh: QS. Yunus: 10, QS. Yunus: 25, QS. Al-Baqarah: 152, HR. Muslim no. 591).
     c. Analisis makna per frasa / kata kunci (*dilalah lafzhiyyah*).

3. STRUKTUR FORMAT DOKUMEN (HARUS PERSIS FORMAT MARKDOWN DI BAWAH):
# {$meta['doc_type']}
**Tema:** {$meta['title']}
**Fokus Bahasan:** [Tuliskan fokus pokok kajian berdasarkan transkrip]
**Pemateri:** {$meta['speaker']}
---

## 1. Intisari dan Latar Belakang Urgensi
[Uraikan latar belakang masalah, urgensi tema dalam kehidupan seorang muslim, dan benang merah materi kajian].

## 2. Analisis Dalil Al-Qur'an dan Sunnah
[Uraikan dalil-dalil utama yang dibahas pemateri. Sertakan teks Arab berharakat lengkap, terjemahan, dan ulasan hikmahnya].

### [Nama Sub-Bahasan Dalil] (QS. [Nama Surah]: [Ayat] atau Riwayat Hadits)
> [Teks Arab Berharakat]
> *"[Terjemahan]"*

* **[Kata Kunci / Frasa Arab 1]:** [Ulasan makna dan pesan teologis/akhlak]
* **[Kata Kunci / Frasa Arab 2]:** [Ulasan makna dan hikmah]

## 3. Hikmah, Konteks Sosial, dan Budaya
[Uraikan hikmah, studi kasus, atau kritik membangun terhadap tradisi masyarakat dan sinkronisasi doa dengan gaya hidup yang dianalogikan oleh pemateri].

## 4. Sesi Diskusi / Tanya Jawab Interaktif
[Jika dalam transkrip ada sesi tanya jawab atau diskusi (misalnya pertanyaan seputar posisi imam setelah salam, kisah para sahabat seperti Umar bin Khattab dan Abu Sulaiman, interaksi pemateri dengan jamaah), rangkum dalam format tanya jawab berbobot yang lugas. Jika tidak ada tanya jawab, rangkum studi kasus penerapan fikih praktis].

## 5. Ringkasan Poin Aksi dan Karakter Pribadi Muslim
1. **[Poin Aksi 1]:** [Penjelasan praktis amalan]
2. **[Poin Aksi 2]:** [Penjelasan praktis amalan]
3. **[Poin Aksi 3]:** [Penjelasan praktis amalan]
4. **[Poin Aksi 4]:** [Penjelasan praktis amalan]
5. **[Poin Aksi 5]:** [Penjelasan praktis amalan]

---

### Catatan Kaki (Footnotes): Informasi Tambahan di Luar Transkrip

[^1]: **[Istilah Fikih / Kaidah]:** [Penjelasan ilmiah tambahan mengenai istilah atau konsep yang disinggung pemateri].
[^2]: **[Identifikasi Referensi Al-Qur'an / Hadits]:** [Keterangan takhrij hadits atau penegasan nomor ayat Al-Qur'an yang dilafalkan secara lisan oleh pemateri].
[^3]: **[Konteks Sejarah / Tokoh]:** [Keterangan biografi singkat sahabat atau tokoh yang disebut dalam transkrip].

(Pastikan setiap catatan kaki [^1], [^2], dst. ditautkan di dalam teks tubuh utama pada kalimat yang relevan).

ATURAN OUTPUT:
- Berikan HANYA teks dokumen Markdown di atas secara langsung dimulai dari "# {$meta['doc_type']}".
- DILARANG menambahkan kalimat pengantar apapun seperti "Berikut adalah hasil notula..." atau penutup.

TRANSKRIP MENTAH YOUTUBE YANG AKAN DIPROSES:
\"\"\"
{$transcript}
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Kajian extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'is_holiday_disabled' => 'boolean',
    ];

    /**
     * Memastikan kolom notula tersedia di tabel kajians (Self-Healing / Auto-Migrate).
     * Mencegah crash / kegagalan simpan jika migration belum dijalankan di server produksi 1Panel.
     */
    public static function ensureNotulaColumnExists(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kajians') && ! \Illuminate\Support\Facades\Schema::hasColumn('kajians', 'notula')) {
                \Illuminate\Support\Facades\Schema::table('kajians', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->longText('notula')->nullable();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika sudah ada atau race condition
        }
    }

    /**
     * Memastikan kolom youtube_url tersedia di tabel kajians (Self-Healing / Auto-Migrate).
     */
    public static function ensureYoutubeColumnExists(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kajians') && ! \Illuminate\Support\Facades\Schema::hasColumn('kajians', 'youtube_url')) {
                \Illuminate\Support\Facades\Schema::table('kajians', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('youtube_url', 500)->nullable();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika sudah ada atau race condition
        }
    }

    /**
     * Ekstraksi ID video YouTube dari berbagai format link (watch, live, embed, youtu.be, shorts).
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|live|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->youtube_url, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * URL Embed YouTube yang aman digunakan dalam iframe preview.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube-nocookie.com/embed/{$id}" : null;
    }

    public function scopePekanan(Builder $query): Builder
    {
        return $query->where('type', 'pekanan');
    }

    public function scopeTematik(Builder $query): Builder
    {
        return $query->where('type', 'tematik');
    }

    public function scopeKajianUmum(Builder $query): Builder
    {
        return $query->whereIn('type', ['pekanan', 'tematik']);
    }

    public function scopeJumat(Builder $query): Builder
    {
        return $query->where('type', 'jumat');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date ? Carbon::parse($this->date)->translatedFormat('l, d F Y') : '-';
    }

    /**
     * URL Foto Pemateri yang terbukti ada secara fisik di storage.
     * Mencegah browser mengirim request HTTP 404 yang memicu delay saat pembuatan poster.
     */
    public function getValidSpeakerPhotoUrlAttribute(): ?string
    {
        if (!empty($this->speaker_photo)) {
            $path = public_path('storage/' . $this->speaker_photo);
            if (file_exists($path)) {
                return asset('storage/' . $this->speaker_photo);
            }
        }
        return null;
    }

    /**
     * Teks Jarkoman WhatsApp terformat sesuai konfigurasi DKM Masjid Salahuddin
     */
    public function getWhatsappBroadcastTextAttribute(): string
    {
        $config = PosterSetting::getAppConfig();

        if ($this->type === 'jumat') {
            $dateCarbon = $this->date ? Carbon::parse($this->date)->locale('id') : Carbon::today('Asia/Jakarta')->locale('id');
            $prayerService = app(\App\Services\PrayerTimeService::class);
            $times = $prayerService->getPrayerTimes(null, $dateCarbon);
            $dzuhurAdzan = null;
            if (!empty($times)) {
                foreach ($times as $p) {
                    if (($p['key'] ?? '') === 'dzuhur') {
                        $dzuhurAdzan = $p['adzan'] ?? null;
                        break;
                    }
                }
            }
            $waktuDzuhur = $dzuhurAdzan ? (str_replace(':', '.', $dzuhurAdzan) . ' WIB') : '11.30 WIB';
            $hijri = $prayerService->getHijriDateOnly($dateCarbon);
            $hijri = str_replace('Awwal', 'Awal', $hijri);
            $masehi = $dateCarbon->translatedFormat('d F Y');
            $khatib = $this->khatib_name ?: '-';
            $muadzin = $this->muadzin_name ?: '-';
            $mc = $this->mc_name ?: '-';

            $template = !empty($config['jarkom_jumat_template']) ? $config['jarkom_jumat_template'] : PosterSetting::defaultJarkomJumatTemplate();

            $replacements = [
                '{khatib}' => $khatib,
                '{muadzin}' => $muadzin,
                '{mc}' => $mc,
                '{waktu_dzuhur}' => $waktuDzuhur,
                '{tanggal_hijri}' => $hijri,
                '{tanggal_masehi}' => $masehi,
                '{hari_tanggal}' => $dateCarbon->translatedFormat('l, d F Y'),
                '{tanggal}' => $masehi,
                '{masjid}' => $config['masjid_line2'] ?? 'Masjid Salahuddin',
            ];

            return str_replace(array_keys($replacements), array_values($replacements), $template);
        }

        // Kajian Pekanan atau Tematik
        $typeLabel = $this->type === 'tematik' ? 'Tematik' : 'Pekanan';
        $speaker = $this->speaker_name ?: '-';
        $dateCarbon = $this->date ? Carbon::parse($this->date)->locale('id') : null;
        $dateStr = $dateCarbon ? $dateCarbon->translatedFormat('l, d F Y') : '-';
        $timeStr = !empty($this->time_display) ? $this->time_display : 'Setelah Sholat Ashar';
        $titleStr = !empty($this->title) ? $this->title : '-';

        $template = !empty($config['jarkom_kajian_template']) ? $config['jarkom_kajian_template'] : PosterSetting::defaultJarkomKajianTemplate();

        $replacements = [
            '{jenis}' => $typeLabel,
            '{pemateri}' => $speaker,
            '{hari_tanggal}' => $dateStr,
            '{tanggal}' => $dateCarbon ? $dateCarbon->translatedFormat('d F Y') : '-',
            '{waktu}' => $timeStr,
            '{tempat}' => !empty($this->location) ? $this->location : 'Masjid Sholahuddin, KPP Madya Malang',
            '{judul}' => $titleStr,
            '{tema}' => $titleStr,
            '{masjid}' => $config['masjid_line2'] ?? 'Masjid Salahuddin',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}

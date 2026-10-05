<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosterSetting extends Model
{
    protected $table = 'poster_settings';

    protected $fillable = [
        'name',
        'template_type',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'template_type' => 'integer',
    ];

    /**
     * Default WhatsApp broadcast text for Kajian (Pekanan & Tematik).
     */
    public static function defaultJarkomKajianTemplate(): string
    {
        return "Assalamualaikum wr wb.\n\n"
            . "Mari hadir dalam kajian {jenis};\n\n"
            . "🕌 Pemateri InsyaAllah oleh\n"
            . "{pemateri}\n\n"
            . "📅 Hari/Tanggal: {hari_tanggal}\n"
            . "🕓 Waktu: {waktu}\n"
            . "📍 Tempat: {tempat}";
    }

    /**
     * Default WhatsApp broadcast text for Khutbah Jumat.
     */
    public static function defaultJarkomJumatTemplate(): string
    {
        return "Assalamu'alaikum wa Rahmatullah wa Barakatuh\n\n"
            . "Semoga keluarga besar KPP Madya Malang senantiasa sehat, sukses, dan berkah selalu.\n\n"
            . "INFO JUMAT\n"
            . "Insya Allah Khotib dan Imam Sholat Jumat hari ini :\n"
            . "👳🏻 Khatib : {khatib}\n"
            . "🎙️ Muadzin : {muadzin}\n"
            . "📄 MC : {mc}\n\n"
            . "Waktu Dzuhur hari ini : {waktu_dzuhur}\n\n"
            . "{tanggal_hijri}\n"
            . "{tanggal_masehi}";
    }

    /**
     * Standard JSON default configuration fallback.
     */
    public static function defaultFallbackConfig(): array
    {
        return [
            'template' => 1,
            'masjid_line1' => 'KPP Madya Malang',
            'masjid_line2' => 'Masjid Salahuddin',
            'logo_url' => '/resources/Logo Masjid Salahuddin.svg',
            'logo_size' => 32,
            'title_font_size' => 42,
            'desc_font_size' => 15,
            'title_y' => 10,
            'desc_y' => 8,
            'title_desc_gap' => 14,
            'schedule_gap' => 20,
            'footer_label' => 'Informasi Kajian :',
            'footer_url' => 'masjidsalahuddin.my.id',
            'content_y' => 0,
            'show_pattern' => true,
            'show_leaves' => true,
            'preview_title1' => 'Kajian',
            'preview_title2' => 'Tematik',
            'preview_subtitle' => "Judul Kajian Judul Kajian Judul Kajian\nJudul Kajian Judul Kajian",
            'jarkom_kajian_template' => self::defaultJarkomKajianTemplate(),
            'jarkom_jumat_template' => self::defaultJarkomJumatTemplate(),
        ];
    }

    /**
     * Retrieve the active app configuration or default fallback.
     */
    public static function getAppConfig(): array
    {
        try {
            $setting = static::where('name', 'default')->first() ?: static::first();
            if ($setting && is_array($setting->config)) {
                $merged = array_merge(static::defaultFallbackConfig(), $setting->config);
                if (!empty($setting->template_type)) {
                    $merged['template'] = (int) $setting->template_type;
                }

                // Proteksi dari URL gambar 404 yang memicu delay timeout jaringan
                if (!empty($merged['logo_url'])) {
                    if (str_contains($merged['logo_url'], 'Logo Masjid-Corner.svg') || str_contains($merged['logo_url'], 'logo-resmi-masjid.svg')) {
                        $merged['logo_url'] = '/resources/Logo Masjid Salahuddin.svg';
                    } else {
                        $parsedPath = parse_url($merged['logo_url'], PHP_URL_PATH) ?? '';
                        $logoPath = public_path(ltrim(urldecode($parsedPath), '/'));
                        if (!file_exists($logoPath)) {
                            $merged['logo_url'] = '/resources/Logo Masjid Salahuddin.svg';
                        }
                    }
                } else {
                    $merged['logo_url'] = '/resources/Logo Masjid Salahuddin.svg';
                }

                return $merged;
            }
        } catch (\Throwable $e) {
            // Silently fallback if database table not yet migrated
        }

        return static::defaultFallbackConfig();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VisitorLog extends Model
{
    use MassPrunable;

    protected $table = 'visitor_logs';

    public $timestamps = false;

    protected $fillable = [
        'ip_address',
        'url',
        'method',
        'device_type',
        'platform',
        'browser',
        'user_agent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Memastikan tabel visitor_logs sudah terbentuk di database (Self-Healing / Auto-Migrate).
     * Mencegah crash jika admin belum menjalankan php artisan migrate di server produksi.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (! Schema::hasTable('visitor_logs')) {
                Schema::create('visitor_logs', function (Blueprint $table) {
                    $table->id();
                    $table->string('ip_address', 45)->nullable()->index();
                    $table->string('url', 255)->index();
                    $table->string('method', 10)->default('GET');
                    $table->string('device_type', 20)->default('desktop')->index();
                    $table->string('platform', 50)->nullable();
                    $table->string('browser', 50)->nullable();
                    $table->string('user_agent', 500)->nullable();
                    $table->timestamp('visited_at')->index();
                });
            }
        } catch (\Throwable $e) {
            // Fail-safe jika tabel dibuat bersamaan atau izin terbatas
        }
    }

    /**
     * Get the prunable model query.
     * Otomatis hapus riwayat log di atas 60 hari untuk menjaga database tetap ramping.
     */
    public function prunable()
    {
        return static::where('visited_at', '<=', now()->subDays(60));
    }

    /**
     * Nama halaman yang ramah dibaca (human-friendly).
     */
    public function getPageTitleAttribute(): string
    {
        $parsed = parse_url($this->url, PHP_URL_PATH) ?? $this->url;
        $path = '/' . trim($parsed, '/');
        if ($path === '/' || $path === '') {
            return 'Beranda Utama';
        }

        return match ($path) {
            '/jadwal-sholat' => 'Jadwal Shalat',
            '/kegiatan', '/kegiatan-masjid' => 'Kegiatan & Kajian',
            '/petugas-sholat' => 'Petugas Shalat',
            '/profil', '/profil-masjid' => 'Profil Masjid',
            '/kas', '/laporan-kas' => 'Laporan Kas',
            default => ucwords(trim(str_replace(['-', '_', '/'], ' ', $path))) ?: 'Beranda Utama',
        };
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityGallery extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'event_date' => 'date',
        'photos' => 'array',
    ];

    /**
     * Memastikan tabel activity_galleries sudah terbentuk di database (Self-Healing / Auto-Migrate).
     * Mencegah crash 500 jika migration belum dijalankan di server produksi 1Panel.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('activity_galleries')) {
                \Illuminate\Support\Facades\Schema::create('activity_galleries', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->date('event_date')->index();
                    $table->string('location');
                    $table->json('photos')->nullable();
                    $table->timestamps();
                });

                $sampleGalleries = [
                    [
                        'title' => 'Kajian Rutin Keislaman Pegawai KPP Madya Malang',
                        'event_date' => '2026-09-24',
                        'location' => 'Masjid Salahuddin, KPP Madya Malang',
                        'photos' => json_encode(['galleries/kajian-rutin-1.webp', 'galleries/shalat-jumat-1.webp']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title' => 'Pelaksanaan Ibadah Shalat Jumat Berjamaah',
                        'event_date' => '2026-09-25',
                        'location' => 'Ruang Utama Masjid Salahuddin',
                        'photos' => json_encode(['galleries/shalat-jumat-1.webp', 'galleries/kajian-rutin-1.webp']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title' => 'Penyaluran Paket Sembako & Santunan Sosial Jamaah',
                        'event_date' => '2026-09-18',
                        'location' => 'Halaman Masjid Salahuddin',
                        'photos' => json_encode(['galleries/santunan-sosial-1.webp', 'galleries/santunan-sosial-2.webp']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];

                \Illuminate\Support\Facades\DB::table('activity_galleries')->insert($sampleGalleries);
            }
        } catch (\Throwable $e) {
            // Fail-safe jika tabel dibuat secara konkuren atau hak akses DDL terbatas
        }
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date ? Carbon::parse($this->event_date)->translatedFormat('l, d F Y') : '-';
    }

    public function getCoverPhotoUrlAttribute(): ?string
    {
        $photos = $this->photos ?: [];
        if (!empty($photos[0])) {
            return asset('storage/' . $photos[0]);
        }
        return null;
    }

    public function getPhotoUrlsAttribute(): array
    {
        $photos = $this->photos ?: [];
        return array_values(array_map(fn($p) => asset('storage/' . $p), $photos));
    }

    public function getPhotosCountAttribute(): int
    {
        return count($this->photos ?: []);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('location', 'like', "%{$term}%");
        });
    }

    public function scopeYear(Builder $query, ?string $year): Builder
    {
        if (empty($year) || $year === 'all') {
            return $query;
        }

        return $query->whereYear('event_date', (int) $year);
    }
}

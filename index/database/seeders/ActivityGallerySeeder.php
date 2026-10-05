<?php

namespace Database\Seeders;

use App\Models\ActivityGallery;
use Illuminate\Database\Seeder;

class ActivityGallerySeeder extends Seeder
{
    public function run(): void
    {
        $galleries = [
            [
                'title' => 'Kajian Rutin Keislaman Pegawai KPP Madya Malang',
                'event_date' => '2026-09-24',
                'location' => 'Masjid Salahuddin, KPP Madya Malang',
                'photos' => [
                    'galleries/kajian-rutin-1.webp',
                    'galleries/shalat-jumat-1.webp',
                ],
            ],
            [
                'title' => 'Pelaksanaan Ibadah Shalat Jumat Berjamaah',
                'event_date' => '2026-09-25',
                'location' => 'Ruang Utama Masjid Salahuddin',
                'photos' => [
                    'galleries/shalat-jumat-1.webp',
                    'galleries/kajian-rutin-1.webp',
                ],
            ],
            [
                'title' => 'Penyaluran Paket Sembako & Santunan Sosial Jamaah',
                'event_date' => '2026-09-18',
                'location' => 'Halaman Masjid Salahuddin',
                'photos' => [
                    'galleries/santunan-sosial-1.webp',
                    'galleries/santunan-sosial-2.webp',
                ],
            ],
        ];

        foreach ($galleries as $data) {
            ActivityGallery::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}

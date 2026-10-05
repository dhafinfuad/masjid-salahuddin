<?php

namespace Database\Seeders;

use App\Models\PrayerDuty;
use Illuminate\Database\Seeder;

class PrayerDutySeeder extends Seeder
{
    public function run(): void
    {
        $duties = [
            // SENIN
            [
                'day_name' => 'Senin',
                'prayer_time' => 'dzuhur',
                'week_pattern' => 'semua',
                'imam_name' => 'Ust. Pujo Santoso',
                'muadzin_name' => 'Mas Zulhaq',
                'notes' => 'Petugas rutin Senin siang',
            ],
            [
                'day_name' => 'Senin',
                'prayer_time' => 'ashar',
                'week_pattern' => 'semua',
                'imam_name' => 'Ust. Ridwan Kamil',
                'muadzin_name' => 'Lukman Hakim',
                'notes' => null,
            ],

            // SELASA
            [
                'day_name' => 'Selasa',
                'prayer_time' => 'dzuhur',
                'week_pattern' => 'semua',
                'imam_name' => 'H. Aris Setianto',
                'muadzin_name' => 'Khudori',
                'notes' => null,
            ],
            [
                'day_name' => 'Selasa',
                'prayer_time' => 'ashar',
                'week_pattern' => 'semua',
                'imam_name' => 'Ust. Sol Djoni Risandy',
                'muadzin_name' => 'Deril Amrizal Kholid',
                'notes' => null,
            ],

            // RABU
            [
                'day_name' => 'Rabu',
                'prayer_time' => 'dzuhur',
                'week_pattern' => 'pekan_1_3_5',
                'imam_name' => 'Ust. Rahman Hakim',
                'muadzin_name' => 'Alan Irfansyah',
                'notes' => 'Pekan Ganjil',
            ],
            [
                'day_name' => 'Rabu',
                'prayer_time' => 'dzuhur',
                'week_pattern' => 'pekan_2_4',
                'imam_name' => 'Ust. Farhan Maulana',
                'muadzin_name' => 'Alan Irfansyah',
                'notes' => 'Pekan Genap',
            ],
            [
                'day_name' => 'Rabu',
                'prayer_time' => 'ashar',
                'week_pattern' => 'semua',
                'imam_name' => 'Yoni Ramadhani',
                'muadzin_name' => 'Mochammad Dzul Hilmi',
                'notes' => null,
            ],

            // KAMIS
            [
                'day_name' => 'Kamis',
                'prayer_time' => 'dzuhur',
                'week_pattern' => 'semua',
                'imam_name' => 'Mahmud Hidayat',
                'muadzin_name' => 'Ugik Endrar Viana',
                'notes' => null,
            ],
            [
                'day_name' => 'Kamis',
                'prayer_time' => 'ashar',
                'week_pattern' => 'semua',
                'imam_name' => 'Ust. Budi Prakoso',
                'muadzin_name' => 'Fathur Rahman',
                'notes' => null,
            ],

            // JUMAT (Ashar - Jumat Dzuhur otomatis disinkronkan dari Kajian Jumat)
            [
                'day_name' => 'Jumat',
                'prayer_time' => 'dzuhur',
                'week_pattern' => 'semua',
                'imam_name' => 'Ust. Cadangan Dzuhur Jumat',
                'muadzin_name' => 'Muadzin Cadangan',
                'notes' => 'Otomatis diisi oleh Khatib & Muadzin Kajian Jumat',
            ],
            [
                'day_name' => 'Jumat',
                'prayer_time' => 'ashar',
                'week_pattern' => 'semua',
                'imam_name' => 'Ust. Hilman Pratama',
                'muadzin_name' => 'Danang Wijaya',
                'notes' => 'Shalat Ashar setelah Shalat Jumat',
            ],
        ];

        foreach ($duties as $duty) {
            $duty['tahun'] = $duty['tahun'] ?? 2026;
            PrayerDuty::firstOrCreate(
                [
                    'day_name' => $duty['day_name'],
                    'prayer_time' => $duty['prayer_time'],
                    'week_pattern' => $duty['week_pattern'],
                    'tahun' => $duty['tahun'],
                ],
                $duty
            );
        }
    }
}

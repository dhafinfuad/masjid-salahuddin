<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\MasjidSetting;
use App\Models\Material;
use App\Models\Registrant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. DKM Users with Specific Roles
        $master = User::updateOrCreate(
            ['email' => 'master@masjidsalahuddin.id'],
            [
                'name' => 'Dr. H. Bambang Irawan (Master Admin)',
                'password' => Hash::make('password'),
                'role' => 'Master',
                'status' => 'AKTIF',
            ]
        );

        $ketua = User::updateOrCreate(
            ['email' => 'admin@masjidsalahuddin.id'],
            [
                'name' => 'Ustadz Abdullah (Ketua DKM)',
                'password' => Hash::make('password'),
                'role' => 'Ketua',
                'status' => 'AKTIF',
            ]
        );

        $sekretaris = User::updateOrCreate(
            ['email' => 'operator@masjidsalahuddin.id'],
            [
                'name' => 'Ahmad Fikri (Sekretaris)',
                'password' => Hash::make('password'),
                'role' => 'Sekretaris',
                'status' => 'AKTIF',
            ]
        );

        $bendahara = User::updateOrCreate(
            ['email' => 'bendahara@masjidsalahuddin.id'],
            [
                'name' => 'H. Mukhlis Syarif (Bendahara)',
                'password' => Hash::make('password'),
                'role' => 'Bendahara',
                'status' => 'AKTIF',
            ]
        );

        $jamaah = User::updateOrCreate(
            ['email' => 'viewer@masjidsalahuddin.id'],
            [
                'name' => 'Haji Mansyur (Jamaah / Penasihat)',
                'password' => Hash::make('password'),
                'role' => 'Jamaah',
                'status' => 'AKTIF',
            ]
        );
        $operator = $sekretaris;

        // 2. Masjid Settings
        MasjidSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Masjid Salahuddin',
                'address' => 'Jl. Jenderal Sudirman No. 45, Senayan, Jakarta Pusat',
                'phone' => '+62 21 555-1234',
                'email' => 'kontak@masjidsalahuddin.id',
                'latitude' => -6.2088000,
                'longitude' => 106.8456000,
                'qibla_angle' => 295.12,
                'calculation_method' => 'KEMENAG',
                'subuh_offset' => 2,
                'dzuhur_offset' => 2,
                'ashar_offset' => 2,
                'maghrib_offset' => 2,
                'isya_offset' => 2,
                'iqamah_delay_minutes' => 10,
                'tv_announcements' => [
                    'Selamat datang di Masjid Salahuddin. Mohon rapatkan dan luruskan shaf sholat demi kesempurnaan ibadah.',
                    "Kajian Rutin Tafsir Al-Mulk setiap Selasa malam ba'da Maghrib bersama Ust. Ahmad Mahfudz, Lc. Terbuka untuk umum.",
                    'Pendaftaran Santri TPA Angkatan ke-14 telah dibuka. Informasi pendaftaran di sekretariat atau portal online.',
                    'Salurkan Infaq & Sedekah terbaik Anda ke Rekening Bank Syariah Indonesia (BSI): 7123-4567-89 a.n. DKM Masjid Salahuddin.',
                ],
                'friday_prayer_info' => [
                    'khatib' => 'Prof. Dr. KH. Nasaruddin Umar, MA',
                    'imam' => 'Ust. H. Syamsul Arifin, Al-Hafizh',
                    'muadzin' => 'Ust. Bilal Ramadhan',
                    'date' => 'Jumat, 15 Mei 2026',
                    'time' => '11:54 WIB',
                ],
                'bank_accounts' => [
                    ['bank' => 'BSI (Bank Syariah Indonesia)', 'account_number' => '7123-4567-89', 'holder' => 'DKM Masjid Salahuddin'],
                    ['bank' => 'BCA Syariah', 'account_number' => '0987-6543-21', 'holder' => 'DKM Masjid Salahuddin'],
                ],
            ]
        );

        // 3. Categories
        $catKajian = Category::withTrashed()->updateOrCreate(['slug' => 'kajian'], [
            'name' => 'Kajian Rutin',
            'color_badge' => 'emerald',
            'description' => 'Kajian tafsir, hadits, dan aqidah islamiyah pekanan.',
            'deleted_at' => null,
        ]);

        $catTahsin = Category::withTrashed()->updateOrCreate(['slug' => 'tahsin'], [
            'name' => 'Tahsin Quran',
            'color_badge' => 'teal',
            'description' => "Bimbingan membaca dan memperbaiki makharijul huruf serta tajwid Al-Qur'an.",
            'deleted_at' => null,
        ]);

        $catTpa = Category::withTrashed()->updateOrCreate(['slug' => 'tpa'], [
            'name' => 'TPA / TPQ',
            'color_badge' => 'amber',
            'description' => "Pendidikan Al-Qur'an anak-anak usia 5-12 tahun.",
            'deleted_at' => null,
        ]);

        $catSosial = Category::withTrashed()->updateOrCreate(['slug' => 'sosial'], [
            'name' => 'Zakat & Sosial',
            'color_badge' => 'rose',
            'description' => 'Layanan pengumpulan dan penyaluran zakat, infaq, dan santunan yatim.',
            'deleted_at' => null,
        ]);

        $catHariBesar = Category::withTrashed()->updateOrCreate(['slug' => 'hari-besar'], [
            'name' => 'Hari Besar Islam',
            'color_badge' => 'indigo',
            'description' => 'Peringatan Maulid Nabi, Isra Mi\'raj, Nuzulul Qur\'an, dan Tahun Baru Hijriyah.',
            'deleted_at' => null,
        ]);

        $catPendidikan = Category::withTrashed()->updateOrCreate(['slug' => 'pendidikan'], [
            'name' => 'Pendidikan',
            'color_badge' => 'emerald',
            'description' => 'Kelas bahasa Arab, fiqih dasar, dan pembinaan muallaf.',
            'deleted_at' => null,
        ]);

        // 4. Events matching prototype datasets
        $ev1 = Event::withTrashed()->updateOrCreate(['slug' => 'kajian-tafsir-al-mulk'], [
            'category_id' => $catKajian->id,
            'title' => 'Kajian Tafsir Al-Mulk',
            'speaker_name' => 'Ust. Ahmad Mahfudz, Lc.',
            'speaker_role' => 'Imam Tetap Masjid Al-Hikmah',
            'event_date' => '2026-05-12',
            'time_display' => "Ba'da Maghrib — 19:30",
            'start_time' => '18:15',
            'end_time' => '19:30',
            'location' => 'Ruang Utama Masjid',
            'capacity' => 200,
            'registered_count' => 142,
            'status' => 'TAYANG',
            'description' => 'Kajian rutin pekanan yang membahas tafsir Surah Al-Mulk dari kitab Tafsir Ibn Katsir. Terbuka untuk seluruh jamaah ikhwan dan akhwat.',
            'deleted_at' => null,
        ]);

        $ev2 = Event::withTrashed()->updateOrCreate(['slug' => 'tahsin-tilawah-pemula'], [
            'category_id' => $catTahsin->id,
            'title' => 'Tahsin Tilawah Pemula',
            'speaker_name' => 'Ustadzah Hafidzah Khairina',
            'speaker_role' => "Pengajar Tahsin Qur'an",
            'event_date' => '2026-05-13',
            'time_display' => '16:00 — 17:30',
            'start_time' => '16:00',
            'end_time' => '17:30',
            'location' => 'Aula Akhwat',
            'capacity' => 40,
            'registered_count' => 40,
            'status' => 'PENUH',
            'description' => "Program intensif membenahi bacaan Al-Qur'an sesuai kaidah tajwid dasar dengan bimbingan ustadzah berpengalaman.",
            'deleted_at' => null,
        ]);

        $ev3 = Event::withTrashed()->updateOrCreate(['slug' => 'buka-pendaftaran-santri-tpa-angkatan-14'], [
            'category_id' => $catTpa->id,
            'title' => 'Buka Pendaftaran Santri TPA Angkatan 14',
            'speaker_name' => 'Panitia TPA Al-Hikmah',
            'speaker_role' => 'Koordinator Pendidikan',
            'event_date' => '2026-05-15',
            'time_display' => '08:00 — 16:00',
            'start_time' => '08:00',
            'end_time' => '16:00',
            'location' => 'Sekretariat',
            'capacity' => 80,
            'registered_count' => 23,
            'status' => 'TAYANG',
            'description' => 'Pendaftaran santri TPA angkatan ke-14 untuk usia 5-12 tahun. Kelas: Iqra, Quran Pemula, dan Quran Lanjutan.',
            'deleted_at' => null,
        ]);

        $ev4 = Event::withTrashed()->updateOrCreate(['slug' => 'penyaluran-zakat-maal-triwulan'], [
            'category_id' => $catSosial->id,
            'title' => 'Penyaluran Zakat Maal Triwulan',
            'speaker_name' => 'Tim BAZNAS Kelurahan',
            'speaker_role' => 'Amil Zakat',
            'event_date' => '2026-05-17',
            'time_display' => '09:00 — 12:00',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'location' => 'Halaman Masjid',
            'capacity' => 150,
            'registered_count' => 87,
            'status' => 'TAYANG',
            'description' => 'Pembagian zakat maal kepada 80 mustahik terdaftar. Mohon hadir tepat waktu dengan membawa kartu identitas dan undangan resmi.',
            'deleted_at' => null,
        ]);

        $ev5 = Event::withTrashed()->updateOrCreate(['slug' => 'tabligh-akbar-tahun-baru-hijriyah'], [
            'category_id' => $catHariBesar->id,
            'title' => 'Tabligh Akbar Tahun Baru Hijriyah',
            'speaker_name' => 'Ust. Dr. Yusuf Hanafi, M.A.',
            'speaker_role' => "Dosen & Da'i Nasional",
            'event_date' => '2026-07-20',
            'time_display' => "Ba'da Isya — 21:00",
            'start_time' => '19:30',
            'end_time' => '21:00',
            'location' => 'Plaza Utama',
            'capacity' => 1000,
            'registered_count' => 0,
            'status' => 'DRAF',
            'description' => 'Peringatan 1 Muharram dengan kajian kebangsaan dan refleksi hijrah Rasulullah SAW.',
            'deleted_at' => null,
        ]);

        $ev6 = Event::withTrashed()->updateOrCreate(['slug' => 'kelas-bahasa-arab-dasar'], [
            'category_id' => $catPendidikan->id,
            'title' => 'Kelas Bahasa Arab Dasar',
            'speaker_name' => 'Ust. Faris Abdullah, M.Pd.',
            'speaker_role' => 'Lulusan Madinah University',
            'event_date' => '2026-05-21',
            'time_display' => '19:00 — 20:30',
            'start_time' => '19:00',
            'end_time' => '20:30',
            'location' => 'Ruang Kelas A',
            'capacity' => 30,
            'registered_count' => 18,
            'status' => 'BERJALAN',
            'description' => "Pekan ke-3 dari 12. Materi pengenalan fi'il madhi dan dhamir muttashil. Modul telah disediakan.",
        ]);

        // 5. Registrants matching prototype datasets
        $registrants = [
            [
                'event_id' => $ev1->id,
                'ticket_code' => 'REG-01',
                'full_name' => 'Bapak Hidayat Wibowo',
                'whatsapp' => '+62 812-8877-6655',
                'email' => 'hidayat.w@gmail.com',
                'gender' => 'ikhwan',
                'status' => 'TERKONFIRMASI',
                'checked_in_at' => null,
            ],
            [
                'event_id' => $ev2->id,
                'ticket_code' => 'REG-02',
                'full_name' => 'Ibu Sumarni Lestari',
                'whatsapp' => '+62 813-9988-1122',
                'email' => 'sumarni@warga.id',
                'gender' => 'akhwat',
                'status' => 'HADIR',
                'checked_in_at' => now()->subHours(5),
                'checked_in_by' => $operator->id,
            ],
            [
                'event_id' => $ev1->id,
                'ticket_code' => 'REG-03',
                'full_name' => 'Sdr. Rifki Nugroho',
                'whatsapp' => '+62 856-1122-3344',
                'email' => 'rifki.n@gmail.com',
                'gender' => 'ikhwan',
                'status' => 'TERKONFIRMASI',
                'checked_in_at' => null,
            ],
            [
                'event_id' => $ev3->id,
                'ticket_code' => 'REG-04',
                'full_name' => 'Bapak Sutopo Hadi',
                'whatsapp' => '+62 818-4455-6677',
                'email' => 'sutopo.h@gmail.com',
                'gender' => 'ikhwan',
                'status' => 'MENUNGGU',
                'checked_in_at' => null,
            ],
            [
                'event_id' => $ev4->id,
                'ticket_code' => 'REG-05',
                'full_name' => 'Ibu Aisyah Rahmani',
                'whatsapp' => '+62 812-3344-5566',
                'email' => 'aisyah@warga.id',
                'gender' => 'akhwat',
                'status' => 'TERKONFIRMASI',
                'checked_in_at' => null,
            ],
            [
                'event_id' => $ev6->id,
                'ticket_code' => 'REG-06',
                'full_name' => 'Sdr. Bagus Pratama',
                'whatsapp' => '+62 878-9900-1122',
                'email' => 'bagus.p@gmail.com',
                'gender' => 'ikhwan',
                'status' => 'TERKONFIRMASI',
                'checked_in_at' => null,
            ],
            [
                'event_id' => $ev2->id,
                'ticket_code' => 'REG-07',
                'full_name' => 'Ibu Khairunnisa',
                'whatsapp' => '+62 811-2233-4455',
                'email' => 'nisa.k@gmail.com',
                'gender' => 'akhwat',
                'status' => 'HADIR',
                'checked_in_at' => now()->subDays(1),
                'checked_in_by' => $operator->id,
            ],
            [
                'event_id' => $ev5->id,
                'ticket_code' => 'REG-08',
                'full_name' => 'Bapak Iskandar Maulana',
                'whatsapp' => '+62 815-6677-8899',
                'email' => 'iskandar.m@gmail.com',
                'gender' => 'ikhwan',
                'status' => 'MENUNGGU',
                'checked_in_at' => null,
            ],
            [
                'event_id' => $ev1->id,
                'ticket_code' => 'REG-09',
                'full_name' => 'Sdr. Faris Akbar',
                'whatsapp' => '+62 812-7788-9900',
                'email' => 'faris.a@gmail.com',
                'gender' => 'ikhwan',
                'status' => 'DIBATALKAN',
                'checked_in_at' => null,
            ],
        ];

        foreach ($registrants as $reg) {
            Registrant::updateOrCreate(
                ['ticket_code' => $reg['ticket_code']],
                $reg
            );
        }

        // 6. Materials
        Material::updateOrCreate(
            ['event_id' => $ev1->id, 'title' => 'Ringkasan Materi Surah Al-Mulk (Ayat 1-15)'],
            [
                'file_path' => 'materials/tafsir-al-mulk-sesi-1.pdf',
                'file_type' => 'pdf',
                'file_size' => 2450000,
                'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'notes' => 'Materi pegangan jamaah untuk kajian tafsir pekan ke-1.',
            ]
        );

        Material::updateOrCreate(
            ['event_id' => $ev6->id, 'title' => 'Modul Nahwu Sharaf Bab Fiil Madhi'],
            [
                'file_path' => 'materials/modul-bahasa-arab-pekan-3.pdf',
                'file_type' => 'pdf',
                'file_size' => 1850000,
                'youtube_url' => null,
                'notes' => 'Latihan tashrif lughawi dan istilahi untuk santri kelas dasar.',
            ]
        );

        $this->call([
            KajianSeeder::class,
            PrayerDutySeeder::class,
            AgendaAndOdojSeeder::class,
            FinanceSeeder::class,
            ActivityGallerySeeder::class,
        ]);
    }
}

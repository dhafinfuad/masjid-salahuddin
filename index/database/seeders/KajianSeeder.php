<?php

namespace Database\Seeders;

use App\Models\Kajian;
use Illuminate\Database\Seeder;

class KajianSeeder extends Seeder
{
    public function run(): void
    {
        Kajian::firstOrCreate(
            ['date' => '2026-09-17', 'type' => 'pekanan'],
            [
                'time_display' => '09:00 - 11:30',
                'title' => 'Tafsir Surat Al-Kahfi: Meneladani Keteguhan Pemuda Beriman',
                'speaker_name' => 'Ustadz Dr. Firdaus, M.A.',
                'speaker_phone' => '+62 812-3456-7890',
            ]
        );

        Kajian::firstOrCreate(
            ['date' => '2026-09-24', 'type' => 'pekanan'],
            [
                'time_display' => '09:00 - 11:30',
                'title' => 'Kajian Riyadush Shalihin: Bab Ikhlas & Keutamaan Niat Bersih',
                'speaker_name' => 'Ustadz Hilman Fauzi, Lc.',
                'speaker_phone' => '+62 813-8877-6655',
            ]
        );

        Kajian::firstOrCreate(
            ['date' => '2026-10-01', 'type' => 'pekanan'],
            [
                'time_display' => '09:00 - 11:30',
                'title' => 'Fiqih Muamalah Syariah: Akad Jual Beli & Investasi Halal',
                'speaker_name' => 'Ustadz Erwandi Tarmizi, Ph.D.',
                'speaker_phone' => '+62 811-2233-4455',
            ]
        );

        Kajian::firstOrCreate(
            ['date' => '2026-09-18', 'type' => 'jumat'],
            [
                'time_display' => '11:45 - 12:45',
                'title' => 'Urgensi Menjaga Amanah dan Kejujuran dalam Kehidupan Berbangsa',
                'is_holiday_disabled' => false,
                'khatib_name' => 'Prof. Dr. KH. Nasaruddin Umar',
                'mc_name' => 'H. Bambang Sugiarto',
                'muadzin_name' => 'Ustadz Bilal Ramadhan',
                'khatib_phone' => '+62 812-9876-5432',
                'mc_notes' => "1. Sholat Jumat dimulai pukul 11.50 WIB.\n2. Dimohon jamaah mengisi shaf depan terlebih dahulu.\n3. Kotak infaq Jumat akan diedarkan selama khutbah kedua.",
            ]
        );

        Kajian::firstOrCreate(
            ['date' => '2026-09-25', 'type' => 'jumat'],
            [
                'time_display' => '11:45 - 12:45',
                'title' => 'Mempersiapkan Bekal Terbaik Menghadap Sang Pencipta',
                'is_holiday_disabled' => false,
                'khatib_name' => 'Ustadz Adi Hidayat, Lc., M.A.',
                'mc_name' => 'Drs. H. Mulyadi',
                'muadzin_name' => 'Ahmad Syauqi',
                'khatib_phone' => '+62 813-1122-3344',
                'mc_notes' => "1. Khutbah Jumat bertema Tazkiyatun Nafs.\n2. Pengumuman penerimaan pendaftaran santri TPA baru.",
            ]
        );

        Kajian::firstOrCreate(
            ['date' => '2026-10-02', 'type' => 'jumat'],
            [
                'time_display' => '11:45 - 12:45',
                'title' => 'Keutamaan Istiqomah di Era Digital',
                'is_holiday_disabled' => false,
                'khatib_name' => 'Ustadz Abdul Somad, Lc., D.E.S.A.',
                'mc_name' => 'Ir. H. Syahrul',
                'muadzin_name' => 'Rizki Pratama',
                'khatib_phone' => '+62 811-9988-7766',
                'mc_notes' => "1. Petugas diharapkan hadir 30 menit sebelum adzan pertama.",
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\OdojEntry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AgendaAndOdojSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Agendas
        if (Agenda::count() === 0) {
            Agenda::create([
                'title' => 'Perayaan Iduladha 1447 H',
                'description' => 'Menyemarakkan perayaan hari raya Iduladha 1447 H di lingkungan masjid salahuddin KPP Madya Malang',
                'event_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'budget' => 65000000,
                'status' => 'Direncanakan',
                'committee_members' => "Khudori (Ketua Panitia)\nJunaedi (Sekretaris)\nBu Indah (Bendahara)\nDeddy (Seksi Logistik)",
                'report_summary' => "LAPORAN KONSOLIDASI & TINJAUAN UMUM\nNama Kegiatan: Perayaan Iduladha 1447 H\nTujuan: Menyemarakkan perayaan hari raya Iduladha 1447 H di lingkungan masjid salahuddin KPP Madya Malang.\nPelaksanaan pemotongan hewan kurban dan pendistribusian daging bagi mustahiq di sekitar wilayah kerja KPP Madya Malang.",
            ]);

            Agenda::create([
                'title' => 'Kegiatan Ramadhan 1447 H',
                'description' => 'menghidupkan bulan Ramadhan di KPP Madya Malang',
                'event_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'budget' => 60000000,
                'status' => 'SELESAI',
                'committee_members' => "Min (Ketua Panitia)\nYohana (Sekretaris)\nMiswati (Bendahara)",
                'report_summary' => "Pelaksanaan serangkaian kegiatan syiar Ramadhan 1447 H telah sukses terlaksana meliputi Kajian Buka Bersama Harian, Shalat Tarawih & Witir Berjamaah, serta I'tikaf 10 Malam Terakhir dengan total realisasi dana Rp 58.450.000.",
            ]);

            Agenda::create([
                'title' => 'Peringatan Maulid Nabi Muhammad SAW 1448 H',
                'description' => 'Peringatan hari kelahiran Rasulullah SAW dengan tabligh akbar dan santunan anak yatim pegawai',
                'event_date' => Carbon::now()->addDays(45)->format('Y-m-d'),
                'budget' => 25000000,
                'status' => 'Berjalan',
                'committee_members' => "Eko (Ketua Pelaksana)\nJuli (Sekretaris)\nDeril (Seksi Acara)\nFahmi (Seksi Konsumsi)",
                'report_summary' => "Persiapan pelaksanaan telah mencapai 75%, koordinasi penceramah utama dan calon penerima santunan telah terkonfirmasi.",
            ]);
        }

        // 2. Seed ODOJ entries for today
        $today = Carbon::today()->format('Y-m-d');
        if (OdojEntry::whereDate('target_date', $today)->count() === 0) {
            $participants = [
                1 => 'Khudori',
                2 => 'Junaedi',
                3 => 'Juli',
                4 => 'Bu Indah',
                5 => 'Min',
                6 => 'Deddy',
                7 => 'Yohana',
                8 => 'Miswati',
                9 => 'Yogi/Deril',
                10 => 'Eko',
                11 => 'Fahmi',
                12 => 'Bambang',
                13 => 'Rizky',
                14 => 'Haryono',
                15 => 'Agus',
                16 => 'Tri',
                17 => 'Wahyu',
                18 => 'Arif',
                19 => 'Hendra',
                20 => 'Surya',
                21 => 'Joko',
                22 => 'Dimas',
                23 => 'Budi',
                24 => 'Fauzi',
                25 => 'Nugroho',
                26 => 'Setiawan',
                27 => 'Gunawan',
                28 => 'Pratama',
                29 => 'Wibowo',
                30 => 'Dhafin Fuad',
            ];

            // Set some as completed for demo realism
            $completedJuz = [1, 2, 3, 5, 7, 8, 10, 12, 15, 18, 20];

            foreach ($participants as $juz => $name) {
                $isDone = in_array($juz, $completedJuz);
                OdojEntry::create([
                    'group_name' => 'Laporan Madya Malang Bertilawah',
                    'jamaah_name' => $name,
                    'juz_number' => $juz,
                    'target_date' => $today,
                    'status' => $isDone ? 'Selesai' : 'Belum',
                    'completed_at' => $isDone ? Carbon::now()->subMinutes(rand(10, 300)) : null,
                ]);
            }
        }
    }
}

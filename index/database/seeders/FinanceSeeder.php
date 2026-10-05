<?php

namespace Database\Seeders;

use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\ProgramParticipant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : null;

        // 1. Categories with Real-World Groups (Penerimaan, Rutin, Non-Rutin)
        $categories = [
            // Penerimaan
            ['name' => 'Saldo Awal', 'type' => 'pemasukan', 'group' => 'penerimaan', 'color' => 'slate'],
            ['name' => 'Infaq Rutin Pegawai (Tukin)', 'type' => 'pemasukan', 'group' => 'penerimaan', 'color' => 'blue'],
            ['name' => 'Kotak Infaq Jumat', 'type' => 'pemasukan', 'group' => 'penerimaan', 'color' => 'emerald'],
            ['name' => 'Infaq QRIS & Donatur Khusus', 'type' => 'pemasukan', 'group' => 'penerimaan', 'color' => 'teal'],
            ['name' => 'Penerimaan Zakat Mal Pegawai', 'type' => 'pemasukan', 'group' => 'penerimaan', 'color' => 'indigo'],
            ['name' => 'Penerimaan Santunan Yatim', 'type' => 'pemasukan', 'group' => 'penerimaan', 'color' => 'amber'],
            ['name' => 'Penerimaan Tabungan Qurban', 'type' => 'pemasukan', 'group' => 'penerimaan', 'color' => 'cyan'],

            // Pengeluaran Rutin
            ['name' => 'Honorarium Khotib & Muadzin Jumat', 'type' => 'pengeluaran', 'group' => 'pengeluaran_rutin', 'color' => 'rose'],
            ['name' => 'Konsumsi Jumat Berkah', 'type' => 'pengeluaran', 'group' => 'pengeluaran_rutin', 'color' => 'amber'],
            ['name' => 'Kajian Pekanan & Bisyarah Asatidz', 'type' => 'pengeluaran', 'group' => 'pengeluaran_rutin', 'color' => 'purple'],
            ['name' => 'Bisyarah Marbot & Kebersihan Rutin', 'type' => 'pengeluaran', 'group' => 'pengeluaran_rutin', 'color' => 'orange'],
            ['name' => 'Operasional Listrik, Air & Jamaah', 'type' => 'pengeluaran', 'group' => 'pengeluaran_rutin', 'color' => 'red'],

            // Pengeluaran Non-Rutin
            ['name' => 'Pengadaan & Servis Alat Kebersihan', 'type' => 'pengeluaran', 'group' => 'pengeluaran_nonrutin', 'color' => 'slate'],
            ['name' => 'Pemeliharaan Sound System & Listrik', 'type' => 'pengeluaran', 'group' => 'pengeluaran_nonrutin', 'color' => 'zinc'],
            ['name' => 'Minyak Wangi Karpet & Pewangi', 'type' => 'pengeluaran', 'group' => 'pengeluaran_nonrutin', 'color' => 'pink'],
            ['name' => 'Biaya Operasional & Kliring Bank', 'type' => 'pengeluaran', 'group' => 'pengeluaran_nonrutin', 'color' => 'stone'],
            ['name' => 'Santunan Anak Yatim', 'type' => 'pengeluaran', 'group' => 'pengeluaran_nonrutin', 'color' => 'emerald'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = FinanceCategory::updateOrCreate(
                ['name' => $cat['name']],
                ['type' => $cat['type'], 'group' => $cat['group'], 'color' => $cat['color']]
            );
        }

        // 2. Finances Transactions
        $today = Carbon::now();
        $qurbanAgenda = \App\Models\Agenda::where('title', 'like', '%Iduladha%')->first();
        $maulidAgenda = \App\Models\Agenda::where('title', 'like', '%Maulid%')->first();

        // Saldo Bulan Lalu (Untuk memastikan saldo berkelanjutan awal bulan berjalan)
        Finance::updateOrCreate(
            ['description' => 'Infaq Kotak Utama Jumat Bulan Lalu'],
            [
                'transaction_date' => Carbon::now()->subMonth()->startOfMonth()->addDays(5)->toDateString(),
                'type' => 'pemasukan',
                'category_id' => $categoryModels['Kotak Infaq Jumat']->id,
                'program_name' => 'Kas Umum',
                'amount' => 15000000,
                'recorded_by' => $adminId,
            ]
        );

        Finance::updateOrCreate(
            ['description' => 'Honorarium Khotib Bulan Lalu'],
            [
                'transaction_date' => Carbon::now()->subMonth()->startOfMonth()->addDays(6)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Honorarium Khotib & Muadzin Jumat']->id,
                'program_name' => 'Kas Umum',
                'amount' => 1500000,
                'recorded_by' => $adminId,
            ]
        );

        // Transaksi Bulan Berjalan
        $transactions = [
            [
                'transaction_date' => Carbon::now()->startOfMonth()->toDateString(),
                'type' => 'pemasukan',
                'category_id' => $categoryModels['Saldo Awal']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 36215031,
                'description' => 'Saldo awal kas kasir masjid awal bulan',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(12)->toDateString(),
                'type' => 'pemasukan',
                'category_id' => $categoryModels['Kotak Infaq Jumat']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 12500000,
                'description' => 'Penerimaan kotak infaq sholat Jumat pekan ke-1',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(10)->toDateString(),
                'type' => 'pemasukan',
                'category_id' => $categoryModels['Infaq Rutin Pegawai (Tukin)']->id,
                'agenda_id' => null,
                'program_name' => 'Infaq Rutin',
                'amount' => 18250000,
                'description' => 'Transfer kliring setoran potongan tukin bulanan pegawai kantor',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(8)->toDateString(),
                'type' => 'pemasukan',
                'category_id' => $categoryModels['Penerimaan Santunan Yatim']->id,
                'agenda_id' => null,
                'program_name' => 'Santunan Anak Yatim',
                'amount' => 7800000,
                'description' => 'Donasi jamaah untuk santunan anak yatim binaan masjid',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(7)->toDateString(),
                'type' => 'pemasukan',
                'category_id' => $categoryModels['Penerimaan Tabungan Qurban']->id,
                'agenda_id' => $qurbanAgenda?->id,
                'program_name' => $qurbanAgenda?->title ?? 'Tabungan Qurban',
                'amount' => 15000000,
                'description' => 'Penerimaan tabungan qurban kolektif jamaah Iduladha',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(6)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Operasional Listrik, Air & Jamaah']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 3450000,
                'description' => 'Pembayaran tagihan listrik PLN dan PDAM bulan berjalan',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(5)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Bisyarah Marbot & Kebersihan Rutin']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 4500000,
                'description' => 'Bisyarah marbot masjid (2 orang) dan petugas kebersihan',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(4)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Honorarium Khotib & Muadzin Jumat']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 1500000,
                'description' => 'Bisyarah khotib & muadzin sholat Jumat pekan berjalan',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(3)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Konsumsi Jumat Berkah']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 2200000,
                'description' => 'Penyediaan 200 porsi nasi kotak Jumat Berkah',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(3)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Santunan Anak Yatim']->id,
                'agenda_id' => null,
                'program_name' => 'Santunan Anak Yatim',
                'amount' => 6000000,
                'description' => 'Penyaluran paket santunan & sembako yatim piatu 30 anak',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(2)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Pemeliharaan Sound System & Listrik']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 2750000,
                'description' => 'Penggantian mikrofon wireless mimbar dan servis 2 unit AC ruang utama',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(1)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Minyak Wangi Karpet & Pewangi']->id,
                'agenda_id' => null,
                'program_name' => 'Kas Umum',
                'amount' => 850000,
                'description' => 'Pembelian bibit minyak wangi karpet dan pengharum otomatis ruang sholat',
            ],
            // Belanja Kegiatan Maulid (Agenda Id 3)
            [
                'transaction_date' => Carbon::now()->subDays(10)->toDateString(),
                'type' => 'pemasukan',
                'category_id' => $categoryModels['Infaq QRIS & Donatur Khusus']->id,
                'agenda_id' => $maulidAgenda?->id,
                'program_name' => $maulidAgenda?->title ?? 'Peringatan Maulid Nabi',
                'amount' => 25000000,
                'description' => 'Penerimaan dana kas masjid & donatur sponsor Maulid Nabi',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(9)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Kajian Pekanan & Bisyarah Asatidz']->id,
                'agenda_id' => $maulidAgenda?->id,
                'program_name' => $maulidAgenda?->title ?? 'Peringatan Maulid Nabi',
                'amount' => 7500000,
                'description' => 'Bisyarah mubaligh utama tabligh akbar Maulid Nabi',
            ],
            [
                'transaction_date' => Carbon::now()->subDays(8)->toDateString(),
                'type' => 'pengeluaran',
                'category_id' => $categoryModels['Konsumsi Jumat Berkah']->id,
                'agenda_id' => $maulidAgenda?->id,
                'program_name' => $maulidAgenda?->title ?? 'Peringatan Maulid Nabi',
                'amount' => 12000000,
                'description' => 'Konsumsi prasmanan jamaah tabligh akbar Maulid Nabi (500 porsi)',
            ],
        ];

        foreach ($transactions as $tx) {
            Finance::updateOrCreate(
                ['description' => $tx['description']],
                array_merge($tx, ['recorded_by' => $adminId])
            );
        }
    }
}

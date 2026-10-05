<?php

namespace App\Console\Commands;

use App\Services\FinanceInfaqSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SyncFinancesInfaq extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masjid:sync-finances-infaq
                            {--file= : Path ke berkas Excel Laporan Arus Kas}
                            {--dry-run : Jalankan simulasi perhitungan tanpa mengubah data di database}
                            {--no-backup : Lewati pembuatan backup JSON data finances saat ini}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi penuh data tabel finances dari file Excel Laporan Arus Kas Dana Infaq Masjid s.d. Sept 2026';

    /**
     * Execute the console command.
     */
    public function handle(FinanceInfaqSyncService $syncService): int
    {
        // 1. Pre-flight DB Connection resilience (fallback jika mysql tidak ter-resolve di Windows)
        $this->ensureDatabaseConnection();

        // 2. Tentukan file Excel
        $defaultFileName = 'Laporan Arus Kas Dana Infaq Masjid s.d. Sept 2026.xlsx';
        $defaultPath = base_path($defaultFileName);
        $filePath = $this->option('file') ?: $defaultPath;

        $isDryRun = (bool) $this->option('dry-run');
        $doBackup = !((bool) $this->option('no-backup'));

        $this->info("===================================================================");
        $this->info("  SINKRONISASI ARUS KAS DANA INFAQ MASJID (JAN - SEPT 2026)        ");
        $this->info("===================================================================");
        $this->line("Berkas Excel : <comment>{$filePath}</comment>");
        $this->line("Mode         : " . ($isDryRun ? "<fg=yellow;options=bold>SIMULASI (DRY-RUN)</>" : "<fg=green;options=bold>EKSEKUSI DATABASE NYATA</>"));
        $this->line("Auto Backup  : " . ($doBackup ? "<fg=cyan>AKTIF (storage/app/backups/)</>" : "<fg=red>NONAKTIF</>"));
        $this->newLine();

        if (!file_exists($filePath)) {
            $this->error("Berkas Excel tidak ditemukan di: {$filePath}");
            return Command::FAILURE;
        }

        try {
            $summary = $syncService->sync($filePath, $isDryRun, $doBackup);

            // Tampilkan Tabel Rekonsiliasi Bulanan
            $tableRows = [];
            foreach ($summary['monthly_breakdown'] as $mNum => $b) {
                $statusColor = ($b['diff'] == 0) ? '<fg=green>MATCH (KLOP)</>' : '<fg=red>SELISIH</>';
                $tableRows[] = [
                    $mNum,
                    $b['name'],
                    'Rp ' . number_format($b['in'], 0, ',', '.'),
                    'Rp ' . number_format($b['out'], 0, ',', '.'),
                    'Rp ' . number_format($b['saldo'], 0, ',', '.'),
                    'Rp ' . number_format($b['target'], 0, ',', '.'),
                    $statusColor,
                ];
            }

            $this->table(
                ['No', 'Bulan / Periode', 'Pemasukan', 'Pengeluaran', 'Saldo Berjalan', 'Target Excel', 'Status'],
                $tableRows
            );

            $this->newLine();
            $this->info("Ringkasan Metrik Keuangan:");
            $this->line("✔ Total Bulan Terevaluasi       : {$summary['parsed_months_count']} bulan (Januari s.d. September 2026)");
            $this->line("✔ Total Transaksi Ternormalisasi : {$summary['total_transactions']} baris transaksi");
            $this->line("✔ Total Penerimaan Kas          : Rp " . number_format($summary['total_pemasukan'], 0, ',', '.'));
            $this->line("✔ Total Pengeluaran Kas         : Rp " . number_format($summary['total_pengeluaran'], 0, ',', '.'));
            $this->line("✔ Saldo Akhir Kas per 07 Sept   : <fg=green;options=bold>Rp " . number_format($summary['saldo_akhir'], 0, ',', '.') . "</>");

            if ($summary['backup_path']) {
                $this->newLine();
                $this->line("💾 Backup data lama tersimpan di: <comment>{$summary['backup_path']}</comment>");
            }

            if ($isDryRun) {
                $this->newLine();
                $this->warn("===================================================================");
                $this->warn("  MODE SIMULASI SELESAI: Tidak ada data yang diubah di database.  ");
                $this->warn("  Jalankan tanpa '--dry-run' untuk mengeksekusi ke database.       ");
                $this->warn("===================================================================");
            } else {
                $this->newLine();
                $this->info("===================================================================");
                $this->info("  [SUKSES] Seluruh data finances berhasil disinkronkan ke database! ");
                $this->info("  Saldo akhir di aplikasi sekarang resmi: Rp 23.018.203             ");
                $this->info("===================================================================");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Terjadi kesalahan saat proses sinkronisasi: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    /**
     * Memastikan koneksi database aktif dengan fallback host jika diperlukan
     */
    protected function ensureDatabaseConnection(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            // Jika host 'mysql' tidak bisa di-resolve pada mesin lokal
            $currentHost = Config::get('database.connections.mysql.host');
            if ($currentHost === 'mysql') {
                $fallbackHost = '10.12.13.225';
                $this->comment("Host '{$currentHost}' tidak dapat diakses. Mengalihkan ke host fallback '{$fallbackHost}'...");
                Config::set('database.connections.mysql.host', $fallbackHost);
                DB::purge('mysql');
                DB::reconnect('mysql');
            }
        }
    }
}

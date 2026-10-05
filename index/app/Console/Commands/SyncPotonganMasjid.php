<?php

namespace App\Console\Commands;

use App\Services\PotonganMasjidSyncService;
use Illuminate\Console\Command;

class SyncPotonganMasjid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masjid:sync-potongan 
                            {--file= : Path ke file Excel Potongan Masjid}
                            {--dry-run : Jalankan simulasi tanpa menyimpan perubahan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data potongan bulanan masjid dari file Excel Januari s.d. Oktober 2026 ke tabel program_participants';

    /**
     * Execute the console command.
     */
    public function handle(PotonganMasjidSyncService $syncService): int
    {
        $defaultPath2 = public_path('resources/Rekapitulasi Potongan Masjid 2.0.xlsx');
        $defaultPathLegacy = public_path('resources/Potongan Masjid.xlsx');
        $filePath = $this->option('file') ?: (file_exists($defaultPath2) ? $defaultPath2 : $defaultPathLegacy);
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("=========================================================");
        $this->info("  SINKRONISASI POTONGAN BULANAN (REKAPITULASI 2.0)       ");
        $this->info("=========================================================");
        $this->line("File Sumber : <comment>{$filePath}</comment>");
        $this->line("Mode        : " . ($isDryRun ? "<fg=yellow>SIMULASI (DRY-RUN)</>" : "<fg=green>EKSEKUSI DATABASE</>"));
        $this->newLine();

        if (!file_exists($filePath)) {
            $this->error("Berkas Excel tidak ditemukan di: {$filePath}");
            return Command::FAILURE;
        }

        try {
            $summary = $syncService->sync($filePath, $isDryRun);

            $this->table(
                ['Program Sosial', 'Bulan Berjalan', 'Nominal Bulan Ini', 'Total Baris', 'Total Nominal', 'Diperbarui', 'Baru'],
                collect($summary['by_program'])->map(function ($p) {
                    return [
                        $p['name'],
                        $p['current_month_count'] . ' org',
                        'Rp ' . number_format($p['current_month_amount'], 0, ',', '.'),
                        $p['total_rows'] . ' baris',
                        'Rp ' . number_format($p['total_amount'], 0, ',', '.'),
                        $p['updated'],
                        $p['inserted'],
                    ];
                })->toArray()
            );

            $this->newLine();
            $this->info("Ringkasan Sinkronisasi:");
            $this->line("✔ Total Baris Potongan di Excel : {$summary['total_rows']}");
            $this->line("✔ Data Diperbarui (Existing)    : {$summary['updated_count']}");
            $this->line("✔ Data Baru Ditambahkan         : {$summary['inserted_count']}");
            $this->line("✔ Periode Berjalan Realtime     : {$summary['current_period']}");
            $this->line("✔ Peserta Bulan Berjalan        : {$summary['current_period_count']} orang");
            $this->line("✔ Komitmen Bulan Berjalan       : Rp " . number_format($summary['current_period_amount'], 0, ',', '.'));
            $this->line("✔ Total Seluruh Periode         : Rp " . number_format($summary['total_amount'], 0, ',', '.'));

            if ($isDryRun) {
                $this->warn("\n[PERINGATAN] Mode simulasi selesai. Tidak ada data yang diubah di database.");
            } else {
                $this->info("\n[SUKSES] Sinkronisasi ke database berhasil diselesaikan secara permanen.");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Terjadi kesalahan saat proses sinkronisasi: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Kajian;
use Illuminate\Console\Command;

class SyncKajianTimesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kajian:sync-times {--force : Paksa sinkronisasi ulang seluruh jadwal kajian}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi kolom time_display dan title jadwal kajian shalat jumat dan pekanan sesuai waktu sholat dinamis per tanggal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->info("=========================================================");
        $this->info("  SINKRONISASI WAKTU SHOLAT JADWAL KAJIAN MASJID         ");
        $this->info("=========================================================");
        $this->line("Mode: " . ($force ? "<fg=yellow>PAKSA ULANG SELURUH JADWAL (--force)</>" : "<fg=green>OTOMATIS (JADWAL LAWAS / BELUM DISINKRONKAN)</>"));
        $this->newLine();

        $updated = Kajian::syncDynamicTimesAndTitles($force);

        $this->info("✓ Berhasil memperbarui {$updated} baris jadwal kajian di database.");
        $this->newLine();

        // Tampilkan 10 sampel jadwal terkini
        $samples = Kajian::orderBy('date', 'desc')->take(10)->get();
        if ($samples->isNotEmpty()) {
            $this->table(
                ['ID', 'Tipe', 'Tanggal', 'Judul / Tema', 'Waktu (time_display)', 'Pembicara / Khatib'],
                $samples->map(function ($k) {
                    return [
                        $k->id,
                        strtoupper($k->type),
                        $k->date ? $k->date->format('Y-m-d') : '-',
                        $k->title ?: ($k->type === 'jumat' ? '<fg=gray>(Khutbah Jumat - Null)</>' : '-'),
                        $k->time_display ?: '<fg=gray>(Null)</>',
                        $k->speaker_name ?: ($k->khatib_name ?: '-'),
                    ];
                })
            );
        }

        return Command::SUCCESS;
    }
}

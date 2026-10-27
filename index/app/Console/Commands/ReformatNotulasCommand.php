<?php

namespace App\Console\Commands;

use App\Models\Kajian;
use App\Services\NotulaAiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReformatNotulasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notula:reformat-all
                            {--id= : Reformat kajian tertentu berdasarkan ID}
                            {--dry-run : Uji coba reformat dan tampilkan preview tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reformat notula kajian eksisting ke format ringkas standar baru Masjid Salahuddin menggunakan Gemini AI';

    /**
     * Execute the console command.
     */
    public function handle(NotulaAiService $aiService): int
    {
        $this->info('=== REFORMAT NOTULA KAJIAN MASJID SALAHUDDIN ===');

        if (! $aiService->isAvailable()) {
            $this->error('Error: Google Gemini API Key belum terkonfigurasi pada file .env.');
            return self::FAILURE;
        }

        $query = Kajian::whereNotNull('notula')->where('notula', '!=', '');

        if ($targetId = $this->option('id')) {
            $query->where('id', $targetId);
        }

        $kajians = $query->orderBy('id')->get();

        if ($kajians->isEmpty()) {
            $this->warn('Tidak ada kajian dengan notula yang ditemukan.');
            return self::SUCCESS;
        }

        $isDryRun = (bool) $this->option('dry-run');
        $this->info("Ditemukan {$kajians->count()} notula untuk diproses" . ($isDryRun ? ' [MODE DRY-RUN]' : '') . '.');

        $successCount = 0;
        $failCount = 0;

        foreach ($kajians as $index => $k) {
            $nomor = $index + 1;
            $this->line('');
            $this->info("[{$nomor}/{$kajians->count()}] Memproses Kajian ID: {$k->id}...");

            $dateFormatted = '';
            if (!empty($k->tanggal)) {
                $dateFormatted = (string) $k->tanggal;
            } elseif (!empty($k->date)) {
                try {
                    $dateFormatted = Carbon::parse($k->date)->translatedFormat('l, d F Y');
                } catch (\Throwable $e) {
                    $dateFormatted = (string) $k->date;
                }
            }

            $fallbackMeta = [
                'title' => $k->judul ?: ($k->title ?: 'Kajian Islam Tematik'),
                'speaker_name' => $k->pemateri ?: ($k->speaker_name ?: 'Asatidz'),
                'date' => $dateFormatted,
                'place' => $k->tempat ?: 'Masjid Salahuddin KPP Madya Malang',
            ];

            try {
                $newNotula = $aiService->reformatExistingNotula($k->notula, $fallbackMeta);

                if ($isDryRun) {
                    $this->comment("Hasil Dry-Run Kajian ID #{$k->id} (Pratinjau):");
                    $this->line(substr($newNotula, 0, 450) . "\n...\n" . substr($newNotula, -250));
                } else {
                    $k->notula = $newNotula;
                    $k->save();
                    $this->info("✓ Kajian ID #{$k->id} berhasil diperbarui ke format baru di database.");
                }

                $successCount++;
            } catch (\Throwable $e) {
                $this->error("✗ Gagal memproses Kajian ID #{$k->id}: " . $e->getMessage());
                $failCount++;
            }

            // Jeda 2 detik antar panggilan API untuk mencegah rate limit / spike 503
            if ($nomor < $kajians->count()) {
                sleep(2);
            }
        }

        $this->line('');
        $this->info("Selesai. Sukses: {$successCount}, Gagal: {$failCount}.");

        return $failCount === 0 ? self::SUCCESS : self::FAILURE;
    }
}

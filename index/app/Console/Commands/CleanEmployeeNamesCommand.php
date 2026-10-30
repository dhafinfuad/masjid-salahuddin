<?php

namespace App\Console\Commands;

use App\Services\EmployeeStatusSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanEmployeeNamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masjid:clean-employee-names {--dry-run : Jalankan simulasi tanpa mengubah data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan seluruh gelar dan kapitalisasi nama di tabel employees, users, prayer_duties, kajians, dan program_participants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $this->info("=========================================================");
        $this->info("  PEMBERSIHAN GELAR & KAPITALISASI NAMA PEGAWAI          ");
        $this->info("=========================================================");
        $this->line("Mode: " . ($isDryRun ? "<fg=yellow>SIMULASI (DRY-RUN)</>" : "<fg=green>EKSEKUSI DATABASE</>"));
        $this->newLine();

        $renameMap = []; // old_raw_name => new_clean_name
        $empUpdated = 0;

        // 1. Bersihkan tabel employees
        if (Schema::hasTable('employees')) {
            $employees = DB::table('employees')->get();
            foreach ($employees as $emp) {
                $raw = (string) $emp->name;
                $clean = EmployeeStatusSyncService::cleanAndCapitalizeName($raw);
                if ($clean !== '' && $clean !== $raw) {
                    $renameMap[$raw] = $clean;
                    $empUpdated++;
                    if (! $isDryRun) {
                        DB::table('employees')->where('id', $emp->id)->update(['name' => $clean]);
                    }
                    $this->line("  [employees] #{$emp->id}: <fg=red>{$raw}</> => <fg=green>{$clean}</>");
                }
            }
        }
        $this->info("Total pegawai disesuaikan: {$empUpdated}");
        $this->newLine();

        // 2. Bersihkan tabel users
        $userUpdated = 0;
        if (Schema::hasTable('users')) {
            $users = DB::table('users')->get();
            foreach ($users as $u) {
                $raw = (string) $u->name;
                $clean = $renameMap[$raw] ?? EmployeeStatusSyncService::cleanAndCapitalizeName($raw);
                if ($clean !== '' && $clean !== $raw) {
                    $userUpdated++;
                    if (! $isDryRun) {
                        DB::table('users')->where('id', $u->id)->update(['name' => $clean]);
                    }
                    $this->line("  [users] #{$u->id}: <fg=red>{$raw}</> => <fg=green>{$clean}</>");
                }
            }
        }
        $this->info("Total akun pengguna disesuaikan: {$userUpdated}");
        $this->newLine();

        // 3. Bersihkan tabel prayer_duties
        $dutyUpdated = 0;
        if (Schema::hasTable('prayer_duties')) {
            $duties = DB::table('prayer_duties')->get();
            foreach ($duties as $d) {
                $up = [];
                if (! empty($d->imam_name)) {
                    $c = $renameMap[$d->imam_name] ?? EmployeeStatusSyncService::cleanAndCapitalizeName($d->imam_name);
                    if ($c !== '' && $c !== $d->imam_name) {
                        $up['imam_name'] = $c;
                        $this->line("  [prayer_duties] #{$d->id} (Imam): <fg=red>{$d->imam_name}</> => <fg=green>{$c}</>");
                    }
                }
                if (! empty($d->muadzin_name)) {
                    $c = $renameMap[$d->muadzin_name] ?? EmployeeStatusSyncService::cleanAndCapitalizeName($d->muadzin_name);
                    if ($c !== '' && $c !== $d->muadzin_name) {
                        $up['muadzin_name'] = $c;
                        $this->line("  [prayer_duties] #{$d->id} (Muadzin): <fg=red>{$d->muadzin_name}</> => <fg=green>{$c}</>");
                    }
                }
                if (! empty($up)) {
                    $dutyUpdated++;
                    if (! $isDryRun) {
                        DB::table('prayer_duties')->where('id', $d->id)->update($up);
                    }
                }
            }
        }
        $this->info("Total jadwal sholat disesuaikan: {$dutyUpdated}");
        $this->newLine();

        // 4. Bersihkan tabel kajians
        $kajianUpdated = 0;
        if (Schema::hasTable('kajians')) {
            $kajians = DB::table('kajians')->get();
            foreach ($kajians as $k) {
                $up = [];
                foreach (['mc_name', 'muadzin_name', 'khatib_name'] as $col) {
                    if (! empty($k->$col)) {
                        $c = $renameMap[$k->$col] ?? EmployeeStatusSyncService::cleanAndCapitalizeName($k->$col);
                        if ($c !== '' && $c !== $k->$col) {
                            $up[$col] = $c;
                            $this->line("  [kajians] #{$k->id} ({$col}): <fg=red>{$k->$col}</> => <fg=green>{$c}</>");
                        }
                    }
                }
                if (! empty($up)) {
                    $kajianUpdated++;
                    if (! $isDryRun) {
                        DB::table('kajians')->where('id', $k->id)->update($up);
                    }
                }
            }
        }
        $this->info("Total data kajian disesuaikan: {$kajianUpdated}");
        $this->newLine();

        // 5. Bersihkan tabel program_participants
        $partUpdated = 0;
        if (Schema::hasTable('program_participants')) {
            $parts = DB::table('program_participants')->get();
            foreach ($parts as $p) {
                $raw = (string) $p->name;
                $clean = $renameMap[$raw] ?? EmployeeStatusSyncService::cleanAndCapitalizeName($raw);
                if ($clean !== '' && $clean !== $raw) {
                    $partUpdated++;
                    if (! $isDryRun) {
                        DB::table('program_participants')->where('id', $p->id)->update(['name' => $clean]);
                    }
                }
            }
        }
        $this->info("Total peserta program sosial disesuaikan: {$partUpdated}");
        $this->newLine();

        $this->info("Pembersihan dan standardisasi nama selesai 100%!");
        return Command::SUCCESS;
    }
}

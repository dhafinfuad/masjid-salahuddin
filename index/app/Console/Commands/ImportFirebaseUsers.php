<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ImportFirebaseUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-firebase-users {filepath : Lokasi berkas JSON ekspor Firebase}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Impor data akun lama dari berkas JSON Firebase ke tabel users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rawPath = $this->argument('filepath');

        // Resolve absolute or relative path
        $filepath = file_exists($rawPath) ? $rawPath : base_path($rawPath);

        if (! file_exists($filepath)) {
            $this->error("Berkas JSON tidak ditemukan pada jalur: {$rawPath}");
            return Command::FAILURE;
        }

        $jsonContent = file_get_contents($filepath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            $this->error("Format berkas JSON tidak valid: " . json_last_error_msg());
            return Command::FAILURE;
        }

        // Support array of users directly or nested under 'users' key
        $usersList = isset($data['users']) && is_array($data['users']) ? $data['users'] : $data;

        if (empty($usersList)) {
            $this->warn("Tidak ada data pengguna yang ditemukan di dalam berkas JSON.");
            return Command::SUCCESS;
        }

        $this->info("Memulai impor " . count($usersList) . " akun dari berkas: {$filepath}");

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar(count($usersList));
        $bar->start();

        foreach ($usersList as $item) {
            $rawEmail = $item['email'] ?? null;

            if (! $rawEmail || ! filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $email = strtolower(trim($rawEmail));
            $name = trim($item['displayName'] ?? $item['name'] ?? $item['nama'] ?? explode('@', $email)[0]);
            $role = $item['role'] ?? 'Jamaah';

            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'name' => $name ?: $user->name,
                    'password' => null,
                    'email_verified_at' => null,
                    'status' => 'AKTIF',
                ]);
                $updated++;
            } else {
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => null,
                    'email_verified_at' => null,
                    'role' => $role,
                    'status' => 'AKTIF',
                ]);
                $created++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Proses impor akun lama Firebase selesai:");
        $this->table(
            ['Kategori', 'Jumlah Akun'],
            [
                ['Akun Baru Dibuat (Password NULL)', $created],
                ['Akun Lama Diperbarui (Reset ke Password NULL)', $updated],
                ['Dilewati (Email Tidak Valid/Kosong)', $skipped],
                ['Total Baris Diproses', count($usersList)],
            ]
        );

        $this->info("Semua akun terimpor belum memiliki kata sandi dan akan diarahkan untuk aktivasi saat login.");

        return Command::SUCCESS;
    }
}

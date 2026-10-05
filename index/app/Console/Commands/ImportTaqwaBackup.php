<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportTaqwaBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masjid:import-taqwa 
                            {file=backup-taqwa-2026-09-18_3kjel.json : Path ke file backup JSON}
                            {--clean-dummy : Hapus data dummy sample sebelum mengimpor}
                            {--dry-run : Uji coba validasi tanpa menyimpan perubahan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data riil dari backup JSON Taqwa ke database Masjid Salahuddin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        if (!file_exists($filePath)) {
            $filePath = base_path($filePath);
        }

        if (!file_exists($filePath)) {
            $this->error("File backup tidak ditemukan: {$filePath}");
            return 1;
        }

        $this->info("Membaca file backup: {$filePath}");
        $raw = file_get_contents($filePath);
        $data = json_decode($raw, true);

        if (!$data || !is_array($data)) {
            $this->error("Format JSON tidak valid atau file kosong.");
            return 1;
        }

        $isDryRun = $this->option('dry-run');
        $cleanDummy = $this->option('clean-dummy');

        if ($isDryRun) {
            $this->warn("=== MODE SIMULASI (DRY RUN): Tidak ada data yang akan ditulis ke database ===");
        }

        DB::beginTransaction();

        try {
            // 1. Update Masjid Setting
            $this->importMasjidSettings($data['mosque_info'] ?? [], $isDryRun);

            // 2. Sync Social Programs
            $programIdMap = $this->importSocialPrograms($data['programs'] ?? [], $isDryRun);

            // 3. Import Program Participants
            $this->importParticipants($data['enrollments'] ?? [], $programIdMap, $cleanDummy, $isDryRun);

            // 4. Import Finances
            $this->importFinances($data['finances'] ?? [], $cleanDummy, $isDryRun);

            // 5. Import Kajians
            $this->importKajians($data['kajian'] ?? [], $cleanDummy, $isDryRun);

            // 6. Import Agendas
            $this->importAgendas($data['activities'] ?? [], $isDryRun);

            // 7. Import Prayer Duties
            $this->importPrayerDuties($data['manual_schedules'] ?? [], $cleanDummy, $isDryRun);

            // 8. Import Users
            $this->importUsers($data['users'] ?? [], $isDryRun);

            if ($isDryRun) {
                DB::rollBack();
                $this->info("\n[DRY RUN SELESAI] Semua validasi pemetaan data berhasil tanpa error!");
            } else {
                DB::commit();
                $this->info("\n[SUKSES] Seluruh data berhasil diimpor ke database aplikasi!");
            }

            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Terjadi kesalahan saat impor: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Helper untuk parse tanggal/timestamp dari Firestore ke string Y-m-d H:i:s
     */
    private function parseDate($val, string $default = '2026-01-01'): string
    {
        if (empty($val)) {
            return $default;
        }

        if (is_array($val) && isset($val['seconds'])) {
            return Carbon::createFromTimestamp($val['seconds'])->toDateTimeString();
        }

        if (is_string($val)) {
            try {
                return Carbon::parse($val)->toDateTimeString();
            } catch (\Exception $e) {
                return $default;
            }
        }

        return $default;
    }

    private function parseDateOnly($val, string $default = '2026-01-01'): string
    {
        $dt = $this->parseDate($val, $default);
        return Carbon::parse($dt)->toDateString();
    }

    /**
     * 1. Import/Update Masjid Settings
     */
    private function importMasjidSettings(array $mosqueInfo, bool $isDryRun): void
    {
        $this->line("\n1. Memproses Profil & Pengaturan Masjid (mosque_info)...");

        $mainInfo = null;
        foreach ($mosqueInfo as $info) {
            if (($info['id'] ?? '') === 'main') {
                $mainInfo = $info;
                break;
            }
        }

        if (!$mainInfo) {
            $this->warn("  - Data mosque_info 'main' tidak ditemukan, melewati.");
            return;
        }

        $setting = MasjidSetting::first();
        if (!$setting) {
            $setting = new MasjidSetting();
        }

        $updatedBanks = [
            [
                'bank' => 'BSI (Bank Syariah Indonesia)',
                'holder' => $mainInfo['bankRecipient'] ?? 'MASJID SALAHUDDIN KPP MADYA MALANG',
                'account_number' => $mainInfo['bankAccount'] ?? '7885858885',
            ],
            [
                'bank' => 'BCA Syariah',
                'holder' => 'DKM Masjid Salahuddin',
                'account_number' => '0987-6543-21',
            ]
        ];

        if (!$isDryRun) {
            $setting->name = 'Masjid Salahuddin';
            $setting->address = $mainInfo['address'] ?? $setting->address;
            $setting->phone = $mainInfo['phone'] ?? $setting->phone;
            $setting->city_id = '1634';
            $setting->city_name = 'KOTA MALANG';
            $setting->bank_accounts = $updatedBanks;
            $setting->save();
        }

        $this->info("  -> Profil masjid diperbarui: Alamat Malang & Rekening BSI ({$mainInfo['bankAccount']}).");
    }

    /**
     * 2. Sync Social Programs
     */
    private function importSocialPrograms(array $programs, bool $isDryRun): array
    {
        $this->line("\n2. Memproses Program Sosial & Dakwah (programs)...");
        $programIdMap = [];

        foreach ($programs as $p) {
            $type = $p['type'] ?? 'infaq';
            $name = $p['name'] ?? 'Program Sosial';
            $backupId = $p['id'];

            // Match based on category / type
            $dbProgram = SocialProgram::where('category', $type)->first();
            if (!$dbProgram) {
                $dbProgram = SocialProgram::where('name', 'like', "%{$type}%")->first();
            }

            if (!$dbProgram) {
                if (!$isDryRun) {
                    $dbProgram = SocialProgram::create([
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'category' => $type,
                        'description' => $p['description'] ?? '',
                        'target_amount' => $p['balance'] ?? 50000000,
                        'period_type' => 'Bulanan',
                        'status' => 'AKTIF',
                    ]);
                } else {
                    $dbProgram = new SocialProgram(['id' => count($programIdMap) + 1, 'name' => $name]);
                }
            } else {
                if (!$isDryRun) {
                    $dbProgram->update([
                        'name' => $name,
                        'description' => $p['description'] ?? $dbProgram->description,
                        'target_amount' => ($p['balance'] ?? 0) > $dbProgram->target_amount ? $p['balance'] : $dbProgram->target_amount,
                        'status' => 'AKTIF',
                    ]);
                }
            }

            $programIdMap[$backupId] = [
                'id' => $dbProgram->id,
                'name' => $dbProgram->name,
            ];

            $this->info("  -> Program terpetakan: [{$backupId}] => [ID: {$dbProgram->id}] {$dbProgram->name}");
        }

        return $programIdMap;
    }

    /**
     * 3. Import Program Participants
     */
    private function importParticipants(array $enrollments, array $programIdMap, bool $cleanDummy, bool $isDryRun): void
    {
        $count = count($enrollments);
        $this->line("\n3. Memproses Peserta Donatur & Tukin (enrollments: {$count} data)...");

        if ($cleanDummy && !$isDryRun) {
            ProgramParticipant::query()->delete();
            $this->warn("  - Data dummy peserta sebelumnya dibersihkan.");
        }

        $imported = 0;
        foreach ($enrollments as $e) {
            $backupProgId = $e['programId'] ?? '';
            $progInfo = $programIdMap[$backupProgId] ?? null;

            $progId = $progInfo['id'] ?? null;
            $progName = $progInfo['name'] ?? 'Infaq Rutin';

            $name = trim($e['fullName'] ?? 'Jamaah');
            $amount = (float) ($e['nominal'] ?? 0);
            $startM = $e['startMonth'] ?? 1;
            $startY = $e['startYear'] ?? 2026;
            $endM = $e['endMonth'] ?? 12;
            $endY = $e['endYear'] ?? 2026;
            $period = "Periode {$startM}/{$startY} - {$endM}/{$endY}";

            $createdAt = $this->parseDate($e['createdAt'] ?? null);

            if (!$isDryRun) {
                ProgramParticipant::create([
                    'social_program_id' => $progId,
                    'name' => $name,
                    'program_name' => $progName,
                    'monthly_amount' => $amount,
                    'period' => $period,
                    'status' => 'AKTIF',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
            $imported++;
        }

        $this->info("  -> Berhasil mengimpor {$imported} data peserta program sosial!");
    }

    /**
     * 4. Import Finances
     */
    private function importFinances(array $finances, bool $cleanDummy, bool $isDryRun): void
    {
        $count = count($finances);
        $this->line("\n4. Memproses Catatan Keuangan Kas Masjid (finances: {$count} transaksi)...");

        if ($cleanDummy && !$isDryRun) {
            Finance::query()->delete();
            $this->warn("  - Data dummy transaksi kas sebelumnya dibersihkan.");
        }

        $imported = 0;
        foreach ($finances as $f) {
            $catName = trim($f['category'] ?? '');
            $desc = trim($f['description'] ?? '');
            $type = ($f['type'] ?? '') === 'in' ? 'pemasukan' : 'pengeluaran';
            $amount = (float) ($f['amount'] ?? 0);
            $date = $this->parseDateOnly($f['date'] ?? null);
            $createdAt = $this->parseDate($f['createdAt'] ?? null, $date . ' 08:00:00');

            // Mapping Category ID
            $catId = $this->mapFinanceCategory($catName, $desc, $type);

            // Tentukan program_name
            $programName = 'Kas Umum';
            if (stripos($catName, 'Tukin') !== false || stripos($desc, 'Tukin') !== false) {
                $programName = 'Infaq Rutin';
            } elseif (stripos($catName, 'Sedekah') !== false || stripos($desc, 'Panti Asuhan') !== false) {
                $programName = 'Santunan Anak Yatim';
            }

            if (!$isDryRun) {
                Finance::create([
                    'transaction_date' => $date,
                    'type' => $type,
                    'category_id' => $catId,
                    'agenda_id' => null,
                    'program_name' => $programName,
                    'amount' => $amount,
                    'description' => $desc,
                    'recorded_by' => 1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
            $imported++;
        }

        $this->info("  -> Berhasil mengimpor {$imported} transaksi keuangan kas masjid!");
    }

    private function mapFinanceCategory(string $category, string $desc, string $type): int
    {
        $catLower = strtolower($category);
        $descLower = strtolower($desc);

        if (stripos($catLower, 'saldo awal') !== false) {
            return 1; // Saldo Awal
        }
        if (stripos($catLower, 'kotak infaq') !== false) {
            return 2; // Kotak Infaq Jumat
        }
        if (stripos($catLower, 'tukin') !== false) {
            return 3; // Infaq Rutin Pegawai (Tukin)
        }
        if (stripos($catLower, 'sedekah') !== false || stripos($catLower, 'bakti sosial') !== false) {
            return 4; // Sedekah & Bakti Sosial
        }
        if (stripos($catLower, 'qris') !== false) {
            return 10; // Infaq QRIS & Donatur Khusus
        }
        if (stripos($catLower, 'jumat berkah') !== false) {
            return 15; // Konsumsi Jumat Berkah
        }
        if (stripos($catLower, 'kafalah') !== false) {
            if (stripos($descLower, 'khotib') !== false) {
                return 14; // Honorarium Khotib & Muadzin Jumat
            }
            if (stripos($descLower, 'kajian') !== false || stripos($descLower, 'tarhib') !== false) {
                return 16; // Kajian Pekanan & Bisyarah Asatidz
            }
            return 8; // Kafalah Marbot & Guru TPA
        }
        if (stripos($catLower, 'fasilitas') !== false) {
            return 6; // Fasilitas & Sarana Masjid
        }
        if (stripos($catLower, 'pengadaan') !== false) {
            return 19; // Pengadaan & Servis Alat Kebersihan
        }
        if (stripos($catLower, 'pemelliharaan') !== false || stripos($catLower, 'pemeliharaan') !== false) {
            return 20; // Pemeliharaan Sound System & Listrik
        }

        return $type === 'pemasukan' ? 2 : 22;
    }

    /**
     * 5. Import Kajians
     */
    private function importKajians(array $kajians, bool $cleanDummy, bool $isDryRun): void
    {
        $count = count($kajians);
        $this->line("\n5. Memproses Agenda Kajian & Khutbah Jumat (kajian: {$count} data)...");

        if ($cleanDummy && !$isDryRun) {
            Kajian::query()->delete();
            $this->warn("  - Data dummy kajian sebelumnya dibersihkan.");
        }

        $imported = 0;
        foreach ($kajians as $kj) {
            $rawType = $kj['type'] ?? 'pekanan';
            $date = $this->parseDateOnly($kj['date'] ?? null);
            $time = trim($kj['time'] ?? '12:00');
            $timeDisplay = str_replace('.', ':', $time);
            if (strlen($timeDisplay) === 5) {
                $timeDisplay .= ' WIB';
            }

            $speaker = trim($kj['speaker'] ?? '');
            $title = trim($kj['title'] ?? '');
            $desc = trim($kj['description'] ?? '');
            $mc = trim($kj['mc'] ?? '');
            $muadzin = trim($kj['muadzin'] ?? '');

            $isPhone = preg_match('/^[0-9\+\-\s]{8,20}$/', $desc);

            if ($rawType === 'khutbah') {
                $type = 'jumat';
                $title = $title ?: 'Khutbah & Shalat Jumat';
                $khatibName = $speaker;
                $khatibPhone = $isPhone ? $desc : null;
                $mcNotes = !$isPhone && !empty($desc) ? $desc : null;
                $speakerName = null;
                $speakerPhone = null;
            } else {
                $type = 'pekanan';
                if ($rawType === 'keputrian') {
                    $title = $title ? "{$title} (Keputrian)" : 'Kajian Keputrian';
                }
                $speakerName = $speaker;
                $speakerPhone = $isPhone ? $desc : null;
                $khatibName = null;
                $khatibPhone = null;
                $mcNotes = !$isPhone && !empty($desc) ? $desc : null;
            }

            $createdAt = $this->parseDate($kj['createdAt'] ?? null, $date . ' 08:00:00');

            if (!$isDryRun) {
                Kajian::create([
                    'type' => $type,
                    'date' => $date,
                    'time_display' => $timeDisplay,
                    'title' => $title,
                    'speaker_name' => $speakerName,
                    'speaker_phone' => $speakerPhone,
                    'is_holiday_disabled' => !empty($kj['isHoliday']),
                    'khatib_name' => $khatibName,
                    'mc_name' => ($mc === '-' || empty($mc)) ? null : $mc,
                    'muadzin_name' => ($muadzin === '-' || empty($muadzin)) ? null : $muadzin,
                    'khatib_phone' => $khatibPhone,
                    'mc_notes' => $mcNotes,
                    'status' => 'AKTIF',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
            $imported++;
        }

        $this->info("  -> Berhasil mengimpor {$imported} jadwal kajian dan khutbah jumat!");
    }

    /**
     * 6. Import Agendas (Activities)
     */
    private function importAgendas(array $activities, bool $isDryRun): void
    {
        $this->line("\n6. Memproses Agenda Kegiatan Besar Masjid (activities: " . count($activities) . " data)...");

        foreach ($activities as $act) {
            $name = trim($act['name'] ?? 'Kegiatan Masjid');
            $purpose = trim($act['purpose'] ?? '');
            $status = ($act['status'] ?? '') === 'finished' ? 'SELESAI' : 'Direncanakan';
            $budgetRaw = preg_replace('/[^0-9]/', '', (string) ($act['activityKas'] ?? '0'));
            $budget = (float) $budgetRaw;

            $panitiaList = $act['panitia'] ?? [];
            $committeeText = is_array($panitiaList) ? implode("\n", $panitiaList) : (string) $panitiaList;

            // Date approximation
            $eventDate = '2026-06-16';
            if (stripos($name, 'Ramadhan') !== false) {
                $eventDate = '2026-03-01';
            }

            $agenda = Agenda::where('title', $name)->first();
            if (!$agenda) {
                $agenda = new Agenda();
            }

            if (!$isDryRun) {
                $agenda->title = $name;
                $agenda->description = $purpose;
                $agenda->event_date = $eventDate;
                $agenda->committee_members = $committeeText;
                $agenda->budget = $budget;
                $agenda->status = $status;
                $agenda->report_summary = ($act['report'] ?? '-') !== '-' ? $act['report'] : "Kegiatan {$name} di lingkungan KPP Madya Malang terlaksana dengan lancar.";
                $agenda->save();
            }

            $this->info("  -> Agenda disinkronkan: {$name} (Anggaran: Rp " . number_format($budget, 0, ',', '.') . ")");
        }
    }

    /**
     * 7. Import Prayer Duties
     */
    private function importPrayerDuties(array $schedules, bool $cleanDummy, bool $isDryRun): void
    {
        $count = count($schedules);
        $this->line("\n7. Memproses Jadwal Petugas Shalat (manual_schedules: {$count} data)...");

        if ($cleanDummy && !$isDryRun) {
            PrayerDuty::query()->delete();
            $this->warn("  - Data dummy petugas shalat sebelumnya dibersihkan.");
        }

        // 1. Ekstrak Muadzin per Hari & Waktu Shalat
        $muadzinMap = [];
        foreach ($schedules as $s) {
            if (($s['type'] ?? '') === 'adzan') {
                $day = trim($s['day'] ?? '');
                $time = strtolower(trim($s['time'] ?? ''));
                $person = trim($s['person'] ?? '');
                $muadzinMap["{$day}_{$time}"] = $person;
            }
        }

        // 2. Pasangkan dengan Imam per Pekan
        $imported = 0;
        foreach ($schedules as $s) {
            if (($s['type'] ?? '') === 'imam') {
                $rawDay = trim($s['day'] ?? '');
                $time = strtolower(trim($s['time'] ?? 'dzuhur'));
                $imam = trim($s['person'] ?? '');

                // Parsing day and week pattern (e.g. "Kamis Pekan 2" or "Selasa")
                $dayName = 'Senin';
                $weekPattern = 'semua';

                foreach (PrayerDuty::DAYS as $d) {
                    if (stripos($rawDay, $d) !== false) {
                        $dayName = $d;
                        break;
                    }
                }

                if (preg_match('/Pekan\s*([1-5])/i', $rawDay, $m)) {
                    $weekPattern = 'pekan_' . $m[1];
                }

                $muadzin = $muadzinMap["{$dayName}_{$time}"] ?? null;

                if (!$isDryRun) {
                    PrayerDuty::create([
                        'prayer_time' => $time,
                        'day_name' => $dayName,
                        'week_pattern' => $weekPattern,
                        'imam_name' => $imam,
                        'muadzin_name' => $muadzin,
                        'notes' => $rawDay,
                    ]);
                }
                $imported++;
            }
        }

        $this->info("  -> Berhasil mengimpor {$imported} jadwal bertugas imam & muadzin!");
    }

    /**
     * 8. Import Users
     */
    private function importUsers(array $users, bool $isDryRun): void
    {
        $count = count($users);
        $this->line("\n8. Memproses Akun Pengguna / Jamaah (users: {$count} data)...");

        $defaultPasswordHash = Hash::make('Salahuddin2026!');
        $imported = 0;
        $updated = 0;

        foreach ($users as $u) {
            $email = strtolower(trim($u['email'] ?? ''));
            if (!$email) {
                continue;
            }

            $name = trim($u['displayName'] ?? 'Jamaah');
            $rawRole = strtolower(trim($u['role'] ?? 'jamaah'));

            $role = match ($rawRole) {
                'master' => 'Master',
                'ketua' => 'Ketua',
                'sekretaris' => 'Sekretaris',
                'bendahara' => 'Bendahara',
                default => 'Jamaah',
            };

            $createdAt = $this->parseDate($u['createdAt'] ?? null);

            $existing = User::where('email', $email)->first();
            if ($existing) {
                if (!$isDryRun) {
                    $existing->update([
                        'name' => $name,
                        'role' => $existing->role ?: $role,
                        'status' => 'AKTIF',
                    ]);
                }
                $updated++;
            } else {
                if (!$isDryRun) {
                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => $defaultPasswordHash,
                        'role' => $role,
                        'status' => 'AKTIF',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
                $imported++;
            }
        }

        $this->info("  -> Akun Pengguna: {$imported} user baru berhasil dibuat, {$updated} user disinkronkan.");
        $this->comment("     (Catatan: Seluruh user baru diberikan default password: 'Salahuddin2026!')");
    }
}

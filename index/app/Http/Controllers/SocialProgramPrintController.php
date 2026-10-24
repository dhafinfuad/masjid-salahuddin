<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MasjidSetting;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use App\Services\EmployeeStatusSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SocialProgramPrintController extends Controller
{
    /**
     * Indonesian Month Names mapping
     */
    protected array $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Canonical 5 programs list
     */
    protected array $canonicalPrograms = [
        'zakat' => [
            'id' => 3,
            'label' => 'Zakat',
            'order' => 1,
        ],
        'infaq' => [
            'id' => 2,
            'label' => 'Infaq & Sedekah',
            'order' => 2,
        ],
        'qurban' => [
            'id' => 4,
            'label' => 'Tabungan Qurban',
            'order' => 3,
        ],
        'yatim' => [
            'id' => 1,
            'label' => 'Santunan Anak Yatim',
            'order' => 4,
        ],
        'dhuafa' => [
            'id' => 5,
            'label' => 'Santunan Dhuafa',
            'order' => 5,
        ],
    ];

    /**
     * Format nama pegawai ke Kapital per Kata (Title Case) dan bersih dari gelar akademik
     */
    public static function formatTitleCaseName(?string $name): ?string
    {
        if (blank($name)) {
            return null;
        }

        $clean = EmployeeStatusSyncService::stripAcademicTitles($name);
        if (blank($clean)) {
            return null;
        }

        return Str::title(strtolower(trim($clean)));
    }

    /**
     * Pencarian cerdas profil Pegawai di database (Exact, Normalized, Substring, hingga User Link)
     */
    protected function findEmployee(?string $name, $employees, $usersByName, $usersByNip): ?Employee
    {
        if (blank($name)) {
            return null;
        }

        $trimmed = trim($name);
        $lower = strtolower($trimmed);
        $normalized = EmployeeStatusSyncService::normalizeName($trimmed);

        // 1. Cocokkan Nama Lengkap Persis
        $exact = $employees->first(fn ($e) => strtolower(trim($e->name)) === $lower);
        if ($exact) {
            return $exact;
        }

        // 2. Cocokkan Nama Ternormalisasi (menghapus gelar , S.Kom., Drs., Ir., spasi ganda, dll)
        $normMatch = $employees->first(fn ($e) => EmployeeStatusSyncService::normalizeName($e->name) === $normalized);
        if ($normMatch) {
            return $normMatch;
        }

        // 3. Cocokkan Substring (jika nama di peserta merupakan bagian nama di master atau sebaliknya)
        if (strlen($normalized) >= 5) {
            $subMatch = $employees->first(function ($e) use ($normalized) {
                $eNorm = EmployeeStatusSyncService::normalizeName($e->name);
                return str_contains($eNorm, $normalized) || str_contains($normalized, $eNorm);
            });
            if ($subMatch) {
                return $subMatch;
            }
        }

        // 4. Cari via tabel User (jika User terhubung dengan data Pegawai via user_id atau NIP)
        $user = $usersByName->get($lower);
        if (! $user && ! empty($normalized)) {
            $user = User::all()->first(fn ($u) => EmployeeStatusSyncService::normalizeName($u->name) === $normalized);
        }

        if ($user) {
            $linked = $employees->first(fn ($e) => $e->user_id === $user->id);
            if ($linked) {
                return $linked;
            }
            if (! empty($user->nip)) {
                $cleanNip = preg_replace('/\D/', '', (string) $user->nip);
                $byNip = $employees->first(fn ($e) => preg_replace('/\D/', '', (string) ($e->nip_panjang ?: $e->nip_pendek)) === $cleanNip);
                if ($byNip) {
                    return $byNip;
                }
            }
        }

        return null;
    }

    /**
     * Cari seluruh data partisipasi (ProgramParticipant) yang cocok untuk pegawai tertentu.
     * Mendukung variasi nama lengkap, nama tanpa gelar akademik (misal A.Md.Pjk., S.E., dll),
     * hingga normalisasi nama toleran.
     */
    protected function getParticipantsForEmployee(?Employee $emp, string $targetName)
    {
        $candidateNames = array_unique(array_filter([
            trim($targetName),
            $emp ? trim($emp->name) : null,
            trim((string) EmployeeStatusSyncService::stripAcademicTitles($targetName)),
            $emp ? trim((string) EmployeeStatusSyncService::stripAcademicTitles($emp->name)) : null,
        ]));

        $participants = ProgramParticipant::where(function ($query) use ($candidateNames) {
            foreach ($candidateNames as $name) {
                if (strlen($name) >= 2) {
                    $query->orWhere('name', $name);
                }
            }
        })->get();

        if ($participants->isEmpty()) {
            $normTarget = EmployeeStatusSyncService::normalizeName($targetName);
            $normEmp = $emp ? EmployeeStatusSyncService::normalizeName($emp->name) : '';

            $participants = ProgramParticipant::all()->filter(function ($p) use ($normTarget, $normEmp) {
                $pNorm = EmployeeStatusSyncService::normalizeName($p->name);
                return (!empty($normTarget) && $pNorm === $normTarget)
                    || (!empty($normEmp) && $pNorm === $normEmp);
            });
        }

        if ($participants->isEmpty()) {
            $normTarget = EmployeeStatusSyncService::normalizeName($targetName);
            if (strlen($normTarget) >= 5) {
                $participants = ProgramParticipant::all()->filter(function ($p) use ($normTarget) {
                    $pNorm = EmployeeStatusSyncService::normalizeName($p->name);
                    return strlen($pNorm) >= 5 && (str_contains($pNorm, $normTarget) || str_contains($normTarget, $pNorm));
                });
            }
        }

        return $participants;
    }

    /**
     * Map participant record to canonical program key
     */
    protected function resolveProgramKey(string $name, ?int $programId = null): ?string
    {
        $lower = strtolower($name);
        if ($programId === 3 || str_contains($lower, 'zakat')) {
            return 'zakat';
        }
        if ($programId === 2 || str_contains($lower, 'infaq') || str_contains($lower, 'sedekah') || str_contains($lower, 'gib')) {
            return 'infaq';
        }
        if ($programId === 4 || str_contains($lower, 'qurban') || str_contains($lower, 'kurban')) {
            return 'qurban';
        }
        if ($programId === 5 || str_contains($lower, 'dhuafa')) {
            return 'dhuafa';
        }
        if ($programId === 1 || str_contains($lower, 'yatim')) {
            return 'yatim';
        }
        return null;
    }

    /**
     * Format compact amount for month cells (e.g. 100k, 50k, 1.5jt, or -)
     */
    protected function formatCompactAmount(float $amount): string
    {
        if ($amount <= 0) {
            return '-';
        }
        if ($amount >= 1000000) {
            $val = $amount / 1000000;
            return ($val == (int)$val ? (int)$val : number_format($val, 1, ',', '.')) . 'jt';
        }
        if ($amount >= 1000) {
            $val = $amount / 1000;
            return ($val == (int)$val ? (int)$val : number_format($val, 1, ',', '.')) . 'k';
        }
        return (string) (int)$amount;
    }

    /**
     * Print Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja
     */
    public function printPdf(Request $request): Response
    {
        $mode = $request->query('mode', 'all'); // 'all' | 'single' | 'new' | 'blank'
        $year = (int) $request->query('year', now()->year);
        $employeeId = $request->query('employee_id');
        $employeeNameQuery = trim((string) $request->query('employee_name', ''));

        // Metadata persuratan dari request / default
        $dateLetter = trim((string) $request->query('date_letter', ''));
        $effectiveMonth = trim((string) $request->query('effective_month', ''));
        $effectiveYear = trim((string) $request->query('effective_year', ''));

        // Default pengurus sekretaris
        $settings = MasjidSetting::first();
        $secretaryName = trim((string) $request->query('secretary_name', 'Deril Amrizal Kholid'));
        $secretarySection = trim((string) $request->query('secretary_section', 'Seksi Penjaminan Kualitas Data'));

        // Fetch master employees and users for fast matching
        $allEmployees = Employee::all();
        $employeesById = $allEmployees->keyBy('id');
        $usersByNip = User::whereNotNull('nip')->get()->keyBy(fn ($u) => preg_replace('/\D/', '', (string)$u->nip));
        $usersByName = User::all()->keyBy(fn ($u) => strtolower(trim($u->name)));

        // Hak akses: Jika pengguna bukan pengurus / tidak canManage(), paksa mode single hanya untuk identitas dirinya sendiri
        $currentUser = auth()->user();
        if ($currentUser && ! $currentUser->canManage()) {
            $mode = 'single';
            $employeeNameQuery = $currentUser->name;
            if ($currentUser->nip) {
                $cleanNip = preg_replace('/\D/', '', (string) $currentUser->nip);
                $foundEmp = $allEmployees->first(function ($e) use ($cleanNip) {
                    return preg_replace('/\D/', '', (string) $e->nip_panjang) === $cleanNip
                        || preg_replace('/\D/', '', (string) $e->nip_pendek) === $cleanNip;
                });
                if ($foundEmp) {
                    $employeeId = $foundEmp->id;
                    $employeeNameQuery = $foundEmp->name;
                }
            }
        }

        $pages = [];

        if ($mode === 'blank') {
            // Halaman kosong manual
            $pages[] = [
                'name' => null,
                'nip' => null,
                'jabatan' => null,
                'seksi' => null,
                'phone' => null,
                'programs' => $this->buildEmptyProgramCommitments(),
                'show_history' => false,
                'history' => [],
            ];
        } elseif ($mode === 'new') {
            // Pegawai baru / belum ada riwayat (Riwayat Potongan disembunyikan)
            $emp = null;
            if ($employeeId && isset($employeesById[$employeeId])) {
                $emp = $employeesById[$employeeId];
            } elseif ($employeeNameQuery) {
                $emp = $this->findEmployee($employeeNameQuery, $allEmployees, $usersByName, $usersByNip);
            }

            $parsedData = $this->extractEmployeeData($emp, $employeeNameQuery, $usersByNip, $usersByName);
            $pages[] = [
                'name' => $parsedData['name'],
                'nip' => $parsedData['nip'],
                'jabatan' => $parsedData['jabatan'],
                'seksi' => $parsedData['seksi'],
                'phone' => $parsedData['phone'],
                'programs' => $this->buildEmptyProgramCommitments(),
                'show_history' => false,
                'history' => [],
            ];
        } elseif ($mode === 'single') {
            // Satu pegawai tertentu (dengan riwayat potongan kondisional)
            $targetName = $employeeNameQuery;
            if ($employeeId && isset($employeesById[$employeeId])) {
                $targetName = $employeesById[$employeeId]->name;
            }

            if (! $targetName) {
                abort(404, 'Nama pegawai tidak ditentukan.');
            }

            $emp = ($employeeId && isset($employeesById[$employeeId]))
                ? $employeesById[$employeeId]
                : $this->findEmployee($targetName, $allEmployees, $usersByName, $usersByNip);

            $parsedData = $this->extractEmployeeData($emp, $targetName, $usersByNip, $usersByName);

            // Ambil semua partisipasi pegawai tersebut (mendukung gelar akademik & normalisasi nama)
            $participants = $this->getParticipantsForEmployee($emp, $targetName);

            $commitments = $this->buildProgramCommitments($participants);
            $history = $this->buildProgramHistory($participants, $year);

            // Evaluasi apakah pegawai pernah memiliki potongan pada tahun $year yang dipilih
            $hasHistoryInYear = false;
            foreach ($history as $h) {
                foreach ($h['months'] as $mVal) {
                    if ($mVal !== '-') {
                        $hasHistoryInYear = true;
                        break 2;
                    }
                }
            }

            $pages[] = [
                'name' => $parsedData['name'],
                'nip' => $parsedData['nip'],
                'jabatan' => $parsedData['jabatan'],
                'seksi' => $parsedData['seksi'],
                'phone' => $parsedData['phone'],
                'programs' => $commitments,
                'show_history' => $hasHistoryInYear,
                'history' => $history,
            ];
        } else {
            // Mode 'all': Mail-merge seluruh pegawai yang memiliki riwayat potongan
            $allParticipants = ProgramParticipant::all()->groupBy(fn ($p) => trim($p->name));

            // Urutkan nama pegawai secara alfabetis
            $sortedNames = $allParticipants->keys()->sort(fn ($a, $b) => strcasecmp($a, $b))->values();

            foreach ($sortedNames as $name) {
                if (blank($name)) {
                    continue;
                }
                $records = $allParticipants->get($name);
                $emp = $this->findEmployee($name, $allEmployees, $usersByName, $usersByNip);

                // Mode 'all': Hanya cetak form untuk pegawai yang saat ini berstatus AKTIF
                if (! $emp || strtoupper(trim((string) $emp->status)) !== 'AKTIF') {
                    continue;
                }

                $parsedData = $this->extractEmployeeData($emp, $name, $usersByNip, $usersByName);

                $commitments = $this->buildProgramCommitments($records);
                $history = $this->buildProgramHistory($records, $year);

                $pages[] = [
                    'name' => $parsedData['name'],
                    'nip' => $parsedData['nip'],
                    'jabatan' => $parsedData['jabatan'],
                    'seksi' => $parsedData['seksi'],
                    'phone' => $parsedData['phone'],
                    'programs' => $commitments,
                    'show_history' => true,
                    'history' => $history,
                ];
            }

            // Jika belum ada data partisipan sama sekali, sajikan 1 template kosong
            if (empty($pages)) {
                $pages[] = [
                    'name' => null,
                    'nip' => null,
                    'jabatan' => null,
                    'seksi' => null,
                    'phone' => null,
                    'programs' => $this->buildEmptyProgramCommitments(),
                    'show_history' => false,
                    'history' => [],
                ];
            }
        }

        return response()->view('admin.print.surat-pernyataan-potongan', [
            'pages' => $pages,
            'year' => $year,
            'dateLetter' => $dateLetter,
            'effectiveMonth' => $effectiveMonth,
            'effectiveYear' => $effectiveYear,
            'secretaryName' => $secretaryName,
            'secretarySection' => $secretarySection,
            'settings' => $settings,
            'canonicalPrograms' => $this->canonicalPrograms,
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Extract employee profile details (Jabatan, Seksi, NIP, Phone)
     */
    protected function extractEmployeeData(?Employee $emp, string $fallbackName, $usersByNip, $usersByName): array
    {
        $rawName = $emp ? $emp->name : ($fallbackName ?: null);
        $name = self::formatTitleCaseName($rawName);

        $rawNip = $emp ? ($emp->nip_panjang ?: $emp->nip_pendek) : null;
        $rawJabatan = $emp ? $emp->jabatan : null;

        $jabatan = null;
        $seksi = null;

        if ($rawJabatan) {
            // Pola umum: "Jabatan (Seksi ...)"
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawJabatan, $matches)) {
                $jabatan = trim($matches[1]);
                $seksi = trim($matches[2]);
            } elseif (preg_match('/(seksi|subbag|kelompok)/i', $rawJabatan)) {
                $jabatan = 'Pelaksana';
                $seksi = $rawJabatan;
            } else {
                $jabatan = $rawJabatan;
                $seksi = $emp->unit_kerja ?: 'KPP Madya Malang';
            }
        } elseif ($emp) {
            $jabatan = 'Pelaksana';
            $seksi = $emp->unit_kerja ?: 'KPP Madya Malang';
        }

        // WhatsApp / Phone resolution
        $phone = null;
        if ($rawNip) {
            $cleanNip = preg_replace('/\D/', '', (string)$rawNip);
            $user = $usersByNip->get($cleanNip);
            if ($user && !empty($user->phone)) {
                $phone = $user->phone;
            }
        }
        if (!$phone && $rawName) {
            $norm = EmployeeStatusSyncService::normalizeName($rawName);
            $user = User::all()->first(fn($u) => EmployeeStatusSyncService::normalizeName($u->name) === $norm);
            if ($user && !empty($user->phone)) {
                $phone = $user->phone;
            }
            if ($user && !empty($user->nip) && empty($rawNip)) {
                $rawNip = $user->nip;
            }
        }

        $formattedNip = $rawNip;

        return [
            'name' => $name,
            'nip' => $formattedNip,
            'jabatan' => $jabatan,
            'seksi' => $seksi,
            'phone' => $phone,
        ];
    }

    /**
     * Build commitments per program for an employee
     */
    protected function buildProgramCommitments($records): array
    {
        $commitments = [];
        foreach ($this->canonicalPrograms as $key => $meta) {
            $commitments[$key] = [
                'order' => $meta['order'],
                'label' => $meta['label'],
                'amount' => 0,
            ];
        }

        if ($records) {
            foreach ($records as $r) {
                $key = $this->resolveProgramKey($r->program_name, $r->social_program_id);
                if ($key && isset($commitments[$key])) {
                    if ((float)$r->monthly_amount > $commitments[$key]['amount']) {
                        $commitments[$key]['amount'] = (float)$r->monthly_amount;
                    }
                }
            }
        }

        return $commitments;
    }

    /**
     * Build empty program commitments for blank/new form
     */
    protected function buildEmptyProgramCommitments(): array
    {
        $commitments = [];
        foreach ($this->canonicalPrograms as $key => $meta) {
            $commitments[$key] = [
                'order' => $meta['order'],
                'label' => $meta['label'],
                'amount' => 0,
            ];
        }
        return $commitments;
    }

    /**
     * Build monthly deduction history matrix (1..12) for an employee in given year
     */
    protected function buildProgramHistory($records, int $year): array
    {
        $matrix = [];
        foreach ($this->canonicalPrograms as $key => $meta) {
            $matrix[$key] = [
                'label' => $meta['label'],
                'months' => array_fill(1, 12, '-'),
            ];
        }

        if ($records) {
            foreach ($records as $r) {
                if (preg_match('/(?:Periode\s+)?(\d{1,2})\/(\d{4})/i', (string)$r->period, $m)) {
                    $mNum = (int)$m[1];
                    $yNum = (int)$m[2];

                    if ($yNum === $year && $mNum >= 1 && $mNum <= 12) {
                        $key = $this->resolveProgramKey($r->program_name, $r->social_program_id);
                        if ($key && isset($matrix[$key])) {
                            $amt = (float)$r->monthly_amount;
                            if ($amt > 0) {
                                $matrix[$key]['months'][$mNum] = $this->formatCompactAmount($amt);
                            }
                        }
                    }
                }
            }
        }

        return $matrix;
    }
}

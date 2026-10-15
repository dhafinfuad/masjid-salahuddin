<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MasjidSetting;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Http\Request;
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
    public function printPdf(Request $request): View
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

        // Fetch master employees index for fast lookup
        $employeesByName = Employee::all()->keyBy(fn ($e) => strtolower(trim($e->name)));
        $employeesById = Employee::all()->keyBy('id');
        $usersByNip = User::whereNotNull('nip')->get()->keyBy(fn ($u) => preg_replace('/\D/', '', (string)$u->nip));
        $usersByName = User::all()->keyBy(fn ($u) => strtolower(trim($u->name)));

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
                $emp = $employeesByName->get(strtolower($employeeNameQuery));
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
            // Satu pegawai tertentu (dengan riwayat potongan)
            $targetName = $employeeNameQuery;
            if ($employeeId && isset($employeesById[$employeeId])) {
                $targetName = $employeesById[$employeeId]->name;
            }

            if (! $targetName) {
                abort(404, 'Nama pegawai tidak ditentukan.');
            }

            $emp = $employeesByName->get(strtolower($targetName)) ?? ($employeeId ? ($employeesById[$employeeId] ?? null) : null);
            $parsedData = $this->extractEmployeeData($emp, $targetName, $usersByNip, $usersByName);

            // Ambil semua partisipasi pegawai tersebut
            $participants = ProgramParticipant::where('name', $targetName)->get();
            $commitments = $this->buildProgramCommitments($participants);
            $history = $this->buildProgramHistory($participants, $year);

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
        } else {
            // Mode 'all': Mail-merge seluruh pegawai yang memiliki riwayat potongan
            // Group by distinct participant names
            $allParticipants = ProgramParticipant::all()->groupBy(fn ($p) => trim($p->name));

            // Urutkan nama pegawai secara alfabetis
            $sortedNames = $allParticipants->keys()->sort(fn ($a, $b) => strcasecmp($a, $b))->values();

            foreach ($sortedNames as $name) {
                if (blank($name)) {
                    continue;
                }
                $records = $allParticipants->get($name);
                $emp = $employeesByName->get(strtolower($name));
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

        return view('admin.print.surat-pernyataan-potongan', [
            'pages' => $pages,
            'year' => $year,
            'dateLetter' => $dateLetter,
            'effectiveMonth' => $effectiveMonth,
            'effectiveYear' => $effectiveYear,
            'secretaryName' => $secretaryName,
            'secretarySection' => $secretarySection,
            'settings' => $settings,
            'canonicalPrograms' => $this->canonicalPrograms,
        ]);
    }

    /**
     * Extract employee profile details (Jabatan, Seksi, NIP, Phone)
     */
    protected function extractEmployeeData(?Employee $emp, string $fallbackName, $usersByNip, $usersByName): array
    {
        $name = $emp ? $emp->name : ($fallbackName ?: null);
        $nip = $emp ? ($emp->nip_panjang ?: $emp->nip_pendek) : null;
        $rawJabatan = $emp ? $emp->jabatan : null;

        $jabatan = null;
        $seksi = null;

        if ($rawJabatan) {
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawJabatan, $matches)) {
                $jabatan = trim($matches[1]);
                $seksi = trim($matches[2]);
            } else {
                $jabatan = $rawJabatan;
                $seksi = $emp->unit_kerja ?: 'KPP Madya Malang';
            }
        } elseif ($emp) {
            $seksi = $emp->unit_kerja ?: 'KPP Madya Malang';
        }

        // WhatsApp / Phone resolution
        $phone = null;
        if ($nip) {
            $cleanNip = preg_replace('/\D/', '', (string)$nip);
            $user = $usersByNip->get($cleanNip);
            if ($user && !empty($user->phone)) {
                $phone = $user->phone;
            }
        }
        if (!$phone && $name) {
            $user = $usersByName->get(strtolower(trim($name)));
            if ($user && !empty($user->phone)) {
                $phone = $user->phone;
            }
        }

        return [
            'name' => $name,
            'nip' => $nip,
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
                    // Simpan nominal komitmen terbesar atau terkini
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
                // Parse month and year from period string: 'Periode 3/2026', 'Periode 03/2026', '3/2026'
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

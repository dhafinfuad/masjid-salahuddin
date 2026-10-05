<?php

namespace App\Services;

use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use SimpleXMLElement;
use ZipArchive;

class FinanceInfaqSyncService
{
    /**
     * Map nama bulan bahasa Indonesia ke nomor bulan
     */
    protected array $monthMap = [
        'JANUARI' => 1,
        'FEBRUARI' => 2,
        'MARET' => 3,
        'APRIL' => 4,
        'MEI' => 5,
        'JUNI' => 6,
        'JULI' => 7,
        'AGUSTUS' => 8,
        'SEPTEMBER' => 9,
    ];

    /**
     * Parse file Excel laporan arus kas dana infaq masjid
     */
    public function parseWorkbook(string $excelFilePath): array
    {
        if (!file_exists($excelFilePath)) {
            throw new \RuntimeException("Berkas Excel tidak ditemukan: {$excelFilePath}");
        }

        $zip = new ZipArchive();
        if ($zip->open($excelFilePath) !== true) {
            throw new \RuntimeException("Gagal membuka arsip Excel: {$excelFilePath}");
        }

        // 1. Shared Strings Table
        $sharedStrings = [];
        $ssXmlStr = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXmlStr) {
            $ssXml = simplexml_load_string($ssXmlStr);
            foreach ($ssXml->si as $si) {
                $text = '';
                if (isset($si->t)) {
                    $text = (string)$si->t;
                } elseif (isset($si->r)) {
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                }
                $sharedStrings[] = $text;
            }
        }

        // 2. Temukan sheet 'Infaq Masjid'
        $wbXmlStr = $zip->getFromName('xl/workbook.xml');
        $wbXml = simplexml_load_string($wbXmlStr);
        $targetSheetId = null;
        foreach ($wbXml->sheets->sheet as $s) {
            if ((string)$s['name'] === 'Infaq Masjid') {
                $targetSheetId = (string)$s->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                break;
            }
        }

        if (!$targetSheetId) {
            $zip->close();
            throw new \RuntimeException("Sheet 'Infaq Masjid' tidak ditemukan dalam file Excel.");
        }

        $relsXmlStr = $zip->getFromName('xl/_rels/workbook.xml.rels');
        $relsXml = simplexml_load_string($relsXmlStr);
        $sheetFile = null;
        foreach ($relsXml->Relationship as $rel) {
            if ((string)$rel['Id'] === $targetSheetId) {
                $sheetFile = 'xl/' . (string)$rel['Target'];
                break;
            }
        }

        $sheetXmlStr = $zip->getFromName($sheetFile);
        $sheetXml = simplexml_load_string($sheetXmlStr);

        $parsedMonths = [];
        $currentMonthKey = null;
        $currentSection = null;

        foreach ($sheetXml->sheetData->row as $row) {
            $rNum = (int)$row['r'];
            if ($rNum < 2690) {
                continue;
            }

            $cells = [];
            foreach ($row->c as $c) {
                $col = preg_replace('/[0-9]/', '', (string)$c['r']);
                $type = (string)$c['t'];
                $val = (string)$c->v;
                if ($type === 's') {
                    $val = $sharedStrings[(int)$val] ?? '';
                }
                $cells[$col] = $val;
            }

            $cA = strtoupper(trim($cells['A'] ?? ''));
            $cB = trim($cells['B'] ?? '');
            $cC = trim($cells['C'] ?? '');
            $cG = (float)($cells['G'] ?? 0);
            $cH = (float)($cells['H'] ?? 0);

            // Deteksi judul bulan 2026
            foreach ($this->monthMap as $mName => $mNum) {
                if (str_contains($cA, $mName) && str_contains($cA, '2026') && !str_contains($cA, 'SALDO') && !str_contains($cA, 'DONASI')) {
                    $currentMonthKey = $mNum;
                    $parsedMonths[$currentMonthKey] = [
                        'name' => $cA,
                        'month_num' => $mNum,
                        'year' => 2026,
                        'saldo_awal' => 0,
                        'penerimaan' => [],
                        'pengeluaran_rutin' => [],
                        'pengeluaran_nonrutin' => [],
                        'total_penerimaan' => 0,
                        'total_pengeluaran' => 0,
                        'saldo_akhir' => 0,
                    ];
                    $currentSection = null;
                    break;
                }
            }

            if ($currentMonthKey === null || !isset($parsedMonths[$currentMonthKey])) {
                continue;
            }

            // Deteksi bagian (section)
            $rowText = strtoupper($cA . ' ' . $cB . ' ' . $cC);
            if (str_contains($rowText, 'PENERIMAAN') && !str_contains($rowText, 'TOTAL')) {
                $currentSection = 'penerimaan';
                continue;
            } elseif (str_contains($rowText, 'PENGELUARAN') && !str_contains($rowText, 'TOTAL') && !str_contains($rowText, 'NON')) {
                $currentSection = 'rutin';
                continue;
            } elseif (str_contains($rowText, 'PENGELUARAN NON-RUTIN') || str_contains($rowText, 'PENGELUARAN NON RUTIN')) {
                $currentSection = 'nonrutin';
                continue;
            }

            if (str_contains($rowText, 'TOTAL PENERIMAAN')) {
                $parsedMonths[$currentMonthKey]['total_penerimaan'] = $cH ?: $cG;
                continue;
            } elseif (str_contains($rowText, 'TOTAL PENGELUARAN')) {
                $parsedMonths[$currentMonthKey]['total_pengeluaran'] = $cH ?: $cG;
                continue;
            } elseif (str_contains($rowText, 'SALDO AKHIR')) {
                $parsedMonths[$currentMonthKey]['saldo_akhir'] = $cH ?: $cG;
                continue;
            }

            if ($currentSection === 'penerimaan') {
                if (!empty($cB) && ($cG > 0 || $cG == 0)) {
                    if (str_contains($cB, 'Saldo Awal')) {
                        $parsedMonths[$currentMonthKey]['saldo_awal'] = $cG;
                    } else {
                        $parsedMonths[$currentMonthKey]['penerimaan'][] = [
                            'description' => $cB,
                            'amount' => $cG,
                            'row' => $rNum,
                        ];
                    }
                }
            } elseif ($currentSection === 'rutin') {
                if (!empty($cC) && $cH > 0) {
                    $parsedMonths[$currentMonthKey]['pengeluaran_rutin'][] = [
                        'description' => $cC,
                        'amount' => $cH,
                        'row' => $rNum,
                    ];
                }
            } elseif ($currentSection === 'nonrutin') {
                if (!empty($cC) && $cH > 0 && $cC !== '-') {
                    $parsedMonths[$currentMonthKey]['pengeluaran_nonrutin'][] = [
                        'description' => $cC,
                        'amount' => $cH,
                        'row' => $rNum,
                    ];
                }
            }
        }

        $zip->close();
        ksort($parsedMonths);

        return $parsedMonths;
    }

    /**
     * Pemetaan kategori keuangan berdasarkan uraian dan jenis transaksi
     */
    public function mapCategory(string $description, string $type, bool $isRutin = false): int
    {
        $d = strtolower(trim($description));

        if ($type === 'pemasukan') {
            if (str_contains($d, 'saldo awal')) {
                return 1; // Saldo Awal
            }
            if (str_contains($d, 'donasi pegawai') || str_contains($d, 'tukin')) {
                return 3; // Infaq Rutin Pegawai (Tukin)
            }
            if (str_contains($d, 'kotak') || str_contains($d, 'qris')) {
                return 10; // Infaq QRIS & Donatur Khusus
            }
            if (str_contains($d, 'karpet')) {
                return 10; // Infaq QRIS & Donatur Khusus
            }
            return 10;
        }

        // Pengeluaran
        if ($isRutin) {
            if (str_contains($d, 'khotib')) {
                return 14; // Honorarium Khotib & Muadzin Jumat
            }
            if (str_contains($d, 'konsumsi') || str_contains($d, 'jumat berkah')) {
                return 15; // Konsumsi Jumat Berkah
            }
            if (str_contains($d, 'kajian') || str_contains($d, 'pengajian') || str_contains($d, 'tarhib') || str_contains($d, 'buka puasa') || str_contains($d, 'keputrian')) {
                return 16; // Kajian Pekanan & Bisyarah Asatidz
            }
            return 16;
        }

        // Pengeluaran Non-Rutin
        if (str_contains($d, 'parfum') || str_contains($d, 'minyak wangi')) {
            return 21; // Minyak Wangi Karpet & Pewangi
        }
        if (str_contains($d, 'kebersihan') || str_contains($d, 'sikat') || str_contains($d, 'laundry') || str_contains($d, 'alat kebersihan')) {
            return 19; // Pengadaan & Servis Alat Kebersihan
        }
        if (str_contains($d, 'panti asuhan') || str_contains($d, 'santunan') || str_contains($d, 'bakti sosial')) {
            return 9; // Santunan Anak Yatim
        }
        if (str_contains($d, 'qurban') || str_contains($d, 'kresek')) {
            return 5; // Tabungan Qurban Jamaah
        }
        if (str_contains($d, 'sound') || str_contains($d, 'listrik') || str_contains($d, 'mikrofon')) {
            return 20; // Pemeliharaan Sound System & Listrik
        }
        
        // Default sarana & prasarana (gorden, lampu, kran, karpet, dll.)
        return 6; // Fasilitas & Sarana Masjid
    }

    /**
     * Generate normalized transaction records from parsed workbook
     */
    public function generateTransactions(array $parsedMonths, ?int $recordedBy = null): array
    {
        $transactions = [];
        $y = 2026;

        // 1. Saldo Awal Utama per 1 Januari 2026
        $transactions[] = [
            'transaction_date' => '2026-01-01',
            'type' => 'pemasukan',
            'category_id' => 1,
            'program_name' => 'Kas Umum',
            'amount' => 36215031.00,
            'description' => 'Saldo Awal Kas Dana Infaq per 31 Desember 2025',
            'recorded_by' => $recordedBy,
        ];

        foreach ($parsedMonths as $mNum => $m) {
            $mStr = str_pad($mNum, 2, '0', STR_PAD_LEFT);

            // A. Penerimaan
            foreach ($m['penerimaan'] as $p) {
                $desc = $p['description'];
                $amt = (float)$p['amount'];
                $catId = $this->mapCategory($desc, 'pemasukan', false);
                $progName = ($catId === 3) ? 'Infaq Rutin' : 'Kas Umum';

                if ($catId === 3) {
                    // Tanggal transfer setoran Tukin
                    $d = ($mNum === 9) ? '01' : (($mNum === 5) ? '07' : (($mNum === 6 || $mNum === 7) ? '08' : '10'));
                    $txDate = "{$y}-{$mStr}-{$d}";
                } elseif (str_contains(strtolower($desc), 'karpet')) {
                    $txDate = "{$y}-{$mStr}-25";
                } else {
                    $lastDay = ($mNum === 9) ? '07' : cal_days_in_month(CAL_GREGORIAN, $mNum, $y);
                    $txDate = "{$y}-{$mStr}-" . str_pad($lastDay, 2, '0', STR_PAD_LEFT);
                }

                $transactions[] = [
                    'transaction_date' => $txDate,
                    'type' => 'pemasukan',
                    'category_id' => $catId,
                    'program_name' => $progName,
                    'amount' => $amt,
                    'description' => $desc,
                    'recorded_by' => $recordedBy,
                ];
            }

            // B. Pengeluaran Rutin
            foreach ($m['pengeluaran_rutin'] as $idx => $pr) {
                $desc = $pr['description'];
                $amt = (float)$pr['amount'];
                $catId = $this->mapCategory($desc, 'pengeluaran', true);

                $day = str_pad(min(28, 5 + ($idx * 7)), 2, '0', STR_PAD_LEFT);
                if ($mNum === 9) {
                    $day = '04'; // periode 1-7 September
                }
                $txDate = "{$y}-{$mStr}-{$day}";

                $transactions[] = [
                    'transaction_date' => $txDate,
                    'type' => 'pengeluaran',
                    'category_id' => $catId,
                    'program_name' => 'Kas Umum',
                    'amount' => $amt,
                    'description' => $desc,
                    'recorded_by' => $recordedBy,
                ];
            }

            // C. Pengeluaran Non-Rutin
            foreach ($m['pengeluaran_nonrutin'] as $idx => $pnr) {
                $desc = $pnr['description'];
                $amt = (float)$pnr['amount'];
                $catId = $this->mapCategory($desc, 'pengeluaran', false);
                $progName = ($catId === 9) ? 'Santunan Anak Yatim' : 'Kas Umum';

                $day = str_pad(min(28, 12 + ($idx * 4)), 2, '0', STR_PAD_LEFT);
                $txDate = "{$y}-{$mStr}-{$day}";

                $transactions[] = [
                    'transaction_date' => $txDate,
                    'type' => 'pengeluaran',
                    'category_id' => $catId,
                    'program_name' => $progName,
                    'amount' => $amt,
                    'description' => $desc,
                    'recorded_by' => $recordedBy,
                ];
            }
        }

        return $transactions;
    }

    /**
     * Backup data tabel finances saat ini ke file JSON
     */
    public function backupCurrentFinances(): string
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $allRows = Finance::orderBy('id', 'asc')->get()->toArray();
        $filename = 'finances_backup_' . date('Ymd_His') . '.json';
        $fullPath = $backupDir . '/' . $filename;

        File::put($fullPath, json_encode([
            'backup_timestamp' => now()->toIso8601String(),
            'total_rows' => count($allRows),
            'finances' => $allRows,
        ], JSON_PRETTY_PRINT));

        return $fullPath;
    }

    /**
     * Eksekusi sinkronisasi penuh ke database
     */
    public function sync(string $excelFilePath, bool $dryRun = false, bool $doBackup = true): array
    {
        $parsedMonths = $this->parseWorkbook($excelFilePath);

        // Cari user admin / recorded_by
        $adminUser = User::whereIn('role', ['Master', 'Ketua', 'Bendahara'])->first()
            ?? User::find(10)
            ?? User::first();
        $recordedBy = $adminUser?->id ?? 1;

        $transactions = $this->generateTransactions($parsedMonths, $recordedBy);

        $backupPath = null;
        if ($doBackup && !$dryRun) {
            $backupPath = $this->backupCurrentFinances();
        }

        $summary = [
            'excel_file' => $excelFilePath,
            'dry_run' => $dryRun,
            'backup_path' => $backupPath,
            'parsed_months_count' => count($parsedMonths),
            'total_transactions' => count($transactions),
            'total_pemasukan' => 0.0,
            'total_pengeluaran' => 0.0,
            'saldo_akhir' => 0.0,
            'monthly_breakdown' => [],
        ];

        foreach ($transactions as $t) {
            if ($t['type'] === 'pemasukan') {
                $summary['total_pemasukan'] += $t['amount'];
            } else {
                $summary['total_pengeluaran'] += $t['amount'];
            }
        }
        $summary['saldo_akhir'] = $summary['total_pemasukan'] - $summary['total_pengeluaran'];

        // Monthly breakdown verification
        $cumSaldo = 0.0;
        for ($m = 1; $m <= 9; $m++) {
            $mIn = 0.0;
            $mOut = 0.0;
            foreach ($transactions as $t) {
                $txM = (int)substr($t['transaction_date'], 5, 2);
                if ($txM === $m) {
                    if ($t['type'] === 'pemasukan') $mIn += $t['amount'];
                    else $mOut += $t['amount'];
                }
            }
            $cumSaldo += ($mIn - $mOut);
            $targetExcel = $parsedMonths[$m]['saldo_akhir'] ?? 0;
            $summary['monthly_breakdown'][$m] = [
                'name' => $parsedMonths[$m]['name'] ?? "Bulan $m",
                'in' => $mIn,
                'out' => $mOut,
                'saldo' => $cumSaldo,
                'target' => $targetExcel,
                'diff' => $cumSaldo - $targetExcel,
            ];
        }

        if (!$dryRun) {
            DB::transaction(function () use ($transactions) {
                // Hapus data finances tahun 2026 (atau seluruhnya karena hanya ada 2026)
                Finance::whereYear('transaction_date', 2026)->delete();

                // Insert massal
                $now = now();
                $rowsToInsert = array_map(function ($tx) use ($now) {
                    return array_merge($tx, [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }, $transactions);

                Finance::insert($rowsToInsert);
            });
        }

        return $summary;
    }
}

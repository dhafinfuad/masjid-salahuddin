<?php

namespace App\Services;

use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use ZipArchive;

class PotonganMasjidSyncService
{
    /**
     * Map of Indonesian and English month names/abbreviations to month numbers (1-12)
     */
    protected array $monthMap = [
        'JANUARI' => 1,
        'JANUARY' => 1,
        'JAN' => 1,
        'FEBRUARI' => 2,
        'FEBRUARY' => 2,
        'FEB' => 2,
        'MARET' => 3,
        'MARCH' => 3,
        'MAR' => 3,
        'APRIL' => 4,
        'APR' => 4,
        'MEI' => 5,
        'MAY' => 5,
        'JUNI' => 6,
        'JUNE' => 6,
        'JUN' => 6,
        'JULI' => 7,
        'JULY' => 7,
        'JUL' => 7,
        'AGUSTUS' => 8,
        'AUGUST' => 8,
        'AGU' => 8,
        'AUG' => 8,
        'SEPTEMBER' => 9,
        'SEPT' => 9,
        'SEP' => 9,
        'OKTOBER' => 10,
        'OCTOBER' => 10,
        'OKT' => 10,
        'OCT' => 10,
        'NOVEMBER' => 11,
        'NOV' => 11,
        'DESEMBER' => 12,
        'DECEMBER' => 12,
        'DES' => 12,
        'DEC' => 12,
    ];

    /**
     * Mapping for program types to social_program_id and canonical name
     */
    protected array $programMap = [
        // Program Zakat Mal Rutin (ID: 3)
        'zakat' => ['id' => 3, 'name' => 'Program Zakat Mal Rutin'],
        'zakat mal' => ['id' => 3, 'name' => 'Program Zakat Mal Rutin'],
        'zakat maal' => ['id' => 3, 'name' => 'Program Zakat Mal Rutin'],
        'zakat mal rutin' => ['id' => 3, 'name' => 'Program Zakat Mal Rutin'],
        'program zakat mal' => ['id' => 3, 'name' => 'Program Zakat Mal Rutin'],
        'program zakat mal rutin' => ['id' => 3, 'name' => 'Program Zakat Mal Rutin'],

        // Infaq Rutin (ID: 2)
        'infaq' => ['id' => 2, 'name' => 'Infaq Rutin'],
        'infaq rutin' => ['id' => 2, 'name' => 'Infaq Rutin'],
        'program infaq rutin' => ['id' => 2, 'name' => 'Infaq Rutin'],
        'gib' => ['id' => 2, 'name' => 'Infaq Rutin'],
        'gerakan infaq beras' => ['id' => 2, 'name' => 'Infaq Rutin'],
        'infaq beras' => ['id' => 2, 'name' => 'Infaq Rutin'],

        // Program Santunan Anak Yatim (ID: 1)
        'pot anak yatim' => ['id' => 1, 'name' => 'Program Santunan Anak Yatim'],
        'anak yatim' => ['id' => 1, 'name' => 'Program Santunan Anak Yatim'],
        'yatim' => ['id' => 1, 'name' => 'Program Santunan Anak Yatim'],
        'santunan anak yatim' => ['id' => 1, 'name' => 'Program Santunan Anak Yatim'],
        'program santunan anak yatim' => ['id' => 1, 'name' => 'Program Santunan Anak Yatim'],
        'santunan yatim' => ['id' => 1, 'name' => 'Program Santunan Anak Yatim'],

        // Program Santunan Dhuafa (ID: 5)
        'santunan dhuafa' => ['id' => 5, 'name' => 'Program Santunan Dhuafa'],
        'dhuafa' => ['id' => 5, 'name' => 'Program Santunan Dhuafa'],
        'pot dhuafa' => ['id' => 5, 'name' => 'Program Santunan Dhuafa'],
        'potongan dhuafa' => ['id' => 5, 'name' => 'Program Santunan Dhuafa'],
        'program santunan dhuafa' => ['id' => 5, 'name' => 'Program Santunan Dhuafa'],

        // Tabungan Qurban (ID: 4)
        'tabungan qurban' => ['id' => 4, 'name' => 'Tabungan Qurban'],
        'qurban' => ['id' => 4, 'name' => 'Tabungan Qurban'],
        'kurban' => ['id' => 4, 'name' => 'Tabungan Qurban'],
        'tabungan kurban' => ['id' => 4, 'name' => 'Tabungan Qurban'],
        'program tabungan qurban' => ['id' => 4, 'name' => 'Tabungan Qurban'],
    ];

    /**
     * Resolve month raw string to month integer (1-12)
     */
    public function resolveMonthNum(string $monthRaw): ?int
    {
        $clean = strtoupper(trim($monthRaw));
        if ($clean === '') {
            return null;
        }

        if (isset($this->monthMap[$clean])) {
            return $this->monthMap[$clean];
        }

        if (is_numeric($clean)) {
            $val = (int)$clean;
            return ($val >= 1 && $val <= 12) ? $val : null;
        }

        foreach ($this->monthMap as $mName => $mNum) {
            if (str_contains($clean, $mName)) {
                return $mNum;
            }
        }

        if (preg_match('/(?:^|[^\d])(0?[1-9]|1[0-2])(?:\/|-|\s)/', $clean, $m)) {
            return (int)$m[1];
        }

        return null;
    }

    /**
     * Resolve program type raw string to program ID and canonical name
     */
    public function resolveProgram(string $typeRaw, $programsFromDb = null): ?array
    {
        $cleanType = strtolower(trim(preg_replace('/\s+/', ' ', $typeRaw)));
        if ($cleanType === '') {
            return null;
        }

        if (isset($this->programMap[$cleanType])) {
            return $this->programMap[$cleanType];
        }

        foreach ($this->programMap as $key => $mapping) {
            if (str_contains($cleanType, $key) || str_contains($key, $cleanType)) {
                return $mapping;
            }
        }

        if ($programsFromDb) {
            foreach ($programsFromDb as $prog) {
                $dbName = strtolower(trim($prog->name));
                if ($dbName === $cleanType || str_contains($cleanType, $dbName) || str_contains($dbName, $cleanType)) {
                    return [
                        'id' => $prog->id,
                        'name' => $prog->name,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Parse the Excel workbook into normalized participant rows.
     * Supports Rekapitulasi 2.0 (single sheet format with TAHUN, BULAN, NAMA, JENIS POTONGAN, JUMLAH POTONGAN)
     * as well as multi-sheet legacy workbooks.
     *
     * @param string $excelFilePath
     * @return array
     */
    public function parseWorkbook(string $excelFilePath): array
    {
        if (!file_exists($excelFilePath)) {
            throw new \InvalidArgumentException("Berkas Excel tidak ditemukan: {$excelFilePath}");
        }

        $zip = new ZipArchive();
        if ($zip->open($excelFilePath) !== true) {
            throw new \RuntimeException("Gagal membuka arsip Excel: {$excelFilePath}");
        }

        // 1. Get sheets
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        if (!$workbookXml) {
            $zip->close();
            throw new \RuntimeException("Berkas xl/workbook.xml tidak ditemukan di dalam Excel.");
        }

        $wb = simplexml_load_string($workbookXml);
        $sheets = [];
        foreach ($wb->sheets->sheet as $s) {
            $sheets[(string)$s['sheetId']] = [
                'name' => (string)$s['name'],
                'rId' => (string)$s->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'],
            ];
        }

        // Relationships
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        $rels = simplexml_load_string($relsXml);
        $sheetTargets = [];
        foreach ($rels->Relationship as $rel) {
            $sheetTargets[(string)$rel['Id']] = (string)$rel['Target'];
        }

        foreach ($sheets as $id => &$info) {
            $target = $sheetTargets[$info['rId']] ?? '';
            if ($target !== '' && strpos($target, '/') !== 0 && strpos($target, 'xl/') !== 0) {
                $target = 'xl/' . $target;
            }
            $info['target'] = $target;
        }
        unset($info);

        // Shared strings
        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml) {
            $ss = simplexml_load_string($ssXml);
            foreach ($ss->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string)$si->t;
                } elseif (isset($si->r)) {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                    $sharedStrings[] = $text;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }

        // Fetch program names from DB if available
        try {
            $programsFromDb = SocialProgram::all()->keyBy('id');
        } catch (\Throwable $e) {
            $programsFromDb = collect();
        }

        $parsedRows = [];

        // Check first sheet to see if it is Format 2.0 (Rekapitulasi 2.0)
        $firstSheet = reset($sheets);
        $firstXml = $firstSheet ? simplexml_load_string($zip->getFromName($firstSheet['target'])) : null;

        $isFormat2 = false;
        $colMap = ['year' => 'A', 'month' => 'B', 'name' => 'C', 'type' => 'D', 'amount' => 'E'];
        $headerRowNumber = 1;

        if ($firstXml && isset($firstXml->sheetData->row)) {
            $rowCount = 0;
            foreach ($firstXml->sheetData->row as $r) {
                $rowCount++;
                if ($rowCount > 6) {
                    break;
                }
                $headerCells = [];
                foreach ($r->c as $c) {
                    $col = preg_replace('/[0-9]/', '', (string)$c['r']);
                    $type = (string)$c['t'];
                    $val = (string)$c->v;
                    $valStr = ($type === 's') ? ($sharedStrings[(int)$val] ?? '') : $val;
                    $headerCells[$col] = strtoupper(trim($valStr));
                }

                $detectedCols = [];
                foreach ($headerCells as $col => $header) {
                    if (str_contains($header, 'TAHUN') || str_contains($header, 'YEAR')) {
                        $detectedCols['year'] = $col;
                    } elseif (str_contains($header, 'BULAN') || str_contains($header, 'MONTH')) {
                        $detectedCols['month'] = $col;
                    } elseif (str_contains($header, 'NAMA') || str_contains($header, 'PEGAWAI')) {
                        $detectedCols['name'] = $col;
                    } elseif (str_contains($header, 'JENIS') || str_contains($header, 'PROGRAM')) {
                        $detectedCols['type'] = $col;
                    } elseif (str_contains($header, 'JUMLAH') || str_contains($header, 'NOMINAL') || str_contains($header, 'POTONGAN')) {
                        $detectedCols['amount'] = $col;
                    }
                }

                if (isset($detectedCols['name']) && (isset($detectedCols['amount']) || isset($detectedCols['type']))) {
                    $colMap = array_merge($colMap, $detectedCols);
                    $headerRowNumber = (int)$r['r'];
                    $isFormat2 = true;
                    break;
                }
            }
        }

        if ($isFormat2) {
            // Process Rekapitulasi 2.0 Format
            foreach ($sheets as $sInfo) {
                $xmlStr = $zip->getFromName($sInfo['target']);
                if (!$xmlStr) continue;
                $sXml = simplexml_load_string($xmlStr);

                foreach ($sXml->sheetData->row as $row) {
                    $rNum = (int)$row['r'];
                    if ($rNum <= $headerRowNumber) continue; // Skip headers

                    $cells = [];
                    foreach ($row->c as $c) {
                        $col = preg_replace('/[0-9]/', '', (string)$c['r']);
                        $type = (string)$c['t'];
                        $val = (string)$c->v;
                        if ($type === 's') {
                            $cells[$col] = $sharedStrings[(int)$val] ?? '';
                        } else {
                            $cells[$col] = $val;
                        }
                    }

                    $yearVal = trim($cells[$colMap['year']] ?? '');
                    $monthRaw = strtoupper(trim($cells[$colMap['month']] ?? ''));
                    $nameRaw = trim(preg_replace('/\s+/', ' ', $cells[$colMap['name']] ?? ''));
                    $typeRaw = strtolower(trim($cells[$colMap['type']] ?? ''));
                    $amountVal = (float)($cells[$colMap['amount']] ?? 0);

                    if (empty($nameRaw) || $amountVal <= 0) {
                        continue;
                    }

                    $monthNum = $this->resolveMonthNum($monthRaw);
                    if (!$monthNum || $monthNum < 1 || $monthNum > 12) {
                        continue;
                    }

                    $progMapping = $this->resolveProgram($typeRaw, $programsFromDb);
                    if (!$progMapping) {
                        continue;
                    }

                    $year = (!empty($yearVal) && is_numeric($yearVal)) ? (int)$yearVal : (int)now()->year;
                    if (empty($yearVal) && preg_match('/20\d\d/', $cells[$colMap['month']] ?? '', $ym)) {
                        $year = (int)$ym[0];
                    }
                    $period = "Periode {$monthNum}/{$year}";

                    $dbProg = $programsFromDb->get($progMapping['id']);
                    $progName = $dbProg?->name ?? $progMapping['name'];

                    $parsedRows[] = [
                        'name' => $nameRaw,
                        'social_program_id' => $progMapping['id'],
                        'program_name' => $progName,
                        'monthly_amount' => $amountVal,
                        'period' => $period,
                        'month_num' => $monthNum,
                        'year' => $year,
                    ];
                }
            }
        } else {
            // Legacy multi-sheet format fallback
            $legacySections = [
                'zakat' => ['nameCol' => 'A', 'amountCol' => 'C', 'id' => 3, 'name' => 'Program Zakat Mal Rutin'],
                'infaq' => ['nameCol' => 'F', 'amountCol' => 'H', 'id' => 2, 'name' => 'Infaq Rutin'],
                'yatim' => ['nameCol' => 'K', 'amountCol' => 'M', 'id' => 1, 'name' => 'Program Santunan Anak Yatim'],
                'qurban' => ['nameCol' => 'P', 'amountCol' => 'R', 'id' => 4, 'name' => 'Tabungan Qurban'],
            ];

            foreach ($sheets as $sInfo) {
                $sName = strtoupper(trim($sInfo['name']));
                $mNum = $this->resolveMonthNum($sName);
                if (!$mNum) continue;

                $xmlStr = $zip->getFromName($sInfo['target']);
                if (!$xmlStr) continue;
                $sXml = simplexml_load_string($xmlStr);

                foreach ($sXml->sheetData->row as $row) {
                    $rNum = (int)$row['r'];
                    if ($rNum < 5) continue;

                    $cells = [];
                    foreach ($row->c as $c) {
                        $col = preg_replace('/[0-9]/', '', (string)$c['r']);
                        $type = (string)$c['t'];
                        $val = (string)$c->v;
                        if ($type === 's') {
                            $cells[$col] = $sharedStrings[(int)$val] ?? '';
                        } else {
                            $cells[$col] = $val;
                        }
                    }

                    $period = "Periode {$mNum}/2026";

                    foreach ($legacySections as $secKey => $cfg) {
                        $name = trim($cells[$cfg['nameCol']] ?? '');
                        $amtStr = trim($cells[$cfg['amountCol']] ?? '');

                        if ($name !== '' && is_numeric($amtStr) && (float)$amtStr > 0) {
                            $cleanName = trim(preg_replace('/\s+/', ' ', $name));
                            $dbProg = $programsFromDb->get($cfg['id']);
                            $progName = $dbProg?->name ?? $cfg['name'];

                            $parsedRows[] = [
                                'name' => $cleanName,
                                'social_program_id' => $cfg['id'],
                                'program_name' => $progName,
                                'monthly_amount' => (float)$amtStr,
                                'period' => $period,
                                'month_num' => $mNum,
                                'year' => 2026,
                            ];
                        }
                    }
                }
            }
        }

        $zip->close();

        return $parsedRows;
    }

    /**
     * Synchronize parsed participants with the database.
     * Replaces existing data for any period present in the imported file cleanly
     * by deleting old period records and inserting the fresh imported rows.
     *
     * @param string $excelFilePath
     * @param bool $dryRun If true, does not persist changes to the database.
     * @return array Summary of operations
     */
    public function sync(string $excelFilePath, bool $dryRun = false): array
    {
        $parsedRows = $this->parseWorkbook($excelFilePath);

        // Fetch program names from DB
        try {
            $programs = SocialProgram::all()->keyBy('id');
        } catch (\Throwable $e) {
            $programs = collect();
        }

        $now = now();
        $currentMonth = (int)$now->month;
        $currentYear = (int)$now->year;
        $currentPeriod = "Periode {$currentMonth}/{$currentYear}";

        $totalRows = count($parsedRows);
        $totalAmount = 0.0;
        $currentPeriodCount = 0;
        $currentPeriodAmount = 0.0;

        $byPeriod = [];
        $byProgram = [];

        // Initialize program summaries
        foreach ($programs as $prog) {
            $byProgram[$prog->id] = [
                'id' => $prog->id,
                'name' => $prog->name,
                'total_rows' => 0,
                'total_amount' => 0.0,
                'current_month_count' => 0,
                'current_month_amount' => 0.0,
                // Legacy view compatibility keys
                'active' => 0,
                'nonactive' => 0,
                'active_amount' => 0.0,
                'updated' => 0,
                'inserted' => 0,
                'total' => 0,
            ];
        }

        // 1. Identify all periods present in the imported file
        $targetPeriods = array_values(array_unique(array_filter(array_column($parsedRows, 'period'))));

        // 2. Build full list of period strings to delete (including potential formatting variations)
        $deletePeriods = [];
        foreach ($targetPeriods as $tp) {
            $deletePeriods[] = $tp;
            if (preg_match('/(\d+)\/(\d+)/', $tp, $m)) {
                $mNum = (int)$m[1];
                $yNum = (int)$m[2];
                $deletePeriods[] = "Periode {$mNum}/{$yNum}";
                $deletePeriods[] = sprintf("Periode %02d/%d", $mNum, $yNum);
                $deletePeriods[] = "{$mNum}/{$yNum}";
                $deletePeriods[] = sprintf("%02d/%d", $mNum, $yNum);
            }
        }
        $deletePeriods = array_values(array_unique($deletePeriods));

        // 3. Count existing records in DB for the target periods that will be replaced
        $existingDeletedCount = 0;
        $existingByProgram = [];
        try {
            $existingDeletedCount = ProgramParticipant::whereIn('period', $deletePeriods)->count();
            $existingByProgram = ProgramParticipant::whereIn('period', $deletePeriods)
                ->groupBy('social_program_id')
                ->select('social_program_id', DB::raw('count(*) as count'))
                ->pluck('count', 'social_program_id')
                ->toArray();
        } catch (\Throwable $e) {
            // DB might be in-memory or offline during preview
        }

        $toInsert = [];

        foreach ($parsedRows as $row) {
            $pId = $row['social_program_id'];
            $amt = (float)$row['monthly_amount'];
            $period = $row['period'];

            $totalAmount += $amt;
            $byPeriod[$period] = ($byPeriod[$period] ?? 0) + 1;

            if (!isset($byProgram[$pId])) {
                $byProgram[$pId] = [
                    'id' => $pId,
                    'name' => $row['program_name'],
                    'total_rows' => 0,
                    'total_amount' => 0.0,
                    'current_month_count' => 0,
                    'current_month_amount' => 0.0,
                    'active' => 0,
                    'nonactive' => 0,
                    'active_amount' => 0.0,
                    'updated' => 0,
                    'inserted' => 0,
                    'total' => 0,
                ];
            }

            $byProgram[$pId]['total_rows']++;
            $byProgram[$pId]['total_amount'] += $amt;
            $byProgram[$pId]['total']++;
            $byProgram[$pId]['inserted']++;
            $byProgram[$pId]['updated'] = $existingByProgram[$pId] ?? 0;

            if ($period === $currentPeriod) {
                $currentPeriodCount++;
                $currentPeriodAmount += $amt;
                $byProgram[$pId]['current_month_count']++;
                $byProgram[$pId]['current_month_amount'] += $amt;
                $byProgram[$pId]['active']++;
                $byProgram[$pId]['active_amount'] += $amt;
            } else {
                $byProgram[$pId]['nonactive']++;
            }

            $toInsert[] = [
                'social_program_id' => $pId,
                'name' => $row['name'],
                'program_name' => $row['program_name'],
                'monthly_amount' => $amt,
                'period' => $period,
            ];
        }

        $summary = [
            'dry_run' => $dryRun,
            'total_rows' => $totalRows,
            'total_excel_unique' => $totalRows,
            'deleted_count' => $existingDeletedCount,
            'updated_count' => $existingDeletedCount, // Backward compatibility with UI/console summaries
            'inserted_count' => count($toInsert),
            'total_amount' => $totalAmount,
            'current_period' => $currentPeriod,
            'current_period_count' => $currentPeriodCount,
            'current_period_amount' => $currentPeriodAmount,
            // Aliases for compatibility
            'active_count' => $currentPeriodCount,
            'active_total_amount' => $currentPeriodAmount,
            'nonactive_count' => $totalRows - $currentPeriodCount,
            'by_period' => $byPeriod,
            'by_program' => $byProgram,
            'target_periods' => $targetPeriods,
        ];

        if (!$dryRun) {
            DB::transaction(function () use ($deletePeriods, $toInsert) {
                // 1. Remove any legacy records with dash in period (e.g. 'Periode 1/2026 - 12/2027')
                ProgramParticipant::where('period', 'like', '% - %')->delete();

                // 2. Delete ALL existing records for the imported months/periods to prevent ghost records
                if (!empty($deletePeriods)) {
                    ProgramParticipant::whereIn('period', $deletePeriods)->delete();
                }

                // 3. Insert newly imported records in chunks of 250 for speed
                $nowTs = now();
                $chunks = array_chunk($toInsert, 250);
                foreach ($chunks as $chunk) {
                    $insertData = array_map(function ($item) use ($nowTs) {
                        $item['created_at'] = $nowTs;
                        $item['updated_at'] = $nowTs;
                        return $item;
                    }, $chunk);
                    ProgramParticipant::insert($insertData);
                }
            });
        }

        return $summary;
    }

    /**
     * Get a lightweight preview summary of the Excel file for modal display.
     *
     * @param string $filePath
     * @param string|null $fileName
     * @param int|null $fileSize
     * @return array
     */
    public function getPreview(string $filePath, ?string $fileName = null, ?int $fileSize = null): array
    {
        $summary = $this->sync($filePath, true);
        $summary['file_name'] = $fileName ?? basename($filePath);
        $summary['file_size'] = $fileSize ?? (file_exists($filePath) ? filesize($filePath) : 0);
        return $summary;
    }
}

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
     * Map of Indonesian month names to month numbers (1-12)
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
        'OKTOBER' => 10,
        'OKT' => 10,
        'NOVEMBER' => 11,
        'DESEMBER' => 12,
    ];

    /**
     * Mapping for program types to social_program_id and canonical name
     */
    protected array $programMap = [
        'zakat' => [
            'id' => 3,
            'name' => 'Program Zakat Mal Rutin',
        ],
        'infaq' => [
            'id' => 2,
            'name' => 'Infaq Rutin',
        ],
        'gib' => [
            'id' => 2,
            'name' => 'Infaq Rutin',
        ],
        'pot anak yatim' => [
            'id' => 1,
            'name' => 'Program Santunan Anak Yatim',
        ],
        'anak yatim' => [
            'id' => 1,
            'name' => 'Program Santunan Anak Yatim',
        ],
        'yatim' => [
            'id' => 1,
            'name' => 'Program Santunan Anak Yatim',
        ],
        'tabungan qurban' => [
            'id' => 4,
            'name' => 'Tabungan Qurban',
        ],
        'qurban' => [
            'id' => 4,
            'name' => 'Tabungan Qurban',
        ],
    ];

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
        $programsFromDb = SocialProgram::all()->keyBy('id');

        $parsedRows = [];

        // Check first sheet to see if it is Format 2.0 (Rekapitulasi 2.0)
        $firstSheet = reset($sheets);
        $firstXml = $firstSheet ? simplexml_load_string($zip->getFromName($firstSheet['target'])) : null;

        $isFormat2 = false;
        $colMap = ['year' => 'A', 'month' => 'B', 'name' => 'C', 'type' => 'D', 'amount' => 'E'];

        if ($firstXml && isset($firstXml->sheetData->row[0])) {
            $headerRow = $firstXml->sheetData->row[0];
            $headerCells = [];
            foreach ($headerRow->c as $c) {
                $col = preg_replace('/[0-9]/', '', (string)$c['r']);
                $type = (string)$c['t'];
                $val = (string)$c->v;
                $valStr = ($type === 's') ? ($sharedStrings[(int)$val] ?? '') : $val;
                $headerCells[$col] = strtoupper(trim($valStr));
            }

            foreach ($headerCells as $col => $header) {
                if (str_contains($header, 'TAHUN') || str_contains($header, 'YEAR')) {
                    $colMap['year'] = $col;
                    $isFormat2 = true;
                } elseif (str_contains($header, 'BULAN') || str_contains($header, 'MONTH')) {
                    $colMap['month'] = $col;
                    $isFormat2 = true;
                } elseif (str_contains($header, 'NAMA') || str_contains($header, 'PEGAWAI')) {
                    $colMap['name'] = $col;
                    $isFormat2 = true;
                } elseif (str_contains($header, 'JENIS') || str_contains($header, 'PROGRAM')) {
                    $colMap['type'] = $col;
                    $isFormat2 = true;
                } elseif (str_contains($header, 'JUMLAH') || str_contains($header, 'NOMINAL') || str_contains($header, 'POTONGAN')) {
                    $colMap['amount'] = $col;
                    $isFormat2 = true;
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
                    if ($rNum === 1) continue; // Skip header

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

                    $monthNum = $this->monthMap[$monthRaw] ?? (is_numeric($monthRaw) ? (int)$monthRaw : null);
                    if (!$monthNum || $monthNum < 1 || $monthNum > 12) {
                        continue;
                    }

                    $progMapping = $this->programMap[$typeRaw] ?? null;
                    if (!$progMapping) {
                        continue;
                    }

                    $year = (!empty($yearVal) && is_numeric($yearVal)) ? (int)$yearVal : (int)now()->year;
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
                $mNum = $this->monthMap[$sName] ?? null;
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
     *
     * @param string $excelFilePath
     * @param bool $dryRun If true, does not persist changes to the database.
     * @return array Summary of operations
     */
    public function sync(string $excelFilePath, bool $dryRun = false): array
    {
        $parsedRows = $this->parseWorkbook($excelFilePath);

        // Fetch program names from DB
        $programs = SocialProgram::all()->keyBy('id');

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

        // Map existing participants in DB for quick lookup: [prog_id][name_upper][period]
        $existingDb = ProgramParticipant::all();
        $dbLookup = [];
        foreach ($existingDb as $ep) {
            $normName = mb_strtoupper(trim(preg_replace('/\s+/', ' ', $ep->name)));
            $dbLookup[$ep->social_program_id][$normName][$ep->period] = $ep;
        }

        $toUpdate = [];
        $toInsert = [];

        foreach ($parsedRows as $row) {
            $pId = $row['social_program_id'];
            $amt = (float)$row['monthly_amount'];
            $period = $row['period'];
            $normName = mb_strtoupper(trim(preg_replace('/\s+/', ' ', $row['name'])));

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

            $existingModel = $dbLookup[$pId][$normName][$period] ?? null;
            if ($existingModel) {
                $toUpdate[] = [
                    'model' => $existingModel,
                    'data' => [
                        'program_name' => $row['program_name'],
                        'monthly_amount' => $amt,
                    ],
                ];
                $byProgram[$pId]['updated']++;
            } else {
                $toInsert[] = [
                    'social_program_id' => $pId,
                    'name' => $row['name'],
                    'program_name' => $row['program_name'],
                    'monthly_amount' => $amt,
                    'period' => $period,
                ];
                $byProgram[$pId]['inserted']++;
            }
        }

        $summary = [
            'dry_run' => $dryRun,
            'total_rows' => $totalRows,
            'total_excel_unique' => $totalRows,
            'updated_count' => count($toUpdate),
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
        ];

        if (!$dryRun) {
            DB::transaction(function () use ($toUpdate, $toInsert) {
                // 1. Remove any legacy records with dash in period (e.g. 'Periode 1/2026 - 12/2027')
                ProgramParticipant::where('period', 'like', '% - %')->delete();

                // 2. Update existing records
                foreach ($toUpdate as $up) {
                    $up['model']->update($up['data']);
                }

                // 3. Insert new records in chunks of 250 for speed
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

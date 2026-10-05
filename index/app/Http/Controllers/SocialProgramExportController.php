<?php

namespace App\Http\Controllers;

use App\Models\ProgramParticipant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class SocialProgramExportController extends Controller
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
     * Export Program Participants / Rekapitulasi Potongan as genuine .xlsx
     * Filename: 'Rekapitulasi Potongan Masjid - Periode [Bulan] [Tahun].xlsx'
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $year = (int) $request->query('year', now()->year);

        // Support start_month & end_month or single month parameter
        $rawStart = $request->query('start_month', $request->query('month', 1));
        $rawEnd = $request->query('end_month', $request->query('month', 12));

        $startMonth = (int) $rawStart;
        $endMonth = (int) $rawEnd;

        $startMonth = max(1, min(12, $startMonth));
        $endMonth = max(1, min(12, $endMonth));

        if ($startMonth > $endMonth) {
            $temp = $startMonth;
            $startMonth = $endMonth;
            $endMonth = $temp;
        }

        // Build target period strings
        $targetPeriods = [];
        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $targetPeriods[] = 'Periode ' . $m . '/' . $year;
            $targetPeriods[] = 'Periode ' . sprintf('%02d', $m) . '/' . $year;
            $targetPeriods[] = $m . '/' . $year;
            $targetPeriods[] = sprintf('%02d', $m) . '/' . $year;
        }

        // Query participants for the selected periods
        $participants = ProgramParticipant::where(function ($q) use ($targetPeriods) {
            $q->whereIn('period', $targetPeriods);
        })->get()->sort(function ($a, $b) {
            preg_match('/(\d+)\/(\d+)/', (string) $a->period, $mA);
            preg_match('/(\d+)\/(\d+)/', (string) $b->period, $mB);
            $yearA = isset($mA[2]) ? (int) $mA[2] : 0;
            $monthA = isset($mA[1]) ? (int) $mA[1] : 0;
            $yearB = isset($mB[2]) ? (int) $mB[2] : 0;
            $monthB = isset($mB[1]) ? (int) $mB[1] : 0;
            if ($yearA !== $yearB) {
                return $yearA <=> $yearB;
            }
            if ($monthA !== $monthB) {
                return $monthA <=> $monthB;
            }
            return strcasecmp((string) $a->name, (string) $b->name);
        });

        // Determine filename
        if ($startMonth === $endMonth) {
            $monthName = $this->months[$startMonth] ?? 'Semua';
            $fileName = "Rekapitulasi Potongan Masjid - Periode {$monthName} {$year}.xlsx";
        } elseif ($startMonth === 1 && $endMonth === 12) {
            $fileName = "Rekapitulasi Potongan Masjid - Seluruh Periode Tahun {$year}.xlsx";
        } else {
            $startName = $this->months[$startMonth] ?? (string) $startMonth;
            $endName = $this->months[$endMonth] ?? (string) $endMonth;
            $fileName = "Rekapitulasi Potongan Masjid - Periode {$startName} - {$endName} {$year}.xlsx";
        }

        // Build XLSX workbook
        $tempZip = tempnam(sys_get_temp_dir(), 'rekap_pot_');
        $zip = new ZipArchive();
        if ($zip->open($tempZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat berkas arsip Excel.');
        }

        $headers = ['TAHUN', 'BULAN', 'NAMA', 'JENIS POTONGAN', 'JUMLAH POTONGAN'];

        // Shared strings dictionary
        $sharedStrings = [];
        $stringIndexMap = [];
        $addString = function (string $str) use (&$sharedStrings, &$stringIndexMap): int {
            if (isset($stringIndexMap[$str])) {
                return $stringIndexMap[$str];
            }
            $idx = count($sharedStrings);
            $sharedStrings[] = $str;
            $stringIndexMap[$str] = $idx;
            return $idx;
        };

        // Header row
        $sheetRowsXml = [];
        $rNum = 1;
        $cXml = [];
        $cols = ['A', 'B', 'C', 'D', 'E'];
        foreach ($headers as $cIdx => $h) {
            $sIdx = $addString($h);
            $cXml[] = '<c r="' . $cols[$cIdx] . $rNum . '" t="s" s="1"><v>' . $sIdx . '</v></c>';
        }
        $sheetRowsXml[] = '<row r="' . $rNum . '">' . implode('', $cXml) . '</row>';

        // Data rows
        foreach ($participants as $p) {
            $rNum++;
            $programName = $p->program_name ?: ($p->socialProgram?->name ?? 'Infaq Rutin');
            $amount = (float) $p->monthly_amount;

            $rowYear = $year;
            $rowMonthName = $this->months[$startMonth] ?? 'Januari';
            if (!empty($p->period) && preg_match('/(\d+)\/(\d+)/', $p->period, $matches)) {
                $mVal = (int) $matches[1];
                $rowMonthName = $this->months[$mVal] ?? $rowMonthName;
                if (isset($matches[2])) {
                    $rowYear = (int) $matches[2];
                }
            }

            $sYear = $rowYear;
            $sMonthIdx = $addString(strtoupper($rowMonthName));
            $sNameIdx = $addString(trim((string)$p->name));
            $sProgIdx = $addString(trim((string)$programName));

            $sheetRowsXml[] = '<row r="' . $rNum . '">' .
                '<c r="A' . $rNum . '"><v>' . $sYear . '</v></c>' .
                '<c r="B' . $rNum . '" t="s"><v>' . $sMonthIdx . '</v></c>' .
                '<c r="C' . $rNum . '" t="s"><v>' . $sNameIdx . '</v></c>' .
                '<c r="D' . $rNum . '" t="s"><v>' . $sProgIdx . '</v></c>' .
                '<c r="E' . $rNum . '" s="2"><v>' . $amount . '</v></c>' .
                '</row>';
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>' .
            '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
            '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>' .
            '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<sheets>' .
            '<sheet name="Rekapitulasi" sheetId="1" r:id="rId1"/>' .
            '</sheets>' .
            '</workbook>';
        $zip->addFromString('xl/workbook.xml', $wb);

        // 5. xl/styles.xml
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<numFmts count="1">' .
            '<numFmt numFmtId="164" formatCode="#,##0"/>' .
            '</numFmts>' .
            '<fonts count="2">' .
            '<font><name val="Calibri"/><sz val="11"/></font>' .
            '<font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFFFF"/></font>' .
            '</fonts>' .
            '<fills count="3">' .
            '<fill><patternFill patternType="none"/></fill>' .
            '<fill><patternFill patternType="gray125"/></fill>' .
            '<fill><patternFill patternType="solid"><fgColor rgb="FF003366"/></patternFill></fill>' .
            '</fills>' .
            '<borders count="2">' .
            '<border><left/><right/><top/><bottom/></border>' .
            '<border>' .
            '<left style="thin"><color rgb="FFD1D5DB"/></left>' .
            '<right style="thin"><color rgb="FFD1D5DB"/></right>' .
            '<top style="thin"><color rgb="FFD1D5DB"/></top>' .
            '<bottom style="thin"><color rgb="FFD1D5DB"/></bottom>' .
            '</border>' .
            '</borders>' .
            '<cellStyleXfs count="1">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
            '</cellStyleXfs>' .
            '<cellXfs count="3">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"/>' .
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>' .
            '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/>' .
            '</cellXfs>' .
            '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/sharedStrings.xml
        $sstItems = [];
        foreach ($sharedStrings as $str) {
            $sstItems[] = '<si><t>' . htmlspecialchars($str, ENT_XML1, 'UTF-8') . '</t></si>';
        }
        $sst = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">' .
            implode('', $sstItems) .
            '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $sst);

        // 7. xl/worksheets/sheet1.xml
        $ws = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<sheetData>' .
            implode('', $sheetRowsXml) .
            '</sheetData>' .
            '</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $ws);

        $zip->close();

        return response()->download($tempZip, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        ])->deleteFileAfterSend(true);
    }
}

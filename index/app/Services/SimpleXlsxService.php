<?php

namespace App\Services;

use Carbon\Carbon;
use SimpleXMLElement;
use ZipArchive;

class SimpleXlsxService
{
    /**
     * Generate an Excel (.xlsx) file binary content with all columns formatted as Text (@).
     *
     * @param array $headers List of column header titles
     * @param array $rows Array of data rows (each row is an indexed array of cell values)
     * @param array $colWidths Optional column widths [width1, width2, ...]
     * @param string $sheetName Optional sheet name
     * @return string Binary contents of the generated .xlsx file
     */
    public static function createXlsx(array $headers, array $rows, array $colWidths = [], string $sheetName = 'Jadwal'): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat arsip ZIP untuk file Excel.');
        }

        // 1. [Content_Types].xml
        $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypesXml);

        // 2. _rels/.rels
        $rootRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>';
        $zip->addFromString('_rels/.rels', $rootRelsXml);

        // 3. xl/_rels/workbook.xml.rels
        $wbRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
            '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRelsXml);

        // 4. xl/workbook.xml
        $sheetNameEscaped = htmlspecialchars($sheetName, ENT_XML1, 'UTF-8');
        $wbXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<sheets>' .
            '<sheet name="' . $sheetNameEscaped . '" sheetId="1" r:id="rId1"/>' .
            '</sheets>' .
            '</workbook>';
        $zip->addFromString('xl/workbook.xml', $wbXml);

        // 5. xl/styles.xml
        // Style 0: Default Normal
        // Style 1: Data cell formatted as Text (@) with border
        // Style 2: Header cell formatted as Text (@) with dark navy fill & bold white font
        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<numFmts count="1">' .
            '<numFmt numFmtId="49" formatCode="@"/>' .
            '</numFmts>' .
            '<fonts count="2">' .
            '<font><sz val="11"/><color rgb="FF1E293B"/><name val="Calibri"/><family val="2"/></font>' .
            '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>' .
            '</fonts>' .
            '<fills count="3">' .
            '<fill><patternFill patternType="none"/></fill>' .
            '<fill><patternFill patternType="gray125"/></fill>' .
            '<fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/></patternFill></fill>' .
            '</fills>' .
            '<borders count="2">' .
            '<border><left/><right/><top/><bottom/><diagonal/></border>' .
            '<border>' .
            '<left style="thin"><color rgb="FFCBD5E1"/></left>' .
            '<right style="thin"><color rgb="FFCBD5E1"/></right>' .
            '<top style="thin"><color rgb="FFCBD5E1"/></top>' .
            '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>' .
            '<diagonal/>' .
            '</border>' .
            '</borders>' .
            '<cellStyleXfs count="1">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
            '</cellStyleXfs>' .
            '<cellXfs count="3">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>' .
            '<xf numFmtId="49" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"><alignment vertical="center"/></xf>' .
            '<xf numFmtId="49" fontId="1" fillId="2" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>' .
            '</cellXfs>' .
            '<cellStyles count="1">' .
            '<cellStyle name="Normal" xfId="0" builtinId="0"/>' .
            '</cellStyles>' .
            '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $stylesXml);

        // 6. xl/worksheets/sheet1.xml
        $colCount = max(count($headers), !empty($rows) ? max(array_map('count', $rows)) : 0);
        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";

        // Columns definition with style="1" (Text format for all columns)
        if ($colCount > 0) {
            $sheetXml .= '  <cols>' . "\n";
            for ($c = 1; $c <= $colCount; $c++) {
                $w = $colWidths[$c - 1] ?? 20;
                $sheetXml .= '    <col min="' . $c . '" max="' . $c . '" width="' . $w . '" style="1" customWidth="1"/>' . "\n";
            }
            $sheetXml .= '  </cols>' . "\n";
        }

        $sheetXml .= '  <sheetData>' . "\n";

        $rowNum = 1;
        // Header Row
        if (!empty($headers)) {
            $sheetXml .= '    <row r="' . $rowNum . '" ht="26" customHeight="1">' . "\n";
            foreach ($headers as $colIdx => $hText) {
                $cellRef = self::columnIndexToLetter($colIdx) . $rowNum;
                $val = htmlspecialchars((string) $hText, ENT_XML1, 'UTF-8');
                $sheetXml .= '      <c r="' . $cellRef . '" s="2" t="inlineStr"><is><t>' . $val . '</t></is></c>' . "\n";
            }
            $sheetXml .= '    </row>' . "\n";
            $rowNum++;
        }

        // Data Rows
        foreach ($rows as $rowData) {
            $sheetXml .= '    <row r="' . $rowNum . '" ht="20" customHeight="1">' . "\n";
            $colIdx = 0;
            foreach ($rowData as $cellVal) {
                $cellRef = self::columnIndexToLetter($colIdx) . $rowNum;
                $val = htmlspecialchars((string) $cellVal, ENT_XML1, 'UTF-8');
                // s="1" -> Format Text (@), t="inlineStr" -> inline string
                $sheetXml .= '      <c r="' . $cellRef . '" s="1" t="inlineStr"><is><t>' . $val . '</t></is></c>' . "\n";
                $colIdx++;
            }
            $sheetXml .= '    </row>' . "\n";
            $rowNum++;
        }

        $sheetXml .= '  </sheetData>' . "\n";
        $sheetXml .= '</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        return $content;
    }

    /**
     * Parse an Excel (.xlsx, .xls) or CSV file into an array of rows (each row is an array of strings).
     *
     * @param string $filePath Absolute path to the spreadsheet file
     * @param string|null $extension File extension (xlsx, csv, txt, etc.)
     * @return array<int, array<int, string>>
     */
    public static function parseFile(string $filePath, ?string $extension = null): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $ext = strtolower($extension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'xlsx' || $ext === 'xls') {
            try {
                return self::parseXlsx($filePath);
            } catch (\Throwable $e) {
                // Fallback to text parsing if unzipping fails (e.g. if file is actually a renamed CSV/tab-delimited file)
                return self::parseCsvText(file_get_contents($filePath));
            }
        }

        return self::parseCsvText(file_get_contents($filePath));
    }

    /**
     * Parse genuine .xlsx file.
     *
     * @param string $filePath
     * @return array<int, array<int, string>>
     */
    public static function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException('Tidak dapat membuka file arsip Excel (.xlsx).');
        }

        // 1. Shared Strings (if any)
        $sharedStrings = [];
        $sharedXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXmlContent !== false && trim($sharedXmlContent) !== '') {
            $sstXml = @simplexml_load_string($sharedXmlContent);
            if ($sstXml && isset($sstXml->si)) {
                foreach ($sstXml->si as $si) {
                    $str = '';
                    if (isset($si->t)) {
                        $str .= (string) $si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $r) {
                            $str .= (string) ($r->t ?? '');
                        }
                    }
                    $sharedStrings[] = $str;
                }
            }
        }

        // 2. Sheet1 XML
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXmlContent === false) {
            // Check if there is another sheet name
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if ($stat && str_starts_with($stat['name'], 'xl/worksheets/sheet') && str_ends_with($stat['name'], '.xml')) {
                    $sheetXmlContent = $zip->getFromName($stat['name']);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXmlContent === false || trim($sheetXmlContent) === '') {
            return [];
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData) || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowCells = [];
            $maxColIdx = -1;

            if (isset($row->c)) {
                foreach ($row->c as $cell) {
                    $cellRef = (string) $cell['r'];
                    $cellType = (string) $cell['t'];

                    // Extract column letters e.g. "A", "B", "AB" from "A1", "AB12"
                    preg_match('/^([A-Z]+)(\d+)$/', $cellRef, $matches);
                    $colLetter = $matches[1] ?? 'A';
                    $colIdx = self::letterToColumnIndex($colLetter);

                    $val = '';
                    if ($cellType === 's') {
                        // Shared string lookup
                        $sIdx = (int) (string) $cell->v;
                        $val = $sharedStrings[$sIdx] ?? '';
                    } elseif ($cellType === 'inlineStr') {
                        $val = (string) ($cell->is->t ?? '');
                    } elseif ($cellType === 'b') {
                        $val = ((string) $cell->v === '1') ? '1' : '0';
                    } else {
                        $val = (string) ($cell->v ?? '');
                    }

                    $rowCells[$colIdx] = trim($val);
                    if ($colIdx > $maxColIdx) {
                        $maxColIdx = $colIdx;
                    }
                }
            }

            if ($maxColIdx >= 0) {
                // Fill sparse entries so that array is complete [0 => '...', 1 => '...', 2 => '...']
                $denseRow = [];
                for ($i = 0; $i <= $maxColIdx; $i++) {
                    $denseRow[$i] = $rowCells[$i] ?? '';
                }
                $rows[] = $denseRow;
            }
        }

        return $rows;
    }

    /**
     * Parse CSV or Tab-delimited text.
     *
     * @param string $content
     * @return array<int, array<int, string>>
     */
    public static function parseCsvText(string $content): array
    {
        // Strip BOM if present
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        $rows = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (str_contains($line, "\t")) {
                $cols = array_map('trim', explode("\t", $line));
            } elseif (str_contains($line, ";")) {
                $cols = array_map('trim', str_getcsv($line, ';'));
            } else {
                $cols = array_map('trim', str_getcsv($line, ','));
            }

            $rows[] = $cols;
        }

        return $rows;
    }

    /**
     * Convert 0-based column index (0, 1, 2) to Excel letter (A, B, C... AA, AB).
     */
    public static function columnIndexToLetter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = (int) (($index - $mod) / 26);
        }
        return $letter;
    }

    /**
     * Convert Excel letter (A, B, C... AA, AB) to 0-based column index (0, 1, 2).
     */
    public static function letterToColumnIndex(string $letter): int
    {
        $letter = strtoupper($letter);
        $len = strlen($letter);
        $index = 0;
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letter[$i]) - 64);
        }
        return $index - 1;
    }

    /**
     * Parse flexible date strings including Indonesian formats, ISO Y-m-d, d/m/Y, and Excel serial dates.
     *
     * @param string|int|float|null $rawDate
     * @return string|null Formatted as 'Y-m-d' or null if invalid
     */
    public static function parseFlexibleDate($rawDate): ?string
    {
        $val = trim((string) $rawDate);
        if ($val === '') {
            return null;
        }

        // Check if it's an Excel serialized date (numeric float/int around 30000..60000)
        if (is_numeric($val) && (float)$val > 30000 && (float)$val < 60000) {
            try {
                $base = Carbon::create(1899, 12, 30, 0, 0, 0, 'Asia/Jakarta');
                return $base->addDays((int) $val)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        // Direct Y-m-d match
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }

        // dd/mm/yyyy or dd-mm-yyyy or dd.mm.yyyy
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        // Clean out Indonesian day prefix e.g. "Jumat, " or "Senin "
        $cleaned = preg_replace('/^(senin|selasa|rabu|kamis|jumat|jum\'at|sabtu|minggu)[,\s]*/ui', '', $val);
        $cleaned = trim($cleaned);

        // Indonesian month translation map
        $monthMap = [
            'januari' => 'January',
            'jan' => 'Jan',
            'februari' => 'February',
            'pebruari' => 'February',
            'feb' => 'Feb',
            'maret' => 'March',
            'mar' => 'Mar',
            'april' => 'April',
            'apr' => 'Apr',
            'mei' => 'May',
            'juni' => 'June',
            'jun' => 'Jun',
            'juli' => 'July',
            'jul' => 'Jul',
            'agustus' => 'August',
            'ags' => 'Aug',
            'agu' => 'Aug',
            'september' => 'September',
            'sep' => 'Sep',
            'oktober' => 'October',
            'okt' => 'Oct',
            'november' => 'November',
            'nopember' => 'November',
            'nov' => 'Nov',
            'desember' => 'December',
            'des' => 'Dec',
        ];

        foreach ($monthMap as $id => $en) {
            $cleaned = preg_replace('/\b' . $id . '\b/ui', $en, $cleaned);
        }

        try {
            return Carbon::parse($cleaned)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}

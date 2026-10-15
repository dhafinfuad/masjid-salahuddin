<?php

namespace App\Services;

use Illuminate\Support\Str;

class EmployeeImportService
{
    /**
     * Membaca dan mengekstrak berkas spreadsheet (Excel .xlsx/.xls atau CSV)
     * menjadi daftar entri pegawai siap simpan ke database.
     *
     * @param string $filePath
     * @param bool $onlyMuslim
     * @return array{
     *     total_rows: int,
     *     items: array<int, array{
     *         name: string,
     *         nip_pendek: ?string,
     *         nip_panjang: ?string,
     *         gender: ?string,
     *         jabatan: ?string,
     *         unit_kerja: string,
     *         status: string,
     *         religion: ?string
     *     }>,
     *     skipped_non_muslim: int,
     *     header_found: bool,
     *     col_map: array
     * }
     */
    public static function parseFile(string $filePath, bool $onlyMuslim = true): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $rows = [];

        if ($extension === 'csv' || $extension === 'txt') {
            $content = file_get_contents($filePath);
            $rows = SimpleXlsxService::parseCsvText($content ?: '');
        } else {
            $rows = SimpleXlsxService::parseXlsx($filePath);
        }

        return self::parseRows($rows, $onlyMuslim);
    }

    /**
     * Parsing array baris spreadsheet dengan deteksi header otomatis.
     *
     * @param array<int, array> $rows
     * @param bool $onlyMuslim
     * @return array
     */
    public static function parseRows(array $rows, bool $onlyMuslim = true): array
    {
        if (empty($rows)) {
            return [
                'total_rows' => 0,
                'items' => [],
                'skipped_non_muslim' => 0,
                'header_found' => false,
                'col_map' => [],
            ];
        }

        // 1. Cari baris header
        $headerRowIdx = null;
        $colMap = [];

        foreach ($rows as $rIdx => $row) {
            $matched = self::detectHeaderMapping($row);
            if (! empty($matched)) {
                $headerRowIdx = $rIdx;
                $colMap = $matched;
                break;
            }
        }

        $startIdx = ($headerRowIdx !== null) ? $headerRowIdx + 1 : 0;
        $dataRowsCount = max(0, count($rows) - $startIdx);

        $results = [
            'total_rows' => $dataRowsCount,
            'items' => [],
            'skipped_non_muslim' => 0,
            'header_found' => ($headerRowIdx !== null),
            'col_map' => $colMap,
        ];

        for ($i = $startIdx; $i < count($rows); $i++) {
            $cols = $rows[$i];
            if (empty(array_filter($cols, fn ($c) => trim((string) $c) !== ''))) {
                continue;
            }

            if (! empty($colMap)) {
                $item = self::extractFromMappedCols($cols, $colMap, $onlyMuslim);
            } else {
                $item = self::extractGenericCols($cols);
            }

            if ($item === 'SKIPPED_NON_MUSLIM') {
                $results['skipped_non_muslim']++;
                continue;
            }

            if ($item && ! empty($item['name'])) {
                $results['items'][] = $item;
            }
        }

        return $results;
    }

    /**
     * Deteksi mapping indeks kolom dari baris header tabel.
     *
     * @param array $cols
     * @return array<string, int>
     */
    public static function detectHeaderMapping(array $cols): array
    {
        $map = [];
        $lowers = array_map(fn ($c) => strtolower(trim((string) $c)), $cols);

        foreach ($lowers as $idx => $header) {
            if ($header === '') {
                continue;
            }

            // NIP 9 (IP Sikka, NIP Pendek, NIP 9, NIP DJP/Kemenkeu)
            if (in_array($header, ['ip sikka', 'nip pendek', 'nip 9', 'nip_pendek', 'nip9', 'nip kemenkeu', 'nip djp'])) {
                $map['nip9'] = $idx;
            }
            // NIP 18 (NIP, NIP Panjang, NIP 18, NIP BKN)
            elseif (in_array($header, ['nip', 'nip panjang', 'nip 18', 'nip_panjang', 'nip18', 'nip bkn', 'nomor induk pegawai'])) {
                $map['nip18'] = $idx;
            }
            // Nama Pegawai
            elseif (in_array($header, ['nama pegawai', 'nama lengkap', 'nama', 'nama_pegawai', 'name'])) {
                $map['name'] = $idx;
            }
            // Gelar Prefix
            elseif (in_array($header, ['gelar prefix', 'gelar depan', 'prefix'])) {
                $map['prefix'] = $idx;
            }
            // Gelar Suffix
            elseif (in_array($header, ['gelar suffix', 'gelar belakang', 'suffix'])) {
                $map['suffix'] = $idx;
            }
            // Jenis Kelamin / Gender
            elseif (in_array($header, ['jenis kelamin', 'gender', 'jk', 'sex'])) {
                $map['gender'] = $idx;
            }
            // Agama
            elseif (in_array($header, ['agama', 'religion'])) {
                $map['agama'] = $idx;
            }
            // Jabatan
            elseif (in_array($header, ['jabatan', 'jabatan sekarang', 'posisi'])) {
                $map['jabatan'] = $idx;
            }
            // Unit Organisasi / Seksi
            elseif (in_array($header, ['unit organisasi', 'unit kerja', 'seksi', 'subbagian', 'unit eselon 4'])) {
                $map['unit_org'] = $idx;
            }
            // Kantor / Instansi
            elseif (in_array($header, ['kantor', 'kpp', 'instansi'])) {
                $map['kantor'] = $idx;
            }
            // Pangkat / Golongan
            elseif (in_array($header, ['pangkat/golongan', 'pangkat', 'golongan'])) {
                $map['pangkat'] = $idx;
            }
            // Posisi Fungsional
            elseif (in_array($header, ['posisi kelompok fungsional'])) {
                $map['posisi_fungsional'] = $idx;
            }
        }

        // Header valid jika memiliki kolom nama atau minimal salah satu NIP
        if (isset($map['name']) || isset($map['nip9']) || isset($map['nip18'])) {
            return $map;
        }

        return [];
    }

    /**
     * Ekstraksi data baris menggunakan peta kolom teridentifikasi.
     *
     * @param array $cols
     * @param array $map
     * @param bool $onlyMuslim
     * @return array|string|null
     */
    private static function extractFromMappedCols(array $cols, array $map, bool $onlyMuslim): array|string|null
    {
        // 1. Filter Agama jika kolom agama tersedia
        $agama = null;
        if (isset($map['agama'])) {
            $agama = trim((string) ($cols[$map['agama']] ?? ''));
            if ($onlyMuslim && $agama !== '' && ! str_contains(strtolower($agama), 'islam')) {
                return 'SKIPPED_NON_MUSLIM';
            }
        }

        // 2. Ekstraksi NIP 9 (IP Sikka)
        $nip9 = isset($map['nip9']) ? preg_replace('/\D/', '', (string) ($cols[$map['nip9']] ?? '')) : null;
        if ($nip9 === '' || strlen($nip9) < 6) {
            $nip9 = null;
        }

        // 3. Ekstraksi NIP 18
        $nip18 = isset($map['nip18']) ? preg_replace('/\D/', '', (string) ($cols[$map['nip18']] ?? '')) : null;
        if ($nip18 === '' || strlen($nip18) !== 18) {
            if ($nip18 && strlen($nip18) === 9 && ! $nip9) {
                $nip9 = $nip18;
                $nip18 = null;
            } else {
                $nip18 = null;
            }
        }

        // 4. Ekstraksi Nama & Gelar
        $nameRaw = isset($map['name']) ? trim((string) ($cols[$map['name']] ?? '')) : '';
        $prefix = isset($map['prefix']) ? trim((string) ($cols[$map['prefix']] ?? '')) : '';
        $suffix = isset($map['suffix']) ? trim((string) ($cols[$map['suffix']] ?? '')) : '';

        // Hapus tanda petik satu atau karakter aneh yang kerap tertinggal
        $nameRaw = trim($nameRaw, "'\"");
        $prefix = trim($prefix, "'\"");
        $suffix = trim($suffix, "'\"");

        if ($nameRaw === '' && ! $nip9 && ! $nip18) {
            return null;
        }

        // Hapus penambahan gelar prefix maupun suffix sesuai mandat: nama pegawai tanpa gelar
        $fullName = EmployeeStatusSyncService::stripAcademicTitles($nameRaw);

        // 5. Gender
        $gender = null;
        if (isset($map['gender'])) {
            $gRaw = strtolower(trim((string) ($cols[$map['gender']] ?? '')));
            if (str_starts_with($gRaw, 'pria') || $gRaw === 'l' || str_starts_with($gRaw, 'laki')) {
                $gender = 'L';
            } elseif (str_starts_with($gRaw, 'wanita') || $gRaw === 'p' || str_starts_with($gRaw, 'perempuan')) {
                $gender = 'P';
            }
        }

        // 6. Jabatan & Unit Organisasi
        $jabatanRaw = isset($map['jabatan']) ? trim((string) ($cols[$map['jabatan']] ?? '')) : '';
        $unitOrg = isset($map['unit_org']) ? trim((string) ($cols[$map['unit_org']] ?? '')) : '';
        $posisiFung = isset($map['posisi_fungsional']) ? trim((string) ($cols[$map['posisi_fungsional']] ?? '')) : '';

        $finalJabatan = $jabatanRaw;
        if ($posisiFung !== '' && ! in_array($posisiFung, ['-', 'Tidak Ada'])) {
            $finalJabatan = $posisiFung;
            if ($unitOrg !== '') {
                $finalJabatan .= ' (' . $unitOrg . ')';
            }
        } elseif ($jabatanRaw !== '' && $unitOrg !== '' && ! str_contains(strtolower($jabatanRaw), strtolower($unitOrg))) {
            $finalJabatan = $jabatanRaw . ' (' . $unitOrg . ')';
        } elseif ($jabatanRaw === '' && $unitOrg !== '') {
            $finalJabatan = $unitOrg;
        }

        // 7. Kantor / Unit Kerja
        $kantor = isset($map['kantor']) ? trim((string) ($cols[$map['kantor']] ?? '')) : 'KPP Madya Malang';
        if ($kantor === '') {
            $kantor = 'KPP Madya Malang';
        }

        return [
            'name' => $fullName ?: 'Pegawai ' . ($nip9 ?: $nip18),
            'nip_pendek' => $nip9,
            'nip_panjang' => $nip18,
            'gender' => $gender,
            'jabatan' => $finalJabatan ?: null,
            'unit_kerja' => $kantor,
            'status' => 'AKTIF',
            'religion' => $agama,
        ];
    }

    /**
     * Fallback parsing jika tidak ditemukan header yang cocok (format daftar sederhana).
     *
     * @param array $cols
     * @return array|null
     */
    private static function extractGenericCols(array $cols): ?array
    {
        $raw = implode(' | ', array_filter($cols));
        $entry = EmployeeStatusSyncService::extractEntryFromTokens($cols, $raw);

        if (! $entry) {
            return null;
        }

        return [
            'name' => $entry['name'] ?: 'Pegawai ' . ($entry['nip9'] ?: $entry['nip18']),
            'nip_pendek' => $entry['nip9'] ?? null,
            'nip_panjang' => $entry['nip18'] ?? null,
            'gender' => null,
            'jabatan' => null,
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
            'religion' => null,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class EmployeeStatusSyncService
{
    /**
     * Parse text into list of employee entries [ ['raw' => ..., 'nip18' => ..., 'nip9' => ..., 'name' => ...] ]
     *
     * @param string $text
     * @return array<int, array{raw: string, nip18: ?string, nip9: ?string, name: ?string}>
     */
    public static function parseText(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($text));
        $entries = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Cek apakah baris header
            if (self::isHeaderRow([$line])) {
                continue;
            }

            $cols = [];
            if (str_contains($line, "\t")) {
                $cols = array_map('trim', explode("\t", $line));
            } elseif (str_contains($line, ";")) {
                $cols = array_map('trim', str_getcsv($line, ';'));
            } elseif (str_contains($line, ",") && preg_match('/[a-zA-Z]/', $line)) {
                $cols = array_map('trim', str_getcsv($line, ','));
            } else {
                $cols = [$line];
            }

            if (self::isHeaderRow($cols)) {
                continue;
            }

            $entry = self::extractEntryFromTokens($cols, $line);
            if ($entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Parse Excel (.xlsx/.xls) or CSV file.
     *
     * @param string $filePath
     * @return array<int, array{raw: string, nip18: ?string, nip9: ?string, name: ?string}>
     */
    public static function parseFile(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $rows = [];

        if ($extension === 'csv' || $extension === 'txt') {
            $content = file_get_contents($filePath);
            $rows = SimpleXlsxService::parseCsvText($content ?: '');
        } else {
            // Excel .xlsx
            $rows = SimpleXlsxService::parseXlsx($filePath);
        }

        $entries = [];
        foreach ($rows as $cols) {
            if (empty($cols) || self::isHeaderRow($cols)) {
                continue;
            }

            $raw = implode(' | ', array_filter($cols));
            $entry = self::extractEntryFromTokens($cols, $raw);
            if ($entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Deteksi apakah baris ini adalah header tabel
     */
    public static function isHeaderRow(array $cols): bool
    {
        $text = strtolower(implode(' ', $cols));
        // Jika terdapat kata header umum dan tidak ada NIP panjang
        $headerWords = ['nama pegawai', 'nama lengkap', 'nip pendek', 'nip panjang', 'unit kerja', 'nomor induk', 'jabatan'];
        foreach ($headerWords as $hw) {
            if (str_contains($text, $hw)) {
                return true;
            }
        }

        $tokens = preg_split('/\s+/', trim($text));
        if (count($tokens) <= 3) {
            $pureTokens = array_filter($tokens, fn($t) => in_array($t, ['no', 'nama', 'nip', 'status', 'email', 'role']));
            if (count($pureTokens) >= 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ekstraksi NIP 18, NIP 9, dan Nama dari kumpulan token / kolom
     *
     * @param array $tokens
     * @param string $rawLine
     * @return array{raw: string, nip18: ?string, nip9: ?string, name: ?string}|null
     */
    public static function extractEntryFromTokens(array $tokens, string $rawLine): ?array
    {
        $nip18 = null;
        $nip9 = null;
        $nameCandidates = [];

        foreach ($tokens as $token) {
            $clean = trim($token);
            if ($clean === '') {
                continue;
            }

            // Hapus spasi dan titik pada angka NIP jika ada format 19850115 201012 1 001
            $digitsOnly = preg_replace('/\D/', '', $clean);

            // Deteksi NIP 18
            if (strlen($digitsOnly) === 18 && is_null($nip18)) {
                $nip18 = $digitsOnly;
                continue;
            }

            // Deteksi NIP 9 (NIP Pendek DJP/Kemenkeu)
            if (strlen($digitsOnly) === 9 && is_null($nip9)) {
                $nip9 = $digitsOnly;
                continue;
            }

            // Jika murni angka 1-4 digit (nomor urut baris seperti "1", "2"), abaikan
            if (ctype_digit($clean) && strlen($clean) <= 4) {
                continue;
            }

            // Jika mengandung huruf, pertimbangkan sebagai nama
            if (preg_match('/[a-zA-Z]/', $clean)) {
                // Abaikan jika token adalah info umum seperti tanggal, status atau unit kerja yang jelas
                $lower = strtolower($clean);
                if (in_array($lower, ['aktif', 'nonaktif', 'pns', 'pppk', 'honorer', 'kpp madya malang', 'djp', 'kemenkeu'])) {
                    continue;
                }
                $nameCandidates[] = $clean;
            }
        }

        // Jika dari kolom belum terdeteksi NIP tetapi di rawLine ada 18 digit atau 9 digit
        if (is_null($nip18) && preg_match('/\b\d{18}\b/', $rawLine, $m18)) {
            $nip18 = $m18[0];
        }
        if (is_null($nip9) && preg_match('/\b\d{9}\b/', $rawLine, $m9)) {
            $nip9 = $m9[0];
        }

        $name = null;
        if (!empty($nameCandidates)) {
            $name = trim(implode(' ', $nameCandidates));
            // Bersihkan nomor urut di awal nama jika ada (misal: "1. Bimo Heriyanto" -> "Bimo Heriyanto")
            $name = preg_replace('/^\d+[\.\-\s]+/', '', $name);
        }

        // Jika tidak ada NIP dan nama kosong, skip
        if (is_null($nip18) && is_null($nip9) && empty($name)) {
            return null;
        }

        return [
            'raw' => $rawLine,
            'nip18' => $nip18,
            'nip9' => $nip9,
            'name' => $name,
        ];
    }

    /**
     * Normalisasi string nama untuk pencocokan toleran
     */
    public static function normalizeName(string $name): string
    {
        $name = strtolower($name);

        // Hapus gelar akademik & kehormatan umum (awalan dan akhiran)
        $patterns = [
            '/\b(dr|drs|dra|ir|h|hj|prof|apt)\b\.?/i',
            '/\b(s\.?e|m\.?m|s\.?kom|m\.?si|s\.?t|m\.?t|s\.?h|m\.?h|s\.?pd|m\.?pd|s\.?sos|ak|bba|mba|ph\.?d|ll\.?m)\b\.?/i',
        ];
        $name = preg_replace($patterns, '', $name);

        // Hapus tanda baca
        $name = preg_replace('/[^a-z0-9\s]/', ' ', $name);

        // Rapikan spasi berlebih
        return preg_replace('/\s+/', ' ', trim($name));
    }

    /**
     * Cek apakah User cocok dengan entri aktif
     *
     * @param User $user
     * @param array{raw: string, nip18: ?string, nip9: ?string, name: ?string} $entry
     * @return bool
     */
    public static function isUserMatch(User $user, array $entry): bool
    {
        $userNip = !empty($user->nip) ? preg_replace('/\D/', '', (string) $user->nip) : null;
        $userEmail = strtolower(trim((string) $user->email));
        $emailPrefix = explode('@', $userEmail)[0] ?? '';

        // 1. Cocokkan via NIP 18
        if (!empty($entry['nip18'])) {
            if ($userNip === $entry['nip18']) {
                return true;
            }
            if ($emailPrefix === $entry['nip18']) {
                return true;
            }
        }

        // 2. Cocokkan via NIP 9 (NIP Pendek)
        if (!empty($entry['nip9'])) {
            if ($userNip === $entry['nip9']) {
                return true;
            }
            if ($emailPrefix === $entry['nip9']) {
                return true;
            }
            // Kadang NIP 18 di DB diawali atau diakhiri NIP 9
            if ($userNip && (str_starts_with($userNip, $entry['nip9']) || str_ends_with($userNip, $entry['nip9']))) {
                return true;
            }
        }

        // 3. Cocokkan via Nama
        if (!empty($entry['name']) && !empty($user->name)) {
            $userNorm = self::normalizeName($user->name);
            $entryNorm = self::normalizeName($entry['name']);

            if (!empty($userNorm) && !empty($entryNorm)) {
                // Exact normalized match
                if ($userNorm === $entryNorm) {
                    return true;
                }

                // Substring match jika nama memiliki minimal 2 kata atau panjang >= 8 karakter
                if ((str_contains($entryNorm, ' ') || strlen($entryNorm) >= 8) && (str_contains($userNorm, ' ') || strlen($userNorm) >= 8)) {
                    if (str_contains($userNorm, $entryNorm) || str_contains($entryNorm, $userNorm)) {
                        return true;
                    }
                }

                // Similiarity toleransi typo minor (>= 88%)
                similar_text($userNorm, $entryNorm, $percent);
                if ($percent >= 88) {
                    return true;
                }
            }

            // Cek nama pada email prefix (misal nama di email "deril.amrizal" vs "Deril Amrizal")
            $emailNameNorm = self::normalizeName(str_replace(['.', '_', '-'], ' ', $emailPrefix));
            if (!empty($emailNameNorm) && (str_contains($emailNameNorm, ' ') || strlen($emailNameNorm) >= 8)) {
                if ($emailNameNorm === $entryNorm || str_contains($emailNameNorm, $entryNorm) || str_contains($entryNorm, $emailNameNorm)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Eksekusi sinkronisasi status ke tabel users
     *
     * @param array<int, array{raw: string, nip18: ?string, nip9: ?string, name: ?string}> $entries
     * @param bool $deactivateMissing
     * @param int|null $currentUserId
     * @return array
     */
    public static function sync(array $entries, bool $deactivateMissing = true, ?int $currentUserId = null): array
    {
        $allUsers = User::all();

        $activated = [];
        $deactivated = [];
        $matchedEntries = [];

        foreach ($allUsers as $user) {
            $matched = false;
            $matchedEntry = null;

            foreach ($entries as $index => $entry) {
                if (self::isUserMatch($user, $entry)) {
                    $matched = true;
                    $matchedEntry = $entry;
                    $matchedEntries[$index] = true;
                    break;
                }
            }

            if ($matched) {
                $payload = ['status' => 'AKTIF'];
                // Update NIP jika sebelumnya kosong
                if (empty($user->nip)) {
                    if (!empty($matchedEntry['nip18'])) {
                        $payload['nip'] = $matchedEntry['nip18'];
                    } elseif (!empty($matchedEntry['nip9'])) {
                        $payload['nip'] = $matchedEntry['nip9'];
                    }
                }
                $user->update($payload);
                $activated[] = $user;
            } else {
                // Proteksi keselamatan: jangan nonaktifkan akun admin yang sedang login
                if ($currentUserId && $user->id === $currentUserId) {
                    continue;
                }
                // Jangan nonaktifkan Master Admin
                if ($user->role === 'Master') {
                    continue;
                }

                if ($deactivateMissing) {
                    $user->update(['status' => 'NONAKTIF']);
                    $deactivated[] = $user;
                }
            }
        }

        // Cari entri input yang tidak terdaftar di akun manapun
        $unmatchedRows = [];
        foreach ($entries as $index => $entry) {
            if (!isset($matchedEntries[$index])) {
                $unmatchedRows[] = $entry['raw'];
            }
        }

        return [
            'total_input_rows' => count($entries),
            'activated_count' => count($activated),
            'deactivated_count' => count($deactivated),
            'unmatched_input_count' => count($unmatchedRows),
            'activated_names' => array_map(fn($u) => $u->name, $activated),
            'deactivated_names' => array_map(fn($u) => $u->name, $deactivated),
            'unmatched_rows' => array_slice($unmatchedRows, 0, 15), // Batasi 15 contoh untuk preview
        ];
    }
}

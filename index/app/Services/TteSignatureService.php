<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TteSignatureService
{
    protected static string $cacheKey = 'masjid_tte_signatures_map';
    protected static string $storageRelativePath = 'app/tte_signatures.json';

    /**
     * Get storage file path
     */
    public static function getFilePath(): string
    {
        return storage_path(static::$storageRelativePath);
    }

    /**
     * Normalize period key
     * Examples: 'monthly-2026-1', 'monthly-2026-all', 'agenda-5'
     */
    public static function normalizeKey(string $type, $yearOrAgendaId = 'all', $month = 'all'): string
    {
        if ($type === 'agenda') {
            return 'agenda-' . ($yearOrAgendaId ?: 'all');
        }

        $year = (string) ($yearOrAgendaId ?: 'all');
        $month = (string) ($month ?: 'all');

        return "monthly-{$year}-{$month}";
    }

    /**
     * Get all signed records
     *
     * @return array<string, array>
     */
    public static function getAll(): array
    {
        return Cache::rememberForever(static::$cacheKey, function () {
            $path = static::getFilePath();
            if (!file_exists($path)) {
                return [];
            }

            $content = @file_get_contents($path);
            if (!$content) {
                return [];
            }

            $data = json_decode($content, true);
            return is_array($data) ? $data : [];
        });
    }

    /**
     * Check if a specific period key is verified & signed
     */
    public static function isSigned(string $key): bool
    {
        $all = static::getAll();
        return !empty($all[$key]) && !empty($all[$key]['is_signed']);
    }

    /**
     * Get signature details for a specific key
     */
    public static function get(string $key): ?array
    {
        $all = static::getAll();
        return $all[$key] ?? null;
    }

    /**
     * Sign a report period
     */
    public static function sign(string $key, ?string $periodLabel = null, ?User $user = null): array
    {
        $all = static::getAll();
        $user = $user ?? Auth::user();

        $tteHash = 'TTE-' . strtoupper(substr(md5($key . ($user?->id ?? 'DKM') . 'SALAHUDDIN'), 0, 10));

        $record = [
            'key' => $key,
            'is_signed' => true,
            'period_label' => $periodLabel,
            'signed_at' => Carbon::now('Asia/Jakarta')->toIso8601String(),
            'signed_by_id' => $user?->id,
            'signed_by_name' => $user?->name ?? 'Pengurus DKM',
            'signed_by_role' => $user?->role ?? 'Bendahara',
            'tte_hash' => $tteHash,
        ];

        $all[$key] = $record;
        static::persist($all);

        return $record;
    }

    /**
     * Unsign / cancel signature for a period
     */
    public static function unsign(string $key): void
    {
        $all = static::getAll();
        if (isset($all[$key])) {
            unset($all[$key]);
            static::persist($all);
        }
    }

    /**
     * Toggle signature status
     */
    public static function toggle(string $key, ?string $periodLabel = null, ?User $user = null): bool
    {
        if (static::isSigned($key)) {
            static::unsign($key);
            return false;
        }

        static::sign($key, $periodLabel, $user);
        return true;
    }

    /**
     * Persist to both file storage and cache
     */
    protected static function persist(array $data): void
    {
        Cache::forever(static::$cacheKey, $data);

        try {
            $path = static::getFilePath();
            $dir = dirname($path);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } catch (\Throwable $e) {
            // Silently handled: cache will still maintain persistence for the process
        }
    }
}

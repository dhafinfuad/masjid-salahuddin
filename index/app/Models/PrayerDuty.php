<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PrayerDuty extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tahun' => 'integer',
    ];

    public function getYearAttribute(): ?int
    {
        return $this->tahun ? (int) $this->tahun : null;
    }

    public function setYearAttribute($value): void
    {
        $this->attributes['tahun'] = $value;
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('tahun', $year);
    }

    public const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    public const PRAYER_TIMES = [
        'dzuhur' => 'Dzuhur',
        'ashar' => 'Ashar',
    ];

    public const MONTHS = [
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

    public const WEEK_PATTERNS = [
        'semua' => 'Semua Pekan',
        'pekan_1' => 'Pekan 1',
        'pekan_2' => 'Pekan 2',
        'pekan_3' => 'Pekan 3',
        'pekan_4' => 'Pekan 4',
        'pekan_5' => 'Pekan 5',
        'pekan_1_3_5' => 'Pekan Ganjil (1, 3, 5)',
        'pekan_2_4' => 'Pekan Genap (2, 4)',
    ];

    public function scopeDzuhur(Builder $query): Builder
    {
        return $query->where('prayer_time', 'dzuhur');
    }

    public function scopeAshar(Builder $query): Builder
    {
        return $query->where('prayer_time', 'ashar');
    }

    public function scopeForDay(Builder $query, string $day): Builder
    {
        return $query->where('day_name', $day);
    }

    public function scopeForWeek(Builder $query, int $weekNumber): Builder
    {
        return $query->where(function (Builder $q) use ($weekNumber) {
            $q->where('week_pattern', 'semua')
              ->orWhere('week_pattern', 'pekan_' . $weekNumber);

            if (in_array($weekNumber, [1, 3, 5])) {
                $q->orWhere('week_pattern', 'pekan_1_3_5');
            }

            if (in_array($weekNumber, [2, 4])) {
                $q->orWhere('week_pattern', 'pekan_2_4');
            }
        });
    }

    public function matchesWeek(int $weekNumber): bool
    {
        if ($this->week_pattern === 'semua') {
            return true;
        }

        if ($this->week_pattern === 'pekan_' . $weekNumber) {
            return true;
        }

        if ($this->week_pattern === 'pekan_1_3_5' && in_array($weekNumber, [1, 3, 5])) {
            return true;
        }

        if ($this->week_pattern === 'pekan_2_4' && in_array($weekNumber, [2, 4])) {
            return true;
        }

        return false;
    }

    public function getWeekPatternLabelAttribute(): string
    {
        return self::WEEK_PATTERNS[$this->week_pattern] ?? $this->week_pattern;
    }

    public function getPrayerTimeLabelAttribute(): string
    {
        return self::PRAYER_TIMES[$this->prayer_time] ?? ucfirst($this->prayer_time);
    }

    /**
     * Get the calendar week of the month (1 - 5) based on standard monthly calendar rows (Sunday - Saturday).
     * This aligns with the physical and digital monthly calendar grids.
     */
    public static function getWeekOfMonth(CarbonInterface|string|null $date = null): int
    {
        if (is_string($date)) {
            $date = Carbon::parse($date, 'Asia/Jakarta');
        } elseif (!$date) {
            $date = Carbon::now('Asia/Jakarta');
        }

        $startDayOfWeek = $date->copy()->startOfMonth()->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        $weekNumber = (int) ceil(($date->day + $startDayOfWeek) / 7);

        return min(5, max(1, $weekNumber));
    }

    /**
     * Cache in-memory per-request untuk pemetaan hari & pekan ke tanggal dalam satu bulan.
     */
    protected static array $monthDayWeekCache = [];

    /**
     * Dapatkan mapping hari & pekan ke tanggal Carbon untuk satu bulan & tahun (O(1) lookup).
     */
    public static function getMonthDayWeekMap(int $year, int $month): array
    {
        $cacheKey = "{$year}_{$month}";
        if (isset(self::$monthDayWeekCache[$cacheKey])) {
            return self::$monthDayWeekCache[$cacheKey];
        }

        $dayMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat'];
        $daysInMonth = Carbon::create($year, $month, 1, 12, 0, 0, 'Asia/Jakarta')->daysInMonth;
        $map = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d, 12, 0, 0, 'Asia/Jakarta');
            $dayOfWeekIso = $date->dayOfWeekIso;
            if (isset($dayMap[$dayOfWeekIso])) {
                $dayName = $dayMap[$dayOfWeekIso];
                $week = self::getWeekOfMonth($date);
                $map["{$dayName}_{$week}"] = $date;
            }
        }

        self::$monthDayWeekCache[$cacheKey] = $map;
        return $map;
    }

    /**
     * Dapatkan 5 hari kerja (Senin s/d Jumat) untuk pekan kalender tertentu dalam tahun dan bulan yang dipilih.
     * Mengakomodasi peralihan pekan yang melintasi pergantian bulan (misal Pekan 5 September yang berakhir di Pekan 1 Oktober).
     *
     * @return array<int, array{day_name: string, date: Carbon, week_number: int, month: int, year: int}>
     */
    public static function getWeekCalendarDays(int $year, int $month, int $weekNumber): array
    {
        $daysInMonth = Carbon::create($year, $month, 1, 12, 0, 0, 'Asia/Jakarta')->daysInMonth;
        $targetDay = null;

        // Cari hari kerja (Senin - Jumat) di bulan ini yang memiliki pekan $weekNumber
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dt = Carbon::create($year, $month, $d, 12, 0, 0, 'Asia/Jakarta');
            if (self::getWeekOfMonth($dt) === $weekNumber && $dt->isWeekday()) {
                $targetDay = $dt;
                break;
            }
        }

        // Fallback jika tidak ada weekday di bulan ini
        if (!$targetDay) {
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dt = Carbon::create($year, $month, $d, 12, 0, 0, 'Asia/Jakarta');
                if (self::getWeekOfMonth($dt) === $weekNumber) {
                    $targetDay = $dt;
                    break;
                }
            }
        }

        if (!$targetDay) {
            return [];
        }

        // Dapatkan Senin untuk pekan tersebut
        $monday = $targetDay->isSunday()
            ? $targetDay->copy()->addDay()
            : $targetDay->copy()->startOfWeek(Carbon::MONDAY);

        $days = [];
        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        for ($i = 0; $i < 5; $i++) {
            $date = $monday->copy()->addDays($i);
            $days[] = [
                'day_name' => $dayNames[$i],
                'date' => $date,
                'week_number' => self::getWeekOfMonth($date),
                'month' => (int) $date->month,
                'year' => (int) $date->year,
            ];
        }

        return $days;
    }

    /**
     * Cari tanggal konkret (Carbon) untuk hari dan pekan tertentu pada bulan dan tahun yang ditentukan.
     */
    public static function findDateForDayAndWeek(int $year, int $month, string $dayName, int $weekNumber): ?Carbon
    {
        $map = self::getMonthDayWeekMap($year, $month);
        return $map["{$dayName}_{$weekNumber}"] ?? null;
    }

    /**
     * Resolusi tanggal konkret untuk instans model PrayerDuty pada bulan dan tahun tertentu.
     * Jika targetWeekNumber diberikan, gunakan pekan tersebut (misal dari filter pekan).
     * Jika tidak, gunakan pekan dari pola record (atau pekan berjalan jika 'semua').
     */
    public function resolveDate(int $year, int $month, ?int $targetWeekNumber = null): ?Carbon
    {
        $map = self::getMonthDayWeekMap($year, $month);
        $weekNum = $targetWeekNumber;

        if (!$weekNum || $weekNum < 1 || $weekNum > 5) {
            if (preg_match('/pekan_(\d)/', $this->week_pattern, $m)) {
                $weekNum = (int) $m[1];
            } elseif ($this->week_pattern === 'semua') {
                $now = Carbon::now('Asia/Jakarta');
                $weekNum = ($year === (int) $now->year && $month === (int) $now->month)
                    ? self::getWeekOfMonth($now)
                    : 1;
            } elseif ($this->week_pattern === 'pekan_1_3_5') {
                foreach ([1, 3, 5] as $w) {
                    if (isset($map["{$this->day_name}_{$w}"])) {
                        $weekNum = $w;
                        break;
                    }
                }
                $weekNum = $weekNum ?: 1;
            } elseif ($this->week_pattern === 'pekan_2_4') {
                foreach ([2, 4] as $w) {
                    if (isset($map["{$this->day_name}_{$w}"])) {
                        $weekNum = $w;
                        break;
                    }
                }
                $weekNum = $weekNum ?: 2;
            }
        }

        if (!$weekNum) {
            return null;
        }

        $date = $map["{$this->day_name}_{$weekNum}"] ?? null;

        // Fallback: jika tanggal tidak ditemukan di bulan ini (misal Kamis/Jumat di Pekan 5 yang melintasi bulan berikutnya),
        // gunakan getWeekCalendarDays untuk menemukan tanggal konkret hari tersebut pada pekan kalender yang sesuai.
        if (!$date) {
            $calendarDays = self::getWeekCalendarDays($year, $month, $weekNum);
            foreach ($calendarDays as $cDay) {
                if ($cDay['day_name'] === $this->day_name) {
                    $date = $cDay['date'];
                    break;
                }
            }
        }

        return $date;
    }
}

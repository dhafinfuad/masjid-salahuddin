<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OdojEntry extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'datetime',
        'juz_number' => 'integer',
    ];

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('target_date', $date);
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', 'Selesai');
    }

    public function scopeBelum(Builder $query): Builder
    {
        return $query->where('status', 'Belum');
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'Selesai';
    }

    public function toggle(): void
    {
        if ($this->status === 'Selesai') {
            $this->status = 'Belum';
            $this->completed_at = null;
        } else {
            $this->status = 'Selesai';
            $this->completed_at = Carbon::now();
        }
        $this->save();
    }

    /**
     * Calculate rotated Juz number after $daysShift days.
     * Modulo 30: (juz - 1 + daysShift) % 30 + 1
     */
    public static function rotateJuz(int $juz, int $daysShift): int
    {
        $mod = ($juz - 1 + $daysShift) % 30;
        if ($mod < 0) {
            $mod += 30;
        }
        return $mod + 1;
    }

    /**
     * Determine which Juz from base date rotates into $targetJuz after $daysShift days.
     */
    public static function getSourceJuz(int $targetJuz, int $daysShift): int
    {
        return self::rotateJuz($targetJuz, -$daysShift);
    }

    /**
     * Default master list of participants if no entries exist in the database.
     */
    public static function getDefaultParticipants(): array
    {
        return [
            1 => 'Deril',
            2 => 'Bimo / Sukirman',
            3 => 'Juli',
            4 => 'Bu Indah',
            5 => 'Min',
            6 => 'Deddy',
            7 => 'Yohana',
            8 => 'Khudori',
            9 => 'Yogi',
            10 => 'Eko',
            11 => 'Fahmi',
            12 => 'Bambang',
            13 => 'Rizky',
            14 => 'Haryono',
            15 => 'Agus',
            16 => 'Tri',
            17 => 'Wahyu',
            18 => 'Arif',
            19 => 'Hendra',
            20 => 'Surya',
            21 => 'Joko',
            22 => 'Dimas',
            23 => 'Budi',
            24 => 'Fauzi',
            25 => 'Nugroho',
            26 => 'Setiawan',
            27 => 'Gunawan',
            28 => 'Pratama',
            29 => 'Laila',
            30 => 'Miswati',
        ];
    }

    /**
     * Fetch or automatically generate rotated entries for the target date.
     * Uses 1 single indexed query when entries already exist (Zero N+1).
     */
    public static function getEntriesForDate(string $targetDate): \Illuminate\Database\Eloquent\Collection
    {
        $entries = static::forDate($targetDate)->orderBy('juz_number')->get();

        if ($entries->count() === 30) {
            return $entries;
        }

        return static::generateEntriesForDate($targetDate);
    }

    /**
     * Generate 30 entries for a target date based on the rotation from the nearest reference date.
     */
    public static function generateEntriesForDate(string $targetDate): \Illuminate\Database\Eloquent\Collection
    {
        $targetCarbon = Carbon::parse($targetDate)->startOfDay();

        // Find nearest prior date with entries
        $baseEntry = static::whereDate('target_date', '<', $targetDate)
            ->orderBy('target_date', 'desc')
            ->first();

        // If none prior, find nearest future date
        if (! $baseEntry) {
            $baseEntry = static::whereDate('target_date', '>', $targetDate)
                ->orderBy('target_date', 'asc')
                ->first();
        }

        $existingEntries = static::forDate($targetDate)->get()->keyBy('juz_number');
        $toInsert = [];
        $now = Carbon::now();

        if ($baseEntry) {
            $baseDate = $baseEntry->target_date->format('Y-m-d');
            $baseCarbon = Carbon::parse($baseDate)->startOfDay();
            $daysShift = (int) $baseCarbon->diffInDays($targetCarbon, false);

            $baseEntries = static::forDate($baseDate)->get()->keyBy('juz_number');
            $defaults = static::getDefaultParticipants();

            for ($juz = 1; $juz <= 30; $juz++) {
                if ($existingEntries->has($juz)) {
                    continue;
                }

                $sourceJuz = static::getSourceJuz($juz, $daysShift);
                $name = $baseEntries->get($sourceJuz)?->jamaah_name 
                    ?? $defaults[$sourceJuz] 
                    ?? "Jamaah {$juz}";

                $toInsert[] = [
                    'group_name' => 'Laporan Madya Malang Bertilawah',
                    'jamaah_name' => $name,
                    'juz_number' => $juz,
                    'target_date' => $targetDate,
                    'status' => 'Belum',
                    'completed_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        } else {
            $defaults = static::getDefaultParticipants();
            for ($juz = 1; $juz <= 30; $juz++) {
                if ($existingEntries->has($juz)) {
                    continue;
                }

                $toInsert[] = [
                    'group_name' => 'Laporan Madya Malang Bertilawah',
                    'jamaah_name' => $defaults[$juz] ?? "Jamaah {$juz}",
                    'juz_number' => $juz,
                    'target_date' => $targetDate,
                    'status' => 'Belum',
                    'completed_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($toInsert)) {
            static::insert($toInsert);
        }

        return static::forDate($targetDate)->orderBy('juz_number')->get();
    }
}

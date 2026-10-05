<?php

namespace App\Http\Controllers;

use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use App\Services\SimpleXlsxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrayerDutyExportController extends Controller
{
    /**
     * Export Prayer Duties Schedule as Excel (.xlsx) with Text formatted columns
     */
    public function exportExcel(Request $request): Response
    {
        $week = $request->query('week', 'all');
        $prayerTime = $request->query('prayer_time', 'all');
        $year = $request->query('year', 'all');
        $month = $request->query('month', 'all');
        $fileName = 'Jadwal_Petugas_Ibadah_' . date('Y-m-d') . '.xlsx';

        $now = Carbon::now('Asia/Jakarta');
        $selectedMonthNum = (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12)
            ? (int) $month
            : (int) $now->month;
        $selectedYearNum = (is_numeric($year) && (int) $year >= 2020)
            ? (int) $year
            : (int) $now->year;
        $selectedWeekNum = is_numeric($week) ? (int) $week : null;

        if ($selectedWeekNum !== null) {
            $weekCalendarDays = PrayerDuty::getWeekCalendarDays($selectedYearNum, $selectedMonthNum, $selectedWeekNum);

            $yearDutiesQuery = PrayerDuty::query();
            if ($year !== 'all' && is_numeric($year)) {
                $selectedYear = (int) $year;
                $yearDutiesQuery->where(function ($q) use ($selectedYear, $now) {
                    $q->where('tahun', $selectedYear);
                    if ($selectedYear === (int) $now->year) {
                        $q->orWhereNull('tahun');
                    }
                });
            }
            $allYearDuties = $yearDutiesQuery->get();

            $times = ($prayerTime !== 'all' && in_array($prayerTime, ['dzuhur', 'ashar']))
                ? [$prayerTime]
                : ['dzuhur', 'ashar'];

            $duties = collect([]);
            foreach ($weekCalendarDays as $dayInfo) {
                foreach ($times as $pTime) {
                    // Kecualikan Jumat Dzuhur karena diatur di halaman Kegiatan (Khutbah Jumat)
                    if ($dayInfo['day_name'] === 'Jumat' && $pTime === 'dzuhur') {
                        continue;
                    }

                    $matchedDuties = $allYearDuties->filter(function ($d) use ($dayInfo, $pTime) {
                        return $d->day_name === $dayInfo['day_name']
                            && $d->prayer_time === $pTime
                            && $d->matchesWeek($dayInfo['week_number']);
                    });

                    foreach ($matchedDuties as $matchedDuty) {
                        $dutyRow = clone $matchedDuty;
                        $dutyRow->resolved_date = $dayInfo['date'];
                        $dutyRow->resolved_week = $dayInfo['week_number'];
                        $dutyRow->resolved_week_label = 'Pekan ' . $dayInfo['week_number'];
                        $duties->push($dutyRow);
                    }
                }
            }
        } else {
            $query = PrayerDuty::query();

            if ($prayerTime !== 'all' && in_array($prayerTime, ['dzuhur', 'ashar'])) {
                $query->where('prayer_time', $prayerTime);
            }

            if ($year !== 'all' && is_numeric($year)) {
                $selectedYear = (int) $year;
                $query->where(function ($q) use ($selectedYear, $now) {
                    $q->where('tahun', $selectedYear);
                    if ($selectedYear === (int) $now->year) {
                        $q->orWhereNull('tahun');
                    }
                });
            }

            // Kecualikan Jumat Dzuhur karena diatur di halaman Kegiatan (Khutbah Jumat)
            $query->where(function ($q) {
                $q->where('day_name', '!=', 'Jumat')
                  ->orWhere('prayer_time', '!=', 'dzuhur');
            });

            $duties = $query->orderByRaw("CASE day_name WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 6 END")
                           ->orderBy('prayer_time', 'desc') // dzuhur before ashar alphabetically inverted
                           ->get();
        }

        $currentWeekNum = PrayerDuty::getWeekOfMonth($now);
        foreach ($duties as $duty) {
            if (!$duty->resolved_date) {
                $dutyWeek = $duty->resolved_week ?? null;
                if (!$dutyWeek) {
                    if (preg_match('/pekan_(\d)/', $duty->week_pattern, $m)) {
                        $dutyWeek = (int) $m[1];
                    } elseif ($duty->matchesWeek($currentWeekNum)) {
                        $dutyWeek = $currentWeekNum;
                    } elseif ($duty->week_pattern === 'semua') {
                        $dutyWeek = $currentWeekNum;
                    } elseif ($duty->week_pattern === 'pekan_1_3_5') {
                        $dutyWeek = 1;
                    } elseif ($duty->week_pattern === 'pekan_2_4') {
                        $dutyWeek = 2;
                    }
                }
                $duty->resolved_week = $dutyWeek;
                if (!$duty->resolved_week_label && $dutyWeek) {
                    $duty->resolved_week_label = 'Pekan ' . $dutyWeek;
                }
                $duty->resolved_date = $duty->resolveDate($selectedYearNum, $selectedMonthNum, $selectedWeekNum ?? $dutyWeek);
            }
        }

        // Urutkan secara default dari tanggal terlama ke tanggal terbaru
        $duties = $duties->sort(function ($a, $b) {
            $timeA = $a->resolved_date ? $a->resolved_date->timestamp : PHP_INT_MAX;
            $timeB = $b->resolved_date ? $b->resolved_date->timestamp : PHP_INT_MAX;

            if ($timeA !== $timeB) {
                return $timeA <=> $timeB;
            }

            $prayerOrder = ['dzuhur' => 1, 'ashar' => 2];
            $orderA = $prayerOrder[$a->prayer_time] ?? 3;
            $orderB = $prayerOrder[$b->prayer_time] ?? 3;

            if ($orderA !== $orderB) {
                return $orderA <=> $orderB;
            }

            return $a->id <=> $b->id;
        })->values();

        $rangeStart = Carbon::create($selectedYearNum, $selectedMonthNum, 1, 0, 0, 0, 'Asia/Jakarta')->startOfMonth()->subWeeks(1);
        $rangeEnd = Carbon::create($selectedYearNum, $selectedMonthNum, 1, 0, 0, 0, 'Asia/Jakarta')->endOfMonth()->addWeeks(1);

        $fridayKajians = Kajian::jumat()
            ->whereBetween('date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
            ->orderBy('date')
            ->get();
        $fridayKajiansByDate = $fridayKajians->keyBy(fn($k) => $k->date->format('Y-m-d'));
        $fridayKajiansByWeek = $fridayKajians->keyBy(fn($k) => PrayerDuty::getWeekOfMonth($k->date));

        $kajianUmum = Kajian::kajianUmum()
            ->whereNotNull('speaker_name')
            ->where('speaker_name', '!=', '')
            ->whereBetween('date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
            ->orderBy('date')
            ->get();
        $kajianUmumByDate = $kajianUmum->keyBy(fn($k) => $k->date->format('Y-m-d'));

        $headers = [
            'No',
            'Hari',
            'Tanggal',
            'Waktu Shalat',
            'Petugas Imam',
            'Petugas Muadzin',
            'Pola Pekan',
        ];

        $colWidths = [6, 14, 20, 16, 26, 26, 18];

        $rows = [];
        $rowNum = 1;
        foreach ($duties as $duty) {
            // Kecualikan Jumat Dzuhur karena diatur di halaman Kegiatan (Khutbah Jumat)
            if ($duty->day_name === 'Jumat' && $duty->prayer_time === 'dzuhur') {
                continue;
            }

            $dutyWeek = $duty->resolved_week ?? null;
            if (!$dutyWeek) {
                $currentWeekNum = PrayerDuty::getWeekOfMonth(now());
                if ($duty->matchesWeek($currentWeekNum)) {
                    $dutyWeek = $currentWeekNum;
                } elseif (preg_match('/pekan_(\d)/', $duty->week_pattern, $m)) {
                    $dutyWeek = (int) $m[1];
                } elseif ($duty->week_pattern === 'semua') {
                    $dutyWeek = $currentWeekNum;
                }
            }

            $resolvedDate = $duty->resolved_date ?? $duty->resolveDate($selectedYearNum, $selectedMonthNum, $selectedWeekNum ?? $dutyWeek);
            $dateStr = $resolvedDate ? $resolvedDate->translatedFormat('d F Y') : '-';
            $dateKey = $resolvedDate ? $resolvedDate->format('Y-m-d') : null;

            $matchedKajianOnDate = ($duty->prayer_time === 'ashar' && $dateKey && isset($kajianUmumByDate[$dateKey]))
                ? $kajianUmumByDate[$dateKey]
                : null;

            $imam = ($duty->imam_name ?: '-');
            $muadzin = ($duty->muadzin_name ?: '-');

            if ($duty->prayer_time === 'ashar' && $matchedKajianOnDate && !empty($matchedKajianOnDate->speaker_name)) {
                $imam = $matchedKajianOnDate->speaker_name;
                if (!empty($matchedKajianOnDate->muadzin_name)) {
                    $muadzin = $matchedKajianOnDate->muadzin_name;
                }
            }

            $rows[] = [
                (string) ($rowNum++),
                $duty->day_name,
                $dateStr,
                ucfirst($duty->prayer_time),
                $imam,
                $muadzin,
                $duty->resolved_week_label ?? $duty->week_pattern,
            ];
        }

        $xlsxContent = SimpleXlsxService::createXlsx($headers, $rows, $colWidths);

        return response($xlsxContent, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Content-Length' => strlen($xlsxContent),
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Printable Clean PDF/Print View for Prayer Duty Schedule
     */
    public function printPdf(Request $request)
    {
        $settings = MasjidSetting::getActive();
        $week = $request->query('week', 'all');
        $prayerTime = $request->query('prayer_time', 'all');
        $year = $request->query('year', 'all');
        $month = $request->query('month', 'all');

        $now = Carbon::now('Asia/Jakarta');
        $selectedMonthNum = (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12)
            ? (int) $month
            : (int) $now->month;
        $selectedYearNum = (is_numeric($year) && (int) $year >= 2020)
            ? (int) $year
            : (int) $now->year;
        $selectedWeekNum = is_numeric($week) ? (int) $week : null;

        if ($selectedWeekNum !== null) {
            $weekCalendarDays = PrayerDuty::getWeekCalendarDays($selectedYearNum, $selectedMonthNum, $selectedWeekNum);

            $yearDutiesQuery = PrayerDuty::query();
            if ($year !== 'all' && is_numeric($year)) {
                $selectedYear = (int) $year;
                $yearDutiesQuery->where(function ($q) use ($selectedYear, $now) {
                    $q->where('tahun', $selectedYear);
                    if ($selectedYear === (int) $now->year) {
                        $q->orWhereNull('tahun');
                    }
                });
            }
            $allYearDuties = $yearDutiesQuery->get();

            $times = ($prayerTime !== 'all' && in_array($prayerTime, ['dzuhur', 'ashar']))
                ? [$prayerTime]
                : ['dzuhur', 'ashar'];

            $duties = collect([]);
            foreach ($weekCalendarDays as $dayInfo) {
                foreach ($times as $pTime) {
                    $matchedDuties = $allYearDuties->filter(function ($d) use ($dayInfo, $pTime) {
                        return $d->day_name === $dayInfo['day_name']
                            && $d->prayer_time === $pTime
                            && $d->matchesWeek($dayInfo['week_number']);
                    });

                    if ($matchedDuties->isEmpty()) {
                        $matchedDuties = $allYearDuties->filter(function ($d) use ($dayInfo, $pTime, $selectedWeekNum) {
                            return $d->day_name === $dayInfo['day_name']
                                && $d->prayer_time === $pTime
                                && $d->matchesWeek($selectedWeekNum);
                        });
                    }

                    foreach ($matchedDuties as $matchedDuty) {
                        $dutyRow = clone $matchedDuty;
                        $dutyRow->resolved_date = $dayInfo['date'];
                        $dutyRow->resolved_week = $dayInfo['week_number'];
                        $dutyRow->resolved_week_label = 'Pekan ' . $dayInfo['week_number'];
                        $duties->push($dutyRow);
                    }
                }
            }
        } else {
            $query = PrayerDuty::query();

            if ($prayerTime !== 'all' && in_array($prayerTime, ['dzuhur', 'ashar'])) {
                $query->where('prayer_time', $prayerTime);
            }

            if ($year !== 'all' && is_numeric($year)) {
                $selectedYear = (int) $year;
                $query->where(function ($q) use ($selectedYear, $now) {
                    $q->where('tahun', $selectedYear);
                    if ($selectedYear === (int) $now->year) {
                        $q->orWhereNull('tahun');
                    }
                });
            }

            $duties = $query->orderByRaw("CASE day_name WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 6 END")
                           ->orderBy('prayer_time', 'desc')
                           ->get();
        }

        $currentWeekNum = PrayerDuty::getWeekOfMonth($now);
        foreach ($duties as $duty) {
            if (!$duty->resolved_date) {
                $dutyWeek = $duty->resolved_week ?? null;
                if (!$dutyWeek) {
                    if (preg_match('/pekan_(\d)/', $duty->week_pattern, $m)) {
                        $dutyWeek = (int) $m[1];
                    } elseif ($duty->matchesWeek($currentWeekNum)) {
                        $dutyWeek = $currentWeekNum;
                    } elseif ($duty->week_pattern === 'semua') {
                        $dutyWeek = $currentWeekNum;
                    } elseif ($duty->week_pattern === 'pekan_1_3_5') {
                        $dutyWeek = 1;
                    } elseif ($duty->week_pattern === 'pekan_2_4') {
                        $dutyWeek = 2;
                    }
                }
                $duty->resolved_week = $dutyWeek;
                if (!$duty->resolved_week_label && $dutyWeek) {
                    $duty->resolved_week_label = 'Pekan ' . $dutyWeek;
                }
                $duty->resolved_date = $duty->resolveDate($selectedYearNum, $selectedMonthNum, $selectedWeekNum ?? $dutyWeek);
            }
        }

        // Urutkan secara default dari tanggal terlama ke tanggal terbaru
        $duties = $duties->sort(function ($a, $b) {
            $timeA = $a->resolved_date ? $a->resolved_date->timestamp : PHP_INT_MAX;
            $timeB = $b->resolved_date ? $b->resolved_date->timestamp : PHP_INT_MAX;

            if ($timeA !== $timeB) {
                return $timeA <=> $timeB;
            }

            $prayerOrder = ['dzuhur' => 1, 'ashar' => 2];
            $orderA = $prayerOrder[$a->prayer_time] ?? 3;
            $orderB = $prayerOrder[$b->prayer_time] ?? 3;

            if ($orderA !== $orderB) {
                return $orderA <=> $orderB;
            }

            return $a->id <=> $b->id;
        })->values();

        $rangeStart = Carbon::create($selectedYearNum, $selectedMonthNum, 1, 0, 0, 0, 'Asia/Jakarta')->startOfMonth()->subWeeks(1);
        $rangeEnd = Carbon::create($selectedYearNum, $selectedMonthNum, 1, 0, 0, 0, 'Asia/Jakarta')->endOfMonth()->addWeeks(1);

        $fridayKajians = Kajian::jumat()
            ->whereBetween('date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
            ->orderBy('date')
            ->get();
        $fridayKajiansByDate = $fridayKajians->keyBy(fn($k) => $k->date->format('Y-m-d'));
        $fridayKajiansByWeek = $fridayKajians->keyBy(fn($k) => PrayerDuty::getWeekOfMonth($k->date));

        $kajianUmum = Kajian::kajianUmum()
            ->whereNotNull('speaker_name')
            ->where('speaker_name', '!=', '')
            ->whereBetween('date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
            ->orderBy('date')
            ->get();
        $kajianUmumByDate = $kajianUmum->keyBy(fn($k) => $k->date->format('Y-m-d'));

        // Penandatangan resmi
        $ketuaDkm = \App\Models\User::where('role', 'Ketua')->where('status', 'AKTIF')->first()
            ?? \App\Models\User::where('role', 'like', '%Ketua%')->first();
        $sekretarisDkm = \App\Models\User::where('role', 'Sekretaris')->where('status', 'AKTIF')->first()
            ?? \App\Models\User::where('role', 'like', '%Sekretaris%')->first();

        $tteHash = 'TTE-' . strtoupper(substr(md5(($settings->name ?? 'MS') . 'PETUGAS' . ($week ?? 'ALL') . date('Y-m')), 0, 10));
        $verifyUrl = url('/admin/petugas/export-pdf?week=' . $week . '&tte=1&hash=' . $tteHash);

        return view('admin.print.prayer-duty-roster', [
            'settings' => $settings,
            'duties' => $duties,
            'week' => $week,
            'prayerTime' => $prayerTime,
            'month' => $month,
            'selectedMonthNum' => $selectedMonthNum,
            'selectedYearNum' => $selectedYearNum,
            'selectedWeekNum' => $selectedWeekNum,
            'fridayKajiansByDate' => $fridayKajiansByDate,
            'fridayKajiansByWeek' => $fridayKajiansByWeek,
            'kajianUmumByDate' => $kajianUmumByDate,
            'printDate' => Carbon::now()->translatedFormat('d F Y'),
            'ketuaDkm' => $ketuaDkm,
            'sekretarisDkm' => $sekretarisDkm,
            'isTteSigned' => true,
            'tteHash' => $tteHash,
            'verifyUrl' => $verifyUrl,
        ]);
    }

    /**
     * Printable Monthly Schedule for Prayer Duties (Public / Admin)
     */
    public function printMonthlyPdf(Request $request)
    {
        $settings = MasjidSetting::getActive();
        $now = Carbon::now('Asia/Jakarta');
        $month = (int) $request->query('month', $now->month);
        $year = (int) $request->query('year', $now->year);

        $startDate = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $fridayKajians = Kajian::jumat()
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($k) => $k->date->format('Y-m-d'));

        $kajianUmumByDate = Kajian::kajianUmum()
            ->whereNotNull('speaker_name')
            ->where('speaker_name', '!=', '')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($k) => $k->date->format('Y-m-d'));

        $allDuties = PrayerDuty::all();
        $dayMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

        $roster = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d, 12, 0, 0, 'Asia/Jakarta');
            $dateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeekIso;
            $dayName = $dayMap[$dayOfWeek] ?? 'Senin';
            $isWeekend = ($dayOfWeek === 6 || $dayOfWeek === 7);
            $isFriday = ($dayOfWeek === 5);
            $weekNumber = PrayerDuty::getWeekOfMonth($date);

            if ($isWeekend) {
                continue;
            }

            if ($isFriday) {
                $kajian = $fridayKajians->get($dateStr);
                $khatib = $kajian ? ($kajian->khatib_name ?: 'Ust. M. Yasak Lc MA') : ($settings->friday_prayer_info['khatib'] ?? 'Ust. M. Yasak Lc MA');
                $mc = $kajian ? ($kajian->mc_name ?: ($kajian->speaker_name ?: 'Alan Irfansyah')) : ($settings->friday_prayer_info['mc'] ?? 'Alan Irfansyah');
                $muadzin = $kajian ? ($kajian->muadzin_name ?: ($kajian->description ?: 'Khodori')) : ($settings->friday_prayer_info['muadzin'] ?? 'Khodori');

                $asharDuty = $allDuties
                    ->where('day_name', 'Jumat')
                    ->where('prayer_time', 'ashar')
                    ->filter(fn($duty) => $duty->matchesWeek($weekNumber))
                    ->first();

                $kajianOnDate = $kajianUmumByDate->get($dateStr);
                $asImam = $asharDuty?->imam_name ?: '-';
                $asMuadzin = $asharDuty?->muadzin_name ?: '-';
                $asharKajianTitle = null;
                if ($kajianOnDate && !empty($kajianOnDate->speaker_name)) {
                    $asImam = $kajianOnDate->speaker_name;
                    if (!empty($kajianOnDate->muadzin_name)) {
                        $asMuadzin = $kajianOnDate->muadzin_name;
                    }
                    $asharKajianTitle = $kajianOnDate->title;
                }

                $roster[] = [
                    'date' => $dateStr,
                    'day_name' => $dayName,
                    'formatted_date' => $date->translatedFormat('d F Y'),
                    'day_number' => $d,
                    'week_number' => $weekNumber,
                    'is_friday' => true,
                    'dzuhur_imam' => $khatib,
                    'dzuhur_muadzin' => $muadzin,
                    'ashar_imam' => $asImam,
                    'ashar_muadzin' => $asMuadzin,
                    'ashar_kajian_title' => $asharKajianTitle,
                    'friday_mc' => $mc,
                    'notes' => 'Shalat Jumat' . ($mc !== '-' ? ' • MC: ' . $mc : ''),
                ];
            } else {
                $matchingDuties = $allDuties
                    ->where('day_name', $dayName)
                    ->filter(fn($duty) => $duty->matchesWeek($weekNumber));

                $dzuhurDuty = $matchingDuties->firstWhere('prayer_time', 'dzuhur');
                $asharDuty = $matchingDuties->firstWhere('prayer_time', 'ashar');
                $kajianOnDate = $kajianUmumByDate->get($dateStr);

                $dzImam = $dzuhurDuty?->imam_name ?: '-';
                $dzMuadzin = $dzuhurDuty?->muadzin_name ?: '-';

                $asImam = $asharDuty?->imam_name ?: '-';
                $asMuadzin = $asharDuty?->muadzin_name ?: '-';
                $asharKajianTitle = null;

                if ($kajianOnDate && !empty($kajianOnDate->speaker_name)) {
                    $asImam = $kajianOnDate->speaker_name;
                    if (!empty($kajianOnDate->muadzin_name)) {
                        $asMuadzin = $kajianOnDate->muadzin_name;
                    }
                    $asharKajianTitle = $kajianOnDate->title;
                }

                $roster[] = [
                    'date' => $dateStr,
                    'day_name' => $dayName,
                    'formatted_date' => $date->translatedFormat('d F Y'),
                    'day_number' => $d,
                    'week_number' => $weekNumber,
                    'is_friday' => false,
                    'dzuhur_imam' => $dzImam,
                    'dzuhur_muadzin' => $dzMuadzin,
                    'ashar_imam' => $asImam,
                    'ashar_muadzin' => $asMuadzin,
                    'ashar_kajian_title' => $asharKajianTitle,
                    'friday_mc' => '-',
                    'notes' => '-',
                ];
            }
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Penandatangan resmi
        $ketuaDkm = \App\Models\User::where('role', 'Ketua')->where('status', 'AKTIF')->first()
            ?? \App\Models\User::where('role', 'like', '%Ketua%')->first();
        $sekretarisDkm = \App\Models\User::where('role', 'Sekretaris')->where('status', 'AKTIF')->first()
            ?? \App\Models\User::where('role', 'like', '%Sekretaris%')->first();

        $tteHash = 'TTE-' . strtoupper(substr(md5(($settings->name ?? 'MS') . 'PETUGAS-BULANAN-' . $month . '-' . $year), 0, 10));
        $verifyUrl = url('/petugas-sholat/cetak?month=' . $month . '&year=' . $year . '&tte=1&hash=' . $tteHash);

        return view('admin.print.prayer-duty-monthly', [
            'settings' => $settings,
            'roster' => $roster,
            'monthName' => $months[$month] ?? 'Januari',
            'month' => $month,
            'year' => $year,
            'printDate' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
            'ketuaDkm' => $ketuaDkm,
            'sekretarisDkm' => $sekretarisDkm,
            'isTteSigned' => true,
            'tteHash' => $tteHash,
            'verifyUrl' => $verifyUrl,
        ]);
    }
}

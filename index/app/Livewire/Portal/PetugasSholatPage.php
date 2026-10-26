<?php

namespace App\Livewire\Portal;

use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Jadwal Petugas Shalat Bulanan — Masjid Salahuddin')]
class PetugasSholatPage extends Component
{
    public int $selectedMonth;
    public int $selectedYear;
    public string $search = '';

    public function mount(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->selectedMonth = (int) $now->month;
        $this->selectedYear = (int) $now->year;
    }

    public function resetFilters(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->selectedMonth = (int) $now->month;
        $this->selectedYear = (int) $now->year;
        $this->search = '';
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    public function render()
    {
        $settings = MasjidSetting::getActive();
        $todayCarbon = Carbon::now('Asia/Jakarta');
        $todayDate = $todayCarbon->format('Y-m-d');
        $isCurrentMonthYear = ($this->selectedMonth === (int) $todayCarbon->month && $this->selectedYear === (int) $todayCarbon->year);

        if ($isCurrentMonthYear) {
            // When viewing current month, start from TODAY and show upcoming 30 days (rolling 30-day window)
            // so users immediately see today's duty followed by upcoming duties sequentially (e.g. 13 Oct, 14 Oct, ...
            // or 31 Oct, 1 Nov, ... up to 29 Nov).
            $startDate = $todayCarbon->copy()->startOfDay();
            $totalDays = 30;
            $endDate = $todayCarbon->copy()->addDays($totalDays - 1)->endOfDay();
        } else {
            // When viewing another month (past or future), show all days of that month from day 1 to end of month.
            $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1, 0, 0, 0, 'Asia/Jakarta')->startOfMonth();
            $totalDays = $startDate->daysInMonth;
            $endDate = $startDate->copy()->endOfMonth();
        }

        // Eager load in 2 unified queries (Zero N+1)
        $fridayKajians = Kajian::jumat()
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($k) => $k->date->format('Y-m-d'));

        $pekananKajians = Kajian::kajianUmum()
            ->whereNotNull('speaker_name')
            ->where('speaker_name', '!=', '')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($k) => $k->date->format('Y-m-d'));

        $allDuties = PrayerDuty::all();

        $dayMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

        $roster = [];

        for ($i = 0; $i < $totalDays; $i++) {
            $date = $startDate->copy()->addDays($i)->setTime(12, 0, 0);
            $dateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeekIso; // 1 = Monday, 7 = Sunday
            $dayName = $dayMap[$dayOfWeek] ?? 'Senin';
            $isWeekend = ($dayOfWeek === 6 || $dayOfWeek === 7);
            $isFriday = ($dayOfWeek === 5);
            $weekNumber = PrayerDuty::getWeekOfMonth($date);
            $isToday = ($dateStr === $todayDate);
            $dayNumber = (int) $date->day;

            if ($isWeekend) {
                $roster[] = [
                    'date' => $dateStr,
                    'day_name' => $dayName,
                    'formatted_date' => $date->translatedFormat('d F Y'),
                    'day_number' => $dayNumber,
                    'week_number' => $weekNumber,
                    'is_weekend' => true,
                    'is_friday' => false,
                    'is_today' => $isToday,
                    'dzuhur_imam' => '-',
                    'dzuhur_muadzin' => '-',
                    'ashar_imam' => '-',
                    'ashar_muadzin' => '-',
                    'ashar_kajian_title' => null,
                    'friday_khatib' => '-',
                    'friday_mc' => '-',
                    'friday_muadzin' => '-',
                    'notes' => 'Libur Operasional Kantor',
                ];
                continue;
            }

            if ($isFriday) {
                $kajian = $fridayKajians->get($dateStr);
                $khatib = $kajian ? ($kajian->khatib_name ?: 'Ust. M. Yasak Lc MA') : ($settings->friday_prayer_info['khatib'] ?? 'Ust. M. Yasak Lc MA');
                $mc = $kajian ? ($kajian->mc_name ?: ($kajian->speaker_name ?: 'Alan Irfansyah')) : ($settings->friday_prayer_info['mc'] ?? 'Alan Irfansyah');
                $muadzin = $kajian ? ($kajian->muadzin_name ?: ($kajian->description ?: 'Khodori')) : ($settings->friday_prayer_info['muadzin'] ?? 'Khodori');

                // Ashar on Friday
                $asharDuty = $allDuties
                    ->where('day_name', 'Jumat')
                    ->where('prayer_time', 'ashar')
                    ->filter(fn($duty) => $duty->matchesWeek($weekNumber))
                    ->first();

                $kajianOnDate = $pekananKajians->get($dateStr);
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
                    'day_number' => $dayNumber,
                    'week_number' => $weekNumber,
                    'is_weekend' => false,
                    'is_friday' => true,
                    'is_today' => $isToday,
                    'dzuhur_imam' => $khatib,
                    'dzuhur_muadzin' => $muadzin,
                    'ashar_imam' => $asImam,
                    'ashar_muadzin' => $asMuadzin,
                    'ashar_kajian_title' => $asharKajianTitle,
                    'friday_khatib' => $khatib,
                    'friday_mc' => $mc,
                    'friday_muadzin' => $muadzin,
                    'notes' => 'Shalat Jumat' . ($mc !== '-' ? ' • MC: ' . $mc : ''),
                ];
            } else {
                // Mon - Thu
                $matchingDuties = $allDuties
                    ->where('day_name', $dayName)
                    ->filter(fn($duty) => $duty->matchesWeek($weekNumber));

                $dzuhurDuty = $matchingDuties->firstWhere('prayer_time', 'dzuhur');
                $asharDuty = $matchingDuties->firstWhere('prayer_time', 'ashar');
                $kajianOnDate = $pekananKajians->get($dateStr);

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
                    'day_number' => $dayNumber,
                    'week_number' => $weekNumber,
                    'is_weekend' => false,
                    'is_friday' => false,
                    'is_today' => $isToday,
                    'dzuhur_imam' => $dzImam,
                    'dzuhur_muadzin' => $dzMuadzin,
                    'ashar_imam' => $asImam,
                    'ashar_muadzin' => $asMuadzin,
                    'ashar_kajian_title' => $asharKajianTitle,
                    'friday_khatib' => '-',
                    'friday_mc' => '-',
                    'friday_muadzin' => '-',
                    'notes' => $dzuhurDuty?->notes ?: ($asharDuty?->notes ?: '-'),
                ];
            }
        }

        if (!empty($this->search)) {
            $searchTerm = strtolower(trim($this->search));
            $roster = array_values(array_filter($roster, function ($row) use ($searchTerm) {
                $searchable = strtolower(
                    ($row['day_name'] ?? '') . ' ' .
                    ($row['formatted_date'] ?? '') . ' ' .
                    ($row['dzuhur_imam'] ?? '') . ' ' .
                    ($row['dzuhur_muadzin'] ?? '') . ' ' .
                    ($row['ashar_imam'] ?? '') . ' ' .
                    ($row['ashar_muadzin'] ?? '') . ' ' .
                    ($row['friday_khatib'] ?? '') . ' ' .
                    ($row['friday_mc'] ?? '') . ' ' .
                    ($row['friday_muadzin'] ?? '') . ' ' .
                    ($row['ashar_kajian_title'] ?? '')
                );
                return str_contains($searchable, $searchTerm);
            }));
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $currentYear = (int) $todayCarbon->year;
        $years = range($currentYear - 2, $currentYear + 2);

        return view('livewire.portal.petugas-sholat-page', [
            'settings' => $settings,
            'roster' => $roster,
            'months' => $months,
            'years' => $years,
            'todayDate' => $todayDate,
            'isCurrentMonthYear' => $isCurrentMonthYear,
        ]);
    }
}

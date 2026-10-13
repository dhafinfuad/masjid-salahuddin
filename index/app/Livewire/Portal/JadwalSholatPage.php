<?php

namespace App\Livewire\Portal;

use App\Models\MasjidSetting;
use App\Services\PrayerTimeService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Jadwal Waktu Shalat Bulanan — Masjid Salahuddin')]
class JadwalSholatPage extends Component
{
    public string $cityId = '1634';
    public string $cityName = 'Kota Malang';
    public int $selectedMonth;
    public int $selectedYear;

    public function mount(PrayerTimeService $prayerService): void
    {
        /** @var MasjidSetting $settings */
        $settings = MasjidSetting::getActive();
        $this->cityId = $settings->city_id ?: '1634';
        $this->cityName = $settings->city_name ? ucwords(strtolower($settings->city_name)) : 'Kota Malang';
        $this->selectedMonth = (int) Carbon::now('Asia/Jakarta')->month;
        $this->selectedYear = (int) Carbon::now('Asia/Jakarta')->year;
    }

    public function updatedCityId(PrayerTimeService $prayerService): void
    {
        $citiesById = $prayerService->getCitiesKeyedById();
        if (isset($citiesById[$this->cityId])) {
            $this->cityName = ucwords(strtolower($citiesById[$this->cityId]['lokasi']));
        }
    }

    public function selectCity(string $id, PrayerTimeService $prayerService): void
    {
        $this->cityId = $id;
        $this->updatedCityId($prayerService);
    }

    public function render(PrayerTimeService $prayerService)
    {
        $settings = MasjidSetting::getActive();
        $cities = $prayerService->getCities();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $todayCarbon = Carbon::now('Asia/Jakarta');
        $currentYear = (int) $todayCarbon->year;
        $years = range($currentYear - 2, $currentYear + 2);
        $todayDate = $todayCarbon->format('Y-m-d');
        $isCurrentMonthYear = ($this->selectedMonth === (int) $todayCarbon->month && $this->selectedYear === (int) $todayCarbon->year);

        if ($isCurrentMonthYear) {
            // When viewing current month, start from TODAY and show upcoming 30 days (rolling 30-day window)
            // sequentially (e.g. 13 Oct, 14 Oct, ... or 31 Oct, 1 Nov, ... up to 29 Nov).
            $startDate = $todayCarbon->copy()->startOfDay();
            $totalDays = 30;
            $endDate = $todayCarbon->copy()->addDays($totalDays - 1)->endOfDay();

            $currSchedule = $prayerService->getMonthlyPrayerSchedule(
                $this->cityId,
                (int) $todayCarbon->year,
                (int) $todayCarbon->month,
                $settings
            );

            $scheduleByDate = collect($currSchedule)->keyBy('date');

            if ((int) $endDate->month !== (int) $todayCarbon->month || (int) $endDate->year !== (int) $todayCarbon->year) {
                $nextSchedule = $prayerService->getMonthlyPrayerSchedule(
                    $this->cityId,
                    (int) $endDate->year,
                    (int) $endDate->month,
                    $settings
                );
                $scheduleByDate = $scheduleByDate->merge(collect($nextSchedule)->keyBy('date'));
            }

            $schedule = [];
            for ($i = 0; $i < $totalDays; $i++) {
                $dateObj = $startDate->copy()->addDays($i);
                $dateKey = $dateObj->format('Y-m-d');

                if ($scheduleByDate->has($dateKey)) {
                    $schedule[] = $scheduleByDate->get($dateKey);
                } else {
                    $schedule[] = [
                        'date' => $dateKey,
                        'day_name' => $dateObj->translatedFormat('l'),
                        'formatted_date' => $dateObj->translatedFormat('d F Y'),
                        'day_number' => (int) $dateObj->day,
                        'imsak' => '-',
                        'subuh' => '-',
                        'terbit' => '-',
                        'dhuha' => '-',
                        'dzuhur' => '-',
                        'ashar' => '-',
                        'maghrib' => '-',
                        'isya' => '-',
                    ];
                }
            }
        } else {
            // When viewing another month (past or future), show all days of that month from day 1 to end of month.
            $schedule = $prayerService->getMonthlyPrayerSchedule(
                $this->cityId,
                $this->selectedYear,
                $this->selectedMonth,
                $settings
            );
        }

        return view('livewire.portal.jadwal-sholat-page', [
            'settings' => $settings,
            'cities' => $cities,
            'schedule' => $schedule,
            'months' => $months,
            'years' => $years,
            'todayDate' => $todayDate,
            'isCurrentMonthYear' => $isCurrentMonthYear,
            'hijriInfo' => $prayerService->getHijriDate(),
        ]);
    }
}

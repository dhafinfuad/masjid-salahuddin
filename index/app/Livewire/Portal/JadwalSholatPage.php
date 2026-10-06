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

        $schedule = $prayerService->getMonthlyPrayerSchedule(
            $this->cityId,
            $this->selectedYear,
            $this->selectedMonth,
            $settings
        );

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

        // If viewing the current month, hoist today's schedule to the top row (index 0)
        // so users immediately see today's prayer times without scrolling.
        if ($isCurrentMonthYear && is_array($schedule)) {
            $todayIndex = null;
            foreach ($schedule as $idx => $item) {
                if (($item['date'] ?? null) === $todayDate) {
                    $todayIndex = $idx;
                    break;
                }
            }

            if ($todayIndex !== null) {
                $todayItem = $schedule[$todayIndex];
                unset($schedule[$todayIndex]);
                array_unshift($schedule, $todayItem);
            }
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

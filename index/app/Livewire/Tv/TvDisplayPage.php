<?php

namespace App\Livewire\Tv;

use App\Models\MasjidSetting;
use App\Services\PrayerTimeService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('TV Display Ruang Sholat — Masjid Salahuddin')]
class TvDisplayPage extends Component
{
    public array $prayers = [];
    public array $nextPrayer = [];
    public string $hijriDate = '';
    public string $gregorianDate = '';

    public function mount(PrayerTimeService $prayerService): void
    {
        $this->refreshPrayers($prayerService);
    }

    public function refreshPrayers(PrayerTimeService $prayerService): void
    {
        /** @var MasjidSetting $settings */
        $settings = MasjidSetting::getActive();
        $now = Carbon::now('Asia/Jakarta');
        $cityId = $settings->city_id ?: '1634';

        $this->prayers = $prayerService->getPrayerTimes($cityId, $now, $settings);
        $this->nextPrayer = $prayerService->getNextPrayer($this->prayers, $now);
        $this->hijriDate = $prayerService->getHijriDate($now);
        $this->gregorianDate = $now->translatedFormat('l, d F Y');
    }

    public function render()
    {
        $settings = MasjidSetting::getActive();

        return view('livewire.tv.tv-display-page', [
            'settings' => $settings,
        ]);
    }
}

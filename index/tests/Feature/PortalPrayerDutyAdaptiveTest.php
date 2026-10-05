<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use App\Models\Category;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalPrayerDutyAdaptiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
            'description' => 'Kajian rutin pekanan',
        ]);

        MasjidSetting::create([
            'name' => 'Masjid Salahuddin',
            'city_id' => '1634',
            'city_name' => 'Kota Malang',
        ]);

        // Setup Friday Kajian (Khutbah) for 25 September 2026
        Kajian::create([
            'type' => 'jumat',
            'title' => 'Khutbah Jumat Istiqomah',
            'date' => '2026-09-25',
            'khatib_name' => 'Ust. M. Yasak Lc MA',
            'mc_name' => 'Alan Irfansyah',
            'muadzin_name' => 'Khudori',
        ]);

        // Setup Friday Ashar duty for week 4
        PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'ashar',
            'week_pattern' => 'pekan_4',
            'imam_name' => 'Ugik Endrar Viana',
            'muadzin_name' => 'Khudori',
            'tahun' => 2026,
        ]);

        // Setup Monday duties for next week (28 September 2026 is week 5)
        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Farhan Dzuhur',
            'muadzin_name' => 'Bilal Syakir',
            'tahun' => 2026,
        ]);
        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Rasyid Ashar',
            'muadzin_name' => 'Bilal Syakir',
            'tahun' => 2026,
        ]);
    }

    public function test_friday_morning_shows_khutbah_jumat(): void
    {
        // 25 September 2026 at 10:00 WIB
        Carbon::setTestNow(Carbon::create(2026, 9, 25, 10, 0, 0, 'Asia/Jakarta'));

        Livewire::test(PortalPage::class)
            ->assertSee('Petugas Jumat')
            ->assertSee('KHATIB & IMAM')
            ->assertSee('Ust. M. Yasak Lc MA')
            ->assertSee('MC / PROTOKOL')
            ->assertSee('Alan Irfansyah')
            ->assertSee('BILAL / MUADZIN')
            ->assertSee('Khudori')
            ->assertDontSee('IMAM SHALAT ASHAR');

        Carbon::setTestNow();
    }

    public function test_friday_afternoon_at_1530_shows_friday_ashar(): void
    {
        // 25 September 2026 at 15:30 WIB
        Carbon::setTestNow(Carbon::create(2026, 9, 25, 15, 30, 0, 'Asia/Jakarta'));

        Livewire::test(PortalPage::class)
            ->assertSee('Petugas Ashar')
            ->assertSee('IMAM SHALAT ASHAR')
            ->assertSee('Ugik Endrar Viana')
            ->assertSee('BILAL / MUADZIN')
            ->assertSee('Khudori')
            ->assertDontSee('KHATIB & IMAM')
            ->assertDontSee('MC / PROTOKOL');

        Carbon::setTestNow();
    }

    public function test_friday_night_after_maghrib_shows_next_monday_h_plus_one(): void
    {
        // 25 September 2026 at 18:30 WIB (night after maghrib)
        Carbon::setTestNow(Carbon::create(2026, 9, 25, 18, 30, 0, 'Asia/Jakarta'));

        Livewire::test(PortalPage::class)
            ->assertSee('Petugas Dhuhur')
            ->assertSee('Senin, 28 September 2026')
            ->assertSee('SHOLAT DZUHUR')
            ->assertSee('Ust. Farhan Dzuhur')
            ->assertSee('SHOLAT ASHAR')
            ->assertSee('Ust. Rasyid Ashar')
            ->assertDontSee('KHATIB & IMAM');

        Carbon::setTestNow();
    }

    public function test_thursday_night_after_maghrib_shows_friday_khutbah(): void
    {
        // 24 September 2026 at 19:00 WIB (Thursday night)
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 19, 0, 0, 'Asia/Jakarta'));

        Livewire::test(PortalPage::class)
            ->assertSee('Petugas Jumat')
            ->assertSee('Jumat, 25 September 2026')
            ->assertSee('KHATIB & IMAM')
            ->assertSee('Ust. M. Yasak Lc MA')
            ->assertSee('MC / PROTOKOL')
            ->assertSee('Alan Irfansyah')
            ->assertSee('BILAL / MUADZIN')
            ->assertSee('Khudori');

        Carbon::setTestNow();
    }

    public function test_monday_afternoon_shows_petugas_ashar(): void
    {
        // 28 September 2026 at 15:00 WIB (Monday afternoon)
        Carbon::setTestNow(Carbon::create(2026, 9, 28, 15, 0, 0, 'Asia/Jakarta'));

        Livewire::test(PortalPage::class)
            ->assertSee('Petugas Ashar')
            ->assertSee('Senin, 28 September 2026')
            ->assertSee('SHOLAT DZUHUR')
            ->assertSee('SHOLAT ASHAR');

        Carbon::setTestNow();
    }

    public function test_monday_night_after_maghrib_shows_tuesday(): void
    {
        PrayerDuty::create([
            'day_name' => 'Selasa',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Selasa Dzuhur',
            'muadzin_name' => 'Bilal Selasa',
            'tahun' => 2026,
        ]);
        PrayerDuty::create([
            'day_name' => 'Selasa',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Selasa Ashar',
            'muadzin_name' => 'Bilal Selasa',
            'tahun' => 2026,
        ]);

        // 28 September 2026 at 19:00 WIB (Monday night)
        Carbon::setTestNow(Carbon::create(2026, 9, 28, 19, 0, 0, 'Asia/Jakarta'));

        Livewire::test(PortalPage::class)
            ->assertSee('Petugas Dhuhur')
            ->assertSee('Selasa, 29 September 2026')
            ->assertSee('SHOLAT DZUHUR')
            ->assertSee('Ust. Selasa Dzuhur')
            ->assertSee('SHOLAT ASHAR')
            ->assertSee('Ust. Selasa Ashar');

        Carbon::setTestNow();
    }
}

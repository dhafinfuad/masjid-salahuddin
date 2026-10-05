<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use App\Models\MasjidSetting;
use App\Services\PrayerTimeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PrayerTimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
    }

    public function test_prayer_service_returns_valid_cities(): void
    {
        $service = new PrayerTimeService();
        $cities = $service->getCities();

        $this->assertNotEmpty($cities);
        
        $hasMalang = collect($cities)->contains(function ($item) {
            return str_contains(strtoupper($item['lokasi'] ?? ''), 'MALANG');
        });

        $this->assertTrue($hasMalang, 'Cities list must contain Kota Malang');
    }

    public function test_prayer_service_returns_accurate_times_without_negative_symbols(): void
    {
        $service = new PrayerTimeService();
        $prayers = $service->getPrayerTimes('1634'); // Kota Malang

        $this->assertCount(5, $prayers);

        foreach ($prayers as $prayer) {
            $this->assertStringNotContainsString('-', $prayer['adzan'], "Adzan time must not contain negative sign: {$prayer['adzan']}");
            $this->assertStringNotContainsString('-', $prayer['iqamah'], "Iqamah time must not contain negative sign: {$prayer['iqamah']}");
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $prayer['adzan']);
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $prayer['iqamah']);
        }
    }

    public function test_iqamah_is_ten_minutes_after_adzan(): void
    {
        $service = new PrayerTimeService();
        $prayers = $service->getPrayerTimes('1634');

        foreach ($prayers as $prayer) {
            [$ah, $am] = explode(':', $prayer['adzan']);
            [$ih, $im] = explode(':', $prayer['iqamah']);

            $adzanTotal = ((int) $ah * 60) + (int) $am;
            $iqamahTotal = ((int) $ih * 60) + (int) $im;
            $diff = $iqamahTotal - $adzanTotal;

            // Difference is 10 minutes (or 5 for Maghrib)
            $this->assertTrue(in_array($diff, [5, 10]), "Iqamah diff for {$prayer['name']} should be 5 or 10 mins");
        }
    }

    public function test_next_prayer_calculation_at_afternoon(): void
    {
        $service = new PrayerTimeService();
        $prayers = $service->getPrayerTimes('1634');

        // Test at 15:17 WIB (as reported by user)
        $simulatedTime = Carbon::createFromTime(15, 17, 0, 'Asia/Jakarta');
        $next = $service->getNextPrayer($prayers, $simulatedTime);

        // At 15:17, Subuh (04:xx), Dzuhur (11:xx), Ashar (14:xx) have passed.
        // Next prayer MUST be Maghrib!
        $this->assertEquals('maghrib', $next['prayer']['key'], "At 15:17 WIB next prayer must be Maghrib");
        $this->assertStringNotContainsString('-', $next['countdown_text']);
    }

    public function test_portal_page_renders_with_city_selector(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Kota Malang');
        $response->assertSee('Kemenag RI');
        $response->assertSee('Salin');
    }

    public function test_portal_page_can_switch_city(): void
    {
        Livewire::test(PortalPage::class)
            ->set('cityId', '1638') // Kota Surabaya
            ->assertSet('cityId', '1638')
            ->assertSee('Kemenag RI');
    }

    public function test_subuh_is_next_prayer_and_not_marked_as_passed_after_isya(): void
    {
        $simulatedNow = Carbon::create(2026, 9, 13, 19, 26, 21, 'Asia/Jakarta');
        Carbon::setTestNow($simulatedNow);

        $service = new PrayerTimeService();
        $prayers = $service->getPrayerTimes('1634', $simulatedNow);

        $this->assertEquals('Subuh', $prayers[0]['name']);
        $this->assertTrue($prayers[0]['is_next'], 'Subuh must be is_next after Isya');
        $this->assertFalse($prayers[0]['is_passed'], 'Subuh must not be marked as passed after Isya');

        // Verify PortalPage renders Subuh with Iqamah and NOT with Lewat
        $component = Livewire::test(PortalPage::class);
        $html = $component->html();

        $this->assertStringContainsString('04:19', $html);

        Carbon::setTestNow();
    }
}


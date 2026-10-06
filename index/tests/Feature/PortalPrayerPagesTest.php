<?php

namespace Tests\Feature;

use App\Livewire\Portal\JadwalSholatPage;
use App\Livewire\Portal\PetugasSholatPage;
use App\Models\MasjidSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalPrayerPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
    }

    public function test_portal_home_renders_official_logo(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Logo Masjid Salahuddin.webp');
    }

    public function test_portal_home_renders_current_hijri_date_and_removes_assalamualaikum(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $expectedHijri = app(\App\Services\PrayerTimeService::class)->getHijriDayAndDate();
        $response->assertSee($expectedHijri);
        $response->assertDontSee('Assalamualaikum Jamaah');
    }

    public function test_jadwal_sholat_page_renders_successfully(): void
    {
        $response = $this->get(route('portal.jadwal-sholat'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Waktu Shalat Bulanan');
        $response->assertSee('wire:loading.inline-flex', false);
        $response->assertSee('Memperbarui jadwal...');
    }

    public function test_jadwal_sholat_city_filter_update_is_fast_and_accurate(): void
    {
        Livewire::test(JadwalSholatPage::class)
            ->set('cityId', '1619') // Kab. Pamekasan
            ->assertSet('cityName', 'Kab. Pamekasan')
            ->assertSee('Kab. Pamekasan')
            ->call('selectCity', '1638') // Kota Surabaya
            ->assertSet('cityId', '1638')
            ->assertSet('cityName', 'Kota Surabaya')
            ->assertSee('Kota Surabaya')
            ->set('selectedMonth', 9)
            ->set('selectedYear', 2026)
            ->assertSee('September 2026 — Kota Surabaya');
    }

    public function test_jadwal_sholat_places_today_at_top_row_for_current_month(): void
    {
        $today = \Carbon\Carbon::now('Asia/Jakarta');
        $todayDate = $today->format('Y-m-d');

        $component = Livewire::test(JadwalSholatPage::class)
            ->set('selectedMonth', (int) $today->month)
            ->set('selectedYear', (int) $today->year);

        $schedule = $component->viewData('schedule');
        $this->assertNotEmpty($schedule);
        $this->assertEquals($todayDate, $schedule[0]['date'], 'First row must be today date');
        $this->assertEquals((int) $today->day, $schedule[0]['day_number']);

        $component->assertDontSee('Hari Ini');
        $component->assertSee('Baris kuning paling atas menandakan waktu shalat hari ini.');
    }

    public function test_jadwal_sholat_keeps_chronological_order_for_other_months(): void
    {
        $today = \Carbon\Carbon::now('Asia/Jakarta');
        $otherMonth = ($today->month === 12) ? 1 : $today->month + 1;
        $otherYear = ($today->month === 12) ? $today->year + 1 : $today->year;

        $component = Livewire::test(JadwalSholatPage::class)
            ->set('selectedMonth', $otherMonth)
            ->set('selectedYear', $otherYear);

        $schedule = $component->viewData('schedule');
        $this->assertNotEmpty($schedule);
        $this->assertEquals(1, $schedule[0]['day_number'], 'Other months must start with day 1');
    }

    public function test_petugas_sholat_page_renders_with_horizontal_loading_indicator(): void
    {
        $response = $this->get(route('portal.petugas-sholat'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Imam');
        $response->assertSee('wire:loading.inline-flex', false);
        $response->assertSee('Memperbarui jadwal...');
    }

    public function test_petugas_sholat_month_filter_updates_roster(): void
    {
        Livewire::test(PetugasSholatPage::class)
            ->set('selectedMonth', 10)
            ->set('selectedYear', 2026)
            ->assertSee('Oktober 2026')
            ->assertSee('Dzuhur')
            ->assertSee('Libur')
            ->assertDontSee('Dzuhur / Shalat Jumat')
            ->assertDontSee('<th class="p-3.5 pr-4">Keterangan</th>', false);
    }

    public function test_petugas_sholat_places_today_at_top_row_for_current_month(): void
    {
        $today = \Carbon\Carbon::now('Asia/Jakarta');
        $todayDate = $today->format('Y-m-d');

        $component = Livewire::test(PetugasSholatPage::class)
            ->set('selectedMonth', (int) $today->month)
            ->set('selectedYear', (int) $today->year);

        $roster = $component->viewData('roster');
        $this->assertNotEmpty($roster);
        $this->assertEquals($todayDate, $roster[0]['date'], 'First row must be today date');
        $this->assertEquals((int) $today->day, $roster[0]['day_number']);
        $this->assertTrue($roster[0]['is_today']);

        $component->assertDontSee('Hari Ini');
        $component->assertSee('Baris kuning paling atas menandakan penugasan hari ini.');
    }

    public function test_portal_khutbah_jumat_has_teks_mc_button_and_is_publicly_printable(): void
    {
        $jumat = \App\Models\Kajian::create([
            'type' => 'jumat',
            'date' => now()->addDays(2),
            'time_display' => '11:45 WIB',
            'title' => 'Shalat Jumat Barakah',
            'khatib_name' => 'Ustadz Fulan, M.Ag',
            'muadzin_name' => 'Bilal Fulan',
            'mc_name' => 'MC Fulan',
        ]);

        // 1. Check portal home renders Teks MC button for jumat
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Teks MC');
        $response->assertSee(route('admin.kajian.teks-mc', $jumat->id));

        // 2. Check public access to printMcText route without authentication
        $printResponse = $this->get(route('admin.kajian.teks-mc', $jumat->id));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('<strong>Yth. Ustadz Fulan, M.Ag</strong>', false);
        $printResponse->assertSee('Bismillahirrohmanirrohim');
        $printResponse->assertSee('Kajian Pekanan, Bakda Sholat Ashar');
        $printResponse->assertSee('Jumat Berkah');
        $printResponse->assertDontSee('DEWAN KEMAKMURAN MASJID (DKM)');
        $printResponse->assertDontSee('TEKS PROTOKOL MC SHALAT JUMAT');
        $printResponse->assertDontSee('Mengetahui,');
        $printResponse->assertDontSee('Komplek Araya Business Center');
        $printResponse->assertDontSee('Dhafin Fuad Mahathir');
        $printResponse->assertDontSee('PANDUAN & NASKAH PROTOKOL MC SHALAT JUMAT');
        $printResponse->assertDontSee('Mode Pratinjau Cetak / Ekspor PDF');
    }

    public function test_portal_cards_vertical_order_matches_specification(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $html = $response->getContent();

        $c1Jadwal = strpos($html, '<!-- CARD 1: Jadwal Sholat -->');
        $c1Petugas = strpos($html, '<!-- CARD 1: Petugas Shalat -->');
        $c2Kegiatan = strpos($html, '<!-- CARD 2: Kegiatan Masjid -->');
        $c2Infaq = strpos($html, '<!-- CARD 2: Infaq & Sedekah Masjid -->');
        $c3Sosial = strpos($html, '<!-- CARD 3: Program Sosial & Komitmen Jamaah -->');
        $c3Profil = strpos($html, '<!-- CARD 3: Profil Masjid -->');
        $footer = strpos($html, '<!-- Footer -->');

        $this->assertNotFalse($c1Jadwal);
        $this->assertNotFalse($c1Petugas);
        $this->assertNotFalse($c2Kegiatan);
        $this->assertNotFalse($c2Infaq);
        $this->assertNotFalse($c3Sosial);
        $this->assertNotFalse($c3Profil);
        $this->assertNotFalse($footer);

        // Verify mobile order classes (order-1 through order-6) ensuring exact top-to-bottom stack on mobile
        $response->assertSee('order-1');
        $response->assertSee('order-2');
        $response->assertSee('order-3');
        $response->assertSee('order-4');
        $response->assertSee('order-5');
        $response->assertSee('order-6');

        // Verify balanced desktop column wrappers preventing vertical stretching/gaps
        $response->assertSee('lg:col-span-8');
        $response->assertSee('lg:col-span-4');

        $this->assertTrue($c3Profil < $footer, 'CARD 3: Profil Masjid must appear before Footer');
    }

    public function test_portal_renders_login_button_linking_to_auth_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee(route('login'));
        $response->assertSee('wire:navigate.hover', false);
        $response->assertSee('Login');
    }

    public function test_portal_renders_dashboard_button_with_mobile_and_desktop_views_when_authenticated(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee(route('admin.dashboard'));
        $response->assertSee('title="Buka Panel Dashboard Admin"', false);
        // Mobile view button: icon-only text-gov-navy
        $response->assertSee('class="sm:hidden px-4 py-2 rounded-lg text-gov-navy transition cursor-pointer inline-flex items-center justify-center gap-2"', false);
        $response->assertSee('data-lucide="layout-dashboard"', false);
        // Desktop view button: solid navy button with text
        $response->assertSee('class="hidden sm:inline-flex px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs sm:text-sm shadow-2xs transition cursor-pointer items-center justify-center gap-2"', false);
        $response->assertSee('Dashboard');
    }

    public function test_portal_kegiatan_highlights_next_kajian(): void
    {
        $pastKajian = \App\Models\Kajian::create([
            'type' => 'pekanan',
            'date' => now()->subDays(10),
            'time_display' => 'Bakda Maghrib',
            'title' => 'Kajian Masa Lalu Yang Telah Selesai',
            'speaker_name' => 'Ustadz Lama',
        ]);

        $nextKajian = \App\Models\Kajian::create([
            'type' => 'pekanan',
            'date' => now()->addDays(2),
            'time_display' => 'Bakda Maghrib',
            'title' => 'Kajian Mendatang Selanjutnya Yang Spesial',
            'speaker_name' => 'Ustadz Baru',
        ]);

        $response = $this->get(route('portal.kegiatan', [
            'selectedMonth' => 'all',
            'selectedYear' => now()->year,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Kajian Mendatang Selanjutnya Yang Spesial');
        $response->assertSee('border-l-4 border-l-amber-500');
        $response->assertSee('Kartu kuning menandakan jadwal kajian selanjutnya atau kegiatan pada pekan berjalan.');
        $response->assertDontSee('Hari Ini');
    }

    public function test_portal_home_duty_card_renders_dynamic_prayer_times_for_dzuhur_and_ashar(): void
    {
        $prayerService = app(\App\Services\PrayerTimeService::class);
        $prayers = $prayerService->getPrayerTimes();
        $dzuhur = collect($prayers)->firstWhere('key', 'dzuhur')['adzan'] ?? '11:45';
        $ashar = collect($prayers)->firstWhere('key', 'ashar')['adzan'] ?? '15:00';

        $component = Livewire::test(\App\Livewire\Portal\PortalPage::class);
        $component->assertViewHas('dzuhurPrayerTime', $dzuhur)
            ->assertViewHas('asharPrayerTime', $ashar);

        if (!now()->isFriday()) {
            $component->assertSee('Petugas Sholat')
                ->assertDontSee('Petugas Ashar')
                ->assertDontSee('Petugas Dzuhur')
                ->assertDontSee('Petugas Dhuhur')
                ->assertSee($dzuhur . ' WIB')
                ->assertSee($ashar . ' WIB');
        }
    }
}




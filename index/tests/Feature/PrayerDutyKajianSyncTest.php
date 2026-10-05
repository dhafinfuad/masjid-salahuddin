<?php

namespace Tests\Feature;

use App\Http\Controllers\PrayerDutyExportController;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Portal\PetugasSholatPage;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Tests\TestCase;

class PrayerDutyKajianSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::create([
            'name' => 'Masjid Salahuddin',
            'friday_prayer_info' => [
                'khatib' => 'Ust. M. Yasak Lc MA',
                'mc' => 'Alan Irfansyah',
                'muadzin' => 'Khodori',
            ],
        ]);
    }

    public function test_monday_5_oct_does_not_pull_alvin_shohih_and_wednesday_7_oct_syncs_muhammad_yahya(): void
    {
        // 1. Setup Prayer Duties:
        // Senin Ashar: regular rawatib
        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Lukman Hakim Imam',
            'muadzin_name' => 'Lukman Hakim',
            'tahun' => 2026,
        ]);

        // Rabu Ashar: duty officer has Syaiful Muslimin
        PrayerDuty::create([
            'day_name' => 'Rabu',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Syaiful Muslimin',
            'muadzin_name' => 'Bilal Rabu',
            'tahun' => 2026,
        ]);

        // 2. Setup Kajians:
        // Wednesday 7 Oct 2026: Tematik Kajian with speaker Muhammad Yahya Ph.D.
        Kajian::create([
            'type' => 'tematik',
            'title' => 'Tadabbur Al-Quran Tematik',
            'speaker_name' => 'Muhammad Yahya Ph.D.',
            'date' => '2026-10-07',
            'time_display' => '15:30 WIB',
        ]);

        // Monday 12 Oct 2026: Pekanan Kajian with speaker Alvin Shohih
        Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Rutin Pekanan',
            'speaker_name' => 'Alvin Shohih',
            'date' => '2026-10-12',
            'time_display' => '15:30 WIB',
        ]);

        // ==========================================
        // Verification A: Monthly PDF Export (/petugas-sholat/cetak)
        // ==========================================
        $controller = new PrayerDutyExportController();
        $request = new Request(['month' => 10, 'year' => 2026]);
        $response = $controller->printMonthlyPdf($request);

        $roster = $response->getData()['roster'];
        $rosterByDate = collect($roster)->keyBy('date');

        // Check Monday 5 Oct 2026
        $this->assertTrue($rosterByDate->has('2026-10-05'));
        $monday5Oct = $rosterByDate->get('2026-10-05');
        $this->assertEquals('Lukman Hakim Imam', $monday5Oct['ashar_imam']);
        $this->assertNotEquals('Alvin Shohih', $monday5Oct['ashar_imam']);

        // Check Wednesday 7 Oct 2026
        $this->assertTrue($rosterByDate->has('2026-10-07'));
        $wed7Oct = $rosterByDate->get('2026-10-07');
        $this->assertEquals('Muhammad Yahya Ph.D.', $wed7Oct['ashar_imam']);
        $this->assertEquals('Bilal Rabu', $wed7Oct['ashar_muadzin']);
        $this->assertEquals('Tadabbur Al-Quran Tematik', $wed7Oct['ashar_kajian_title']);

        // Check Monday 12 Oct 2026
        $this->assertTrue($rosterByDate->has('2026-10-12'));
        $monday12Oct = $rosterByDate->get('2026-10-12');
        $this->assertEquals('Alvin Shohih', $monday12Oct['ashar_imam']);

        // ==========================================
        // Verification B: Portal Petugas Sholat Page (/petugas-sholat)
        // ==========================================
        Livewire::test(PetugasSholatPage::class)
            ->set('selectedMonth', 10)
            ->set('selectedYear', 2026)
            ->assertSee('Lukman Hakim Imam')
            ->assertSee('Muhammad Yahya Ph.D.')
            ->assertSee('Alvin Shohih')
            ->assertSee('Tadabbur Al-Quran Tematik');

        // ==========================================
        // Verification C: Admin Petugas Tab (/admin/petugas)
        // ==========================================
        $admin = User::factory()->create([
            'role' => 'Administrator',
            'status' => 'AKTIF',
        ]);

        $this->actingAs($admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->set('currentTab', 'petugas')
            ->set('prayerDutyFilterYear', '2026')
            ->set('prayerDutyFilterMonth', '10')
            ->set('prayerDutyFilterWeek', '2') // Week 2 of Oct 2026 includes 5 Oct and 7 Oct
            ->assertSee('Muhammad Yahya Ph.D.')
            ->assertSee('Lukman Hakim Imam')
            ->assertDontSee('Alvin Shohih'); // Alvin Shohih is on 12 Oct (Week 3), not week 2!

        // ==========================================
        // Verification D: Admin Petugas Print PDF (/admin/petugas/export-pdf)
        // ==========================================
        $printPdfRequest = new Request([
            'week' => '2',
            'month' => 10,
            'year' => 2026,
            'prayer_time' => 'ashar',
        ]);
        $printPdfView = $controller->printPdf($printPdfRequest);
        $renderedHtml = $printPdfView->render();

        $this->assertStringContainsString('Muhammad Yahya Ph.D.', $renderedHtml);
        $this->assertStringContainsString('Lukman Hakim Imam', $renderedHtml);
        $this->assertStringNotContainsString('Alvin Shohih', $renderedHtml);
    }

    public function test_excel_export_syncs_kajian_on_ashar_and_retains_regular_duty(): void
    {
        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Lukman Hakim Imam',
            'muadzin_name' => 'Lukman Hakim',
            'tahun' => 2026,
        ]);

        PrayerDuty::create([
            'day_name' => 'Rabu',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Syaiful Muslimin',
            'muadzin_name' => 'Bilal Rabu',
            'tahun' => 2026,
        ]);

        Kajian::create([
            'type' => 'tematik',
            'title' => 'Tadabbur Al-Quran Tematik',
            'speaker_name' => 'Muhammad Yahya Ph.D.',
            'date' => '2026-10-07',
            'time_display' => '15:30 WIB',
        ]);

        Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Rutin Pekanan',
            'speaker_name' => 'Alvin Shohih',
            'date' => '2026-10-12',
            'time_display' => '15:30 WIB',
        ]);

        $controller = new PrayerDutyExportController();
        $request = new Request([
            'week' => '2',
            'month' => 10,
            'year' => 2026,
            'prayer_time' => 'ashar',
        ]);

        $response = $controller->exportExcel($request);
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();

        $zip = new \ZipArchive();
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        file_put_contents($tmpFile, $content);
        $zip->open($tmpFile);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml') ?: '';
        $zip->close();
        @unlink($tmpFile);

        $xmlCombined = $sheetXml . ' ' . $sharedStringsXml;
        $this->assertStringContainsString('Muhammad Yahya Ph.D.', $xmlCombined);
        $this->assertStringContainsString('Lukman Hakim Imam', $xmlCombined);
        $this->assertStringNotContainsString('Alvin Shohih', $xmlCombined);
    }

    public function test_friday_dzuhur_syncs_with_khutbah_jumat_and_ashar_retains_duty(): void
    {
        PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Default Jumat Imam',
            'muadzin_name' => 'Default Jumat Muadzin',
            'tahun' => 2026,
        ]);

        PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Jumat Ashar',
            'muadzin_name' => 'Bilal Jumat Ashar',
            'tahun' => 2026,
        ]);

        Kajian::create([
            'type' => 'jumat',
            'title' => 'Khutbah Jumat Berkah',
            'khatib_name' => 'Ust. Khotib Spesial',
            'muadzin_name' => 'Muadzin Jumat Spesial',
            'mc_name' => 'MC Jumat Spesial',
            'date' => '2026-10-02',
        ]);

        $controller = new PrayerDutyExportController();
        $request = new Request(['month' => 10, 'year' => 2026]);
        $response = $controller->printMonthlyPdf($request);

        $roster = $response->getData()['roster'];
        $rosterByDate = collect($roster)->keyBy('date');

        $this->assertTrue($rosterByDate->has('2026-10-02'));
        $fridayRow = $rosterByDate->get('2026-10-02');

        $this->assertEquals('Ust. Khotib Spesial', $fridayRow['dzuhur_imam']);
        $this->assertEquals('Muadzin Jumat Spesial', $fridayRow['dzuhur_muadzin']);
        $this->assertEquals('MC Jumat Spesial', $fridayRow['friday_mc']);
        $this->assertEquals('Ust. Jumat Ashar', $fridayRow['ashar_imam']);
        $this->assertEquals('Bilal Jumat Ashar', $fridayRow['ashar_muadzin']);
    }

    public function test_admin_petugas_date_sorting_and_pdf_export_default_chronological_order(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_sort@masjid.id',
            'password' => bcrypt('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);
        $this->actingAs($admin);

        // Setup duties with week 5, week 1, week 2
        // Mon Pekan 5 (later date: 2026-10-26)
        $dutyPekan5 = PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_5',
            'imam_name' => 'Adim Kadimuloh',
            'muadzin_name' => 'Bilal Lima',
            'tahun' => 2026,
        ]);

        // Mon Pekan 1 (earlier date: 2026-09-28 or 2026-10-05 depending on calendar)
        $dutyPekan1 = PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_1',
            'imam_name' => 'Pak Dhanny',
            'muadzin_name' => 'Bilal Satu',
            'tahun' => 2026,
        ]);

        // Tue Pekan 2 (middle date)
        $dutyPekan2 = PrayerDuty::create([
            'day_name' => 'Selasa',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_2',
            'imam_name' => 'Ust. Kedua',
            'muadzin_name' => 'Bilal Dua',
            'tahun' => 2026,
        ]);

        // 1. Livewire Admin Dashboard: Test Sorting by Date (Semua Pekan)
        $component = Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->set('prayerDutyFilterYear', '2026')
            ->set('prayerDutyFilterMonth', '10')
            ->set('prayerDutyFilterWeek', 'all')
            ->call('sortBy', 'date', 'petugas');

        $dutiesAsc = $component->viewData('allPrayerDuties');
        $this->assertNotEmpty($dutiesAsc);
        $datesAsc = collect($dutiesAsc->items())->map(fn($d) => $d->resolved_date?->format('Y-m-d'))->filter()->values();
        $this->assertTrue($datesAsc->count() >= 2);
        // Dates must be in ascending order (earliest to latest)
        for ($i = 0; $i < $datesAsc->count() - 1; $i++) {
            $this->assertLessThanOrEqual($datesAsc[$i + 1], $datesAsc[$i]);
        }

        // Test Sorting by Date Descending
        $component->call('sortBy', 'date', 'petugas');
        $dutiesDesc = $component->viewData('allPrayerDuties');
        $datesDesc = collect($dutiesDesc->items())->map(fn($d) => $d->resolved_date?->format('Y-m-d'))->filter()->values();
        // Dates must be in descending order (latest to earliest)
        for ($i = 0; $i < $datesDesc->count() - 1; $i++) {
            $this->assertGreaterThanOrEqual($datesDesc[$i + 1], $datesDesc[$i]);
        }

        // 2. Export Cetak Jadwal (PDF): verify default order is earliest to latest date (terlama ke terbaru)
        $pdfResponse = $this->get('/admin/petugas/export-pdf?week=all&month=10&year=2026');
        $pdfResponse->assertStatus(200);
        $exportDuties = $pdfResponse->original->getData()['duties'];
        $this->assertNotEmpty($exportDuties);

        $exportDates = collect($exportDuties)->map(fn($d) => $d->resolved_date?->format('Y-m-d'))->filter()->values();
        $this->assertTrue($exportDates->count() >= 2);
        for ($i = 0; $i < $exportDates->count() - 1; $i++) {
            $this->assertLessThanOrEqual($exportDates[$i + 1], $exportDates[$i]);
        }
    }
}

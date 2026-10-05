<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PrayerDutyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->admin = User::factory()->create([
            'name' => 'Ustadz Admin DKM',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    public function test_authenticated_admin_can_access_petugas_tab(): void
    {
        $this->actingAs($this->admin);

        $duty = PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Pujo Santoso',
            'muadzin_name' => 'Mas Zulhaq',
        ]);

        $response = $this->get('/admin/petugas');
        $response->assertStatus(200);
        $response->assertSee('Jadwal Penugasan Petugas Ibadah');
        $response->assertSee('Ust. Pujo Santoso');
        $response->assertSee('Mas Zulhaq');
    }

    public function test_admin_can_create_prayer_duty_via_livewire(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class)
            ->call('openCreatePrayerDuty', 'Selasa', 'ashar')
            ->assertSet('dutyDayName', 'Selasa')
            ->assertSet('dutyPrayerTime', 'ashar')
            ->set('dutyWeekPattern', 'pekan_1_3_5')
            ->set('dutyImamName', 'Ust. Hilman Pratama')
            ->set('dutyMuadzinName', 'Ahmad Danang')
            ->call('savePrayerDuty')
            ->assertDispatched('close-duty-modal');

        $this->assertDatabaseHas('prayer_duties', [
            'day_name' => 'Selasa',
            'prayer_time' => 'ashar',
            'week_pattern' => 'pekan_1_3_5',
            'imam_name' => 'Ust. Hilman Pratama',
            'muadzin_name' => 'Ahmad Danang',
        ]);
    }

    public function test_admin_can_edit_and_delete_prayer_duty(): void
    {
        $this->actingAs($this->admin);

        $duty = PrayerDuty::create([
            'day_name' => 'Rabu',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Lama',
            'muadzin_name' => 'Muadzin Lama',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('openEditPrayerDuty', $duty->id)
            ->assertSet('editingPrayerDutyId', $duty->id)
            ->assertSet('dutyImamName', 'Ust. Lama')
            ->set('dutyImamName', 'Ust. Baru Diperbarui')
            ->call('savePrayerDuty');

        $this->assertDatabaseHas('prayer_duties', [
            'id' => $duty->id,
            'imam_name' => 'Ust. Baru Diperbarui',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('deletePrayerDuty', $duty->id);

        $this->assertDatabaseMissing('prayer_duties', [
            'id' => $duty->id,
        ]);
    }

    public function test_friday_dzuhur_synchronizes_with_kajian_jumat(): void
    {
        $this->actingAs($this->admin);

        // Create Friday Kajian with Khatib and Muadzin
        $now = Carbon::now();
        $upcomingFriday = $now->copy()->next(Carbon::FRIDAY)->format('Y-m-d');

        Kajian::create([
            'type' => 'jumat',
            'date' => $upcomingFriday,
            'time_display' => '11:45 - 12:45',
            'title' => 'Khutbah Pentingnya Menjaga Amanah',
            'khatib_name' => 'Prof. Dr. KH. Nasaruddin Umar',
            'muadzin_name' => 'Ustadz Bilal Ramadhan',
        ]);

        PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Petugas Cadangan',
            'muadzin_name' => 'Muadzin Cadangan',
        ]);

        $response = $this->get('/admin/petugas');
        $response->assertStatus(200);
        $response->assertSee('Prof. Dr. KH. Nasaruddin Umar');
        $response->assertSee('Ustadz Bilal Ramadhan');
        $response->assertSee('Tersinkron Khatib Shalat Jumat');
    }

    public function test_export_endpoints_return_successful_response(): void
    {
        $this->actingAs($this->admin);

        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Pujo',
            'muadzin_name' => 'Zulhaq',
        ]);

        // Test Excel export (.xlsx with Text format)
        $excelResponse = $this->get(route('admin.petugas.export-excel'));
        $excelResponse->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $excelResponse->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $excelResponse->headers->get('content-disposition'));

        // Test PDF print view
        $pdfResponse = $this->get(route('admin.petugas.export-pdf'));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('JADWAL PENUGASAN IMAM', false);
        $pdfResponse->assertSee('Ust. Pujo');
    }

    public function test_petugas_tab_defaults_to_current_week_filter(): void
    {
        $this->actingAs($this->admin);

        $now = Carbon::now('Asia/Jakarta');
        $expectedWeek = (string) PrayerDuty::getWeekOfMonth($now);

        Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->assertSet('prayerDutyFilterWeek', $expectedWeek);
    }

    public function test_prayer_duty_week_of_month_matches_calendar_and_21_september_2026_is_week_4(): void
    {
        $this->actingAs($this->admin);

        // Grid kalender September 2026 (1 Sep = Selasa, w1 = 2):
        // Baris 1: 1 - 5 Sep -> Pekan 1
        $this->assertSame(1, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-01')));
        $this->assertSame(1, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-05')));

        // Baris 2: 6 - 12 Sep -> Pekan 2
        $this->assertSame(2, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-06')));
        $this->assertSame(2, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-12')));

        // Baris 3: 13 - 19 Sep -> Pekan 3
        $this->assertSame(3, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-13')));
        $this->assertSame(3, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-19')));

        // Baris 4: 20 - 26 Sep -> Pekan 4 (21 September 2026 = Pekan 4!)
        $this->assertSame(4, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-20')));
        $this->assertSame(4, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-21')));
        $this->assertSame(4, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-22')));
        $this->assertSame(4, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-25')));
        $this->assertSame(4, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-26')));

        // Baris 5: 27 - 30 Sep -> Pekan 5
        $this->assertSame(5, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-27')));
        $this->assertSame(5, PrayerDuty::getWeekOfMonth(Carbon::parse('2026-09-30')));

        // Time travel to Monday, September 21, 2026
        Carbon::setTestNow(Carbon::parse('2026-09-21 10:00:00', 'Asia/Jakarta'));

        // AdminDashboard tab petugas must default to week 4
        Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->assertSet('prayerDutyFilterWeek', '4')
            ->assertSee('Pekan 4 ⭐ (Hari Ini)');

        Carbon::setTestNow(); // Reset time travel
    }

    public function test_prayer_duty_tahun_column_and_year_filter_on_admin_petugas(): void
    {
        $this->actingAs($this->admin);

        Carbon::setTestNow(Carbon::parse('2026-09-21 10:00:00', 'Asia/Jakarta'));

        // 1. Create duty for 2026 and duty for 2025
        $duty2026 = PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_4',
            'tahun' => 2026,
            'imam_name' => 'Ust. Imam 2026',
            'muadzin_name' => 'Bilal 2026',
        ]);

        $duty2025 = PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_4',
            'tahun' => 2025,
            'imam_name' => 'Ust. Imam 2025',
            'muadzin_name' => 'Bilal 2025',
        ]);

        // 2. Tab petugas should default prayerDutyFilterYear to current year (2026)
        $component = Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->assertSet('prayerDutyFilterYear', '2026')
            ->assertSee('TAHUN:')
            ->assertSee('Ust. Imam 2026')
            ->assertDontSee('Ust. Imam 2025');

        // 3. Switch year filter to 2025
        $component->set('prayerDutyFilterYear', '2025')
            ->assertSee('Ust. Imam 2025')
            ->assertDontSee('Ust. Imam 2026');

        // 4. Switch year filter to 'all'
        $component->set('prayerDutyFilterYear', 'all')
            ->assertSee('Ust. Imam 2026')
            ->assertSee('Ust. Imam 2025');

        // 5. Admin can create duty with tahun
        $component->call('openCreatePrayerDuty', 'Rabu', 'ashar')
            ->set('dutyWeekPattern', 'semua')
            ->set('dutyTahun', 2027)
            ->set('dutyImamName', 'Ust. Imam 2027')
            ->call('savePrayerDuty')
            ->assertDispatched('close-duty-modal');

        $this->assertDatabaseHas('prayer_duties', [
            'imam_name' => 'Ust. Imam 2027',
            'tahun' => 2027,
        ]);

        // 6. Test export endpoints accept year parameter
        $excelResponse = $this->get(route('admin.petugas.export-excel', ['year' => 2026]));
        $excelResponse->assertStatus(200);

        Carbon::setTestNow();
    }

    public function test_ustaz_kajian_synchronizes_with_speaker_name_from_kajians_table(): void
    {
        $this->actingAs($this->admin);

        // Fix date to Monday, September 21, 2026 (Pekan 4)
        $testDate = Carbon::parse('2026-09-21 10:00:00', 'Asia/Jakarta');
        Carbon::setTestNow($testDate);

        // 1. Create a Kajian Pekanan with specific speaker_name
        Kajian::create([
            'type' => 'pekanan',
            'date' => '2026-09-24', // Kamis Pekan 4
            'time_display' => '09:00 - 11:30',
            'title' => 'Kajian Riyadush Shalihin: Bab Ikhlas',
            'speaker_name' => 'Ustadz Hilman Fauzi, Lc.',
        ]);

        // 2. Create Prayer Duty with imam_name = 'Ustaz Kajian'
        PrayerDuty::create([
            'day_name' => 'Kamis',
            'prayer_time' => 'ashar',
            'week_pattern' => 'pekan_4',
            'tahun' => 2026,
            'imam_name' => 'Ustaz Kajian',
            'muadzin_name' => 'Ahmad Danang',
        ]);

        // 3. Test Dashboard tab replaces 'Ustaz Kajian' with 'Ustadz Hilman Fauzi, Lc.'
        $dashResponse = $this->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Ustadz Hilman Fauzi, Lc.');
        $dashResponse->assertSee('Kajian Pekanan');

        // 4. Test Petugas tab replaces 'Ustaz Kajian' with 'Ustadz Hilman Fauzi, Lc.'
        $petugasResponse = $this->get('/admin/petugas');
        $petugasResponse->assertStatus(200);
        $petugasResponse->assertSee('Ustadz Hilman Fauzi, Lc.');

        // 5. Test Export Excel (.xlsx) replaces 'Ustaz Kajian' with 'Ustadz Hilman Fauzi, Lc.'
        $exportExcel = $this->get(route('admin.petugas.export-excel'));
        $exportExcel->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $exportExcel->headers->get('content-type'));

        $tempXlsx = tempnam(sys_get_temp_dir(), 'test_exp_') . '.xlsx';
        file_put_contents($tempXlsx, $exportExcel->getContent());
        $parsedRows = \App\Services\SimpleXlsxService::parseFile($tempXlsx);
        @unlink($tempXlsx);

        $foundSynchronized = false;
        foreach ($parsedRows as $row) {
            if (in_array('Ustadz Hilman Fauzi, Lc.', $row)) {
                $foundSynchronized = true;
                break;
            }
        }
        $this->assertTrue($foundSynchronized, 'Exported .xlsx must contain synchronized Ustaz Kajian speaker name.');

        // 6. Test Export PDF replaces 'Ustaz Kajian' with 'Ustadz Hilman Fauzi, Lc.'
        $exportPdf = $this->get(route('admin.petugas.export-pdf'));
        $exportPdf->assertStatus(200);
        $exportPdf->assertSee('Ustadz Hilman Fauzi, Lc.');

        Carbon::setTestNow();
    }

    public function test_export_and_reimport_prayer_duty_xlsx(): void
    {
        $this->actingAs($this->admin);

        // 1. Verify UI button says "Download Excel (.xlsx)", modal tab says "Upload File Excel", no drag & drop text, and no notes field in modal
        $page = $this->get('/admin/petugas');
        $page->assertStatus(200);
        $page->assertSee('Download Excel (.xlsx)');
        $page->assertDontSee('Download Excel (.csv)');
        $page->assertSee('Upload File Excel');
        $page->assertDontSee('tarik file ke area ini');
        $page->assertDontSee('Keterangan / Catatan (Opsional)');

        // 2. Create initial prayer duties including Jumat Dzuhur and Jumat Ashar
        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'tahun' => 2026,
            'imam_name' => 'Ust. Awal',
            'muadzin_name' => 'Muadzin Awal',
        ]);

        PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'tahun' => 2026,
            'imam_name' => 'Ust. Khatib Khutbah Jumat',
            'muadzin_name' => 'Bilal Jumat',
        ]);

        PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'tahun' => 2026,
            'imam_name' => 'Ust. Ashar Jumat',
            'muadzin_name' => 'Muadzin Ashar Jumat',
        ]);

        $fridayKajian = Kajian::create([
            'type' => 'jumat',
            'title' => 'Khutbah Jumat Pekan Ini',
            'date' => Carbon::now('Asia/Jakarta')->next(Carbon::FRIDAY)->format('Y-m-d'),
            'khatib_name' => 'Ust. M. Yasak Lc MA',
            'muadzin_name' => 'Khudori',
            'mc_name' => 'Alan Irfansyah',
        ]);

        // Verify that on /admin/petugas, the edit button for Jumat Dzuhur calls openFridayKajianFromPetugas
        $pageWithDuties = $this->get('/admin/petugas');
        $pageWithDuties->assertStatus(200);
        $pageWithDuties->assertSee("openFridayKajianFromPetugas", false);

        // Test Livewire calling openFridayKajianFromPetugas switches tab & subtab and dispatches modal open event
        Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->call('openFridayKajianFromPetugas', $fridayKajian->id)
            ->assertSet('currentTab', 'kegiatan')
            ->assertSet('kegiatanSubTab', 'jumat')
            ->assertDispatched('open-edit-kajian-modal');

        // 3. Export to .xlsx
        $exportRes = $this->get(route('admin.petugas.export-excel', ['year' => 2026]));
        $exportRes->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $exportRes->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $exportRes->headers->get('content-disposition'));

        $tempFile = tempnam(sys_get_temp_dir(), 'exp_duty_') . '.xlsx';
        file_put_contents($tempFile, $exportRes->getContent());

        // 4. Verify exported rows structure & text formatting, and verify Jumat Dzuhur and notes are excluded!
        $rows = \App\Services\SimpleXlsxService::parseFile($tempFile);
        $this->assertNotEmpty($rows);
        $this->assertSame(['No', 'Hari', 'Tanggal', 'Waktu Shalat', 'Petugas Imam', 'Petugas Muadzin', 'Pola Pekan'], $rows[0]);

        // Assert Jumat Dzuhur is excluded, while Jumat Ashar is included
        $hasJumatDzuhur = false;
        $hasJumatAshar = false;
        foreach ($rows as $r) {
            $waktuShalat = strtolower($r[3] ?? '');
            if (($r[1] ?? '') === 'Jumat' && $waktuShalat === 'dzuhur') {
                $hasJumatDzuhur = true;
            }
            if (($r[1] ?? '') === 'Jumat' && $waktuShalat === 'ashar') {
                $hasJumatAshar = true;
            }
        }
        $this->assertFalse($hasJumatDzuhur, 'Jumat Dzuhur must be excluded from Excel export.');
        $this->assertTrue($hasJumatAshar, 'Jumat Ashar must be included in Excel export.');

        // 5. Edit the exported data (simulating user editing Excel)
        $editedHeaders = $rows[0];
        $editedRows = [
            ['1', 'Senin', '-', 'Dzuhur', 'Ust. Hasil Edit Excel', 'Muadzin Hasil Edit', 'semua'],
            ['2', 'Selasa', '-', 'Ashar', 'Ust. Baru Tambahan', 'Muadzin Baru', 'pekan_1_3_5'],
        ];
        $editedXlsxContent = \App\Services\SimpleXlsxService::createXlsx($editedHeaders, $editedRows);
        @unlink($tempFile);

        // 6. Test re-importing the edited .xlsx via file upload
        $fakeUpload = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal_reimport.xlsx', $editedXlsxContent);

        Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->set('prayerDutyFilterYear', '2026')
            ->set('importDutyFile', $fakeUpload)
            ->call('processImportDutyFile')
            ->assertDispatched('close-import-duty-modal');

        $this->assertDatabaseHas('prayer_duties', [
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'tahun' => 2026,
            'imam_name' => 'Ust. Hasil Edit Excel',
            'muadzin_name' => 'Muadzin Hasil Edit',
        ]);

        $this->assertDatabaseHas('prayer_duties', [
            'day_name' => 'Selasa',
            'prayer_time' => 'ashar',
            'week_pattern' => 'pekan_1_3_5',
            'tahun' => 2026,
            'imam_name' => 'Ust. Baru Tambahan',
            'muadzin_name' => 'Muadzin Baru',
        ]);

        // 7. Test re-importing via Copypaste Excel mechanism
        $pastedText = "Rabu\tDzuhur\tUst. Dari Paste Excel\tMuadzin Paste\tpekan_2_4";

        Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->set('prayerDutyFilterYear', '2026')
            ->set('importDutyPasteText', $pastedText)
            ->call('processImportDutyPaste')
            ->assertDispatched('close-import-duty-modal');

        $this->assertDatabaseHas('prayer_duties', [
            'day_name' => 'Rabu',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_2_4',
            'tahun' => 2026,
            'imam_name' => 'Ust. Dari Paste Excel',
            'muadzin_name' => 'Muadzin Paste',
        ]);
    }

    public function test_admin_can_search_prayer_duties_without_query_exception(): void
    {
        $this->actingAs($this->admin);

        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'tahun' => 2026,
            'imam_name' => 'Ust. Pujo Santoso',
            'muadzin_name' => 'Mas Bilal',
        ]);

        PrayerDuty::create([
            'day_name' => 'Selasa',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'tahun' => 2026,
            'imam_name' => 'Ust. Ahmad Fauzi',
            'muadzin_name' => 'Mas Danang',
        ]);

        $component = Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->set('search', 'pujo');

        $duties = $component->viewData('allPrayerDuties');
        $this->assertCount(1, $duties);
        $this->assertEquals('Ust. Pujo Santoso', $duties->first()->imam_name);

        $component->set('search', 'Fauzi');
        $dutiesFauzi = $component->viewData('allPrayerDuties');
        $this->assertCount(1, $dutiesFauzi);
        $this->assertEquals('Ust. Ahmad Fauzi', $dutiesFauzi->first()->imam_name);

        $component->set('search', 'Senin');
        $dutiesSenin = $component->viewData('allPrayerDuties');
        $this->assertCount(1, $dutiesSenin);
        $this->assertEquals('Ust. Pujo Santoso', $dutiesSenin->first()->imam_name);
    }

    public function test_export_pdf_displays_concrete_dates_for_kamis_and_jumat_crossing_into_next_month(): void
    {
        $this->actingAs($this->admin);

        // Buat penugasan Pekan 5 untuk Kamis dan Jumat tahun 2026
        $kamisDuty = PrayerDuty::create([
            'day_name' => 'Kamis',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_5',
            'tahun' => 2026,
            'imam_name' => 'Dharma Setiawan',
            'muadzin_name' => 'Ugik Endrar Viana',
        ]);

        $jumatDuty = PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'ashar',
            'week_pattern' => 'pekan_5',
            'tahun' => 2026,
            'imam_name' => 'Hadi Purnomo',
            'muadzin_name' => 'Khudori',
        ]);

        // Verifikasi resolveDate untuk Kamis & Jumat di Pekan 5 September 2026
        // Karena September 2026 berakhir di Rabu 30 Sep, Kamis & Jumat jatuh pada 1 dan 2 Oktober 2026
        $kamisDate = $kamisDuty->resolveDate(2026, 9, 5);
        $this->assertNotNull($kamisDate);
        $this->assertSame('2026-10-01', $kamisDate->format('Y-m-d'));

        $jumatDate = $jumatDuty->resolveDate(2026, 9, 5);
        $this->assertNotNull($jumatDate);
        $this->assertSame('2026-10-02', $jumatDate->format('Y-m-d'));

        // Akses route export PDF untuk Pekan 5 September 2026
        $response = $this->get(route('admin.petugas.export-pdf', [
            'week' => 5,
            'month' => 9,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('01 Okt 2026');
        $response->assertSee('02 Okt 2026');
        $response->assertSee('font-semibold text-slate-900', false);
    }

    public function test_export_pdf_displays_friday_dzuhur_with_khotib_muadzin_mc_labels(): void
    {
        $this->actingAs($this->admin);

        // Buat penugasan Jumat Dzuhur
        PrayerDuty::create([
            'day_name' => 'Jumat',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'pekan_1',
            'tahun' => 2026,
            'imam_name' => 'Khotib Jumat',
            'muadzin_name' => 'Muadzin Jumat',
        ]);

        // Buat Kajian Jumat untuk Pekan 1 (2 Oktober 2026)
        \App\Models\Kajian::create([
            'title' => 'Khutbah Jumat',
            'type' => 'jumat',
            'date' => '2026-10-02',
            'khatib_name' => 'Ust. Rois Imron Rosi M.Pd.',
            'muadzin_name' => 'Mochammad Dzulfikri Yul Zamzami',
            'mc_name' => 'Mochammad Dzulfikri Yul Zamzami',
        ]);

        $response = $this->get(route('admin.petugas.export-pdf', [
            'week' => 5,
            'month' => 9,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Khotib :');
        $response->assertSee('Ust. Rois Imron Rosi M.Pd.');
        $response->assertSee('Muadzin :');
        $response->assertSee('MC :');
        $response->assertDontSee('✓ Khatib Jumat');
        $response->assertDontSee('✓ Bilal Jumat');
    }
}



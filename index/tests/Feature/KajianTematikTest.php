<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianTematikTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->adminUser = User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'master@masjidsalahuddin.id',
            'role' => 'Master',
        ]);
    }

    /** @test */
    public function test_admin_can_create_kajian_tematik()
    {
        $this->actingAs($this->adminUser);

        $payload = [
            'id' => null,
            'type' => 'tematik',
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'time_display' => '09:00 - 11:30',
            'title' => 'Peringatan Isra Miraj 1448 H',
            'speaker_name' => 'Prof. Dr. KH. Nasaruddin Umar',
            'speaker_phone' => '081234567890',
        ];

        Livewire::test(AdminDashboard::class)
            ->call('saveKajian', $payload)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kajians', [
            'type' => 'tematik',
            'title' => 'Peringatan Isra Miraj 1448 H',
            'speaker_name' => 'Prof. Dr. KH. Nasaruddin Umar',
        ]);
    }

    /** @test */
    public function test_kajian_tematik_requires_speaker_name()
    {
        $this->actingAs($this->adminUser);

        $payload = [
            'id' => null,
            'type' => 'tematik',
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'time_display' => '09:00 - 11:30',
            'title' => 'Kajian Tahun Baru Hijriah',
            'speaker_name' => '', // kosong
        ];

        Livewire::test(AdminDashboard::class)
            ->call('saveKajian', $payload)
            ->assertHasErrors(['kajianSpeakerName']);
    }

    /** @test */
    public function test_kajian_tab_displays_both_pekanan_and_tematik_with_proper_badges()
    {
        $this->actingAs($this->adminUser);

        $pekanan = Kajian::create([
            'type' => 'pekanan',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time_display' => '09:00 - 11:30',
            'title' => 'Kajian Rutin Tafsir Al-Kahfi',
            'speaker_name' => 'Ustadz Firdaus',
        ]);

        $tematik = Kajian::create([
            'type' => 'tematik',
            'date' => Carbon::now()->addDays(4)->format('Y-m-d'),
            'time_display' => '19:30 - 21:00',
            'title' => 'Kajian Spesial Nuzulul Quran',
            'speaker_name' => 'Ustadz Hanan Attaki',
        ]);

        $response = $this->get('/admin/kegiatan');
        $response->assertOk();

        // Verifikasi nama tab di antarmuka
        $response->assertSee('Kajian');

        // Verifikasi kedua judul tampil
        $response->assertSee('Kajian Rutin Tafsir Al-Kahfi');
        $response->assertSee('Kajian Spesial Nuzulul Quran');

        // Verifikasi badge status jenis kajian sesuai UI_GUIDELINES
        $response->assertSee('Pekanan');
        $response->assertSee('Tematik');
        $response->assertSee('bg-blue-50 text-blue-700 border-blue-300', false);
        $response->assertSee('bg-amber-100 text-amber-800 border-amber-300', false);
    }

    /** @test */
    public function test_modal_pop_up_includes_kajian_tematik_button()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/kegiatan');
        $response->assertOk();

        // Opsi pilihan jenis jadwal baru di modal
        $response->assertSee('Kajian Tematik');
        $response->assertSee("kajianForm.type = 'tematik'", false);
    }

    /** @test */
    public function test_export_includes_both_pekanan_and_tematik()
    {
        $this->actingAs($this->adminUser);

        Kajian::create([
            'type' => 'pekanan',
            'date' => Carbon::now()->format('Y-m-d'),
            'time_display' => '09:00 - 11:30',
            'title' => 'Kajian Akhlak Terpuji',
            'speaker_name' => 'Ustadz Abdul',
        ]);

        Kajian::create([
            'type' => 'tematik',
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'time_display' => '08:30 - 10:30',
            'title' => 'Kajian Tahun Baru Islam 1448 H',
            'speaker_name' => 'Ustadz Somad',
        ]);

        $response = $this->get('/admin/kajian/export-excel?type=pekanan');
        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    /** @test */
    public function test_ui_displays_download_excel_xlsx_and_upload_file_excel()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/kegiatan');
        $response->assertOk();
        $response->assertSee('Download Excel (.xlsx)');
        $response->assertSee('Upload File Excel');
    }

    /** @test */
    public function test_export_and_reimport_kajian_pekanan_xlsx()
    {
        $this->actingAs($this->adminUser);

        $date1 = Carbon::now('Asia/Jakarta')->addDays(3)->format('Y-m-d');
        Kajian::create([
            'type' => 'pekanan',
            'date' => $date1,
            'time_display' => '09:00 - 11:30',
            'title' => 'Tafsir Surat Al-Baqarah',
            'speaker_name' => 'Ust. Awaluddin',
            'speaker_phone' => '081234567890',
        ]);

        // 1. Export Excel (.xlsx)
        $export = $this->get(route('admin.kajian.export-excel', ['type' => 'pekanan']));
        $export->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $export->headers->get('content-type'));

        $tempFile = tempnam(sys_get_temp_dir(), 'exp_kajian_') . '.xlsx';
        file_put_contents($tempFile, $export->getContent());

        // 2. Parse exported .xlsx
        $rows = \App\Services\SimpleXlsxService::parseFile($tempFile);
        $this->assertNotEmpty($rows);
        $this->assertSame(['No', 'Jenis', 'Hari & Tanggal', 'Waktu', 'Judul / Tema Kajian', 'Pembicara', 'No HP Pembicara'], $rows[0]);
        @unlink($tempFile);

        // 3. Edit exported data: update speaker of existing row and add a new row
        $editedHeaders = $rows[0];
        $date2 = Carbon::now('Asia/Jakarta')->addDays(10)->format('Y-m-d');
        $formattedDate2 = Carbon::parse($date2)->translatedFormat('l, d F Y');

        $editedRows = [
            // Row 1: update Ust. Awaluddin -> Ust. Hasil Edit Excel
            ['1', 'Pekanan', $rows[1][2], '09:00 - 11:30', 'Tafsir Surat Al-Baqarah', 'Ust. Hasil Edit Excel', '081299998888'],
            // Row 2: new Tematik Kajian
            ['2', 'Tematik', $formattedDate2, '13:30 - 15:30', 'Kajian Akbar Hijrah', 'Ust. Pembicara Baru', '081122334455'],
        ];

        $editedXlsx = \App\Services\SimpleXlsxService::createXlsx($editedHeaders, $editedRows, [], 'Jadwal Pekanan');
        $fakeUpload = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal_kajian_edit.xlsx', $editedXlsx);

        // 4. Re-import via Livewire processImportKajianFile
        Livewire::test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('importKajianType', 'pekanan')
            ->set('importKajianFile', $fakeUpload)
            ->call('processImportKajianFile')
            ->assertDispatched('close-import-kajian-modal');

        // Verify existing record was updated
        $this->assertDatabaseHas('kajians', [
            'type' => 'pekanan',
            'speaker_name' => 'Ust. Hasil Edit Excel',
            'speaker_phone' => '081299998888',
        ]);
        $this->assertTrue(Kajian::query()->whereDate('date', $date1)->where('speaker_name', 'Ust. Hasil Edit Excel')->exists());

        // Verify new record was created
        $this->assertDatabaseHas('kajians', [
            'type' => 'tematik',
            'title' => 'Kajian Akbar Hijrah',
            'speaker_name' => 'Ust. Pembicara Baru',
        ]);
        $this->assertTrue(Kajian::query()->whereDate('date', $date2)->where('speaker_name', 'Ust. Pembicara Baru')->exists());
    }

    /** @test */
    public function test_export_and_reimport_kajian_jumat_xlsx()
    {
        $this->actingAs($this->adminUser);

        $jumatDate = Carbon::now('Asia/Jakarta')->next(Carbon::FRIDAY)->format('Y-m-d');
        Kajian::create([
            'type' => 'jumat',
            'date' => $jumatDate,
            'time_display' => '11:45 - 12:45',
            'title' => 'Khutbah Pentingnya Menuntut Ilmu',
            'khatib_name' => 'Ust. Khatib Lama',
            'mc_name' => 'MC Lama',
            'muadzin_name' => 'Muadzin Lama',
            'khatib_phone' => '081234567890',
            'is_holiday_disabled' => false,
        ]);

        // 1. Export Excel (.xlsx)
        $export = $this->get(route('admin.kajian.export-excel', ['type' => 'jumat']));
        $export->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $export->headers->get('content-type'));

        $tempFile = tempnam(sys_get_temp_dir(), 'exp_jumat_') . '.xlsx';
        file_put_contents($tempFile, $export->getContent());

        // 2. Parse exported .xlsx
        $rows = \App\Services\SimpleXlsxService::parseFile($tempFile);
        $this->assertNotEmpty($rows);
        $this->assertSame(['No', 'Hari & Tanggal', 'Waktu', 'Status Libur', 'Judul / Tema Khutbah', 'Khatib', 'MC', 'Muadzin', 'No HP Khatib'], $rows[0]);
        @unlink($tempFile);

        // 3. Edit exported data: update Khatib, MC, Muadzin
        $editedHeaders = $rows[0];
        $editedRows = [
            ['1', $rows[1][1], '11:45 - 12:45', 'AKTIF', 'Khutbah Pentingnya Menuntut Ilmu', 'Ust. Khatib Terupdate', 'MC Baru', 'Muadzin Baru', '081299990000'],
        ];

        $editedXlsx = \App\Services\SimpleXlsxService::createXlsx($editedHeaders, $editedRows, [], 'Jadwal Jumat');
        $fakeUpload = \Illuminate\Http\UploadedFile::fake()->createWithContent('jadwal_jumat_edit.xlsx', $editedXlsx);

        // 4. Re-import via Livewire processImportKajianFile
        Livewire::test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('importKajianType', 'jumat')
            ->set('importKajianFile', $fakeUpload)
            ->call('processImportKajianFile')
            ->assertDispatched('close-import-kajian-modal');

        // Verify Friday schedule was updated
        $this->assertDatabaseHas('kajians', [
            'type' => 'jumat',
            'khatib_name' => 'Ust. Khatib Terupdate',
            'mc_name' => 'MC Baru',
            'muadzin_name' => 'Muadzin Baru',
        ]);
        $this->assertTrue(Kajian::query()->whereDate('date', $jumatDate)->where('khatib_name', 'Ust. Khatib Terupdate')->exists());
    }

    /** @test */
    public function test_paste_import_kajian_from_excel_text()
    {
        $this->actingAs($this->adminUser);

        $dateStr = Carbon::now('Asia/Jakarta')->addDays(7)->format('Y-m-d');
        $pastedText = "No\tJenis\tHari & Tanggal\tWaktu\tJudul / Tema Kajian\tPembicara\tNo HP Pembicara\n" .
                      "1\tPekanan\t{$dateStr}\t09:00 - 11:30\tAdab Menuntut Ilmu\tUst. Hamdan\t081345678901";

        Livewire::test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('importKajianType', 'pekanan')
            ->set('importKajianPasteText', $pastedText)
            ->call('processImportKajianPaste')
            ->assertDispatched('close-import-kajian-modal');

        $this->assertDatabaseHas('kajians', [
            'type' => 'pekanan',
            'title' => 'Adab Menuntut Ilmu',
            'speaker_name' => 'Ust. Hamdan',
        ]);
        $this->assertTrue(Kajian::query()->whereDate('date', $dateStr)->where('speaker_name', 'Ust. Hamdan')->exists());
    }

    /** @test */
    public function test_kajian_month_filter_smart_rollover_when_all_current_month_kajians_have_passed()
    {
        $this->actingAs($this->adminUser);

        // Simulasi hari ini: 29 September 2026 (semua kajian September telah selesai kemarin, 28 September)
        Carbon::setTestNow(Carbon::parse('2026-09-29 10:00:00', 'Asia/Jakarta'));

        // Buat kajian yang sudah lewat di bulan September 2026
        Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Sirah Nabawiyah',
            'date' => '2026-09-07',
            'speaker_name' => 'Ust. Fulan',
        ]);

        Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Tematik',
            'date' => '2026-09-28',
            'speaker_name' => 'Ust Hasyim Azhari SpdI',
        ]);

        // Karena tidak ada lagi jadwal di September pada/setelah 29 September,
        // filter otomatis berganti (smart rollover) ke bulan Oktober (10)
        Livewire::test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->assertSet('kajianMonthFilter', '10')
            ->assertSet('kajianYearFilter', '2026');

        // Uji jika hari ini masih ada jadwal yang akan datang (misal simulasi 20 September 2026)
        Carbon::setTestNow(Carbon::parse('2026-09-20 10:00:00', 'Asia/Jakarta'));

        // Karena masih ada jadwal 28 September (akan datang), filter tetap di September (9)
        Livewire::test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->assertSet('kajianMonthFilter', '9')
            ->assertSet('kajianYearFilter', '2026');

        Carbon::setTestNow(); // Reset time
    }
}


<?php

namespace Tests\Feature;

use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintExportViewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->adminUser = User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'master.print@masjidsalahuddin.id',
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);
    }

    public function test_kajian_print_pdf_view_renders_without_errors(): void
    {
        $this->actingAs($this->adminUser);

        // Test default query
        $response = $this->get('/admin/kajian/export-pdf');
        $response->assertStatus(200);
        $response->assertSee('Unduh PDF', false);
        $response->assertSee('Cetak / Simpan PDF', false);
        $response->assertSee('downloadKajianPdf', false);

        // Test with month and year queries (similar to exact error report)
        $responseFilter = $this->get('/admin/kajian/export-pdf?type=jumat&month=10&year=2026');
        $responseFilter->assertStatus(200);
        $responseFilter->assertSee('Unduh PDF', false);
        $responseFilter->assertSee('Cetak / Simpan PDF', false);
        $responseFilter->assertSee('downloadKajianPdf', false);
    }

    public function test_agenda_print_pdf_view_renders_without_errors(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/agenda/export-pdf');
        $response->assertStatus(200);
        $response->assertSee('Unduh PDF', false);
        $response->assertSee('Cetak / Simpan PDF', false);
        $response->assertSee('downloadAgendaSchedulePdf', false);
    }

    public function test_prayer_duty_print_pdf_views_render_without_errors(): void
    {
        $this->actingAs($this->adminUser);

        $responseRoster = $this->get('/admin/petugas/export-pdf');
        $responseRoster->assertStatus(200);
        $responseRoster->assertSee('Unduh PDF', false);
        $responseRoster->assertSee('downloadRosterPdf', false);

        $responseMonthly = $this->get('/petugas-sholat/cetak?month=10&year=2026');
        $responseMonthly->assertStatus(200);
        $responseMonthly->assertSee('Unduh PDF', false);
        $responseMonthly->assertSee('downloadMonthlyPdf', false);
    }

    public function test_takmir_official_sk_print_view_renders_without_errors(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/kepengurusan/cetak-sk');
        $response->assertStatus(200);
        $response->assertSee('Unduh PDF', false);
        $response->assertSee('downloadOfficialSkPdf', false);
    }

    public function test_takmir_official_sk_direct_pdf_download(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/kepengurusan/unduh-sk-pdf');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('Dokumen_Resmi_SK_Takmir_2026-2029.pdf', str_replace('"', '', $response->headers->get('content-disposition')));

        // Verify underlying view data contains QR code and Kemenkeu Kop
        $controller = app(\App\Http\Controllers\TakmirDocController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareOfficialSkData');
        $method->setAccessible(true);
        $data = $method->invoke($controller, request());

        $this->assertNotEmpty($data['qrCodeBase64']);
        $this->assertStringStartsWith('data:image/png;base64,', $data['qrCodeBase64']);

        $viewHtml = view('pdf.sk-takmir-official-pdf', $data)->render();
        $this->assertStringContainsString('KEPUTUSAN KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG', $viewHtml);
        $this->assertStringContainsString('Ditandatangani secara elektronik', $viewHtml);
        $this->assertStringContainsString('SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN', $viewHtml);
        $this->assertStringContainsString('PENJABARAN TUGAS POKOK DAN FUNGSI (TUPOKSI)', $viewHtml);
    }

    public function test_monthly_prayer_duty_direct_pdf_download_with_qrcode(): void
    {
        $response = $this->get('/petugas-sholat/unduh-pdf?month=10&year=2026');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('filename=Jadwal_Petugas_Shalat_Bulan_Oktober_2026.pdf', str_replace('"', '', $response->headers->get('content-disposition')));

        // Verify the underlying view data includes QR code and unbolded styling
        $controller = app(\App\Http\Controllers\PrayerDutyExportController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareMonthlyDutyData');
        $method->setAccessible(true);
        $data = $method->invoke($controller, 10, 2026);

        $this->assertNotEmpty($data['qrCodeBase64']);
        $this->assertStringStartsWith('data:image/png;base64,', $data['qrCodeBase64']);
        $this->assertEquals(url('/petugas-sholat?month=10&year=2026'), $data['verifyUrl']);

        $viewHtml = view('pdf.prayer-duty-monthly-pdf', $data)->render();
        $this->assertStringContainsString('font-weight: normal;', $viewHtml);
        $this->assertStringContainsString('sig-qr-box', $viewHtml);
        $this->assertStringContainsString('TTE BSrE DISAHKAN', $viewHtml);
    }

    public function test_kajian_direct_pdf_download(): void
    {
        $this->actingAs($this->adminUser);

        // Test Pekanan
        $responsePekanan = $this->get('/admin/kajian/unduh-pdf?type=pekanan&month=10&year=2026');
        $responsePekanan->assertStatus(200);
        $responsePekanan->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('filename=Jadwal_Kajian_Pekanan_Oktober_2026.pdf', str_replace('"', '', $responsePekanan->headers->get('content-disposition')));

        // Test Jumat
        $responseJumat = $this->get('/admin/kajian/unduh-pdf?type=jumat&month=10&year=2026');
        $responseJumat->assertStatus(200);
        $responseJumat->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('filename=Jadwal_Shalat_Jumat_Oktober_2026.pdf', str_replace('"', '', $responseJumat->headers->get('content-disposition')));
    }

    public function test_prayer_duty_roster_direct_pdf_download(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/petugas/unduh-pdf?week=all&month=10&year=2026');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('filename=Roster_Petugas_Ibadah_Oktober_2026.pdf', str_replace('"', '', $response->headers->get('content-disposition')));
    }

    public function test_agenda_direct_pdf_download(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/agenda/unduh-pdf');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('filename=Agenda_Kegiatan_Masjid_' . date('Y-m-d') . '.pdf', str_replace('"', '', $response->headers->get('content-disposition')));

        // Verify underlying view contains QR code and table elements
        $controller = app(\App\Http\Controllers\AgendaExportController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareAgendaScheduleData');
        $method->setAccessible(true);
        $data = $method->invoke($controller, request());

        $this->assertNotEmpty($data['qrCodeBase64']);
        $this->assertStringStartsWith('data:image/png;base64,', $data['qrCodeBase64']);

        $viewHtml = view('pdf.agenda-schedule-pdf', $data)->render();
        $this->assertStringContainsString('DAFTAR PERENCANAAN &amp; REALISASI AGENDA KEGIATAN', $viewHtml);
        $this->assertStringContainsString('TTE BSrE DISAHKAN', $viewHtml);
        $this->assertStringContainsString('TOTAL ANGGARAN', $viewHtml);
    }

    public function test_finance_print_pdf_view_renders_without_errors(): void
    {
        $this->actingAs($this->adminUser);

        // Monthly
        $responseMonthly = $this->get('/admin/finance/export-pdf?type=monthly&month=10&year=2026');
        $responseMonthly->assertStatus(200);
        $responseMonthly->assertSee('Unduh PDF', false);
        $responseMonthly->assertSee('downloadFinancePdf', false);

        // Agenda
        $agenda = \App\Models\Agenda::create([
            'title' => 'Peringatan Maulid Nabi 1448 H',
            'event_date' => '2026-09-15',
            'budget' => 15000000,
            'status' => 'TERLAKSANA',
        ]);

        $responseAgenda = $this->get('/admin/finance/export-pdf?type=agenda&agenda_id=' . $agenda->id);
        $responseAgenda->assertStatus(200);
        $responseAgenda->assertSee('Unduh PDF', false);
        $responseAgenda->assertSee('downloadFinancePdf', false);
    }

    public function test_finance_direct_pdf_download_monthly(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/finance/unduh-pdf?type=monthly&month=10&year=2026');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('filename=Laporan_Arus_Kas_Bulan_10_2026.pdf', str_replace('"', '', $response->headers->get('content-disposition')));

        // Verify underlying view contains QR code and table elements
        $controller = app(\App\Http\Controllers\FinanceExportController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareFinanceReportData');
        $method->setAccessible(true);
        $data = $method->invoke($controller, request()->merge(['type' => 'monthly', 'month' => 10, 'year' => 2026]));

        $this->assertNotEmpty($data['qrCodeBase64']);
        $this->assertStringStartsWith('data:image/png;base64,', $data['qrCodeBase64']);

        $viewHtml = view('pdf.finance-report-pdf', $data)->render();
        $this->assertStringContainsString('LAPORAN ARUS KAS MASJID SALAHUDDIN', $viewHtml);
        $this->assertStringContainsString('TTE BSrE DISAHKAN', $viewHtml);
    }

    public function test_finance_direct_pdf_download_agenda(): void
    {
        $this->actingAs($this->adminUser);

        $agenda = \App\Models\Agenda::create([
            'title' => 'Santunan Anak Yatim Muharram',
            'event_date' => '2026-07-20',
            'budget' => 8000000,
            'status' => 'TERLAKSANA',
        ]);

        $response = $this->get('/admin/finance/unduh-pdf?type=agenda&agenda_id=' . $agenda->id);
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('filename=LPJ_Keuangan_santunan-anak-yatim-muharram.pdf', str_replace('"', '', $response->headers->get('content-disposition')));

        // Verify underlying view contains QR code and table elements
        $controller = app(\App\Http\Controllers\FinanceExportController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareFinanceReportData');
        $method->setAccessible(true);
        $data = $method->invoke($controller, request()->merge(['type' => 'agenda', 'agenda_id' => $agenda->id]));

        $this->assertNotEmpty($data['qrCodeBase64']);
        $this->assertStringStartsWith('data:image/png;base64,', $data['qrCodeBase64']);

        $viewHtml = view('pdf.finance-report-pdf', $data)->render();
        $this->assertStringContainsString('LAPORAN PERTANGGUNGJAWABAN (LPJ) KEUANGAN KEGIATAN', $viewHtml);
        $this->assertStringContainsString('Santunan Anak Yatim Muharram', $viewHtml);
        $this->assertStringContainsString('TTE BSrE DISAHKAN', $viewHtml);
    }
}


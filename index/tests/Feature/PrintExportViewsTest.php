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
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Admin\AdminDashboard;

class UniversalFileDownloaderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /**
     * Verifikasi bahwa helper window.downloadFileDirect terpasang di layouts/app.blade.php
     * dengan dukungan MIME type dinamis dan integrasi Web Share API Level 2 untuk iOS PWA.
     */
    public function test_layout_contains_universal_file_downloader_with_pwa_share_support(): void
    {
        $layoutContent = file_get_contents(resource_path('views/layouts/app.blade.php'));

        // Harus memiliki fungsi universal downloadFileDirect
        $this->assertStringContainsString('window.downloadFileDirect = async function', $layoutContent);

        // Harus memiliki deteksi MIME types untuk .csv, .xlsx, dan .pdf
        $this->assertStringContainsString("text/csv;charset=utf-8", $layoutContent);
        $this->assertStringContainsString("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $layoutContent);
        $this->assertStringContainsString("application/pdf", $layoutContent);

        // Harus memanggil Web Share API files untuk mobile/iOS PWA
        $this->assertStringContainsString("navigator.canShare({ files: [file] })", $layoutContent);
        $this->assertStringContainsString("await navigator.share({", $layoutContent);

        // Harus tetap menyediakan alias window.downloadPdfDirect untuk kompatibilitas penuh
        $this->assertStringContainsString('window.downloadPdfDirect = function', $layoutContent);
    }

    /**
     * Verifikasi tombol Export Kas & Keuangan di tab admin dan mobile action sheet menggunakan downloadFileDirect.
     */
    public function test_finance_export_buttons_use_download_file_direct(): void
    {
        $financeTabContent = file_get_contents(resource_path('views/livewire/admin/tabs/_tab-finance.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.finance.export-excel') }}'", $financeTabContent);

        $actionSheetContent = file_get_contents(resource_path('views/livewire/admin/tabs/_mobile-action-sheet.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.finance.export-excel') }}'", $actionSheetContent);
    }

    /**
     * Verifikasi tombol Export Jadwal Kajian dan Shalat Jumat menggunakan downloadFileDirect.
     */
    public function test_kajian_and_jumat_export_buttons_use_download_file_direct(): void
    {
        $kegiatanTabContent = file_get_contents(resource_path('views/livewire/admin/tabs/_tab-kegiatan.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.kajian.export-excel') }}", $kegiatanTabContent);

        $actionSheetContent = file_get_contents(resource_path('views/livewire/admin/tabs/_mobile-action-sheet.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.kajian.export-excel') }}?type=pekanan", $actionSheetContent);
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.kajian.export-excel') }}?type=jumat", $actionSheetContent);
    }

    /**
     * Verifikasi tombol Export Agenda Kegiatan Akbar menggunakan downloadFileDirect.
     */
    public function test_agenda_export_buttons_use_download_file_direct(): void
    {
        $kegiatanTabContent = file_get_contents(resource_path('views/livewire/admin/tabs/_tab-kegiatan.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.agenda.export-excel') }}'", $kegiatanTabContent);

        $actionSheetContent = file_get_contents(resource_path('views/livewire/admin/tabs/_mobile-action-sheet.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.agenda.export-excel') }}'", $actionSheetContent);
    }

    /**
     * Verifikasi tombol Export Petugas Shalat menggunakan downloadFileDirect.
     */
    public function test_petugas_export_buttons_use_download_file_direct(): void
    {
        $petugasTabContent = file_get_contents(resource_path('views/livewire/admin/tabs/_tab-petugas.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.petugas.export-excel') }}", $petugasTabContent);

        $actionSheetContent = file_get_contents(resource_path('views/livewire/admin/tabs/_mobile-action-sheet.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.petugas.export-excel') }}", $actionSheetContent);
    }

    /**
     * Verifikasi tombol Export Rekap Potongan Pegawai di modal menggunakan downloadFileDirect.
     */
    public function test_potongan_modal_export_button_uses_download_file_direct(): void
    {
        $modalsContent = file_get_contents(resource_path('views/livewire/admin/tabs/_modals.blade.php'));
        $this->assertStringContainsString("window.downloadFileDirect('{{ route('admin.programs.export-excel') }}", $modalsContent);
    }

    /**
     * Verifikasi endpoint ekspor finance mengembalikan streamed CSV dengan header attachment.
     */
    public function test_finance_export_endpoint_returns_valid_attachment(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.finance.export-excel'));

        $response->assertStatus(200);
        $disposition = (string) $response->headers->get('content-disposition');
        $this->assertStringContainsString('attachment;', $disposition);
        $this->assertStringContainsString('Laporan_Kas_Masjid_Salahuddin_', $disposition);
    }
}

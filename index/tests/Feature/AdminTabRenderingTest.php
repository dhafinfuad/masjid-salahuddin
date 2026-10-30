<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\MasjidSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTabRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
        $this->admin = User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'admin@pajak.go.id',
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);
    }

    public function test_all_admin_tabs_render_properly(): void
    {
        $tabs = ['kegiatan', 'finance', 'programs', 'users', 'settings'];

        foreach ($tabs as $tab) {
            $response = $this->actingAs($this->admin)->get("/admin/{$tab}");
            $response->assertOk();
            $this->assertNotEmpty($response->getContent(), "HTML content for tab {$tab} should not be empty");

            $test = Livewire::actingAs($this->admin)
                ->test(AdminDashboard::class, ['tab' => $tab]);
            $test->assertOk();
            $html = $test->html();
            $this->assertNotEmpty(trim($html), "Livewire rendered html for {$tab} should not be empty");
        }
    }

    public function test_navy_header_card_banners_do_not_have_icon_boxes_on_desktop(): void
    {
        $tabs = ['kegiatan', 'finance', 'programs', 'users', 'settings', 'petugas', 'galeri', 'saran', 'statistics'];

        foreach ($tabs as $tab) {
            $html = Livewire::actingAs($this->admin)
                ->test(AdminDashboard::class, ['tab' => $tab])
                ->html();

            // Memastikan tidak ada icon box (hidden sm:flex w-9 h-9 bg-white/10) pada header card banner
            $this->assertStringNotContainsString('hidden sm:flex w-9 h-9 rounded-lg bg-white/10', $html, "Tab {$tab} still contains desktop header icon box");
        }
    }

    public function test_statistics_kpi_cards_render_carousel_on_mobile_and_grid_on_desktop(): void
    {
        $html = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'statistics'])
            ->html();

        // 1. Memastikan mobile carousel track dan Alpine state tersedia
        $this->assertStringContainsString('kpiTotal: 4', $html);
        $this->assertStringContainsString('x-ref="kpiTrack"', $html);
        $this->assertStringContainsString('snap-x snap-mandatory', $html);

        // 2. Memastikan desktop grid tersedia
        $this->assertStringContainsString('hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-4', $html);

        // 3. Memastikan semua kartu metrik utama ter-render
        $this->assertStringContainsString('Total Kunjungan (Hits)', $html);
        $this->assertStringContainsString('Pengunjung Unik (IP)', $html);
        $this->assertStringContainsString('Kunjungan Hari Ini', $html);
        $this->assertStringContainsString('Dominasi Perangkat', $html);
    }

    public function test_desktop_single_row_searchbar_and_filters_are_rendered_on_tabs_except_finance_kas_utama(): void
    {
        // 1. Tab kegiatan, petugas, programs, users, galeri, saran memiliki searchbar flex-1 min-w-0 pada container md:flex-row
        $tabs = ['kegiatan', 'petugas', 'programs', 'users', 'galeri', 'saran'];

        foreach ($tabs as $tab) {
            $html = Livewire::actingAs($this->admin)
                ->test(AdminDashboard::class, ['tab' => $tab])
                ->html();

            $this->assertStringContainsString('md:flex-row', $html, "Tab {$tab} should contain md:flex-row for desktop single-row layout");
            $this->assertStringContainsString('flex-1 min-w-0', $html, "Tab {$tab} should contain flex-1 min-w-0 for expanding searchbar");
        }

        // 2. Tab finance Kas Utama tetap mempertahankan layout aslinya (type pills dan input tidak diubah ke md:flex-row pada toolbar kas utama)
        $financeHtml = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'finance'])
            ->html();

        // Kas Utama tetap memiliki pills type selector dan pos anggaran kategori memiliki responsive single row
        $this->assertStringContainsString("activeFinanceType === 'pemasukan'", $financeHtml);
        $this->assertStringContainsString("catSearch", $financeHtml);
    }

    public function test_ustadz_database_separated_modals_and_action_menu(): void
    {
        // 1. Buat data ustadz sampel
        \App\Models\Ustadz::create([
            'name' => 'Ust. Dr. Syafiq Riza Basalamah, M.A.',
            'title' => 'Kajian Tematik & Sirah',
            'phone' => '081234567890',
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'settings']);

        // 2. Buka modal tambah ustadz
        $component->call('openCreateUstadzModal')
            ->assertSet('showCreateUstadzModal', true)
            ->assertSet('showEditUstadzModal', false)
            ->assertDispatched('open-create-ustadz-modal');

        // 3. Tutup modal tambah ustadz
        $component->call('closeCreateUstadzModal')
            ->assertSet('showCreateUstadzModal', false)
            ->assertDispatched('close-create-ustadz-modal');

        // 4. Buka modal edit ustadz
        $ustadz = \App\Models\Ustadz::first();
        $component->call('editUstadz', $ustadz->id)
            ->assertSet('showEditUstadzModal', true)
            ->assertSet('editingUstadzId', $ustadz->id)
            ->assertSet('ustadzName', $ustadz->name)
            ->assertDispatched('open-edit-ustadz-modal');

        // 5. Tutup modal edit ustadz
        $component->call('closeEditUstadzModal')
            ->assertSet('showEditUstadzModal', false)
            ->assertDispatched('close-edit-ustadz-modal');

        // 6. Validasi rendering HTML
        $html = $component->html();

        // Modal tambah dan edit harus ada di DOM
        $this->assertStringContainsString('showCreateUstadzModal', $html);
        $this->assertStringContainsString('showEditUstadzModal', $html);
        $this->assertStringContainsString('Tambah Ustadz Baru', $html);
        $this->assertStringContainsString('Edit Profil Ustadz', $html);

        // Form inline 5 kolom di tab settings sudah tidak ada
        $this->assertStringNotContainsString('KOLOM KIRI: Form Tambah / Edit Profil & Foto Ustadz', $html);

        // Menu titik tiga ter-render pada kartu ustadz
        $this->assertStringContainsString('title="Menu Aksi"', $html);
        $this->assertStringContainsString('openEditUstadzModal(' . $ustadz->id . ')', $html);
        $this->assertStringContainsString('Hapus Data Ustadz', $html);

        // 7. Validasi: Menghapus ustadz TIDAK boleh membuka modal tambah ustadz
        $component->call('deleteUstadz', $ustadz->id)
            ->assertSet('showCreateUstadzModal', false)
            ->assertSet('showEditUstadzModal', false)
            ->assertNotDispatched('open-ustadz-modal')
            ->assertNotDispatched('open-create-ustadz-modal');

        $this->assertDatabaseMissing('ustadzs', ['id' => $ustadz->id]);
    }

    public function test_surat_pernyataan_button_changes(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'programs']);

        $html = $component->html();

        // 1. Memastikan tombol "Pratinjau / Cetak" sudah dihapus
        $this->assertStringNotContainsString('admin.programs.print-surat-pernyataan', $html);
        $this->assertStringNotContainsString('Pratinjau / Cetak', $html);

        // 2. Memastikan tombol "Unduh Surat (PDF)" ter-render dengan fungsi downloadSuratPernyataanPdf dan teks Memproses
        $this->assertStringContainsString('downloadSuratPernyataanPdf()', $html);
        $this->assertStringContainsString('isDownloadingPernyataanPdf', $html);
        $this->assertStringContainsString('Unduh Surat (PDF)', $html);
        $this->assertStringContainsString('Memproses...', $html);

        // 3. Memastikan bottom drawer penolong iOS WebKit ter-render untuk tab surat pernyataan
        $this->assertStringContainsString('pernyataanReadyShareDrawer', $html);
        $this->assertStringContainsString('executePernyataanShareFromDrawer()', $html);
        $this->assertStringContainsString('Berkas Siap', $html);
        $this->assertStringContainsString('Buka Menu Simpan (Save to Files)', $html);
    }

    public function test_top_screen_progress_bar_replaces_old_interstitial_reload_bar(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'users']);

        $html = $component->html();

        // 1. Memastikan reload bar lama yang berkedip di bawah sub-tab sudah dihapus bersih
        $this->assertStringNotContainsString('bg-linear-to-r from-amber-400 via-gov-navy to-amber-400', $html);

        // 2. Memastikan progress bar baru di layar paling atas (top-0) dengan warna #2299dd terpasang
        $this->assertStringContainsString('fixed top-0 inset-x-0', $html);
        $this->assertStringContainsString('livewire-progress-bar-stream', $html);
        $this->assertStringContainsString('#2299dd', $html);
        $this->assertStringContainsString('wire:target="switchTab, gotoPage, nextPage, previousPage"', $html);

        // 3. Memastikan styling keyframes terdefinisi di layout app
        $layoutContent = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $this->assertStringContainsString('livewireProgressStream', $layoutContent);
        $this->assertStringContainsString('.livewire-progress-bar-stream', $layoutContent);
    }
}

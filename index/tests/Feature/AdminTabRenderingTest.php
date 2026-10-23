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
}

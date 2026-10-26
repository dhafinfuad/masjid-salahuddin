<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\MasjidSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MobileActionSheetTest extends TestCase
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

    public function test_mobile_floating_action_button_and_bottom_sheet_are_rendered(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan']);

        $component->assertOk();

        // 1. Verifikasi tombol Circular FAB (+) mobile ada di HTML
        $component->assertSeeHtml('openMobileActionSheet()');
        $component->assertSeeHtml('fixed bottom-6 right-5 z-40 sm:hidden');

        // 2. Verifikasi Bottom Sheet drawer panel ada di HTML
        $component->assertSeeHtml('Menu Tindakan Cepat');
        $component->assertSeeHtml('getMobileActionSubtitle()');
        $component->assertSeeHtml('closeMobileActionSheet()');

        // 3. Verifikasi aksi-aksi cepat utama tersedia dalam Bottom Sheet
        $component->assertSeeHtml('openCreateKajian(\'pekanan\')');
        $component->assertSeeHtml('Direktori Asatidz');
    }

    public function test_top_action_cards_are_hidden_on_mobile_across_tabs(): void
    {
        $tabs = ['kegiatan', 'petugas', 'finance', 'programs', 'users', 'galeri', 'settings'];

        foreach ($tabs as $tab) {
            $component = Livewire::actingAs($this->admin)
                ->test(AdminDashboard::class, ['tab' => $tab]);

            $component->assertOk();
            $html = $component->html();

            // Verifikasi top header card memiliki kelas 'hidden sm:flex' sehingga tidak muncul di mobile
            $this->assertStringContainsString('hidden sm:flex', $html, "Tab {$tab} must hide top action header on mobile using hidden sm:flex");
        }
    }

    public function test_redundant_buttons_removed_and_fab_hidden_on_settings_and_statistics_and_saran(): void
    {
        $this->actingAs($this->admin);
        $rendered = view('livewire.admin.tabs._mobile-action-sheet')->render();

        // 1. Verifikasi FAB memiliki logika sembunyi untuk settings, statistics, dan saran
        $this->assertStringContainsString("['dashboard', 'settings', 'statistics', 'statistik', 'saran'].includes(activeTab)", $rendered);

        // 2. Verifikasi tombol Tambah Peserta tidak ada di action sheet programs
        $this->assertStringNotContainsString('openCreateParticipant()', $rendered);

        // 3. Verifikasi tombol navigasi pindah sub-tab di finance dan users tidak ada di action sheet
        $this->assertStringNotContainsString("financeSubTab = 'kategori'", $rendered);
        $this->assertStringNotContainsString("financeSubTab = 'utama'", $rendered);
        $this->assertStringNotContainsString("userSubTab = 'kepengurusan'", $rendered);
        $this->assertStringNotContainsString("userSubTab = 'pengguna'", $rendered);

        // 4. Verifikasi tombol reset takmir dan lihat di portal di users/gallery/petugas/saran tidak ada
        $this->assertStringNotContainsString('resetTakmirToDefault()', $rendered);
        $this->assertStringNotContainsString('/petugas-sholat', $rendered);
        $this->assertStringNotContainsString("activeTab === 'saran'", $rendered);
    }

    public function test_tambah_pos_hidden_on_mobile_and_user_counts_removed(): void
    {
        $this->actingAs($this->admin);

        // Verifikasi tombol Tambah Pos disembunyikan di mobile dengan hidden md:inline-flex
        $financeHtml = Livewire::test(AdminDashboard::class, ['tab' => 'finance'])->html();
        $this->assertStringContainsString('hidden md:inline-flex shrink-0 justify-center items-center gap-1.5 h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer', $financeHtml);

        // Verifikasi keterangan counter 'aktif' dan 'pengguna' tidak ada di tab users
        $usersHtml = Livewire::test(AdminDashboard::class, ['tab' => 'users'])->html();
        $this->assertStringNotContainsString('aktif)', $usersHtml);
        $this->assertStringNotContainsString('pengguna</span>', $usersHtml);
    }

    public function test_agenda_action_sheet_uses_three_columns_compact_layout(): void
    {
        $this->actingAs($this->admin);
        $rendered = view('livewire.admin.tabs._mobile-action-sheet')->render();

        // Verifikasi subtab agenda memiliki grid-cols-3
        $this->assertStringContainsString("kegiatanSubTab === 'agenda'", $rendered);
        $this->assertMatchesRegularExpression(
            '/kegiatanSubTab\s*===\s*\'agenda\'.*?grid grid-cols-3 gap-2.*?Import File.*?Download Excel.*?(Unduh|Cetak) PDF/s',
            $rendered
        );
    }

    public function test_single_secondary_button_under_primary_uses_compact_border_slate_400_style(): void
    {
        $this->actingAs($this->admin);
        $rendered = view('livewire.admin.tabs._mobile-action-sheet')->render();

        // 1. Verifikasi subtab Surat Pernyataan telah dihapus sepenuhnya dari Action Sheet & FAB disembunyikan
        $this->assertStringNotContainsString('Unggah Berkas Scan', $rendered);
        $this->assertStringNotContainsString('openPrintPernyataanModal', $rendered);
        $this->assertStringContainsString("!(activeTab === 'programs' && socialSubTab === 'pernyataan')", $rendered);

        // 2. Subtab Setoran Kantor (Export Setoran)
        $this->assertStringContainsString('Export Setoran', $rendered);
        $this->assertMatchesRegularExpression(
            '/showExportPotonganModal = true[^>]*class="[^"]*py-3 px-4[^"]*border-slate-400[^"]*"/s',
            $rendered
        );

        // 3. Subtab Pegawai (Impor Spreadsheet)
        $this->assertStringContainsString('Impor Spreadsheet', $rendered);
        $this->assertMatchesRegularExpression(
            '/showImportEmployeeModal = true[^>]*class="[^"]*py-3 px-4[^"]*border-slate-400[^"]*"/s',
            $rendered
        );
    }
}


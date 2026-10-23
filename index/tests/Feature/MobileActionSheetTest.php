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

    public function test_action_sheet_uses_theme_colors_and_valid_actions(): void
    {
        $html = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'programs'])
            ->html();

        $this->assertStringContainsString('bg-gov-navy', $html);
        $this->assertStringNotContainsString('bg-[#0e2547]', $html);
        $this->assertStringNotContainsString('pointer-events-none sm:hidden', $html);
        $this->assertStringNotContainsString('exportSocialPrograms', $html);
        $this->assertStringNotContainsString('exportSocialParticipants', $html);
        $this->assertStringContainsString('showExportPotonganModal = true', $html);
    }
}

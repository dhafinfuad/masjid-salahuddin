<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Employee;
use App\Models\MasjidSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserProfileDrawerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
        $this->admin = User::factory()->create([
            'name' => 'Dhafin Fuad Mahathir',
            'email' => 'dhafin@pajak.go.id',
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);
    }

    public function test_top_header_renders_portal_link_and_user_avatar(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'dashboard']);

        $component->assertOk();

        // 1. Verifikasi tombol Portal telah dipindahkan dari Top Header Bar dan Sidebar ke Popover & Drawer
        $component->assertDontSeeHtml('<span class="whitespace-nowrap">Portal</span>');
        $component->assertDontSeeHtml('<span class="text-xs font-medium">Portal</span>');
        $component->assertSeeHtml('Portal Jamaah');
        $component->assertSeeHtml('href="' . url('/') . '"');

        // 2. Verifikasi Avatar inisial di Top Header berformat bg-gov-navy dan text-white (hanya avatar ikon tanpa teks nama)
        $component->assertSeeHtml('userProfileDrawerOpen = !userProfileDrawerOpen');
        $component->assertSeeHtml('class="w-8 h-8 rounded-xl bg-gov-navy text-white font-bold flex items-center justify-center text-xs shadow-2xs ring-2 ring-slate-100 shrink-0"');
        $component->assertSeeHtml('DF'); // Inisial kapital Dhafin Fuad

        // 3. Verifikasi sidebar kiri tidak memuat avatar footer section lagi
        $html = $component->html();
        $this->assertStringNotContainsString('Sidebar User Footer', $html);
        $this->assertStringNotContainsString('border-t border-[#0F2F59] bg-[#06172E]', $html);

        // 3. Verifikasi Mobile Profile Drawer ter-render dengan avatar navy
        $component->assertSeeHtml('userProfileDrawerOpen && window.innerWidth < 768');
        $component->assertSeeHtml('w-12 h-12 rounded-xl bg-gov-navy text-white font-bold text-lg flex items-center justify-center shadow-xs ring-4 ring-slate-200/60 shrink-0');
        $component->assertSeeHtml('Profil Pengguna');
        $component->assertSeeHtml('Keluar dari Akun');

        // 4. Verifikasi Desktop Dropdown Popover ter-render dan memuat tombol Portal Jamaah & Keluar
        $component->assertSeeHtml('userProfileDrawerOpen && window.innerWidth >= 768');
        
        // 5. Verifikasi file blade popover dan drawer secara eksplisit memuat tombol Portal Jamaah di atas Keluar
        $dashboardBlade = file_get_contents(resource_path('views/livewire/admin/admin-dashboard.blade.php'));
        $this->assertStringContainsString('Portal Jamaah', $dashboardBlade);
        $this->assertStringNotContainsString('title="Kunjungi Portal Publik Jamaah"', $dashboardBlade);

        $userDrawerBlade = file_get_contents(resource_path('views/livewire/admin/tabs/_mobile-user-drawer.blade.php'));
        $this->assertStringContainsString('Portal Jamaah', $userDrawerBlade);
        $this->assertStringContainsString('Keluar dari Akun', $userDrawerBlade);
    }

    public function test_mobile_module_nav_drawer_is_clean_without_logout_button(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'dashboard']);

        $component->assertOk();

        // Verifikasi 10 modul tetap ada di menu navigasi
        $component->assertSeeHtml('Menu Navigasi Modul');
        $component->assertSeeHtml('Dashboard');
        $component->assertSeeHtml('Jadwal Kegiatan');
        $component->assertSeeHtml('Petugas Shalat');
        $component->assertSeeHtml('Kas & Keuangan');
        $component->assertSeeHtml('Program Sosial');
        $component->assertSeeHtml('Galeri Kegiatan');
        $component->assertSeeHtml('Saran & Kritik');
        $component->assertSeeHtml('Pengaturan');
        $component->assertSeeHtml('Pengguna & SK');
        $component->assertSeeHtml('Statistik');

        // Verifikasi file _mobile-nav-drawer.blade.php bersih tanpa tombol logout maupun portal jamaah serta memiliki pb-12
        $drawerBlade = file_get_contents(resource_path('views/livewire/admin/tabs/_mobile-nav-drawer.blade.php'));
        $this->assertStringNotContainsString('wire:click="logout"', $drawerBlade);
        $this->assertStringNotContainsString('Portal Jamaah', $drawerBlade);
        $this->assertStringContainsString('pb-12', $drawerBlade);
        $this->assertStringContainsString('padding-bottom: 3rem;', $drawerBlade);

        // Verifikasi bottom nav active style menggunakan font-medium dan stroke-[1.8]
        $bottomBarBlade = file_get_contents(resource_path('views/livewire/admin/tabs/_mobile-bottom-bar.blade.php'));
        $this->assertStringContainsString('border-t-2 border-amber-500 text-gov-navy', $bottomBarBlade);
        $this->assertStringContainsString('font-medium', $bottomBarBlade);
        $this->assertStringContainsString('stroke-[1.8]', $bottomBarBlade);
    }

    public function test_logout_action_clears_session_and_redirects(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'dashboard'])
            ->call('logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}

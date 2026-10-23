<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class JamaahSidebarMenuTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::create([
            'name' => 'Tester ' . $role,
            'email' => strtolower($role) . '@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'AKTIF',
        ]);
    }

    public function test_jamaah_does_not_see_restricted_menus(): void
    {
        $this->actingAs($this->makeUser('Jamaah'));

        Livewire::test(AdminDashboard::class, ['tab' => 'dashboard'])
            ->assertOk()
            ->assertSee('Galeri Kegiatan')
            ->assertSee('Portal Jamaah')
            ->assertDontSee('/admin/saran')
            ->assertDontSee('/admin/users')
            ->assertDontSee('/admin/statistics')
            ->assertDontSee('/admin/settings');
    }

    public function test_jamaah_cannot_open_restricted_tabs_directly(): void
    {
        $this->actingAs($this->makeUser('Jamaah'));

        foreach (['saran', 'users', 'statistics', 'statistik', 'settings'] as $tab) {
            Livewire::test(AdminDashboard::class, ['tab' => $tab])
                ->assertRedirect('/admin/dashboard');
        }

        Livewire::test(AdminDashboard::class, ['tab' => 'dashboard'])
            ->call('switchTab', 'users')
            ->assertSet('currentTab', 'dashboard');
    }

    public function test_jamaah_is_redirected_from_poster_setting_route(): void
    {
        $this->actingAs($this->makeUser('Jamaah'))
            ->get('/admin/poster-setting')
            ->assertRedirect('/admin/dashboard');
    }

    public function test_officer_can_still_open_poster_setting_route(): void
    {
        $this->actingAs($this->makeUser('Master'))
            ->get('/admin/poster-setting')
            ->assertOk();
    }

    public function test_officer_still_sees_all_menus(): void
    {
        $this->actingAs($this->makeUser('Master'));

        Livewire::test(AdminDashboard::class, ['tab' => 'dashboard'])
            ->assertOk()
            ->assertSee('/admin/saran', false)
            ->assertSee('/admin/users', false)
            ->assertSee('/admin/statistics', false)
            ->assertSee('/admin/settings', false);
    }
}

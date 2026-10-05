<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $master;
    protected User $ketua;
    protected User $sekretaris;
    protected User $bendahara;
    protected User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat Akun Pengurus & Akun Jamaah
        $this->master = User::create([
            'name' => 'Dr. H. Bambang Irawan',
            'email' => 'master@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $this->ketua = User::create([
            'name' => 'Ustadz Abdullah',
            'email' => 'ketua@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Ketua',
            'status' => 'AKTIF',
        ]);

        $this->sekretaris = User::create([
            'name' => 'Ahmad Fikri',
            'email' => 'sekretaris@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Sekretaris',
            'status' => 'AKTIF',
        ]);

        $this->bendahara = User::create([
            'name' => 'H. Mukhlis Syarif',
            'email' => 'bendahara@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Bendahara',
            'status' => 'AKTIF',
        ]);

        $this->jamaah = User::create([
            'name' => 'Haji Mansyur',
            'email' => 'jamaah@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);
    }

    public function test_user_roles_and_helpers_work_correctly(): void
    {
        $this->assertTrue($this->master->isMaster());
        $this->assertTrue($this->master->isOfficer());
        $this->assertTrue($this->master->canManage());
        $this->assertFalse($this->master->isReadOnly());

        $this->assertTrue($this->ketua->isOfficer());
        $this->assertTrue($this->ketua->canManage());

        $this->assertTrue($this->sekretaris->isOfficer());
        $this->assertTrue($this->sekretaris->canManage());

        $this->assertTrue($this->bendahara->isOfficer());
        $this->assertTrue($this->bendahara->canManage());

        $this->assertTrue($this->jamaah->isJamaah());
        $this->assertTrue($this->jamaah->isReadOnly());
        $this->assertFalse($this->jamaah->canManage());
    }

    public function test_officer_can_access_users_tab_and_see_user_list(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->assertOk()
            ->assertSee('Manajemen Pengguna')
            ->assertSee('Dr. H. Bambang Irawan')
            ->assertSee('Ustadz Abdullah')
            ->assertSee('Ahmad Fikri')
            ->assertSee('H. Mukhlis Syarif')
            ->assertSee('Haji Mansyur');
    }

    public function test_officer_can_create_new_user(): void
    {
        $this->actingAs($this->ketua);

        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->set('userName', 'Ustadz Hanan Attaki')
            ->set('userEmail', 'hanan@masjidsalahuddin.id')
            ->set('userPassword', 'Password123!')
            ->set('userRole', 'Ketua')
            ->set('userStatus', 'AKTIF')
            ->call('saveUser')
            ->assertSet('showUserModal', false);

        $this->assertDatabaseHas('users', [
            'name' => 'Ustadz Hanan Attaki',
            'email' => 'hanan@masjidsalahuddin.id',
            'role' => 'Ketua',
        ]);
    }

    public function test_officer_can_edit_existing_user(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('openEditUser', $this->sekretaris->id)
            ->assertSet('isEditingUser', true)
            ->assertSet('userName', 'Ahmad Fikri')
            ->set('userName', 'Ahmad Fikri, S.Kom')
            ->set('userRole', 'Sekretaris')
            ->call('saveUser');

        $this->assertDatabaseHas('users', [
            'id' => $this->sekretaris->id,
            'name' => 'Ahmad Fikri, S.Kom',
        ]);
    }

    public function test_officer_can_toggle_user_status(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('toggleUserStatus', $this->jamaah->id);

        $this->assertEquals('NONAKTIF', $this->jamaah->fresh()->status);

        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('toggleUserStatus', $this->jamaah->id);

        $this->assertEquals('AKTIF', $this->jamaah->fresh()->status);
    }

    public function test_officer_can_delete_user(): void
    {
        $this->actingAs($this->master);

        $targetUser = User::create([
            'name' => 'User Hapus',
            'email' => 'hapus@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('deleteUser', $targetUser->id);

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_jamaah_level_is_read_only_and_cannot_create_or_delete_user(): void
    {
        $this->actingAs($this->jamaah);

        // Jamaah trying to create user
        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->set('userName', 'User Ilegal')
            ->set('userEmail', 'ilegal@masjidsalahuddin.id')
            ->set('userPassword', 'password123')
            ->set('userRole', 'Master')
            ->call('saveUser')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('users', ['email' => 'ilegal@masjidsalahuddin.id']);

        // Jamaah trying to delete user
        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('deleteUser', $this->sekretaris->id)
            ->assertDispatched('toast');

        $this->assertDatabaseHas('users', ['id' => $this->sekretaris->id]);
    }
}

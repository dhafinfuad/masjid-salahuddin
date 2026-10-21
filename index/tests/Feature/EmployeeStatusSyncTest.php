<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Auth\LoginPage;
use App\Models\Employee;
use App\Models\MasjidSetting;
use App\Models\User;
use App\Services\EmployeeStatusSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class EmployeeStatusSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->admin = User::factory()->create([
            'name' => 'Ustadz Admin DKM',
            'email' => 'admin@pajak.go.id',
            'role' => 'Master',
            'status' => 'AKTIF',
            'password' => Hash::make('password123'),
        ]);

        $this->viewer = User::factory()->create([
            'name' => 'Pak Viewer',
            'email' => 'viewer@pajak.go.id',
            'role' => 'viewer',
            'status' => 'AKTIF',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_user_status_is_automatically_synced_from_employees_table(): void
    {
        // 1. Pegawai Aktif
        $pegawai1 = User::factory()->create([
            'name' => 'Deril Amrizal Kholid',
            'email' => '817931806@pajak.go.id',
            'nip' => '198501152010121001',
            'role' => 'Jamaah',
            'status' => 'NONAKTIF', // Sebelumnya nonaktif
        ]);

        Employee::create([
            'name' => 'Deril Amrizal Kholid, S.E.',
            'nip_pendek' => '817931806',
            'nip_panjang' => '198501152010121001',
            'status' => 'AKTIF',
            'user_id' => $pegawai1->id,
        ]);

        // 2. Pegawai Mutasi / Nonaktif di tabel employees
        $pegawai2Mutasi = User::factory()->create([
            'name' => 'Ichtiar Rachmatullah',
            'email' => '060098765@pajak.go.id',
            'nip' => '198703122011011002',
            'role' => 'Jamaah',
            'status' => 'AKTIF', // Awalnya aktif
        ]);

        Employee::create([
            'name' => 'Ichtiar Rachmatullah',
            'nip_pendek' => '060098765',
            'nip_panjang' => '198703122011011002',
            'status' => 'MUTASI',
            'user_id' => $pegawai2Mutasi->id,
        ]);

        // 3. Pegawai yang sama sekali tidak ada di tabel employees
        $pegawai3TidakAda = User::factory()->create([
            'name' => 'Bimo Heriyanto',
            'email' => 'bimo.heriyanto@pajak.go.id',
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        // Jalankan sinkronisasi otomatis
        $result = EmployeeStatusSyncService::syncFromEmployeesTable($this->admin->id);

        // Pegawai 1 harus otomatis AKTIF
        $this->assertEquals('AKTIF', $pegawai1->fresh()->status);

        // Pegawai 2 yang mutasi harus otomatis NONAKTIF
        $this->assertEquals('NONAKTIF', $pegawai2Mutasi->fresh()->status);

        // Pegawai 3 yang tidak ada di tabel employees harus otomatis NONAKTIF
        $this->assertEquals('NONAKTIF', $pegawai3TidakAda->fresh()->status);

        // Akun Master Admin dilindungi, harus tetap AKTIF
        $this->assertEquals('AKTIF', $this->admin->fresh()->status);
    }

    public function test_toggle_employee_status_automatically_updates_user_account_status(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'name' => 'Ahmad Fulan',
            'email' => 'ahmad.fulan@pajak.go.id',
            'nip' => '199001012015011001',
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        $employee = Employee::create([
            'name' => 'Ahmad Fulan',
            'nip_pendek' => '060012345',
            'nip_panjang' => '199001012015011001',
            'status' => 'AKTIF',
            'user_id' => $user->id,
        ]);

        // Toggle status pegawai menjadi MUTASI
        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('toggleEmployeeStatus', $employee->id);

        $this->assertEquals('MUTASI', $employee->fresh()->status);
        $this->assertEquals('NONAKTIF', $user->fresh()->status);

        // Toggle kembali status pegawai menjadi AKTIF
        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('toggleEmployeeStatus', $employee->id);

        $this->assertEquals('AKTIF', $employee->fresh()->status);
        $this->assertEquals('AKTIF', $user->fresh()->status);
    }

    public function test_inactive_employee_cannot_login_and_sees_notification(): void
    {
        $user = User::factory()->create([
            'name' => 'Mantan Pegawai KPP',
            'email' => 'mantan.pegawai@pajak.go.id',
            'status' => 'AKTIF', // di database akun sempat AKTIF
            'password' => Hash::make('password123'),
        ]);

        // Namun di tabel employees statusnya MUTASI
        Employee::create([
            'name' => 'Mantan Pegawai KPP',
            'nip_pendek' => '060055555',
            'nip_panjang' => '198801012010011005',
            'status' => 'MUTASI',
            'user_id' => $user->id,
        ]);

        Livewire::test(LoginPage::class)
            ->set('email', 'mantan.pegawai@pajak.go.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertSet('errorMessage', 'Anda sudah bukan lagi pegawai KPP Madya Malang.');

        // Status user otomatis tersinkronisasi menjadi NONAKTIF di database
        $this->assertEquals('NONAKTIF', $user->fresh()->status);
        $this->assertGuest();
    }

    public function test_active_employee_with_previously_inactive_account_can_login_and_auto_activates(): void
    {
        $user = User::factory()->create([
            'name' => 'Pegawai Aktif KPP',
            'email' => 'pegawai.aktif@pajak.go.id',
            'status' => 'NONAKTIF', // Sebelumnya status akun NONAKTIF
            'password' => Hash::make('password123'),
        ]);

        // Namun di tabel employees statusnya AKTIF
        Employee::create([
            'name' => 'Pegawai Aktif KPP',
            'nip_pendek' => '060077777',
            'nip_panjang' => '199501012018011007',
            'status' => 'AKTIF',
            'user_id' => $user->id,
        ]);

        Livewire::test(LoginPage::class)
            ->set('email', 'pegawai.aktif@pajak.go.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        // Status user otomatis dipulihkan menjadi AKTIF
        $this->assertEquals('AKTIF', $user->fresh()->status);
        $this->assertAuthenticatedAs($user);
    }

    public function test_employee_with_academic_degree_is_automatically_linked_to_user_account(): void
    {
        $this->actingAs($this->admin);

        // Buat user akun aplikasi atas nama Dhafin Fuad Mahathir (Role Master)
        $user = User::factory()->create([
            'name' => 'Dhafin Fuad Mahathir',
            'email' => 'dhafin.mahathir@pajak.go.id',
            'role' => 'Master',
            'status' => 'AKTIF',
            'nip' => null,
        ]);

        // Buat record pegawai dengan penulisan resmi SIKKA bergelar akademik
        $employee = Employee::create([
            'name' => 'DHAFIN FUAD MAHATHIR, A.Md.Pjk.',
            'nip_pendek' => '817931806',
            'nip_panjang' => '199802122018011003',
            'jabatan' => 'Pelaksana (Seksi Penjaminan Kualitas Data)',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
            'user_id' => null,
        ]);

        $this->assertNull($employee->user_id);

        // Kunjungi subtab pegawai pada AdminDashboard
        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->set('userSubTab', 'pegawai')
            ->call('syncEmployeeLinks');

        // Pastikan record pegawai kini otomatis tertaut ke akun user
        $this->assertEquals($user->id, $employee->fresh()->user_id);
        $this->assertEquals('199802122018011003', $user->fresh()->nip);
    }

    public function test_create_user_from_employee_links_existing_user_instead_of_duplicating(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'name' => 'Dhafin Fuad Mahathir',
            'email' => 'dhafin.mahathir@pajak.go.id',
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $employee = Employee::create([
            'name' => 'DHAFIN FUAD MAHATHIR, A.Md.Pjk.',
            'nip_pendek' => '817931806',
            'nip_panjang' => '199802122018011003',
            'status' => 'AKTIF',
            'user_id' => null,
        ]);

        $initialUserCount = User::count();

        // Klik tombol createUserFromEmployee
        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->call('createUserFromEmployee', $employee->id);

        // Jumlah user tidak boleh bertambah (mencegah akun ganda dummy)
        $this->assertEquals($initialUserCount, User::count());
        $this->assertEquals($user->id, $employee->fresh()->user_id);
    }
}

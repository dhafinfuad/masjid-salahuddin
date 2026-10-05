<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Auth\LoginPage;
use App\Models\MasjidSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_admin_can_sync_employee_status_via_text(): void
    {
        $this->actingAs($this->admin);

        // Buat 3 user pegawai di database
        $pegawai1 = User::factory()->create([
            'name' => 'Deril Amrizal Kholid',
            'email' => '817931806@pajak.go.id',
            'nip' => '198501152010121001',
            'status' => 'AKTIF',
        ]);

        $pegawai2 = User::factory()->create([
            'name' => 'Bimo Heriyanto',
            'email' => 'bimo.heriyanto@pajak.go.id',
            'nip' => null,
            'status' => 'NONAKTIF', // Sebelumnya nonaktif
        ]);

        $pegawai3Mutasi = User::factory()->create([
            'name' => 'Ichtiar Rachmatullah',
            'email' => 'ichtiar.r@pajak.go.id',
            'nip' => '198703122011011002',
            'status' => 'AKTIF', // Pegawai yang sudah mutasi/keluar
        ]);

        // Input text hanya berisikan pegawai 1 dan pegawai 2
        $pastedText = "817931806\t198501152010121001\tDeril Amrizal Kholid\nBimo Heriyanto, S.E.";

        Livewire::test(AdminDashboard::class)
            ->set('employeeStatusInputMode', 'text')
            ->set('employeeStatusText', $pastedText)
            ->set('deactivateMissingEmployees', true)
            ->call('syncEmployeeStatus')
            ->assertHasNoErrors();

        // Pegawai 1 dan 2 harus AKTIF
        $this->assertEquals('AKTIF', $pegawai1->fresh()->status);
        $this->assertEquals('AKTIF', $pegawai2->fresh()->status);

        // Pegawai 3 tidak ada di daftar, harus menjadi NONAKTIF
        $this->assertEquals('NONAKTIF', $pegawai3Mutasi->fresh()->status);

        // Admin yang sedang login tidak boleh dinonaktifkan
        $this->assertEquals('AKTIF', $this->admin->fresh()->status);
    }

    public function test_inactive_employee_cannot_login_and_sees_notification(): void
    {
        $pegawaiNonaktif = User::factory()->create([
            'name' => 'Mantan Pegawai KPP',
            'email' => 'mantan.pegawai@pajak.go.id',
            'status' => 'NONAKTIF',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(LoginPage::class)
            ->set('email', 'mantan.pegawai@pajak.go.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertSet('errorMessage', 'Anda sudah bukan lagi pegawai KPP Madya Malang.');

        $this->assertGuest();
    }

    public function test_active_employee_can_login_successfully(): void
    {
        $pegawaiAktif = User::factory()->create([
            'name' => 'Pegawai Aktif KPP',
            'email' => 'pegawai.aktif@pajak.go.id',
            'status' => 'AKTIF',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(LoginPage::class)
            ->set('email', 'pegawai.aktif@pajak.go.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($pegawaiAktif);
    }

    public function test_admin_can_sync_employee_status_via_csv_file(): void
    {
        $this->actingAs($this->admin);

        $pegawai = User::factory()->create([
            'name' => 'Sukirman',
            'email' => 'sukirman@pajak.go.id',
            'status' => 'NONAKTIF',
        ]);

        $csvContent = "No,NIP Pendek,NIP Panjang,Nama Pegawai\n1,060012345,198001012005011001,Sukirman";
        $file = UploadedFile::fake()->createWithContent('daftar_pegawai.csv', $csvContent);

        Livewire::test(AdminDashboard::class)
            ->set('employeeStatusInputMode', 'file')
            ->set('employeeStatusFile', $file)
            ->call('syncEmployeeStatus')
            ->assertHasNoErrors();

        $this->assertEquals('AKTIF', $pegawai->fresh()->status);
    }

    public function test_viewer_cannot_sync_employee_status(): void
    {
        $this->actingAs($this->viewer);

        Livewire::test(AdminDashboard::class)
            ->set('employeeStatusText', 'Nama Pegawai')
            ->call('syncEmployeeStatus')
            ->assertSet('toastMessage', 'Akses ditolak: Hanya pengurus yang berwenang memperbarui status pegawai.');
    }
}

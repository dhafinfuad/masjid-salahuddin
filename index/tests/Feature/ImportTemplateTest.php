<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ImportTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Pengurus',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $this->jamaah = User::create([
            'name' => 'Jamaah Warga',
            'email' => 'jamaah@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);
    }

    public function test_admin_can_download_all_import_templates(): void
    {
        $this->actingAs($this->admin);

        $types = [
            'kajian_pekanan',
            'kajian_jumat',
            'petugas_sholat',
            'agenda_akbar',
            'keuangan',
            'kas',
            'potongan_sosial',
            'pegawai',
        ];

        foreach ($types as $type) {
            $response = $this->get(route('admin.template-import', $type));
            $response->assertStatus(200);
            $this->assertStringContainsString('spreadsheetml.sheet', (string) $response->headers->get('Content-Type'));
            $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
        }
    }

    public function test_guest_or_regular_jamaah_cannot_download_templates(): void
    {
        // Guest
        $response = $this->get(route('admin.template-import', 'pegawai'));
        $response->assertRedirect(route('login'));

        // Jamaah
        $this->actingAs($this->jamaah);
        $response = $this->get(route('admin.template-import', 'pegawai'));
        $response->assertStatus(403);
    }

    public function test_invalid_template_type_returns_404(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.template-import', 'invalid_unknown_type'));
        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Kajian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McTextBackButtonTest extends TestCase
{
    use RefreshDatabase;

    protected Kajian $jumat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jumat = Kajian::create([
            'title' => 'Khutbah Jumat Berkah',
            'type' => 'jumat',
            'date' => now()->next('Friday')->toDateString(),
            'time_display' => '11:45 WIB',
            'speaker_name' => 'Ust. Dr. Fulan',
            'khatib_name' => 'Ust. Dr. Fulan',
            'muadzin_name' => 'Bilal Fulan',
            'mc_name' => 'MC Fulan',
        ]);
    }

    /**
     * Tamu/Jamaah umum yang belum login saat membuka teks MC harus mendapatkan tombol kembali ke portal kegiatan.
     */
    public function test_guest_user_back_button_points_to_portal_kegiatan(): void
    {
        $response = $this->get(route('admin.kajian.teks-mc', $this->jumat->id));

        $response->assertStatus(200);

        // Harus mengarah ke portal kegiatan, bukan ke /admin/kajian atau /login
        $response->assertSee('href="' . route('portal.kegiatan') . '"', false);
        $response->assertDontSee('href="' . url('/admin/kajian') . '"', false);

        // Harus memiliki guard window.history.back()
        $response->assertSee('window.history.back()', false);
    }

    /**
     * User yang membuka teks MC dengan parameter from=portal harus mendapatkan tombol kembali ke portal.
     */
    public function test_explicit_from_portal_parameter_points_to_portal_kegiatan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Bahkan jika yang mengakses adalah admin yang sudah login, jika ada parameter from=portal, tombol kembali tetap ke portal kegiatan
        $response = $this->actingAs($admin)->get(route('admin.kajian.teks-mc', ['id' => $this->jumat->id, 'from' => 'portal']));

        $response->assertStatus(200);
        $response->assertSee('href="' . route('portal.kegiatan') . '"', false);
        $response->assertDontSee('href="' . url('/admin/kajian') . '"', false);
    }

    /**
     * Admin yang membuka dari dashboard admin dengan from=admin mendapatkan tombol kembali ke admin kegiatan.
     */
    public function test_admin_user_with_from_admin_parameter_points_to_admin_kegiatan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.kajian.teks-mc', ['id' => $this->jumat->id, 'from' => 'admin']));

        $response->assertStatus(200);
        $response->assertSee('href="' . url('/admin/kegiatan') . '"', false);
    }
}

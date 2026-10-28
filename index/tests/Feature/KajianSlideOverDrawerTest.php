<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianSlideOverDrawerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 10:00:00');
        MasjidSetting::getActive();

        $this->adminUser = User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'master@masjidsalahuddin.id',
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @test */
    public function test_kegiatan_tab_renders_slide_over_drawer_with_teleportation_and_backdrop()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/kegiatan');
        $response->assertOk();

        // 1. Verifikasi template x-teleport="body" agar modal terbebas dari siklus morphing Livewire tab
        $response->assertSee('x-teleport="body"', false);

        // 2. Verifikasi keberadaan backdrop pengunci layar penuh (z-[100])
        $response->assertSee('fixed inset-0 z-[100] overflow-hidden', false);
        $response->assertSee('fixed inset-0 bg-slate-900/60 backdrop-blur-xs', false);

        // 3. Verifikasi drawer panel sisi kanan (translate-x-full / translate-x-0)
        $response->assertSee('translate-x-full', false);
        $response->assertSee('translate-x-0', false);

        // 4. Verifikasi tombol Batal dan Simpan di footer sticky
        $response->assertSee('closeKajianModal()', false);
        $response->assertSee('saveKajian', false);
    }

    /** @test */
    public function test_slide_over_drawer_lifecycle_and_livewire_events()
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kegiatanSubTab', 'pekanan')
            // Buka drawer pembuatan jadwal
            ->call('openCreateKajian', 'pekanan')
            ->assertSet('showKajianModal', true)
            ->assertSet('kajianType', 'pekanan')
            ->assertDispatched('open-kajian-modal')
            // Tutup drawer
            ->call('closeKajianModal')
            ->assertSet('showKajianModal', false)
            ->assertDispatched('close-kajian-modal');
    }

    /** @test */
    public function test_saving_kajian_from_drawer_persists_data_and_closes_drawer()
    {
        $payload = [
            'id' => null,
            'type' => 'pekanan',
            'date' => '2026-10-20',
            'time_display' => '09:00 - 11:30',
            'title' => 'Kajian Rutin Sirah Nabawiyah',
            'speaker_name' => 'Ustadz Khalid Basalamah',
            'speaker_phone' => '081234567890',
            'is_holiday_disabled' => false,
        ];

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->call('openCreateKajian', 'pekanan')
            ->assertSet('showKajianModal', true)
            ->call('saveKajian', $payload)
            ->assertSet('showKajianModal', false)
            ->assertDispatched('close-kajian-modal');

        $this->assertDatabaseHas('kajians', [
            'title' => 'Kajian Rutin Sirah Nabawiyah',
            'speaker_name' => 'Ustadz Khalid Basalamah',
            'type' => 'pekanan',
        ]);
    }
}

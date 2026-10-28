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
    public function test_kegiatan_tab_renders_solid_centered_modal_without_buggy_teleportation()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/kegiatan');
        $response->assertOk();

        // 1. Pastikan modal tidak menggunakan x-teleport="body" (penyebab duplikasi dan modal tembus pandang di Livewire)
        $this->assertStringNotContainsString('x-teleport="body"', $response->getContent());

        // 2. Verifikasi keberadaan backdrop pengunci layar penuh dengan z-index inline 9999
        $response->assertSee('z-index: 9999', false);
        $response->assertSee('rgba(15, 23, 42, 0.75)', false);

        // 3. Verifikasi modal card putih solid dengan z-index 10000
        $response->assertSee('z-index: 10000', false);
        $response->assertSee('background-color: #ffffff', false);

        // 4. Verifikasi header memiliki latar belakang solid navy (#0b2b54) dan teks putih (#ffffff)
        $response->assertSee('background-color: #0b2b54', false);
        $response->assertSee('color: #ffffff', false);

        // 5. Verifikasi tombol Batal dan Simpan di footer
        $response->assertSee('closeKajianModal()', false);
        $response->assertSee('saveKajian', false);
    }

    /** @test */
    public function test_kajian_modal_lifecycle_and_livewire_events()
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kegiatanSubTab', 'pekanan')
            // Buka modal pembuatan jadwal
            ->call('openCreateKajian', 'pekanan')
            ->assertSet('showKajianModal', true)
            ->assertSet('kajianType', 'pekanan')
            ->assertDispatched('open-kajian-modal')
            // Tutup modal
            ->call('closeKajianModal')
            ->assertSet('showKajianModal', false)
            ->assertDispatched('close-kajian-modal');
    }

    /** @test */
    public function test_saving_kajian_from_modal_persists_data_and_closes_modal()
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

<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianModalStateTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'Master',
            'status' => 'AKTIF',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function test_kegiatan_subtab_switching_to_jumat_preserves_state(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->assertSet('currentTab', 'kegiatan')
            ->assertSet('kegiatanSubTab', 'pekanan')
            ->call('switchKegiatanSubTab', 'jumat')
            ->assertSet('kegiatanSubTab', 'jumat')
            ->assertSet('kajianSubTab', 'jumat');
    }

    /** @test */
    public function test_open_create_kajian_sets_modal_state_and_dispatches_event(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kegiatanSubTab', 'jumat')
            ->call('openCreateKajian', 'jumat')
            ->assertSet('showKajianModal', true)
            ->assertSet('kajianType', 'jumat')
            ->assertDispatched('open-kajian-modal')
            ->assertSeeHtml("showKajianModal: true");
    }

    /** @test */
    public function test_close_kajian_modal_resets_state_and_dispatches_event(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('showKajianModal', true)
            ->call('closeKajianModal')
            ->assertSet('showKajianModal', false)
            ->assertDispatched('close-kajian-modal')
            ->assertSeeHtml("showKajianModal: false");
    }

    /** @test */
    public function test_save_kajian_closes_modal_and_resets_state(): void
    {
        $payload = [
            'type' => 'jumat',
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'time_display' => '11:45 - 12:45',
            'title' => 'Khutbah Jumat Berkah',
            'khatib_name' => 'Ustadz Ahmad',
            'mc_name' => 'Bambang',
            'muadzin_name' => 'Bilal',
            'khatib_phone' => '081234567890',
            'mc_notes' => 'Catatan DKM',
        ];

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('showKajianModal', true)
            ->call('saveKajian', $payload)
            ->assertSet('showKajianModal', false)
            ->assertDispatched('close-kajian-modal');

        $this->assertDatabaseHas('kajians', [
            'title' => 'Khutbah Jumat Berkah',
            'type' => 'jumat',
            'khatib_name' => 'Ustadz Ahmad',
        ]);
    }
}

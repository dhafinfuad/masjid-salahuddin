<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\MasjidSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianModalActionTokenTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-28 10:00:00');
        MasjidSetting::getActive();

        $this->adminUser = User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'master@masjidsalahuddin.id',
            'role' => 'Master',
            'status' => 'AKTIF',
            'email_verified_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @test */
    public function test_open_create_kajian_with_action_token_dispatches_event_with_token_and_type(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kegiatanSubTab', 'pekanan')
            ->call('openCreateKajian', 'pekanan', 'act-token-pekanan-123')
            ->assertSet('showKajianModal', true)
            ->assertSet('kajianType', 'pekanan')
            ->assertSet('actionToken', 'act-token-pekanan-123')
            ->assertDispatched('open-kajian-modal', function ($event, $params) {
                return ($params['token'] ?? null) === 'act-token-pekanan-123'
                    && ($params['type'] ?? null) === 'pekanan';
            });
    }

    /** @test */
    public function test_close_kajian_modal_clears_action_token(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->call('openCreateKajian', 'pekanan', 'act-token-to-clear')
            ->assertSet('actionToken', 'act-token-to-clear')
            ->call('closeKajianModal')
            ->assertSet('showKajianModal', false)
            ->assertSet('actionToken', null)
            ->assertDispatched('close-kajian-modal');
    }

    /** @test */
    public function test_rapid_subtab_switching_preserves_token_isolation(): void
    {
        // Simulasi skenario race condition:
        // 1. User klik Tambah Jadwal di Tab Kajian Pekanan (token: tok-pekanan)
        // 2. User menutup modal
        // 3. User beralih ke subtab Jumat dan klik Tambah Jadwal (token: tok-jumat)
        // 4. Pastikan state server mencerminkan token & type Jumat secara terisolasi tanpa bentrok
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kegiatanSubTab', 'pekanan')
            ->call('openCreateKajian', 'pekanan', 'tok-pekanan')
            ->assertSet('actionToken', 'tok-pekanan')
            ->assertSet('kajianType', 'pekanan')
            // User menutup modal
            ->call('closeKajianModal')
            ->assertSet('actionToken', null)
            // User pindah ke subtab jumat
            ->call('switchKegiatanSubTab', 'jumat')
            ->assertSet('kegiatanSubTab', 'jumat')
            // User buka modal khutbah jumat
            ->call('openCreateKajian', 'jumat', 'tok-jumat')
            ->assertSet('actionToken', 'tok-jumat')
            ->assertSet('kajianType', 'jumat')
            ->assertDispatched('open-kajian-modal', function ($event, $params) {
                return ($params['token'] ?? null) === 'tok-jumat'
                    && ($params['type'] ?? null) === 'jumat';
            });
    }

    /** @test */
    public function test_get_default_kajian_time_accepts_action_token_nonce(): void
    {
        $component = Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan']);

        $result = $component->instance()->getDefaultKajianTime('pekanan', '2026-10-28', 'token-nonce-check');
        $this->assertNotNull($result);
    }

    /** @test */
    public function test_rendered_view_contains_action_token_nonce_guard_logic(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/kegiatan');
        $response->assertOk();

        // Verifikasi keberadaan token management di Alpine x-data
        $response->assertSee('currentActionToken: null', false);
        $response->assertSee('startAction(scope)', false);
        $response->assertSee('cancelAction()', false);
        $response->assertSee('startAction(\'kajian-\'', false);

        // Verifikasi guard pada event listener open-kajian-modal
        $response->assertSee('token === currentActionToken', false);
    }
}

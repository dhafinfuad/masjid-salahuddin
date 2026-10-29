<?php

namespace Tests\Feature;

use App\Livewire\Admin\PosterSettingManager;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PosterSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JarkomanSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->adminUser = User::factory()->create([
            'name' => 'Admin DKM',
            'email' => 'admin@masjidsalahuddin.id',
            'role' => 'Master',
        ]);
    }

    /** @test */
    public function test_poster_setting_fallback_includes_default_jarkoman_templates()
    {
        $config = PosterSetting::defaultFallbackConfig();

        $this->assertArrayHasKey('jarkom_kajian_template', $config);
        $this->assertArrayHasKey('jarkom_jumat_template', $config);
        $this->assertStringContainsString('{pemateri}', $config['jarkom_kajian_template']);
        $this->assertStringContainsString('{khatib}', $config['jarkom_jumat_template']);
    }

    /** @test */
    public function test_poster_setting_manager_renders_mode_switcher_and_inline_template_wa()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PosterSettingManager::class, ['isEmbedded' => true])
            ->assertSee('Template WA')
            ->assertSee('Poster Kajian')
            ->assertSee('Format Template Teks Kajian')
            ->assertSee('Simpan Konfigurasi')
            ->assertDontSee('Data Ustadz')
            ->assertDontSee('Edit Jarkom')
            ->assertDontSee('downloadPreviewHD()');
    }

    /** @test */
    public function test_poster_setting_manager_renders_subtab_navigation_template_selector_and_can_switch_template()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PosterSettingManager::class, ['isEmbedded' => true])
            ->assertSee('1. Kajian Tematik')
            ->assertSee('2. Rutin Pekanan')
            ->assertDontSee('Pilihan Desain & Preset Baku')
            ->assertDontSee('Desain Template Aktif (Rasio 1:1)')
            ->call('selectTemplate', 2)
            ->assertSet('template', 2)
            ->call('selectTemplate', 1)
            ->assertSet('template', 1);
    }

    /** @test */
    public function test_admin_can_update_jarkoman_templates()
    {
        $this->actingAs($this->adminUser);

        $customKajian = "INFO KAJIAN SPESIAL\nPemateri: {pemateri}\nTanggal: {hari_tanggal}\nTempat: {tempat}";
        $customJumat = "INFO JUMAT BERKAH\nKhatib: {khatib}\nMuadzin: {muadzin}\nDzuhur: {waktu_dzuhur}";

        Livewire::test(PosterSettingManager::class)
            ->set('jarkom_kajian_template', $customKajian)
            ->set('jarkom_jumat_template', $customJumat)
            ->call('saveJarkom')
            ->assertDispatched('close-jarkom-modal')
            ->assertDispatched('toast');

        $activeConfig = PosterSetting::getAppConfig();
        $this->assertEquals($customKajian, $activeConfig['jarkom_kajian_template']);
        $this->assertEquals($customJumat, $activeConfig['jarkom_jumat_template']);
    }

    /** @test */
    public function test_admin_can_save_via_bottom_sticky_save_button()
    {
        $this->actingAs($this->adminUser);

        $customKajian = "KAJIAN VIA STICKY SAVE:\nPemateri: {pemateri}";

        Livewire::test(PosterSettingManager::class)
            ->set('jarkom_kajian_template', $customKajian)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $activeConfig = PosterSetting::getAppConfig();
        $this->assertEquals($customKajian, $activeConfig['jarkom_kajian_template']);
    }

    /** @test */
    public function test_admin_can_reset_jarkoman_templates_to_default()
    {
        $this->actingAs($this->adminUser);

        // First set custom
        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => 1,
                'config' => array_merge(PosterSetting::defaultFallbackConfig(), [
                    'jarkom_kajian_template' => 'Custom text',
                    'jarkom_jumat_template' => 'Custom jumat text',
                ]),
            ]
        );

        Livewire::test(PosterSettingManager::class)
            ->call('resetJarkomToDefault', 'kajian')
            ->assertSet('jarkom_kajian_template', PosterSetting::defaultJarkomKajianTemplate());

        $activeConfig = PosterSetting::getAppConfig();
        $this->assertEquals(PosterSetting::defaultJarkomKajianTemplate(), $activeConfig['jarkom_kajian_template']);
    }

    /** @test */
    public function test_kajian_whatsapp_broadcast_text_uses_customized_template()
    {
        $customKajian = "SERUAN DAKWAH:\nUstadz: {pemateri}\nWaktu: {waktu}\nJudul: {judul}";

        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => 1,
                'config' => array_merge(PosterSetting::defaultFallbackConfig(), [
                    'jarkom_kajian_template' => $customKajian,
                ]),
            ]
        );

        $kajian = Kajian::create([
            'type' => 'pekanan',
            'date' => Carbon::parse('2026-10-15'),
            'time_display' => '15:30 WIB',
            'title' => 'Tazkiyatun Nafs',
            'speaker_name' => 'Ust. Abdullah',
        ]);

        $broadcastText = $kajian->whatsapp_broadcast_text;

        $this->assertStringContainsString('SERUAN DAKWAH:', $broadcastText);
        $this->assertStringContainsString('Ust. Abdullah', $broadcastText);
        $this->assertStringContainsString('15:30 WIB', $broadcastText);
        $this->assertStringContainsString('Tazkiyatun Nafs', $broadcastText);
    }

    /** @test */
    public function test_jumat_whatsapp_broadcast_text_uses_customized_template()
    {
        $customJumat = "JUMATAN DI {masjid}:\nKhatib Utama: {khatib}\nMuadzin: {muadzin}\nMC: {mc}";

        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => 1,
                'config' => array_merge(PosterSetting::defaultFallbackConfig(), [
                    'jarkom_jumat_template' => $customJumat,
                    'masjid_line2' => 'Masjid Raya Salahuddin',
                ]),
            ]
        );

        $jumat = Kajian::create([
            'type' => 'jumat',
            'date' => Carbon::parse('2026-10-16'),
            'khatib_name' => 'Ust. Dr. Hamzah',
            'muadzin_name' => 'Bilal R.',
            'mc_name' => 'H. Salim',
            'title' => 'Sholat Jumat',
        ]);

        $broadcastText = $jumat->whatsapp_broadcast_text;

        $this->assertStringContainsString('JUMATAN DI Masjid Raya Salahuddin:', $broadcastText);
        $this->assertStringContainsString('Ust. Dr. Hamzah', $broadcastText);
        $this->assertStringContainsString('Bilal R.', $broadcastText);
        $this->assertStringContainsString('H. Salim', $broadcastText);
    }
}

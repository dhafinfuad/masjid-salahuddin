<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use App\Models\Category;
use App\Models\Event;
use App\Models\MasjidSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_portal_page_returns_a_successful_response(): void
    {
        MasjidSetting::getActive();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Masjid Salahuddin');
    }

    public function test_the_tv_display_page_returns_a_successful_response(): void
    {
        MasjidSetting::getActive();

        $response = $this->get('/display');
        $response->assertStatus(200);
        $response->assertSee('WAKTU SEKARANG');
        $response->assertSee('SHOLAT BERIKUTNYA');
        $response->assertSee('JADWAL SHOLAT LIMA WAKTU');
    }

    public function test_jamaah_can_register_for_social_program(): void
    {
        MasjidSetting::getActive();

        $program = \App\Models\SocialProgram::create([
            'name' => 'Santunan Anak Yatim',
            'slug' => 'santunan-anak-yatim',
            'category' => 'yatim',
            'period_type' => 'bulanan',
            'target_amount' => 10000000,
            'current_amount' => 0,
            'participants_count' => 0,
            'icon' => 'heart-handshake',
            'color_theme' => 'emerald',
            'status' => 'AKTIF',
        ]);

        Livewire::test(PortalPage::class)
            ->call('openSocialRegisterModal', $program->id)
            ->set('socialParticipantName', 'Ahmad Fauzan')
            ->set('socialParticipantAmount', '250000')
            ->call('submitSocialRegistration')
            ->assertSet('socialRegisterSuccess', true)
            ->assertSee('Alhamdulillah!');

        $this->assertDatabaseHas('program_participants', [
            'social_program_id' => $program->id,
            'name' => 'Ahmad Fauzan',
        ]);
    }
}

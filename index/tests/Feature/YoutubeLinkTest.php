<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Portal\KegiatanMasjidPage;
use App\Livewire\Portal\PortalPage;
use App\Models\Agenda;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class YoutubeLinkTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Kajian $sampleKajian;
    protected Agenda $sampleAgenda;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->adminUser = User::factory()->create([
            'name' => 'Admin DKM',
            'email' => 'admin@masjidsalahuddin.id',
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $this->regularUser = User::factory()->create([
            'name' => 'Jamaah Biasa',
            'email' => 'jamaah@masjidsalahuddin.id',
            'role' => 'Jamaah',
        ]);

        $this->sampleKajian = Kajian::create([
            'type' => 'pekanan',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time_display' => '09:00 - 11:30',
            'title' => 'Tafsir Surat Al-Kahfi',
            'speaker_name' => 'Ustadz Fulan',
            'notula' => '# Notula Pembahasan Al-Kahfi',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->sampleAgenda = Agenda::create([
            'title' => 'Kajian Akbar Ramadhan 1447H',
            'description' => 'Persiapan menyambut bulan suci ramadhan',
            'event_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'budget' => 5000000,
            'status' => 'Direncanakan',
            'youtube_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);
    }

    /** @test */
    public function test_admin_can_save_youtube_link_for_kajian()
    {
        $this->actingAs($this->adminUser);

        $newUrl = 'https://www.youtube.com/watch?v=newVideo123';

        Livewire::test(AdminDashboard::class)
            ->call('openYoutubeModal', $this->sampleKajian->id, 'kajian')
            ->assertSet('youtubeItemId', $this->sampleKajian->id)
            ->assertSet('youtubeItemType', 'kajian')
            ->assertSet('showYoutubeModal', true)
            ->assertDispatched('open-youtube-modal')
            ->call('saveYoutubeLink', $this->sampleKajian->id, $newUrl, 'kajian')
            ->assertDispatched('toast')
            ->assertDispatched('youtube-saved');

        $this->assertEquals($newUrl, $this->sampleKajian->fresh()->youtube_url);
    }

    /** @test */
    public function test_admin_can_save_youtube_link_for_agenda()
    {
        $this->actingAs($this->adminUser);

        $newUrl = 'https://youtu.be/agendaLiveStream';

        Livewire::test(AdminDashboard::class)
            ->call('openYoutubeModal', $this->sampleAgenda->id, 'agenda')
            ->assertSet('youtubeItemId', $this->sampleAgenda->id)
            ->assertSet('youtubeItemType', 'agenda')
            ->assertSet('showYoutubeModal', true)
            ->assertDispatched('open-youtube-modal')
            ->call('saveYoutubeLink', $this->sampleAgenda->id, $newUrl, 'agenda')
            ->assertDispatched('toast')
            ->assertDispatched('youtube-saved');

        $this->assertEquals($newUrl, $this->sampleAgenda->fresh()->youtube_url);
    }

    /** @test */
    public function test_admin_can_remove_youtube_link()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(AdminDashboard::class)
            ->call('saveYoutubeLink', $this->sampleKajian->id, '', 'kajian')
            ->assertDispatched('toast')
            ->assertDispatched('youtube-saved');

        $this->assertNull($this->sampleKajian->fresh()->youtube_url);
    }

    /** @test */
    public function test_invalid_youtube_url_is_rejected_with_notification()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(AdminDashboard::class)
            ->call('saveYoutubeLink', $this->sampleKajian->id, 'bukan-url-valid', 'kajian')
            ->assertDispatched('toast');

        // URL in database should remain unchanged
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $this->sampleKajian->fresh()->youtube_url);
    }

    /** @test */
    public function test_regular_user_cannot_save_youtube_link()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(AdminDashboard::class)
            ->call('saveYoutubeLink', $this->sampleKajian->id, 'https://www.youtube.com/watch?v=shouldNotSave', 'kajian')
            ->assertDispatched('toast');

        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $this->sampleKajian->fresh()->youtube_url);
    }

    /** @test */
    public function test_kajian_form_save_supports_youtube_url()
    {
        $this->actingAs($this->adminUser);

        $ytUrl = 'https://www.youtube.com/watch?v=fullFormKajian';

        Livewire::test(AdminDashboard::class)
            ->call('saveKajian', [
                'type' => 'pekanan',
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time_display' => '09:00 - 11:30',
                'title' => 'Kajian Pekanan Bersama YouTube',
                'speaker_name' => 'Ustadz Ahmad',
                'youtube_url' => $ytUrl,
            ])
            ->assertDispatched('toast');

        $this->assertDatabaseHas('kajians', [
            'title' => 'Kajian Pekanan Bersama YouTube',
            'youtube_url' => $ytUrl,
        ]);
    }

    /** @test */
    public function test_agenda_form_save_supports_youtube_url()
    {
        $this->actingAs($this->adminUser);

        $ytUrl = 'https://youtu.be/fullFormAgenda';

        Livewire::test(AdminDashboard::class)
            ->call('saveAgenda', [
                'title' => 'Agenda Akbar Bersama YouTube',
                'event_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'budget' => 2000000,
                'status' => 'Direncanakan',
                'youtube_url' => $ytUrl,
            ])
            ->assertDispatched('toast');

        $this->assertDatabaseHas('agendas', [
            'title' => 'Agenda Akbar Bersama YouTube',
            'youtube_url' => $ytUrl,
        ]);
    }

    /** @test */
    public function test_youtube_link_appears_on_portal_home_page_beside_notula()
    {
        Livewire::test(PortalPage::class)
            ->assertSeeHtml($this->sampleKajian->youtube_url)
            ->assertSeeHtml('YouTube')
            ->assertSeeHtml('Notula');
    }

    /** @test */
    public function test_youtube_link_appears_on_portal_kegiatan_page_beside_notula()
    {
        Livewire::test(KegiatanMasjidPage::class)
            ->assertSeeHtml($this->sampleKajian->youtube_url)
            ->assertSeeHtml('YouTube')
            ->assertSeeHtml('Notula');
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Portal\ProfilMasjidPage;
use App\Models\MasjidSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalProfilMasjidTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_render_profil_masjid_page_successfully(): void
    {
        $response = $this->get(route('portal.profil'));
        $response->assertStatus(200);
        $response->assertSee('Profil Masjid');
        $response->assertSee('Kepengurusan');
    }

    public function test_profil_masjid_page_handles_custom_or_partial_takmir_documents_gracefully(): void
    {
        $settings = MasjidSetting::getActive();
        $settings->takmir_documents = [
            'sk' => [
                'title' => 'Surat Keputusan Custom',
                'number' => 'KEP-CUSTOM/2026',
                // deliberately omit 'icon', 'badge', 'badge_color'
            ],
            'sk_lengkap' => [
                'title' => 'SK Lengkap Dokumen',
                'file' => 'custom.pdf',
            ],
        ];
        $settings->save();

        $response = $this->get(route('portal.profil'));
        $response->assertStatus(200);
        $response->assertSee('Surat Keputusan Custom');

        Livewire::test(ProfilMasjidPage::class)
            ->set('activeTab', 'pengurus')
            ->assertStatus(200)
            ->assertSee('Surat Keputusan Custom');
    }
}

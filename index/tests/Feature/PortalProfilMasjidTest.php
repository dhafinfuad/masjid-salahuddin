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

    public function test_only_single_sk_card_and_unduh_pdf_button_are_rendered(): void
    {
        $response = $this->get(route('portal.profil'));
        $response->assertStatus(200);

        // SK Card and Unduh PDF button must exist
        $response->assertSee('Surat Keputusan Kepala KPP Madya Malang');
        $response->assertSee('Unduh PDF');
        $response->assertSee(route('admin.kepengurusan.download-sk-pdf'));

        // Lampiran I, Lampiran II, and Buka Dokumen must NOT exist
        $response->assertDontSee('Lampiran I: Susunan Pengurus Takmir');
        $response->assertDontSee('Lampiran II: Penjabaran Tugas & Wewenang');
        $response->assertDontSee('Buka Dokumen');
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
            ->assertSee('Surat Keputusan Custom')
            ->assertSee('Unduh PDF')
            ->assertDontSee('Lampiran I: Susunan Pengurus Takmir')
            ->assertDontSee('Buka Dokumen');
    }
}

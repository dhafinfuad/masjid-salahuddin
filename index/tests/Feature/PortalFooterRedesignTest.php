<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use App\Livewire\Portal\ProfilMasjidPage;
use App\Livewire\Portal\SaranKritikPage;
use App\Models\MasjidSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalFooterRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_homepage_renders_gov_navy_ribbon_footer_with_youtube_channel(): void
    {
        MasjidSetting::updateOrCreate(
            ['id' => 1],
            ['name' => 'Masjid Salahuddin']
        );

        $test = Livewire::test(PortalPage::class)
            ->assertSee('Masjid Salahuddin')
            ->assertSee('KPP Madya Malang')
            ->assertDontSee('Kementerian Keuangan RI')
            ->assertSee('Channel YouTube')
            ->assertSee('https://www.youtube.com/@MasjidSalahuddinKPPMadyaMalang');

        $html = $test->html();

        // Ensure footer uses Gov-Navy Solid Ribbon styling
        $this->assertStringContainsString('class="bg-gov-navy text-white rounded-xl shadow-xs', $html);
        $this->assertStringContainsString('bg-gov-pattern', $html);
        $this->assertStringContainsString('href="https://www.youtube.com/@MasjidSalahuddinKPPMadyaMalang"', $html);

        // Ensure official mosque logo is rendered
        $this->assertStringContainsString('Logo Masjid Salahuddin.webp', $html);

        // Ensure YouTube icon and text are white
        $this->assertStringContainsString('Channel YouTube</span>', $html);
        $this->assertStringContainsString('group-hover:scale-110 transition-transform', $html);
    }

    public function test_profil_and_saran_pages_render_consistent_navy_footer(): void
    {
        MasjidSetting::updateOrCreate(
            ['id' => 1],
            ['name' => 'Masjid Salahuddin']
        );

        Livewire::test(ProfilMasjidPage::class)
            ->assertSee('Masjid Salahuddin')
            ->assertSee('KPP Madya Malang')
            ->assertDontSee('Kementerian Keuangan RI')
            ->assertSee('Channel YouTube')
            ->assertSee('https://www.youtube.com/@MasjidSalahuddinKPPMadyaMalang');

        Livewire::test(SaranKritikPage::class)
            ->assertSee('Masjid Salahuddin')
            ->assertSee('KPP Madya Malang')
            ->assertDontSee('Kementerian Keuangan RI')
            ->assertSee('Channel YouTube')
            ->assertSee('https://www.youtube.com/@MasjidSalahuddinKPPMadyaMalang');
    }
}


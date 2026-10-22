<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalNextKajianHighlightTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
    }

    public function test_next_kajian_card_gets_amber_border_and_ring_style(): void
    {
        // Set tanggal 'hari ini' ke 22 Oktober 2026
        Carbon::setTestNow('2026-10-22 08:00:00');

        $jumatKajian = Kajian::create([
            'type' => 'jumat',
            'title' => 'Khutbah Sholat Jumat',
            'date' => '2026-10-23',
            'time_display' => '11:27 WIB',
            'khatib_name' => 'Ust. Deden Ferry Yuliantoro S.Si S.Pd',
            'muadzin_name' => 'Deril Amrizal Kholid',
            'mc_name' => 'Lukman Hakim',
        ]);

        $pekananKajian = Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Hadits',
            'date' => '2026-10-26',
            'time_display' => '15:01 WIB',
            'speaker_name' => 'M. Yasak Lc MA',
        ]);

        // Uji pada tanggal 22 Oktober 2026
        // Kajian berikutnya harus Khutbah Jumat 23 Okt 2026
        $component = Livewire::test(PortalPage::class);
        $component->assertViewHas('nextKajianId', $jumatKajian->id);

        $html = $component->html();

        // Verifikasi card 23 Okt memiliki style border-amber-500 ring-1 sm:ring-1 ring-amber-400/30
        $this->assertStringContainsString('border border-amber-500 ring-1 sm:ring-1 ring-amber-400/30', $html);

        // Pastikan container card Khutbah Jumat memiliki kelas border amber
        $jumatCardSnippet = 'wire:key="portal-kajian-card-' . $jumatKajian->id . '"';
        $this->assertStringContainsString($jumatCardSnippet, $html);

        // Pastikan container card Pekanan memiliki border-gov-border biasa
        $pekananCardSnippet = 'wire:key="portal-kajian-card-' . $pekananKajian->id . '"';
        $this->assertStringContainsString($pekananCardSnippet, $html);

        // Sekarang ubah tanggal 'hari ini' ke 24 Oktober 2026
        // Kajian 23 Okt sudah berlalu, sehingga yang mendapat style amber adalah Kajian Pekanan 26 Okt 2026
        Carbon::setTestNow('2026-10-24 08:00:00');

        $component2 = Livewire::test(PortalPage::class);
        $component2->assertViewHas('nextKajianId', $pekananKajian->id);

        $html2 = $component2->html();
        $this->assertStringContainsString('border border-amber-500 ring-1 sm:ring-1 ring-amber-400/30', $html2);

        // Reset TestNow
        Carbon::setTestNow();
    }
}

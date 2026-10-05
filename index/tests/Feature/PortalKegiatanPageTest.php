<?php

namespace Tests\Feature;

use App\Livewire\Portal\KegiatanMasjidPage;
use App\Models\Agenda;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalKegiatanPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
    }

    public function test_portal_home_renders_selengkapnya_button_for_kegiatan_masjid(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee(route('portal.kegiatan'));
        $response->assertSee('Selengkapnya');
    }

    public function test_kegiatan_masjid_page_renders_successfully(): void
    {
        $response = $this->get(route('portal.kegiatan'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Kegiatan & Ibadah Masjid', false);
        $response->assertSee('Filter Bulan');
        $response->assertSee('wire:loading.inline-flex', false);
        $response->assertSee('Memperbarui kegiatan...');
    }

    public function test_kegiatan_masjid_page_filters_by_category_and_month(): void
    {
        // 1. Create a Friday prayer in September 2026
        $jumat = Kajian::create([
            'type' => 'jumat',
            'date' => '2026-09-25',
            'time_display' => '11:27 WIB',
            'title' => 'Khutbah Jumat Syawal',
            'khatib_name' => 'Ust. M. Yasak Lc MA',
            'muadzin_name' => 'Khudori',
            'mc_name' => 'Alan Irfansyah',
        ]);

        // 2. Create a Pekanan study in October 2026
        $pekanan = Kajian::create([
            'type' => 'pekanan',
            'date' => '2026-10-12',
            'time_display' => '15:01 WIB',
            'title' => 'Kajian Sirah Nabawiyah Spesial',
            'speaker_name' => 'Alvin Shohih',
        ]);

        // 3. Create an Agenda in September 2026
        $agenda = Agenda::create([
            'title' => 'Festival Muharram Ceria',
            'description' => 'Lomba mewarnai anak santri se-Malang',
            'event_date' => '2026-09-30',
            'budget' => 5000000,
            'status' => 'Direncanakan',
        ]);

        // Test Livewire component
        Livewire::test(KegiatanMasjidPage::class)
            ->set('selectedYear', 2026)
            ->set('selectedMonth', 9)
            ->assertSee('Ust. M. Yasak Lc MA')
            ->assertSee('Festival Muharram Ceria')
            ->assertDontSee('Kajian Sirah Nabawiyah Spesial')
            // Switch to month 10 (October)
            ->set('selectedMonth', 10)
            ->assertSee('Kajian Sirah Nabawiyah Spesial')
            ->assertDontSee('Ust. M. Yasak Lc MA')
            // Test category filter 'jumat' on Semua Bulan
            ->set('selectedMonth', 'all')
            ->call('setCategory', 'jumat')
            ->assertSee('Ust. M. Yasak Lc MA')
            ->assertDontSee('Festival Muharram Ceria')
            // Test search
            ->call('setCategory', 'all')
            ->set('search', 'Yasak')
            ->assertSee('Ust. M. Yasak Lc MA')
            ->assertDontSee('Festival Muharram Ceria');
    }

    public function test_kegiatan_masjid_page_pagination_renders_unified_bar_and_no_raw_translation_keys(): void
    {
        // Create 15 Kajian items in September 2026 to exceed perPage (12)
        for ($i = 1; $i <= 15; $i++) {
            Kajian::create([
                'type' => 'pekanan',
                'date' => '2026-09-' . str_pad((string) min($i, 28), 2, '0', STR_PAD_LEFT),
                'time_display' => '15:00 WIB',
                'title' => "Kajian Seri {$i}",
                'speaker_name' => "Pemateri {$i}",
            ]);
        }

        Livewire::test(KegiatanMasjidPage::class)
            ->set('selectedYear', 2026)
            ->set('selectedMonth', 9)
            // Never show raw translation keys
            ->assertDontSee('pagination.previous')
            ->assertDontSee('pagination.next')
            ->assertDontSee('pagination.showing')
            // Verify Indonesian info text
            ->assertSee('Menampilkan')
            ->assertSee('sampai')
            ->assertSee('dari')
            ->assertSee('15')
            // Verify unified bar styling
            ->assertSee('bg-gov-navy')
            ->assertSee('Kajian Seri 1')
            ->call('gotoPage', 2)
            ->assertDontSee('pagination.previous')
            ->assertDontSee('pagination.next')
            ->assertSee('Menampilkan')
            ->assertSee('bg-gov-navy');
    }
}


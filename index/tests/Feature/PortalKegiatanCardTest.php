<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use App\Models\Agenda;
use App\Models\Kajian;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalKegiatanCardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 10, 26, 10, 0, 0, 'Asia/Jakarta'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_portal_only_displays_two_upcoming_kajian_and_two_upcoming_jumat(): void
    {
        // 4 Kajian Umum mendatang
        Kajian::create(['title' => 'Kajian 1', 'type' => 'pekanan', 'date' => '2026-10-27']);
        Kajian::create(['title' => 'Kajian 2', 'type' => 'tematik', 'date' => '2026-10-29']);
        Kajian::create(['title' => 'Kajian 3', 'type' => 'pekanan', 'date' => '2026-11-03']);
        Kajian::create(['title' => 'Kajian 4', 'type' => 'pekanan', 'date' => '2026-11-10']);

        // 4 Khutbah Jumat mendatang
        Kajian::create(['title' => 'Khutbah 1', 'type' => 'jumat', 'khatib_name' => 'Khatib 1', 'date' => '2026-10-30']);
        Kajian::create(['title' => 'Khutbah 2', 'type' => 'jumat', 'khatib_name' => 'Khatib 2', 'date' => '2026-11-06']);
        Kajian::create(['title' => 'Khutbah 3', 'type' => 'jumat', 'khatib_name' => 'Khatib 3', 'date' => '2026-11-13']);
        Kajian::create(['title' => 'Khutbah 4', 'type' => 'jumat', 'khatib_name' => 'Khatib 4', 'date' => '2026-11-20']);

        $test = Livewire::test(PortalPage::class);
        $kajians = $test->viewData('kajians');

        // Total 4 card: 2 kajian umum + 2 khutbah jumat
        $this->assertCount(4, $kajians);

        $titles = $kajians->pluck('title')->filter()->values()->toArray();
        $this->assertContains('Kajian 1', $titles);
        $this->assertContains('Kajian 2', $titles);
        $this->assertNotContains('Kajian 3', $titles);
        $this->assertNotContains('Kajian 4', $titles);

        $khatibs = $kajians->pluck('khatib_name')->filter()->values()->toArray();
        $this->assertContains('Khatib 1', $khatibs);
        $this->assertContains('Khatib 2', $khatibs);
        $this->assertNotContains('Khatib 3', $khatibs);
        $this->assertNotContains('Khatib 4', $khatibs);
    }

    public function test_portal_agendas_are_scoped_to_current_year_dynamically(): void
    {
        $agenda2025 = Agenda::create([
            'title' => 'Agenda Tahun Lalu 2025',
            'event_date' => '2025-06-15',
            'status' => 'SELESAI',
            'budget' => 50000000,
        ]);

        $agenda2026 = Agenda::create([
            'title' => 'Perayaan Iduladha 1447 H',
            'event_date' => '2026-06-10',
            'status' => 'SELESAI',
            'budget' => 65000000,
            'report_pdf_path' => 'lpj/iduladha.pdf',
        ]);

        $agenda2027 = Agenda::create([
            'title' => 'Agenda Mendatang 2027',
            'event_date' => '2027-02-15',
            'status' => 'DRAFT',
            'budget' => 70000000,
        ]);

        $test = Livewire::test(PortalPage::class);
        $agendas = $test->viewData('agendas');

        $this->assertCount(1, $agendas);
        $this->assertEquals('Perayaan Iduladha 1447 H', $agendas->first()->title);

        // Simulasi berganti tahun ke 2027
        Carbon::setTestNow(Carbon::create(2027, 1, 15, 10, 0, 0, 'Asia/Jakarta'));
        $test2027 = Livewire::test(PortalPage::class);
        $agendas2027 = $test2027->viewData('agendas');

        $this->assertCount(1, $agendas2027);
        $this->assertEquals('Agenda Mendatang 2027', $agendas2027->first()->title);
    }

    public function test_portal_agenda_card_renders_unduh_lpj_button_when_report_pdf_path_exists(): void
    {
        $agenda = Agenda::create([
            'title' => 'Perayaan Iduladha 1447 H',
            'description' => 'Menyemarakkan hari raya Iduladha',
            'event_date' => '2026-06-10',
            'status' => 'SELESAI',
            'budget' => 65000000,
            'report_pdf_path' => 'lpj/iduladha.pdf',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        Livewire::test(PortalPage::class)
            ->assertSeeHtml('Unduh LPJ')
            ->assertSeeHtml(route('admin.agenda.lpj', $agenda->id))
            ->assertSeeHtml('min-w-[96px] justify-center inline-flex items-center')
            ->assertSeeHtml('whitespace-nowrap')
            ->assertSeeHtml('10 Jun 2026')
            ->assertDontSeeHtml('Rp 65.000.000')
            ->assertSeeHtml('YouTube');
    }
}

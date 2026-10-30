<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianMonthFilterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_default_kajian_filter_period_matches_current_calendar_month_even_on_day_31(): void
    {
        $admin = User::factory()->create([
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        // Freeze time at the 31st of October
        Carbon::setTestNow(Carbon::parse('2026-10-31 10:00:00', 'Asia/Jakarta'));

        // Seed some past kajians in October and future in November and December
        Kajian::create([
            'type' => 'jumat',
            'date' => '2026-10-30',
            'speaker_name' => 'Khatib 1',
        ]);
        Kajian::create([
            'type' => 'jumat',
            'date' => '2026-11-06',
            'speaker_name' => 'Khatib 2',
        ]);
        Kajian::create([
            'type' => 'jumat',
            'date' => '2026-12-04',
            'speaker_name' => 'Khatib 3',
        ]);

        $dashboard = new AdminDashboard();
        [$month, $year] = $dashboard->getDefaultKajianFilterPeriod();

        // Must remain in October (month 10), never jump to December (month 12)
        $this->assertEquals(10, $month);
        $this->assertEquals(2026, $year);

        // Verify Livewire component mount
        Livewire::actingAs($admin)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->assertSet('kajianMonthFilter', '10')
            ->assertSet('kajianYearFilter', '2026');

        Carbon::setTestNow(); // clear mock
    }
}


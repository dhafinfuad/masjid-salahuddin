<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalPrayerDutyShortMonthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test format_short_month_id correctly returns Indonesian 3-letter month abbreviations,
     * specifically Nov, Mar, and Agu (not Agt).
     */
    public function test_format_short_month_id_abbreviations(): void
    {
        $expected = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu', // Sesuai instruksi pengguna: Agustus menjadi Agu
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov', // Sesuai instruksi pengguna: November menjadi Nov
            12 => 'Des',
        ];

        foreach ($expected as $monthNum => $shortName) {
            $this->assertEquals($shortName, format_short_month_id($monthNum));
            $carbonDate = Carbon::create(2026, $monthNum, 1);
            $this->assertEquals($shortName, format_short_month_id($carbonDate));
        }
    }

    /**
     * Test format_date_id_short outputs the expected Indonesian short date format.
     */
    public function test_format_date_id_short_format(): void
    {
        // 2 November 2026 is a Monday
        $novDate = Carbon::create(2026, 11, 2);
        $this->assertEquals('Senin, 02 Nov 2026', format_date_id_short($novDate));

        // 2 Maret 2026 is a Monday
        $marDate = Carbon::create(2026, 3, 2);
        $this->assertEquals('Senin, 02 Mar 2026', format_date_id_short($marDate));

        // 2 Agustus 2026 is a Sunday
        $aguDate = Carbon::create(2026, 8, 2);
        $this->assertEquals('Minggu, 02 Agu 2026', format_date_id_short($aguDate));
    }

    /**
     * Test portal page renders short month name in Petugas Sholat badge.
     */
    public function test_portal_page_renders_short_month_in_petugas_sholat_badge(): void
    {
        // Case 1: Monday, 2 November 2026 -> Senin, 02 Nov 2026
        Carbon::setTestNow(Carbon::create(2026, 11, 2, 10, 0, 0));
        try {
            Livewire::test(PortalPage::class)
                ->assertSee('Petugas Sholat')
                ->assertSee('Senin, 02 Nov 2026')
                ->assertDontSee('Senin, 02 November 2026');
        } finally {
            Carbon::setTestNow();
        }

        // Case 2: Monday, 2 March 2026 -> Senin, 02 Mar 2026
        Carbon::setTestNow(Carbon::create(2026, 3, 2, 10, 0, 0));
        try {
            Livewire::test(PortalPage::class)
                ->assertSee('Petugas Sholat')
                ->assertSee('Senin, 02 Mar 2026')
                ->assertDontSee('Senin, 02 Maret 2026');
        } finally {
            Carbon::setTestNow();
        }

        // Case 3: Monday, 3 August 2026 -> Senin, 03 Agu 2026
        Carbon::setTestNow(Carbon::create(2026, 8, 3, 10, 0, 0));
        try {
            Livewire::test(PortalPage::class)
                ->assertSee('Petugas Sholat')
                ->assertSee('Senin, 03 Agu 2026')
                ->assertDontSee('Senin, 03 Agustus 2026')
                ->assertDontSee('03 Agt 2026');
        } finally {
            Carbon::setTestNow();
        }
    }
}

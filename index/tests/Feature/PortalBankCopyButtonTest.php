<?php

namespace Tests\Feature;

use App\Livewire\Portal\PortalPage;
use App\Models\MasjidSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalBankCopyButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_bank_account_and_saran_buttons_match_profil_masjid_style(): void
    {
        MasjidSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Masjid Salahuddin',
                'bank_accounts' => [
                    [
                        'bank' => 'BSI (BANK SYARIAH INDONESIA)',
                        'account_number' => '7885858885',
                        'holder' => 'MASJID SALAHUDDIN KPP MADYA MALANG',
                    ],
                ],
            ]
        );

        $test = Livewire::test(PortalPage::class)
            ->assertSee('BSI (BANK SYARIAH INDONESIA)')
            ->assertSee('7885858885')
            ->assertSee('MASJID SALAHUDDIN KPP MADYA MALANG')
            ->assertSee('Salin Nomor Rekening')
            ->assertSee('Saran & Kritik')
            ->assertDontSee('Sampaikan Saran & Kritik');

        $html = $test->html();

        // Ensure the bank copy button has matching styling with profil masjid button
        $this->assertStringContainsString('class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer shrink-0 select-none"', $html);
        $this->assertStringContainsString("copyRekening('7885858885'", $html);

        // Ensure saran & kritik button has matching styling with profil masjid button
        $this->assertStringContainsString('class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer shrink-0"', $html);
    }
}

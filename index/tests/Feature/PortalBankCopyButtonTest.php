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

    public function test_bank_account_clickable_with_inline_copy_icon_and_saran_style(): void
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
            ->assertSee('Saran & Kritik')
            ->assertDontSee('Sampaikan Saran & Kritik');

        $html = $test->html();

        // Ensure the entire bank account container is clickable with copyRekening
        $this->assertStringContainsString("copyRekening('7885858885'", $html);
        $this->assertStringContainsString('title="Salin Nomor Rekening"', $html);
        $this->assertStringContainsString('cursor-pointer', $html);

        // Ensure copy icon and checkmark icon are next to account number
        $this->assertStringContainsString('data-lucide="copy"', $html);
        $this->assertStringContainsString('data-lucide="check"', $html);

        // Ensure the standalone separate button is removed
        $this->assertStringNotContainsString('pt-2.5 border-t border-slate-200/60 flex justify-end', $html);

        // Ensure saran & kritik button has matching styling with profil masjid button
        $this->assertStringContainsString('class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer shrink-0"', $html);
    }
}

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

    public function test_bank_account_renders_full_width_copy_button_below_details(): void
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
            ->assertSee('Salin Nomor Rekening');

        $html = $test->html();

        // Ensure the button has w-full class
        $this->assertStringContainsString('class="w-full py-2 px-3 rounded-lg', $html);
        $this->assertStringContainsString("copyRekening('7885858885'", $html);
    }
}

<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class WhatsAppHelperTest extends TestCase
{
    public function test_format_phone_for_wa_normalizes_various_formats(): void
    {
        // Leading 08
        $this->assertSame('6281330705421', format_phone_for_wa('081330705421'));
        $this->assertSame('6281234567890', format_phone_for_wa('0812-3456-7890'));
        $this->assertSame('6281234567890', format_phone_for_wa('0812 3456 7890'));

        // Leading +62
        $this->assertSame('6281330705421', format_phone_for_wa('+6281330705421'));
        $this->assertSame('6281330705421', format_phone_for_wa('+62 813-3070-5421'));

        // Leading 62
        $this->assertSame('6281330705421', format_phone_for_wa('6281330705421'));

        // Leading 8 (without 0 or 62)
        $this->assertSame('6281330705421', format_phone_for_wa('81330705421'));

        // Empty or null
        $this->assertSame('', format_phone_for_wa(''));
        $this->assertSame('', format_phone_for_wa(null));
        $this->assertSame('', format_phone_for_wa('   '));
    }

    public function test_wa_link_generates_valid_urls(): void
    {
        $this->assertSame('https://wa.me/6281330705421', wa_link('081330705421'));
        $this->assertSame('https://wa.me/6281330705421', wa_link('+62 813-3070-5421'));
        $this->assertSame('https://wa.me/6281330705421?text=Halo+Takmir', wa_link('081330705421', 'Halo Takmir'));
        $this->assertSame('#', wa_link(''));
        $this->assertSame('#', wa_link(null));
    }
}

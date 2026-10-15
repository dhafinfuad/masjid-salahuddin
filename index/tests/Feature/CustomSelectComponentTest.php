<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class CustomSelectComponentTest extends TestCase
{
    public function test_custom_select_renders_with_associative_options()
    {
        $options = [
            'all' => 'Semua Program',
            '1' => 'Infaq Rutin',
            '2' => 'Santunan Yatim',
        ];

        $html = Blade::render('<x-custom-select :options="$options" placeholder="Pilih Program" />', [
            'options' => $options
        ]);

        $this->assertStringContainsString('Semua Program', $html);
        $this->assertStringContainsString('Infaq Rutin', $html);
        $this->assertStringContainsString('Santunan Yatim', $html);
        $this->assertStringContainsString('x-data="{', $html);
    }

    public function test_custom_select_renders_with_collection_options()
    {
        $options = collect([
            (object)['id' => 10, 'name' => 'Program A'],
            (object)['id' => 20, 'name' => 'Program B'],
        ]);

        $html = Blade::render('<x-custom-select :options="$options" icon="heart-handshake" />', [
            'options' => $options
        ]);

        $this->assertStringContainsString('Program A', $html);
        $this->assertStringContainsString('Program B', $html);
        $this->assertStringContainsString('heart-handshake', $html);
    }

    public function test_custom_select_renders_with_searchable()
    {
        $options = ['2024', '2025', '2026'];

        $html = Blade::render('<x-custom-select :options="$options" :searchable="true" searchPlaceholder="Cari tahun..." />', [
            'options' => $options
        ]);

        $this->assertStringContainsString('Cari tahun...', $html);
        $this->assertStringContainsString('searchable: true', $html);
    }

    public function test_custom_select_renders_with_group_headers()
    {
        $options = [
            ['value' => '', 'label' => '-- Pilih Pos --'],
            ['label' => 'PENERIMAAN', 'is_header' => true],
            ['value' => '1', 'label' => 'Infaq Rutin'],
            ['label' => 'PENGELUARAN', 'is_header' => true],
            ['value' => '2', 'label' => 'Listrik'],
        ];

        $html = Blade::render('<x-custom-select :options="$options" placeholder="Pilih Pos" />', [
            'options' => $options
        ]);

        $this->assertStringContainsString('PENERIMAAN', $html);
        $this->assertStringContainsString('is_header', $html);
        $this->assertStringContainsString('Infaq Rutin', $html);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModalInputsStylingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\MasjidSetting::getActive();

        $this->adminUser = User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'master@masjidsalahuddin.id',
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);
    }

    /** @test */
    public function test_custom_select_component_applies_p_2_5_without_px_py_conflict(): void
    {
        $rendered = $this->blade('<x-custom-select :options="[\'a\' => \'A\', \'b\' => \'B\']" buttonClass="p-2.5 bg-slate-50/50" />');

        $this->assertStringContainsString('p-2.5', $rendered);
        $this->assertStringContainsString('bg-slate-50/50', $rendered);
        // Ensure default 'px-2.5 py-1.5' is suppressed when custom padding is passed
        $this->assertStringNotContainsString('px-2.5 py-1.5', $rendered);
        // Ensure default 'bg-white' is suppressed when custom bg is passed
        $this->assertStringNotContainsString('bg-white text-xs', $rendered);
    }

    /** @test */
    public function test_custom_select_component_retains_default_for_table_filters(): void
    {
        $rendered = $this->blade('<x-custom-select :options="[\'a\' => \'A\', \'b\' => \'B\']" />');

        $this->assertStringContainsString('h-9 px-2.5', $rendered);
        $this->assertStringContainsString('bg-white', $rendered);
    }

    /** @test */
    public function test_admin_modals_render_with_p_2_5_selects(): void
    {
        $this->actingAs($this->adminUser);

        $response = Livewire::test(\App\Livewire\Admin\AdminDashboard::class, ['tab' => 'petugas']);

        $response->assertSee('dutyForm.day_name');
        $response->assertSee('p-2.5 bg-slate-50/50');
    }
}

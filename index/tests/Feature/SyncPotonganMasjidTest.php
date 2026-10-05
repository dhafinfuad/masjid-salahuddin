<?php

namespace Tests\Feature;

use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Services\PotonganMasjidSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncPotonganMasjidTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed social programs
        SocialProgram::create([
            'id' => 1,
            'name' => 'Program Santunan Anak Yatim',
            'slug' => 'santunan-anak-yatim',
            'category' => 'yatim',
            'description' => 'Program Santunan Anak Yatim',
            'target_amount' => 33325000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'heart-handshake',
            'color' => 'emerald',
        ]);

        SocialProgram::create([
            'id' => 2,
            'name' => 'Infaq Rutin',
            'slug' => 'infaq-rutin',
            'category' => 'infaq',
            'description' => 'Infaq Rutin',
            'target_amount' => 43575000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'wallet',
            'color' => 'blue',
        ]);

        SocialProgram::create([
            'id' => 3,
            'name' => 'Program Zakat Mal Rutin',
            'slug' => 'zakat-mal-rutin',
            'category' => 'zakat',
            'description' => 'Program Zakat Mal Rutin',
            'target_amount' => 29500000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'coins',
            'color' => 'amber',
        ]);

        SocialProgram::create([
            'id' => 4,
            'name' => 'Tabungan Qurban',
            'slug' => 'tabungan-qurban',
            'category' => 'qurban',
            'description' => 'Tabungan Qurban',
            'target_amount' => 112400000,
            'period_type' => 'tahunan',
            'status' => 'AKTIF',
            'icon' => 'sparkles',
            'color' => 'teal',
        ]);
    }

    public function test_sync_potongan_dry_run_does_not_persist(): void
    {
        $this->artisan('masjid:sync-potongan', ['--dry-run' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('SIMULASI (DRY-RUN)');

        $this->assertEquals(0, ProgramParticipant::count());
    }

    public function test_sync_potongan_executes_and_populates_participants(): void
    {
        $this->artisan('masjid:sync-potongan')
            ->assertSuccessful()
            ->expectsOutputToContain('EKSEKUSI DATABASE');

        $this->assertEquals(1606, ProgramParticipant::count());
        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        $this->assertEquals(130, ProgramParticipant::where('period', $currentPeriod)->count());
        $this->assertEquals(19600000.00, (float) ProgramParticipant::where('period', $currentPeriod)->sum('monthly_amount'));

        // Check idempotency: running again should not duplicate records
        $this->artisan('masjid:sync-potongan')
            ->assertSuccessful();

        $this->assertEquals(1606, ProgramParticipant::count());
        $this->assertEquals(130, ProgramParticipant::where('period', $currentPeriod)->count());
    }

    public function test_livewire_import_potongan_modal_flow(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Administrator Masjid',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        \App\Models\Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
            'description' => 'Kajian rutin pekanan',
        ]);

        $filePath = public_path('resources/Rekapitulasi Potongan Masjid 2.0.xlsx');
        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'Rekapitulasi Potongan Masjid 2.0.xlsx',
            file_get_contents($filePath)
        );

        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\AdminDashboard::class)
            ->set('currentTab', 'programs')
            ->call('openImportPotonganModal')
            ->assertSet('showImportPotonganModal', true)
            ->assertDispatched('open-import-potongan-modal')
            ->set('potonganFile', $uploadedFile)
            ->assertSet('importPotonganPreview.total_excel_unique', 1606)
            ->assertSet('importPotonganPreview.current_period_count', 130)
            ->assertSet('importPotonganPreview.current_period_amount', 19600000.0)
            ->call('processConfirmImportPotongan')
            ->assertDispatched('close-import-potongan-modal')
            ->assertSet('showImportPotonganModal', false);

        $this->assertEquals(1606, ProgramParticipant::count());
        $this->assertEquals(130, ProgramParticipant::where('period', $currentPeriod)->count());
    }
}

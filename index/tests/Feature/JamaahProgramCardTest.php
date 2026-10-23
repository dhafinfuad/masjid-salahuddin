<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class JamaahProgramCardTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role, string $name): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)) . '@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'AKTIF',
        ]);
    }

    private function seedPrograms(): void
    {
        $period = 'Periode ' . now()->month . '/' . now()->year;

        $infaq = SocialProgram::create([
            'name' => 'Infaq Rutin Pegawai', 'slug' => 'infaq-rutin-pegawai', 'category' => 'infaq',
            'period_type' => 'bulanan', 'status' => 'AKTIF', 'icon' => 'wallet',
        ]);
        $yatim = SocialProgram::create([
            'name' => 'Santunan Anak Yatim', 'slug' => 'santunan-anak-yatim', 'category' => 'yatim',
            'period_type' => 'bulanan', 'status' => 'AKTIF', 'icon' => 'heart-handshake',
        ]);

        ProgramParticipant::create(['social_program_id' => $infaq->id, 'name' => 'Haji Mansyur', 'program_name' => $infaq->name, 'monthly_amount' => 100000, 'period' => $period]);
        ProgramParticipant::create(['social_program_id' => $infaq->id, 'name' => 'Pegawai Lain', 'program_name' => $infaq->name, 'monthly_amount' => 900000, 'period' => $period]);
        // Jamaah tidak ikut program yatim; hanya orang lain
        ProgramParticipant::create(['social_program_id' => $yatim->id, 'name' => 'Pegawai Lain', 'program_name' => $yatim->name, 'monthly_amount' => 700000, 'period' => $period]);
    }

    public function test_jamaah_sees_only_own_deduction_and_no_realisasi_dana(): void
    {
        $this->seedPrograms();
        $this->actingAs($this->makeUser('Jamaah', 'Haji Mansyur'));

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertOk()
            ->assertSee('Rp 100.000')
            ->assertDontSee('Rp 900.000')
            ->assertDontSee('Rp 1.000.000')
            ->assertDontSee('Rp 700.000')
            ->assertDontSee('Realisasi Dana:');
    }

    public function test_jamaah_catalog_toolbar_replaces_summary_cards_and_filters_by_search(): void
    {
        $this->seedPrograms();
        $this->actingAs($this->makeUser('Jamaah', 'Haji Mansyur'));

        $infaqKey = 'socprog-card-' . SocialProgram::where('slug', 'infaq-rutin-pegawai')->value('id');
        $yatimKey = 'socprog-card-' . SocialProgram::where('slug', 'santunan-anak-yatim')->value('id');

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertSee('Cari nama program...')
            ->assertDontSee('Dana Terhimpun')
            ->assertSeeHtml($infaqKey)
            ->assertSeeHtml($yatimKey)
            ->set('catalogSearch', 'Yatim')
            ->assertDontSeeHtml($infaqKey)
            ->assertSeeHtml($yatimKey)
            ->call('resetCatalogFilters')
            ->assertSet('catalogSearch', '')
            ->assertSeeHtml($infaqKey);
    }

    public function test_jamaah_catalog_period_filter_changes_deduction(): void
    {
        $this->seedPrograms();
        $this->actingAs($this->makeUser('Jamaah', 'Haji Mansyur'));

        // Bulan tahun lalu: tidak ada data -> potongan user Rp 0, tidak bocor total orang lain
        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertSee('Rp 100.000')
            ->set('catalogYear', (string) (now()->year - 1))
            ->assertDontSee('Rp 100.000')
            ->assertDontSee('Rp 900.000')
            ->assertDontSee('Rp 1.000.000');
    }

    public function test_officer_still_sees_totals_and_realisasi_dana(): void
    {
        $this->seedPrograms();
        $this->actingAs($this->makeUser('Master', 'Administrator DKM'));

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertOk()
            ->assertSee('Rp 1.000.000')
            ->assertSee('Rp 700.000')
            ->assertSee('Realisasi Dana:');
    }
}

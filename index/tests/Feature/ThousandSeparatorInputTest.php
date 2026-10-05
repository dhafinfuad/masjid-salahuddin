<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Portal\PortalPage;
use App\Models\Agenda;
use App\Models\Category;
use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ThousandSeparatorInputTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected SocialProgram $socialProgram;
    protected FinanceCategory $financeCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
            'description' => 'Kajian rutin pekanan',
        ]);

        $this->admin = User::create([
            'name' => 'Admin DKM',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Master',
            
        ]);

        $this->socialProgram = SocialProgram::create([
            'name' => 'Santunan Yatim Dhuafa',
            'slug' => 'santunan-yatim-dhuafa',
            'category' => 'yatim',
            'description' => 'Santunan yatim',
            'target_amount' => 10000000,
            'period_type' => 'bulanan',
            
            'icon' => 'heart-handshake',
            'color' => 'emerald',
        ]);

        $this->financeCategory = FinanceCategory::create([
            'name' => 'Operasional Masjid',
            'type' => 'pengeluaran',
            'color' => 'amber',
        ]);
    }

    public function test_social_program_target_with_thousand_dots_is_stored_as_pure_number(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->call('openCreateSocialProgram')
            ->set('socialProgramName', 'Beasiswa Generasi Quran')
            ->set('socialProgramCategory', 'sosial')
            ->set('socialProgramTarget', '15.000.000') // With thousand dots!
            ->set('socialProgramPeriodType', 'bulanan')
            ->set('socialProgramStatus', 'AKTIF')
            ->call('saveSocialProgram')
            ->assertHasNoErrors()
            ->assertDispatched('close-social-program-modal');

        $this->assertDatabaseHas('social_programs', [
            'name' => 'Beasiswa Generasi Quran',
            'target_amount' => 15000000,
        ]);
    }

    public function test_finance_amount_with_thousand_dots_is_stored_as_pure_number(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeDate', '2026-09-20')
            ->set('financeType', 'pengeluaran')
            ->set('financeCategoryId', $this->financeCategory->id)
            ->set('financeAmount', '1.750.000') // With thousand dots!
            ->set('financeDescription', 'Perbaikan Sound System Ruang Utama')
            ->call('saveFinance')
            ->assertHasNoErrors()
            ->assertDispatched('close-finance-modal');

        $this->assertDatabaseHas('finances', [
            'amount' => 1750000,
            'description' => 'Perbaikan Sound System Ruang Utama',
        ]);
    }

    public function test_lump_sum_amount_with_thousand_dots_is_stored_as_pure_number(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('openLumpSumModal')
            ->set('lumpSumDate', '2026-09-20')
            ->set('lumpSumProgram', 'Santunan Yatim Dhuafa')
            ->set('lumpSumAmount', '18.250.000') // With thousand dots!
            ->set('lumpSumNotes', 'Setoran potong tukin September')
            ->call('processLumpSumDeposit')
            ->assertHasNoErrors()
            ->assertDispatched('close-lumpsum-modal');

        $this->assertDatabaseHas('finances', [
            'amount' => 18250000,
            'program_name' => 'Santunan Yatim Dhuafa',
        ]);
    }

    public function test_participant_amount_with_thousand_dots_is_stored_as_pure_number(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('participantName', 'Budi Santoso')
            ->set('participantProgram', $this->socialProgram->name)
            ->set('participantAmount', '250.000') // With thousand dots!
            ->set('participantPeriod', 'Bulanan')
            ->set('participantStatus', 'AKTIF')
            ->call('saveParticipant')
            ->assertHasNoErrors()
            ->assertDispatched('close-participant-modal');

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Budi Santoso',
            'monthly_amount' => 250000,
            
        ]);
    }

    public function test_portal_social_registration_with_thousand_dots_is_stored_as_pure_number(): void
    {
        Livewire::test(PortalPage::class)
            ->call('openSocialRegisterModal', $this->socialProgram->id)
            ->set('socialParticipantName', 'Hamba Allah')
            ->set('socialParticipantProgram', $this->socialProgram->name)
            ->set('socialParticipantAmount', '500.000') // With thousand dots!
            ->set('socialParticipantPeriod', 'Bulanan')
            ->call('submitSocialRegistration')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Hamba Allah',
            'monthly_amount' => 500000,
            
        ]);
    }

    public function test_agenda_budget_with_thousand_dots_is_stored_as_pure_number(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'agenda'])
            ->call('saveAgenda', [
                'id' => null,
                'title' => 'Peringatan Maulid Nabi 1448 H',
                'description' => 'Tabligh akbar maulid',
                'event_date' => '2026-10-15',
                'status' => 'Direncanakan',
                'committee_members' => 'Fauzi (Ketua)',
                'budget' => '12.500.000', // Formatted string with dots from client
                'report_summary' => '',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('agendas', [
            'title' => 'Peringatan Maulid Nabi 1448 H',
            'budget' => 12500000,
        ]);
    }
}

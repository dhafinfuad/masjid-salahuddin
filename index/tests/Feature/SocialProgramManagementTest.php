<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Portal\PortalPage;
use App\Models\Category;
use App\Models\Finance;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SocialProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected SocialProgram $yatimProgram;
    protected SocialProgram $infaqProgram;

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
            'name' => 'Administrator DKM',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $this->yatimProgram = SocialProgram::create([
            'name' => 'Santunan Anak Yatim',
            'slug' => 'santunan-anak-yatim',
            'category' => 'yatim',
            'description' => 'Santunan bulanan yatim dhuafa',
            'target_amount' => 10000000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'heart-handshake',
            'color' => 'emerald',
        ]);

        $this->infaqProgram = SocialProgram::create([
            'name' => 'Infaq Rutin Pegawai',
            'slug' => 'infaq-rutin-pegawai',
            'category' => 'infaq',
            'description' => 'Infaq rutin bulanan pegawai',
            'target_amount' => 20000000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'wallet',
            'color' => 'blue',
        ]);
    }

    public function test_social_program_model_calculations_and_relations(): void
    {
        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        $otherPeriod = 'Periode ' . (now()->month == 1 ? 12 : now()->month - 1) . '/' . now()->year;

        $participant1 = ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Ahmad Dahlan',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 500000,
            'period' => $currentPeriod,
        ]);

        $participant2 = ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Siti Walidah',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 250000,
            'period' => $otherPeriod,
        ]);

        Finance::create([
            'type' => 'pemasukan',
            'program_name' => $this->yatimProgram->name,
            'amount' => 2500000,
            'transaction_date' => now(),
            'description' => 'Setoran donasi yatim',
        ]);

        Finance::create([
            'type' => 'pengeluaran',
            'program_name' => $this->yatimProgram->name,
            'amount' => 1000000,
            'transaction_date' => now(),
            'description' => 'Penyaluran santunan tahap 1',
        ]);

        $this->assertEquals(1, $this->yatimProgram->active_participants_count);
        $this->assertEquals(500000, $this->yatimProgram->monthly_commitment_total);
        $this->assertEquals(750000, $this->yatimProgram->total_collected);
        $this->assertEquals(1000000, $this->yatimProgram->total_disbursed);
        $this->assertEquals(-250000, $this->yatimProgram->current_balance);
        $this->assertEquals(7.5, $this->yatimProgram->target_progress_percentage);
    }

    public function test_admin_dashboard_can_load_programs_tab(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertSet('currentTab', 'programs')
            ->assertSeeHtml('Program Sosial')
            ->assertSee('Santunan Anak Yatim')
            ->assertSee('Infaq Rutin Pegawai')
            ->assertSeeHtml('Katalog Program Sosial')
            ->assertSeeHtml('Rekapitulasi Peserta')
            ->assertSee('Dana Terhimpun')
            ->assertSee('Peserta Terdaftar')
            ->assertSee('Januari - ' . now()->translatedFormat('F Y'))
            ->assertSee('per ' . now()->translatedFormat('F Y'))
            ->assertSee('Import')
            ->assertSee('Export')
            ->assertSee('Peserta')
            ->assertSee('Program');
    }

    public function test_admin_can_create_new_social_program(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->call('openCreateSocialProgram')
            ->set('socialProgramName', 'Zakat Mal Rutin')
            ->set('socialProgramCategory', 'zakat')
            ->set('socialProgramTarget', '15000000')
            ->set('socialProgramPeriodType', 'bulanan')
            ->set('socialProgramStatus', 'AKTIF')
            ->set('socialProgramDescription', 'Penghimpunan zakat profesi pegawai')
            ->call('saveSocialProgram')
            ->assertHasNoErrors()
            ->assertDispatched('close-social-program-modal');

        $this->assertDatabaseHas('social_programs', [
            'name' => 'Zakat Mal Rutin',
            'category' => 'zakat',
            'target_amount' => 15000000,
            'status' => 'AKTIF',
        ]);
    }

    public function test_admin_can_update_social_program(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->call('openEditSocialProgram', $this->yatimProgram->id)
            ->assertSet('socialProgramName', 'Santunan Anak Yatim')
            ->set('socialProgramTarget', '12500000')
            ->call('saveSocialProgram')
            ->assertHasNoErrors()
            ->assertDispatched('close-social-program-modal');

        $this->assertDatabaseHas('social_programs', [
            'id' => $this->yatimProgram->id,
            'target_amount' => 12500000,
        ]);
    }

    public function test_admin_can_register_participant(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('participantName', 'Budi Santoso')
            ->set('participantProgram', $this->yatimProgram->name)
            ->set('participantAmount', '300000')
            ->set('participantPeriod', 'Periode ' . now()->month . '/' . now()->year)
            ->call('saveParticipant')
            ->assertHasNoErrors()
            ->assertDispatched('close-participant-modal');

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Budi Santoso',
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 300000,
        ]);
    }

    public function test_admin_can_filter_participants_by_program_and_period(): void
    {
        $this->actingAs($this->admin);

        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        $otherPeriod = 'Periode ' . (now()->month == 1 ? 12 : now()->month - 1) . '/' . now()->year;

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Peserta Yatim Bulan Ini',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 200000,
            'period' => $currentPeriod,
        ]);

        ProgramParticipant::create([
            'social_program_id' => $this->infaqProgram->id,
            'name' => 'Peserta Infaq Bulan Lalu',
            'program_name' => $this->infaqProgram->name,
            'monthly_amount' => 100000,
            'period' => $otherPeriod,
        ]);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('socialProgramFilter', (string) $this->yatimProgram->id)
            ->assertViewHas('socialParticipantsList', function ($list) {
                return $list->contains('name', 'Peserta Yatim Bulan Ini')
                    && ! $list->contains('name', 'Peserta Infaq Bulan Lalu');
            })
            ->set('socialProgramFilter', 'all')
            ->set('socialParticipantPeriodFilter', $otherPeriod)
            ->assertViewHas('socialParticipantsList', function ($list) {
                return $list->contains('name', 'Peserta Infaq Bulan Lalu')
                    && ! $list->contains('name', 'Peserta Yatim Bulan Ini');
            });
    }

    public function test_portal_displays_social_programs_list(): void
    {
        Livewire::test(PortalPage::class)
            ->assertSeeHtml('Program Sosial')
            ->assertSee('Santunan Anak Yatim')
            ->assertSee('Infaq Rutin Pegawai')
            ->assertSeeHtml('Daftar Sekarang');
    }

    public function test_portal_can_check_my_participation_by_name(): void
    {
        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Faisal Basri',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 350000,
            'period' => 'Periode ' . now()->month . '/' . now()->year,
        ]);

        // Cari via Nama
        Livewire::test(PortalPage::class)
            ->set('searchParticipantQuery', 'Faisal Basri')
            ->call('checkMyParticipation')
            ->assertHasNoErrors()
            ->assertSet('hasSearchedParticipation', true)
            ->assertSet('myParticipations.0.name', 'Faisal Basri')
            ->assertSet('myParticipations.0.program_name', 'Santunan Anak Yatim')
            ->assertSet('myParticipations.0.monthly_amount', 350000.0);

        // Cari data yang tidak ada
        Livewire::test(PortalPage::class)
            ->set('searchParticipantQuery', 'TidakAdaDiDatabase')
            ->call('checkMyParticipation')
            ->assertSet('myParticipations', []);
    }

    public function test_portal_can_register_new_social_commitment(): void
    {
        Livewire::test(PortalPage::class)
            ->call('openSocialRegisterModal', $this->infaqProgram->id)
            ->assertSet('showSocialRegisterModal', true)
            ->set('socialParticipantName', 'Dewi Lestari')
            ->set('socialParticipantProgram', $this->infaqProgram->name)
            ->set('socialParticipantAmount', '150000')
            ->set('socialParticipantPeriod', 'Bulanan')
            ->call('submitSocialRegistration')
            ->assertHasNoErrors()
            ->assertSet('socialRegisterSuccess', true)
            ->assertSee('Pendaftaran Berhasil');

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Dewi Lestari',
            'social_program_id' => $this->infaqProgram->id,
            'program_name' => $this->infaqProgram->name,
            'monthly_amount' => 150000,
        ]);
    }

    public function test_jamaah_user_can_access_and_register_participant(): void
    {
        $jamaah = User::create([
            'name' => 'Haji Mansyur',
            'email' => 'jamaah@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        $this->actingAs($jamaah);

        // Verify page loads and contains Daftarkan Peserta button
        $test = Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertSet('currentTab', 'programs')
            ->assertSeeHtml('Daftarkan Peserta')
            ->call('openParticipantModal')
            ->assertSet('showParticipantModal', true)
            ->assertSet('participantName', 'Haji Mansyur')
            ->set('participantProgram', $this->yatimProgram->name)
            ->set('participantAmount', '200000')
            ->call('saveParticipant')
            ->assertHasNoErrors()
            ->assertDispatched('close-participant-modal')
            ->assertSet('toastMessage', 'Peserta baru berhasil didaftarkan ke program.');

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Haji Mansyur',
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 200000,
        ]);
    }

    public function test_setoran_kantor_sub_tab_and_lump_sum_deposit_on_programs_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertSet('currentTab', 'programs')
            ->assertSee('Katalog Program Sosial')
            ->assertSee('Rekapitulasi Peserta')
            ->assertSee('Setoran Kantor')
            ->assertSee('Riwayat Penerimaan Transfer Kliring')
            ->assertDontSee('Target Potongan Tukin')
            ->assertDontSee('Realisasi Kas Masuk Kantor')
            ->assertDontSee('Indikator Selisih')
            ->set('socialSubTab', 'setoran')
            ->call('openLumpSumModal')
            ->assertSet('showLumpSumModal', true)
            ->set('lumpSumAmount', '15000000')
            ->set('lumpSumDate', now()->format('Y-m-d'))
            ->set('lumpSumProgram', $this->infaqProgram->name)
            ->set('lumpSumNotes', 'Kliring Tukin KPP Pratama Bulan Berjalan')
            ->call('processLumpSumDeposit')
            ->assertHasNoErrors()
            ->assertSet('showLumpSumModal', false);

        $this->assertDatabaseHas('finances', [
            'type' => 'pemasukan',
            'program_name' => $this->infaqProgram->name,
            'amount' => 15000000,
        ]);
    }

    public function test_admin_can_export_rekapitulasi_potongan_xlsx(): void
    {
        $this->actingAs($this->admin);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Ahmad Dahlan',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 500000,
            'period' => 'Periode 9/2026',
        ]);

        $response = $this->get('/admin/programs/export-excel?year=2026&month=9');
        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename="Rekapitulasi Potongan Masjid - Periode September 2026.xlsx"');

        // Verify that the response file starts with PK (standard ZIP / OpenXML header)
        $file = $response->getFile();
        $this->assertNotNull($file);
        $content = file_get_contents($file->getPathname());
        $this->assertStringStartsWith("PK", $content);
    }

    public function test_admin_can_filter_social_participants_by_multiple_periods_simultaneously(): void
    {
        $this->actingAs($this->admin);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Peserta Periode 8',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 100000,
            'period' => 'Periode 8/2026',
        ]);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Peserta Periode 9',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 100000,
            'period' => 'Periode 9/2026',
        ]);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Peserta Periode 10',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 100000,
            'period' => 'Periode 10/2026',
        ]);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('socialParticipantPeriodFilter', ['Periode 8/2026', 'Periode 10/2026'])
            ->assertViewHas('socialParticipantsList', function ($list) {
                return $list->contains('name', 'Peserta Periode 8')
                    && $list->contains('name', 'Peserta Periode 10')
                    && ! $list->contains('name', 'Peserta Periode 9');
            });
    }

    public function test_admin_can_export_social_participants_by_month_range_or_full_year(): void
    {
        $this->actingAs($this->admin);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Budi Santoso',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 200000,
            'period' => 'Periode 1/2026',
        ]);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Citra Lestari',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 300000,
            'period' => 'Periode 3/2026',
        ]);

        // Range export (Januari - Maret)
        $rangeResponse = $this->get('/admin/programs/export-excel?year=2026&start_month=1&end_month=3');
        $rangeResponse->assertStatus(200);
        $rangeResponse->assertHeader('content-disposition', 'attachment; filename="Rekapitulasi Potongan Masjid - Periode Januari - Maret 2026.xlsx"');

        // Full Year export (1 - 12)
        $fullYearResponse = $this->get('/admin/programs/export-excel?year=2026&start_month=1&end_month=12');
        $fullYearResponse->assertStatus(200);
        $fullYearResponse->assertHeader('content-disposition', 'attachment; filename="Rekapitulasi Potongan Masjid - Seluruh Periode Tahun 2026.xlsx"');
    }

    public function test_toggle_all_and_reset_participant_periods(): void
    {
        $this->actingAs($this->admin);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Peserta A',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 100000,
            'period' => 'Periode 1/2026',
        ]);

        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Peserta B',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 100000,
            'period' => 'Periode 2/2026',
        ]);

        $component = Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('socialParticipantPeriodFilter', ['Periode 1/2026'])
            ->call('toggleAllParticipantPeriods')
            ->assertSet('socialParticipantPeriodFilter', ['Periode 2/2026', 'Periode 1/2026'])
            ->call('toggleAllParticipantPeriods')
            ->assertSet('socialParticipantPeriodFilter', [])
            ->set('socialParticipantPeriodFilter', ['Periode 1/2026'])
            ->call('resetParticipantPeriods')
            ->assertSet('socialParticipantPeriodFilter', []);
    }

    public function test_jamaah_role_social_programs_permissions_and_scoped_data(): void
    {
        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;
        $currentPeriod = "Periode {$currentMonth}/{$currentYear}";
        $prevPeriod = "Periode 1/{$currentYear}";

        $jamaah = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@masjid.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        // Data milik user Jamaah
        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Fulan bin Fulan',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 150000,
            'period' => $currentPeriod,
        ]);

        if ($currentMonth > 1) {
            ProgramParticipant::create([
                'social_program_id' => $this->yatimProgram->id,
                'name' => 'Fulan bin Fulan',
                'program_name' => $this->yatimProgram->name,
                'monthly_amount' => 150000,
                'period' => $prevPeriod,
            ]);
        }

        // Data milik orang lain
        ProgramParticipant::create([
            'social_program_id' => $this->yatimProgram->id,
            'name' => 'Orang Lain',
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 1000000,
            'period' => $currentPeriod,
        ]);

        $this->actingAs($jamaah);

        $expectedCollected = ($currentMonth > 1) ? 300000 : 150000;
        $expectedCommitment = 150000;

        $component = Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->assertViewHas('totalSocialCollectedAll', (float) $expectedCollected)
            ->assertViewHas('totalSocialCommitment', (float) $expectedCommitment)
            ->assertSeeHtml('<span>Katalog Program Sosial</span>')
            ->assertDontSeeHtml('<span>Rekapitulasi Peserta</span>')
            ->assertDontSeeHtml('<span>Setoran Kantor</span>')
            ->assertSeeHtml('<span>Peserta</span>')
            ->assertDontSeeHtml('<span>Program</span>')
            ->assertDontSeeHtml('<span>Import</span>')
            ->assertDontSeeHtml('<span>Export</span>')
            ->assertDontSee('title="Edit Program Sosial"')
            ->assertDontSee('title="Hapus Program"')
            ->assertDontSee('Lihat Peserta');

        // Jamaah tidak dapat membuka modal program (tombol disembunyikan dan aksi ditolak)
        $component->call('openCreateSocialProgram')
            ->assertNotDispatched('open-social-program-modal');

        // Jamaah dapat menyimpan pendaftaran peserta baru
        $component->set('participantName', $jamaah->name)
            ->set('participantProgram', $this->yatimProgram->name)
            ->set('participantAmount', '200000')
            ->set('participantPeriod', 'Bulanan')
            ->set('participantStatus', 'AKTIF')
            ->call('saveParticipant');

        $this->assertDatabaseHas('program_participants', [
            'name' => $jamaah->name,
            'program_name' => $this->yatimProgram->name,
            'monthly_amount' => 200000,
        ]);
    }
}



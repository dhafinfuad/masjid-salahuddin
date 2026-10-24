<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Portal\JadwalSholatPage;
use App\Livewire\Portal\KegiatanMasjidPage;
use App\Livewire\Portal\PetugasSholatPage;
use App\Livewire\Portal\ProfilMasjidPage;
use App\Models\MasjidSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilterResetButtonsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
        $this->admin = User::factory()->create([
            'role' => 'Master',
        ]);
    }

    public function test_admin_kajian_filter_reset_restores_default_smart_rollover_period(): void
    {
        $this->actingAs($this->admin);

        $now = Carbon::now('Asia/Jakarta');
        $defaultMonth = (string) $now->month;
        $defaultYear = (string) $now->year;

        Livewire::test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('pekananSearch', 'Pekan Sirah')
            ->set('jumatSearch', 'Khutbah Istiqlal')
            ->set('kajianMonthFilter', '1')
            ->set('kajianYearFilter', '2023')
            ->call('resetKajianFilters')
            ->assertSet('pekananSearch', '')
            ->assertSet('jumatSearch', '')
            ->assertSet('kajianMonthFilter', $defaultMonth)
            ->assertSet('kajianYearFilter', $defaultYear)
            ->assertSee('Reset');
    }

    public function test_admin_agenda_filter_reset_restores_default_state(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('agendaSearch', 'Tabligh Akbar')
            ->set('agendaFilterStatus', 'Selesai')
            ->call('resetAgendaFilters')
            ->assertSet('agendaSearch', '')
            ->assertSet('agendaFilterStatus', 'all');
    }

    public function test_admin_prayer_duty_filter_reset_restores_current_week_and_period(): void
    {
        $this->actingAs($this->admin);

        $now = Carbon::now('Asia/Jakarta');
        $firstDayOfMonth = Carbon::create($now->year, $now->month, 1, 0, 0, 0, 'Asia/Jakarta');
        $currentWeekNumber = (int) ceil(($now->day + $firstDayOfMonth->dayOfWeekIso - 1) / 7);

        Livewire::test(AdminDashboard::class, ['tab' => 'petugas'])
            ->set('prayerDutyFilterWeek', 'all')
            ->set('prayerDutyFilterMonth', '1')
            ->set('prayerDutyFilterYear', '2022')
            ->call('resetPrayerDutyFilters')
            ->assertSet('prayerDutyFilterWeek', (string) $currentWeekNumber)
            ->assertSet('prayerDutyFilterMonth', (string) $now->month)
            ->assertSet('prayerDutyFilterYear', (string) $now->year);
    }

    public function test_admin_finance_filter_reset_restores_initial_defaults(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeSearch', 'Infaq Khusus')
            ->set('financeTypeFilter', 'pemasukan')
            ->set('financeMonthFilter', ['1', '2'])
            ->set('financeYearFilter', '2021')
            ->call('resetFinanceFilters')
            ->assertSet('financeSearch', '')
            ->assertSet('financeTypeFilter', 'all')
            ->assertSet('financeMonthFilter', [(string) today()->month])
            ->assertSet('financeYearFilter', (string) today()->year);
    }

    public function test_admin_employee_filter_reset_restores_initial_defaults(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'pegawai'])
            ->set('employeeSearch', 'Marbot')
            ->set('employeeFilterStatus', 'Aktif')
            ->set('employeeFilterLink', 'linked')
            ->call('resetEmployeeFilters')
            ->assertSet('employeeSearch', '')
            ->assertSet('employeeFilterStatus', 'all')
            ->assertSet('employeeFilterLink', 'all');
    }

    public function test_admin_catalog_and_participant_filter_reset(): void
    {
        $this->actingAs($this->admin);

        $now = Carbon::now('Asia/Jakarta');

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('catalogSearch', 'Santunan')
            ->set('catalogMonth', '1')
            ->set('catalogYear', '2024')
            ->call('resetCatalogFilters')
            ->assertSet('catalogSearch', '')
            ->assertSet('catalogMonth', (string) $now->month)
            ->assertSet('catalogYear', (string) $now->year)
            ->set('socialParticipantSearch', 'Mustahik')
            ->set('socialProgramFilter', 'Santunan')
            ->set('socialParticipantPeriodFilter', ['Bulanan'])
            ->call('resetParticipantFilters')
            ->assertSet('socialParticipantSearch', '')
            ->assertSet('socialProgramFilter', 'all')
            ->assertSet('socialParticipantPeriodFilter', []);
    }

    public function test_admin_saran_filter_reset_restores_initial_defaults(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'saran'])
            ->set('saranSearch', 'Tempat Wudhu')
            ->set('saranStatusFilter', 'Dibaca')
            ->set('saranCategoryFilter', 'Fasilitas')
            ->call('resetSaranFilters')
            ->assertSet('saranSearch', '')
            ->assertSet('saranStatusFilter', 'all')
            ->assertSet('saranCategoryFilter', 'all');
    }

    public function test_admin_gallery_filter_reset_restores_initial_defaults(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'galeri'])
            ->set('gallerySearch', 'Buka Bersama')
            ->set('galleryYearFilter', '2023')
            ->call('resetGalleryFilters')
            ->assertSet('gallerySearch', '')
            ->assertSet('galleryYearFilter', 'all');
    }

    public function test_admin_user_filter_reset_restores_initial_defaults(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class, ['tab' => 'users'])
            ->set('search', 'DKM User')
            ->set('userFilterRole', 'Ketua')
            ->call('resetUserFilters')
            ->assertSet('search', '')
            ->assertSet('userFilterRole', 'all');
    }

    public function test_portal_kegiatan_page_reset_filters(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        Livewire::test(KegiatanMasjidPage::class)
            ->set('search', 'Khutbah Spesial')
            ->set('selectedCategory', 'jumat')
            ->set('selectedMonth', '2')
            ->set('selectedYear', 2024)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('selectedCategory', 'all')
            ->assertSet('selectedMonth', (string) $now->month)
            ->assertSet('selectedYear', (int) $now->year)
            ->assertSee('Reset');
    }

    public function test_portal_petugas_sholat_page_reset_filters(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        Livewire::test(PetugasSholatPage::class)
            ->set('selectedMonth', 5)
            ->set('selectedYear', 2023)
            ->call('resetFilters')
            ->assertSet('selectedMonth', (int) $now->month)
            ->assertSet('selectedYear', (int) $now->year)
            ->assertSee('Reset');
    }

    public function test_portal_jadwal_sholat_page_reset_filters(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        Livewire::test(JadwalSholatPage::class)
            ->set('selectedMonth', 3)
            ->set('selectedYear', 2022)
            ->set('cityId', '1301')
            ->call('resetFilters')
            ->assertSet('selectedMonth', (int) $now->month)
            ->assertSet('selectedYear', (int) $now->year)
            ->assertSet('cityId', '1634')
            ->assertSee('Reset');
    }

    public function test_portal_profil_masjid_page_reset_filters(): void
    {
        Livewire::test(ProfilMasjidPage::class)
            ->set('activeTab', 'galeri')
            ->set('gallerySearch', 'Idul Adha')
            ->set('galleryYear', '2024')
            ->call('resetFilters')
            ->assertSet('gallerySearch', '')
            ->assertSet('galleryYear', 'all')
            ->assertSee('Reset');
    }
}

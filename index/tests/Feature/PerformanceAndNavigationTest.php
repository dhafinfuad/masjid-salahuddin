<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Portal\PortalPage;
use App\Models\Agenda;
use App\Models\Kajian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PerformanceAndNavigationTest extends TestCase
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
    public function test_admin_dashboard_renders_scoped_tab_partials()
    {
        $this->actingAs($this->adminUser);

        // Tab: kegiatan (unified)
        $responseKegiatan = $this->get('/admin/kegiatan');
        $responseKegiatan->assertOk();
        $responseKegiatan->assertSee('Manajemen Kegiatan Masjid');
        $responseKegiatan->assertSee('Kajian');
        $responseKegiatan->assertSee('Kajian Jumat', false);
        $responseKegiatan->assertSee('Kegiatan Akbar');
        $responseKegiatan->assertSee('One Day One Juz');

        // Tab: kajian (backward compatibility alias)
        $responseKajian = $this->get('/admin/kajian');
        $responseKajian->assertOk();
        $responseKajian->assertSee('Manajemen Jadwal Kajian');

        // Tab: finance
        $responseFinance = $this->get('/admin/finance');
        $responseFinance->assertOk();
        $responseFinance->assertSee('Kas & Keuangan Masjid', false);

        // Tab: petugas
        $responsePetugas = $this->get('/admin/petugas');
        $responsePetugas->assertOk();
        $responsePetugas->assertSee('Jadwal Penugasan Petugas Ibadah');
    }

    /** @test */
    public function test_livewire_tab_switching_works_smoothly()
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class)
            ->assertSet('currentTab', 'dashboard')
            ->call('switchTab', 'kegiatan')
            ->assertSet('currentTab', 'kegiatan')
            ->assertSee('Manajemen Kegiatan Masjid')
            ->call('switchKegiatanSubTab', 'jumat')
            ->assertSet('kegiatanSubTab', 'jumat')
            ->call('switchKegiatanSubTab', 'agenda')
            ->assertSet('kegiatanSubTab', 'agenda')
            ->call('switchKegiatanSubTab', 'odoj')
            ->assertSet('kegiatanSubTab', 'odoj')
            ->call('switchTab', 'kajian')
            ->assertSet('currentTab', 'kajian')
            ->assertSee('Manajemen Jadwal Kajian')
            ->call('switchTab', 'finance')
            ->assertSet('currentTab', 'finance')
            ->assertSee('Kas & Keuangan Masjid', false)
            ->call('switchTab', 'petugas')
            ->assertSet('currentTab', 'petugas')
            ->assertSee('Jadwal Penugasan Petugas Ibadah');
    }

    /** @test */
    public function test_server_side_pagination_and_reset_hooks()
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class)
            ->call('switchTab', 'kajian')
            ->set('search', 'Tafsir')
            ->call('sortBy', 'title')
            ->assertOk();

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class)
            ->call('switchTab', 'finance')
            ->set('financeSearch', 'Infaq')
            ->call('sortBy', 'amount', 'finance')
            ->assertOk();
    }

    /** @test */
    public function test_portal_and_login_contain_instant_wire_navigate()
    {
        $portalResponse = $this->get('/');
        $portalResponse->assertOk();
        $portalResponse->assertSee('wire:navigate.hover', false);
        $portalResponse->assertSee('Kegiatan Masjid');
        $portalResponse->assertSee('Kajian');
        $portalResponse->assertSee('Khutbah Jumat');
        $portalResponse->assertSee('Kegiatan Akbar');

        $loginResponse = $this->get('/auth/login');
        $loginResponse->assertOk();
        $loginResponse->assertSee('wire:navigate.hover', false);
    }

    /** @test */
    public function test_independent_search_across_kegiatan_subtabs()
    {
        $today = \Carbon\Carbon::now()->format('Y-m-d');

        Kajian::create([
            'type' => 'pekanan',
            'title' => 'Tafsir Ibnu Katsir Surat Al-Baqarah',
            'speaker_name' => 'Ustadz Abdullah',
            'date' => $today,
            'time_display' => '09:00 - 11:00',
        ]);

        Kajian::create([
            'type' => 'jumat',
            'title' => 'Menjaga Keikhlasan dalam Beribadah',
            'khatib_name' => 'Dr. H. Mulyadi, M.Ag',
            'date' => $today,
            'time_display' => '11:45 - 12:45',
        ]);

        Agenda::create([
            'title' => 'Peringatan Nuzulul Quran Akbar',
            'description' => 'Tabligh akbar dan santunan anak yatim',
            'event_date' => $today,
            'budget' => 20000000,
            'status' => 'Direncanakan',
            'committee_members' => 'Khudori dkk',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class)
            ->call('switchTab', 'kegiatan')
            // 1. Search in Pekanan only
            ->set('pekananSearch', 'Tafsir')
            ->assertSee('Tafsir Ibnu Katsir')
            ->assertSee('Menjaga Keikhlasan') // Jumat is NOT filtered by pekananSearch!
            ->assertSee('Peringatan Nuzulul Quran') // Agenda is NOT filtered by pekananSearch!
            // 2. Search in Jumat only
            ->set('pekananSearch', '')
            ->set('jumatSearch', 'Keikhlasan')
            ->assertSee('Tafsir Ibnu Katsir') // Pekanan is NOT filtered by jumatSearch!
            ->assertSee('Menjaga Keikhlasan')
            ->assertSee('Peringatan Nuzulul Quran') // Agenda is NOT filtered by jumatSearch!
            // 3. Search in Agenda only
            ->set('jumatSearch', '')
            ->set('agendaSearch', 'Nuzulul')
            ->assertSee('Tafsir Ibnu Katsir') // Pekanan is NOT filtered by agendaSearch!
            ->assertSee('Menjaga Keikhlasan') // Jumat is NOT filtered by agendaSearch!
            ->assertSee('Peringatan Nuzulul Quran')
            // 4. Reset filters
            ->call('resetKajianFilters')
            ->assertSet('pekananSearch', '')
            ->assertSet('jumatSearch', '')
            ->call('resetAgendaFilters')
            ->assertSet('agendaSearch', '');
    }

    /** @test */
    public function test_breadcrumb_dynamically_reflects_active_tab_subtab_and_subview()
    {
        $component = Livewire::actingAs($this->adminUser)->test(AdminDashboard::class);

        // 1. Dashboard
        $this->assertEquals('Dashboard', $component->instance()->getBreadcrumbTitle());

        // 2. Kegiatan & its sub-tabs
        $component->call('switchTab', 'kegiatan');
        $component->set('kegiatanSubTab', 'pekanan');
        $this->assertEquals('Kegiatan > Kajian Pekanan', $component->instance()->getBreadcrumbTitle());

        $component->set('kegiatanSubTab', 'jumat');
        $this->assertEquals('Kegiatan > Kajian Jumat & Khutbah', $component->instance()->getBreadcrumbTitle());

        $component->set('kegiatanSubTab', 'agenda');
        $this->assertEquals('Kegiatan > Kegiatan Akbar', $component->instance()->getBreadcrumbTitle());

        $component->set('kegiatanSubTab', 'odoj');
        $this->assertEquals('Kegiatan > One Day One Juz', $component->instance()->getBreadcrumbTitle());

        // 3. Petugas Shalat
        $component->call('switchTab', 'petugas');
        $this->assertEquals('Petugas Shalat', $component->instance()->getBreadcrumbTitle());

        // 4. Kas & Keuangan (Utama, Program with sub-views, Kategori)
        $component->call('switchTab', 'finance');
        $component->set('financeSubTab', 'utama');
        $this->assertEquals('Kas & Keuangan > Kas Utama', $component->instance()->getBreadcrumbTitle());

        $component->set('financeSubTab', 'program');
        $component->set('programSubView', 'ringkasan');
        $this->assertEquals('Kas & Keuangan > Kas Program > Ringkasan', $component->instance()->getBreadcrumbTitle());

        $component->set('programSubView', 'peserta');
        $this->assertEquals('Kas & Keuangan > Kas Program > Peserta', $component->instance()->getBreadcrumbTitle());

        $component->set('programSubView', 'potongan');
        $this->assertEquals('Kas & Keuangan > Kas Program > Potongan', $component->instance()->getBreadcrumbTitle());

        $component->set('programSubView', 'penerimaan');
        $this->assertEquals('Kas & Keuangan > Kas Program > Setoran Kantor', $component->instance()->getBreadcrumbTitle());

        $component->set('financeSubTab', 'kategori');
        $this->assertEquals('Kas & Keuangan > Kategori', $component->instance()->getBreadcrumbTitle());

        // 5. Program Sosial
        $component->call('switchTab', 'programs');
        $component->set('socialSubTab', 'katalog');
        $this->assertEquals('Program Sosial > Katalog Program', $component->instance()->getBreadcrumbTitle());

        $component->set('socialSubTab', 'peserta');
        $this->assertEquals('Program Sosial > Rekapitulasi Peserta', $component->instance()->getBreadcrumbTitle());

        // 6. Pengguna & Pengaturan
        $component->call('switchTab', 'users');
        $this->assertEquals('Pengguna & Role', $component->instance()->getBreadcrumbTitle());

        $component->call('switchTab', 'settings');
        $this->assertEquals('Pengaturan', $component->instance()->getBreadcrumbTitle());
    }

    /** @test */
    public function test_page_title_dynamically_reflects_active_tab_and_subtab()
    {
        $component = Livewire::actingAs($this->adminUser)->test(AdminDashboard::class);

        // 1. Dashboard
        $this->assertEquals('Dashboard — Masjid Salahuddin', $component->instance()->getPageTitle());

        // 2. Kegiatan & sub-tabs
        $component->call('switchTab', 'kegiatan');
        $component->set('kegiatanSubTab', 'pekanan');
        $this->assertEquals('Kajian — Masjid Salahuddin', $component->instance()->getPageTitle());

        $component->set('kegiatanSubTab', 'jumat');
        $this->assertEquals('Kajian Jumat & Khutbah — Masjid Salahuddin', $component->instance()->getPageTitle());

        $component->set('kegiatanSubTab', 'agenda');
        $this->assertEquals('Kegiatan Akbar — Masjid Salahuddin', $component->instance()->getPageTitle());

        $component->set('kegiatanSubTab', 'odoj');
        $this->assertEquals('One Day One Juz — Masjid Salahuddin', $component->instance()->getPageTitle());

        // 3. Petugas Shalat
        $component->call('switchTab', 'petugas');
        $this->assertEquals('Petugas Shalat — Masjid Salahuddin', $component->instance()->getPageTitle());

        // 4. Finance & sub-tabs
        $component->call('switchTab', 'finance');
        $component->set('financeSubTab', 'utama');
        $this->assertEquals('Kas & Keuangan — Masjid Salahuddin', $component->instance()->getPageTitle());

        $component->set('financeSubTab', 'program');
        $this->assertEquals('Kas Program — Masjid Salahuddin', $component->instance()->getPageTitle());

        $component->set('financeSubTab', 'kategori');
        $this->assertEquals('Kategori Kas — Masjid Salahuddin', $component->instance()->getPageTitle());

        // 5. Social programs
        $component->call('switchTab', 'programs');
        $this->assertEquals('Program Sosial — Masjid Salahuddin', $component->instance()->getPageTitle());

        // 6. Users
        $component->call('switchTab', 'users');
        $this->assertEquals('Manajemen Pengguna — Masjid Salahuddin', $component->instance()->getPageTitle());
        $component->set('userSubTab', 'kepengurusan');
        $this->assertEquals('Takmir & Kepengurusan — Masjid Salahuddin', $component->instance()->getPageTitle());

        // 7. Settings
        $component->call('switchTab', 'settings');
        $this->assertEquals('Pengaturan — Masjid Salahuddin', $component->instance()->getPageTitle());

        // 8. HTTP GET /admin/kegiatan displays title in HTML
        $response = $this->actingAs($this->adminUser)->get('/admin/kegiatan');
        $response->assertOk();
        $response->assertSee('<title>Kajian — Masjid Salahuddin</title>', false);
    }

    /** @test */
    public function test_search_bar_x_button_clears_search_and_resets_to_current_month_and_year()
    {
        $now = \Carbon\Carbon::now();
        $component = Livewire::actingAs($this->adminUser)->test(AdminDashboard::class);

        // 1. In Tab Kegiatan -> Jumat
        $component->call('switchTab', 'kegiatan');
        $component->call('switchKegiatanSubTab', 'jumat');
        $component->set('jumatSearch', 'dhafin');
        $this->assertEquals('dhafin', $component->get('jumatSearch'));

        // Call clearKajianSearch('jumat') - simulates clicking X button
        $component->call('clearKajianSearch', 'jumat');
        $this->assertEquals('', $component->get('jumatSearch'));
        $this->assertEquals((string) $now->month, $component->get('kajianMonthFilter'));
        $this->assertEquals((string) $now->year, $component->get('kajianYearFilter'));

        // 2. In Tab Kegiatan -> Pekanan
        $component->call('switchKegiatanSubTab', 'pekanan');
        $component->set('pekananSearch', 'Tafsir');
        $this->assertEquals('Tafsir', $component->get('pekananSearch'));

        // Call clearKajianSearch('pekanan') - simulates clicking X button
        $component->call('clearKajianSearch', 'pekanan');
        $this->assertEquals('', $component->get('pekananSearch'));
        $this->assertEquals((string) $now->month, $component->get('kajianMonthFilter'));
        $this->assertEquals((string) $now->year, $component->get('kajianYearFilter'));
    }

    /** @test */
    public function test_portal_program_register_and_dedicated_pages()
    {
        // 1. Portal Home Page Checks
        $portalResponse = $this->get('/');
        $portalResponse->assertOk();
        $portalResponse->assertSee('Daftar Sekarang');
        $portalResponse->assertSee('Silakan login terlebih dahulu untuk mendaftar!');
        $portalResponse->assertSee(route('portal.jadwal-sholat'));
        $portalResponse->assertSee(route('portal.petugas-sholat'));

        // 2. Jadwal Sholat Bulanan Page
        $jadwalResponse = $this->get('/jadwal-sholat');
        $jadwalResponse->assertOk();
        $jadwalResponse->assertSee('Jadwal Waktu Shalat Bulanan');
        $jadwalResponse->assertSee('Kota / Kabupaten (Kemenag)');
        $jadwalResponse->assertSee('Kota Malang');

        // 3. Petugas Sholat Bulanan Page
        $petugasResponse = $this->get('/petugas-sholat');
        $petugasResponse->assertOk();
        $petugasResponse->assertSee('Jadwal Petugas Shalat Bulanan');
        $petugasResponse->assertSee('Cetak PDF');
        $petugasResponse->assertSee(route('portal.petugas-sholat.print'));

        // 4. Cetak PDF Official View
        $cetakResponse = $this->get('/petugas-sholat/cetak?month=9&year=2026');
        $cetakResponse->assertOk();
        $cetakResponse->assertSee('JADWAL PENUGASAN IMAM');
        $cetakResponse->assertSee('window.print()');
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Portal\KegiatanMasjidPage;
use App\Livewire\Portal\PortalPage;
use App\Models\Kajian;
use App\Models\User;
use App\Services\PrayerTimeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianTimeAndTitleSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected PrayerTimeService $prayerService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'Master',
            'status' => 'AKTIF',
            'email_verified_at' => now(),
        ]);

        $this->prayerService = app(PrayerTimeService::class);
    }

    /** @test */
    public function test_prayer_service_calculates_correct_default_times_plus_20_minutes(): void
    {
        $testDate = Carbon::parse('2026-10-23'); // A Friday

        // 1. Jumat -> Dzuhur + 20 min
        $jumatTime = $this->prayerService->getDefaultKajianTime('jumat', $testDate);
        $this->assertNotNull($jumatTime);
        $this->assertStringEndsWith('WIB', $jumatTime);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2} WIB$/', $jumatTime);

        // 2. Pekanan -> Ashar + 20 min
        $pekananTime = $this->prayerService->getDefaultKajianTime('pekanan', $testDate);
        $this->assertNotNull($pekananTime);
        $this->assertStringEndsWith('WIB', $pekananTime);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2} WIB$/', $pekananTime);

        // 3. Tematik -> null
        $tematikTime = $this->prayerService->getDefaultKajianTime('tematik', $testDate);
        $this->assertNull($tematikTime);

        // Uji variasi antar bulan (memastikan waktu berbeda-beda tiap bulan dan minggu)
        $timeJan = $this->prayerService->getDefaultKajianTime('pekanan', '2026-01-15');
        $timeOct = $this->prayerService->getDefaultKajianTime('pekanan', '2026-10-15');
        $this->assertNotEquals($timeJan, $timeOct, 'Waktu Ashar Pekanan Januari dan Oktober harus berbeda karena pergerakan matahari.');

        $jumatJan = $this->prayerService->getDefaultKajianTime('jumat', '2026-01-15');
        $jumatOct = $this->prayerService->getDefaultKajianTime('jumat', '2026-10-15');
        $this->assertNotEquals($jumatJan, $jumatOct, 'Waktu Dzuhur Jumat Januari dan Oktober harus berbeda karena pergerakan matahari.');
    }

    /** @test */
    public function test_save_kajian_jumat_allows_nullable_title_and_syncs_time_display(): void
    {
        $fridayDate = Carbon::now()->next(Carbon::FRIDAY)->format('Y-m-d');
        $expectedDzuhurPlus20 = $this->prayerService->getDefaultKajianTime('jumat', $fridayDate);

        $payload = [
            'type' => 'jumat',
            'date' => $fridayDate,
            'time_display' => '', // sengaja dikosongkan
            'title' => '',        // sengaja dikosongkan
            'khatib_name' => 'Prof. Dr. KH. M. Hasyim',
            'mc_name' => 'Ahmad Faisal',
            'muadzin_name' => 'Bilal Al-Banjari',
            'khatib_phone' => '081234567890',
            'mc_notes' => 'Catatan Shalat Jumat',
        ];

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('showKajianModal', true)
            ->call('saveKajian', $payload)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kajians', [
            'type' => 'jumat',
            'date' => $fridayDate . ' 00:00:00',
            'title' => null,
            'time_display' => $expectedDzuhurPlus20,
            'khatib_name' => 'Prof. Dr. KH. M. Hasyim',
        ]);
    }

    /** @test */
    public function test_save_kajian_pekanan_syncs_time_display_with_ashar_plus_20_minutes(): void
    {
        $pekananDate = Carbon::now()->addDays(3)->format('Y-m-d');
        $expectedAsharPlus20 = $this->prayerService->getDefaultKajianTime('pekanan', $pekananDate);

        $payload = [
            'type' => 'pekanan',
            'date' => $pekananDate,
            'time_display' => '', // kosongkan agar tersinkron otomatis
            'title' => 'Kajian Riyadhus Shalihin Bab Ikhlas',
            'speaker_name' => 'Ustadz Hanan Attaki',
            'speaker_phone' => '081234567891',
        ];

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('showKajianModal', true)
            ->call('saveKajian', $payload)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kajians', [
            'type' => 'pekanan',
            'date' => $pekananDate . ' 00:00:00',
            'title' => 'Kajian Riyadhus Shalihin Bab Ikhlas',
            'time_display' => $expectedAsharPlus20,
            'speaker_name' => 'Ustadz Hanan Attaki',
        ]);
    }

    /** @test */
    public function test_save_kajian_tematik_keeps_time_display_empty_when_not_provided(): void
    {
        $tematikDate = Carbon::now()->addDays(7)->format('Y-m-d');

        $payload = [
            'type' => 'tematik',
            'date' => $tematikDate,
            'time_display' => '',
            'title' => 'Tabligh Akbar Menyambut Ramadhan 1448 H',
            'speaker_name' => 'Ustadz Adi Hidayat',
            'speaker_phone' => '081234567892',
        ];

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('showKajianModal', true)
            ->call('saveKajian', $payload)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kajians', [
            'type' => 'tematik',
            'date' => $tematikDate . ' 00:00:00',
            'title' => 'Tabligh Akbar Menyambut Ramadhan 1448 H',
            'time_display' => null,
        ]);
    }

    /** @test */
    public function test_ui_views_omit_time_display_for_friday_khutbah(): void
    {
        $fridayDate = Carbon::now()->next(Carbon::FRIDAY)->format('Y-m-d');
        $uniqueFridayTime = '11:43 WIB';

        $jumat = Kajian::create([
            'type' => 'jumat',
            'date' => $fridayDate,
            'time_display' => $uniqueFridayTime,
            'title' => null,
            'khatib_name' => 'Khatib Jumat Spesial',
            'muadzin_name' => 'Muadzin Spesial',
            'mc_name' => 'MC Spesial',
        ]);

        $pekananTime = '15:20 WIB';
        $pekanan = Kajian::create([
            'type' => 'pekanan',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time_display' => $pekananTime,
            'title' => 'Kajian Pekanan Spesial',
            'speaker_name' => 'Ustadz Spesial',
        ]);

        // 1. Admin Dashboard Kegiatan Tab
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->call('switchKegiatanSubTab', 'jumat')
            ->assertSee('Khatib Jumat Spesial')
            // Di tab jumat, waktu keterangan tidak boleh muncul di baris tabel
            ->assertDontSee('• ' . $uniqueFridayTime);

        // 2. Portal Page
        Livewire::test(PortalPage::class)
            ->assertSee('Khatib Jumat Spesial')
            ->assertDontSee('• ' . $uniqueFridayTime)
            ->assertSee('Kajian Pekanan Spesial')
            ->assertSee('• ' . $pekananTime);

        // 3. Kegiatan Masjid Page
        Livewire::test(KegiatanMasjidPage::class)
            ->assertSee('Khatib Jumat Spesial')
            ->assertDontSee('• ' . $uniqueFridayTime)
            ->assertSee('Kajian Pekanan Spesial')
            ->assertSee('• ' . $pekananTime);
    }

    /** @test */
    public function test_database_migration_retroactively_syncs_existing_records(): void
    {
        $fridayDate = Carbon::now()->next(Carbon::FRIDAY)->format('Y-m-d');
        $pekananDate = Carbon::now()->addDays(2)->format('Y-m-d');
        $tematikDate = Carbon::now()->addDays(4)->format('Y-m-d');

        // Buat record lawas sebelum sinkronisasi
        $jumatId = \Illuminate\Support\Facades\DB::table('kajians')->insertGetId([
            'type' => 'jumat',
            'date' => $fridayDate,
            'time_display' => '11:45 - 12:45',
            'title' => 'Judul Lama Khutbah',
            'khatib_name' => 'Khatib Lama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pekananId = \Illuminate\Support\Facades\DB::table('kajians')->insertGetId([
            'type' => 'pekanan',
            'date' => $pekananDate,
            'time_display' => '09:00 - 11:30',
            'title' => 'Judul Pekanan Lama',
            'speaker_name' => 'Pemateri Lama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tematikId = \Illuminate\Support\Facades\DB::table('kajians')->insertGetId([
            'type' => 'tematik',
            'date' => $tematikDate,
            'time_display' => '09:00 - 11:30',
            'title' => 'Judul Tematik Lama',
            'speaker_name' => 'Pemateri Tematik',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jalankan migrasi sinkronisasi
        $migration = require database_path('migrations/2026_10_24_100000_update_kajians_time_display_and_title.php');
        $migration->up();

        // 1. Verifikasi Jumat: title dikosongkan (null) dan time_display disinkronkan ke Dzuhur + 20 min
        $expectedDzuhurTime = $this->prayerService->getDefaultKajianTime('jumat', $fridayDate);
        $jumatRow = \Illuminate\Support\Facades\DB::table('kajians')->find($jumatId);
        $this->assertNull($jumatRow->title);
        $this->assertEquals($expectedDzuhurTime, $jumatRow->time_display);

        // 2. Verifikasi Pekanan: time_display disinkronkan ke Ashar + 20 min
        $expectedAsharTime = $this->prayerService->getDefaultKajianTime('pekanan', $pekananDate);
        $pekananRow = \Illuminate\Support\Facades\DB::table('kajians')->find($pekananId);
        $this->assertEquals($expectedAsharTime, $pekananRow->time_display);

        // 3. Verifikasi Tematik: time_display dikosongkan (null)
        $tematikRow = \Illuminate\Support\Facades\DB::table('kajians')->find($tematikId);
        $this->assertNull($tematikRow->time_display);
    }

    /** @test */
    public function test_artisan_kajian_sync_times_command(): void
    {
        $fridayDate = '2026-05-15';
        $pekananDate = '2026-05-18';

        $jumatId = \Illuminate\Support\Facades\DB::table('kajians')->insertGetId([
            'type' => 'jumat',
            'date' => $fridayDate,
            'time_display' => '11.27',
            'title' => 'Judul Jumat Lama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pekananId = \Illuminate\Support\Facades\DB::table('kajians')->insertGetId([
            'type' => 'pekanan',
            'date' => $pekananDate,
            'time_display' => '15:01',
            'title' => 'Judul Pekanan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('kajian:sync-times', ['--force' => true])
            ->assertSuccessful();

        $expectedDzuhurMay = $this->prayerService->getDefaultKajianTime('jumat', $fridayDate);
        $expectedAsharMay = $this->prayerService->getDefaultKajianTime('pekanan', $pekananDate);

        $jRow = \Illuminate\Support\Facades\DB::table('kajians')->find($jumatId);
        $this->assertEquals($expectedDzuhurMay, $jRow->time_display);
        $this->assertNull($jRow->title);

        $pRow = \Illuminate\Support\Facades\DB::table('kajians')->find($pekananId);
        $this->assertEquals($expectedAsharMay, $pRow->time_display);
    }

    /** @test */
    public function test_admin_dashboard_mount_self_heals_legacy_1501_and_1127_times(): void
    {
        $fridayDate = '2026-11-20';
        $pekananDate = '2026-11-23';

        // Rekayasa data legacy persis seperti keluhan pengguna
        $jumatId = \Illuminate\Support\Facades\DB::table('kajians')->insertGetId([
            'type' => 'jumat',
            'date' => $fridayDate,
            'time_display' => '11.27',
            'title' => 'Khutbah Legacy',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pekananId = \Illuminate\Support\Facades\DB::table('kajians')->insertGetId([
            'type' => 'pekanan',
            'date' => $pekananDate,
            'time_display' => '15:01',
            'title' => 'Pekanan Legacy',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Saat admin membuka dashboard, fungsi self-healing harus otomatis mendeteksi dan memperbarui baris-baris ini
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan']);

        $expectedDzuhurNov = $this->prayerService->getDefaultKajianTime('jumat', $fridayDate);
        $expectedAsharNov = $this->prayerService->getDefaultKajianTime('pekanan', $pekananDate);

        $jRow = \Illuminate\Support\Facades\DB::table('kajians')->find($jumatId);
        $this->assertEquals($expectedDzuhurNov, $jRow->time_display);
        $this->assertNull($jRow->title);

        $pRow = \Illuminate\Support\Facades\DB::table('kajians')->find($pekananId);
        $this->assertEquals($expectedAsharNov, $pRow->time_display);
    }
}

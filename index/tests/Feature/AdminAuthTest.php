<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Auth\LoginPage;
use App\Models\Category;
use App\Models\Event;
use App\Models\MasjidSetting;
use App\Models\Registrant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->admin = User::factory()->create([
            'name' => 'Ustadz Admin DKM',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->operator = User::factory()->create([
            'name' => 'Kang Operator DKM',
            'email' => 'operator@masjidsalahuddin.id',
            'password' => bcrypt('password'),
            'role' => 'operator',
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/auth/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/auth/login');
        $response->assertStatus(200);
        $response->assertSee('Login Pengurus DKM');
    }

    public function test_user_can_login_via_livewire(): void
    {
        Livewire::test(LoginPage::class)
            ->set('email', 'admin@masjidsalahuddin.id')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_user_can_login_using_email_id_prefix_only(): void
    {
        $user = User::factory()->create([
            'email' => 'dhafin.mahathir@pajak.go.id',
            'password' => bcrypt('password123'),
            'role' => 'operator',
            'status' => 'AKTIF',
        ]);

        Livewire::test(LoginPage::class)
            ->set('email', 'dhafin.mahathir')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        Livewire::test(LoginPage::class)
            ->set('email', 'admin@masjidsalahuddin.id')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertSet('errorMessage', 'Kombinasi email dan kata sandi tidak cocok.');

        $this->assertGuest();
    }

    public function test_quick_login_works_on_login_page(): void
    {
        Livewire::test(LoginPage::class)
            ->call('quickLogin', 'admin')
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Ahlan wa Sahlan');
        $response->assertSee('Agenda Kegiatan');
    }

    public function test_admin_can_switch_tabs(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class)
            ->call('switchTab', 'kajian')
            ->assertSet('currentTab', 'kajian')
            ->call('switchTab', 'petugas')
            ->assertSet('currentTab', 'petugas')
            ->call('switchTab', 'agenda')
            ->assertSet('currentTab', 'agenda')
            ->call('switchTab', 'settings')
            ->assertSet('currentTab', 'settings');
    }

    public function test_admin_can_create_event(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('openCreateEventModal')
            ->set('eventCategoryId', $category->id)
            ->set('eventTitle', 'Tabligh Akbar Menyambut Ramadhan')
            ->set('eventSpeakerName', 'Habib Muhammad')
            ->set('eventDate', '2026-03-20')
            ->set('eventTimeDisplay', '19:30 WIB')
            ->set('eventLocation', 'Ruang Sholat Utama')
            ->set('eventCapacity', 250)
            ->set('eventStatus', 'TAYANG')
            ->call('saveEvent')
            ->assertHasNoErrors()
            ->assertSet('showEventModal', false);

        $this->assertDatabaseHas('events', [
            'title' => 'Tabligh Akbar Menyambut Ramadhan',
            'speaker_name' => 'Habib Muhammad',
            'capacity' => 250,
            'status' => 'TAYANG',
        ]);
    }

    public function test_admin_can_toggle_registrant_attendance(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
        ]);

        $event = Event::create([
            'category_id' => $category->id,
            'title' => 'Kajian Fiqih Ibadah',
            'slug' => 'kajian-fiqih-ibadah',
            'speaker_name' => 'Ust. Zulkifli',
            'event_date' => '2026-04-10',
            'time_display' => '08:30 WIB',
            'location' => 'Masjid',
            'capacity' => 100,
            'status' => 'TAYANG',
        ]);

        $registrant = Registrant::create([
            'event_id' => $event->id,
            'ticket_code' => 'REG-TEST-001',
            'full_name' => 'Budi Santoso',
            'whatsapp' => '081234567899',
            'email' => 'budi@test.com',
            'gender' => 'ikhwan',
            'status' => 'TERKONFIRMASI',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('toggleAttendance', $registrant->id);

        $this->assertEquals('HADIR', $registrant->fresh()->status);
        $this->assertNotNull($registrant->fresh()->checked_in_at);

        // Toggle back
        Livewire::test(AdminDashboard::class)
            ->call('toggleAttendance', $registrant->id);

        $this->assertEquals('TERKONFIRMASI', $registrant->fresh()->status);
        $this->assertNull($registrant->fresh()->checked_in_at);
    }

    public function test_admin_can_update_settings(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class)
            ->set('settingsName', 'Masjid Raya Salahuddin Al-Ayyubi')
            ->set('settingsPhone', '081199887766')
            ->call('saveSettings')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('masjid_settings', [
            'name' => 'Masjid Raya Salahuddin Al-Ayyubi',
            'phone' => '081199887766',
        ]);
    }

    public function test_admin_can_export_csv(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
        ]);

        $event = Event::create([
            'category_id' => $category->id,
            'title' => 'Kajian Subuh Berkah',
            'slug' => 'kajian-subuh-berkah',
            'speaker_name' => 'Ust. Zulkifli',
            'event_date' => '2026-04-10',
            'time_display' => '05:00 WIB',
            'location' => 'Masjid',
            'capacity' => 100,
            'status' => 'TAYANG',
        ]);

        Registrant::create([
            'event_id' => $event->id,
            'ticket_code' => 'REG-CSV-001',
            'full_name' => 'Ahmad Fulan',
            'whatsapp' => '081234567890',
            'email' => 'fulan@test.com',
            'gender' => 'ikhwan',
            'status' => 'HADIR',
            'checked_in_at' => now(),
        ]);

        $component = Livewire::test(AdminDashboard::class);
        $response = $component->call('exportCsv');
        
        $this->assertNotNull($response);
    }

    public function test_check_in_records_correct_timezone_wib(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
        ]);

        $event = Event::create([
            'category_id' => $category->id,
            'title' => 'Kajian Tafsir',
            'slug' => 'kajian-tafsir',
            'speaker_name' => 'Ust. Budi',
            'event_date' => '2026-04-10',
            'time_display' => '18:30 WIB',
            'location' => 'Masjid',
            'capacity' => 100,
            'status' => 'TAYANG',
        ]);

        $reg = Registrant::create([
            'event_id' => $event->id,
            'ticket_code' => 'REG-TIME-001',
            'full_name' => 'Cahyo Utomo',
            'whatsapp' => '081299998888',
            'email' => 'cahyo@test.com',
            'gender' => 'ikhwan',
            'status' => 'TERKONFIRMASI',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('toggleAttendance', $reg->id);

        $fresh = $reg->fresh();
        $this->assertEquals('HADIR', $fresh->status);
        $this->assertNotNull($fresh->checked_in_at);
        $this->assertEquals('Asia/Jakarta', config('app.timezone'));
    }

    public function test_admin_can_logout(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class)
            ->call('logout')
            ->assertRedirect('/auth/login');

        $this->assertGuest();
    }

    public function test_viewer_has_read_only_protection_and_cannot_modify_data(): void
    {
        $viewer = User::factory()->create([
            'name' => 'Haji Viewer',
            'email' => 'viewer@masjidsalahuddin.id',
            'role' => 'viewer',
        ]);

        $this->actingAs($viewer);

        $category = Category::create([
            'name' => 'Kajian Umum',
            'slug' => 'kajian-umum',
            'color_badge' => 'emerald',
        ]);

        $event = Event::create([
            'category_id' => $category->id,
            'title' => 'Kajian Subuh',
            'slug' => 'kajian-subuh',
            'speaker_name' => 'Ust. Fulan',
            'event_date' => '2026-05-10',
            'location' => 'Ruang Utama',
            'capacity' => 100,
            'status' => 'DRAF',
        ]);

        $reg = Registrant::create([
            'event_id' => $event->id,
            'ticket_code' => 'REG-VIEW-01',
            'full_name' => 'Jamaah Test',
            'whatsapp' => '08123456789',
            'gender' => 'ikhwan',
            'status' => 'TERKONFIRMASI',
        ]);

        // 1. Viewer cannot create or edit event
        Livewire::test(AdminDashboard::class)
            ->call('saveEvent', [
                'id' => $event->id,
                'title' => 'Hacked Title',
                'category_id' => $category->id,
                'speaker_name' => 'Hacker',
                'event_date' => '2026-05-11',
                'capacity' => 50,
                'status' => 'TAYANG',
            ])
            ->assertSet('toastMessage', 'Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');

        $this->assertEquals('Kajian Subuh', $event->fresh()->title);

        // 2. Viewer cannot change event status
        Livewire::test(AdminDashboard::class)
            ->call('quickSetEventStatus', $event->id, 'TAYANG')
            ->assertSet('toastMessage', 'Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');

        $this->assertEquals('DRAF', $event->fresh()->status);

        // 3. Viewer cannot check in attendance
        Livewire::test(AdminDashboard::class)
            ->call('toggleAttendance', $reg->id)
            ->assertSet('toastMessage', 'Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');

        $this->assertEquals('TERKONFIRMASI', $reg->fresh()->status);

        // 4. Viewer cannot save settings
        Livewire::test(AdminDashboard::class)
            ->call('saveSettings')
            ->assertSet('toastMessage', 'Akses ditolak: Hanya Admin DKM yang berwenang mengubah pengaturan masjid.');

        // 5. But Viewer CAN view dashboard and export CSV
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Viewer (Read-Only)');
        $response->assertSee('Mode Akses: Penasihat DKM (Viewer / Read-Only Protection)');
    }

    public function test_admin_and_operator_can_save_event_instantly_with_payload(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Tahsin Quran',
            'slug' => 'tahsin-quran',
            'color_badge' => 'teal',
        ]);

        $event = Event::create([
            'category_id' => $category->id,
            'title' => 'Tahsin Pemula Awal',
            'slug' => 'tahsin-pemula-awal',
            'speaker_name' => 'Ustadzah Hafidzah',
            'event_date' => '2026-05-13',
            'location' => 'Aula Akhwat',
            'capacity' => 40,
            'status' => 'DRAF',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('saveEvent', [
                'id' => $event->id,
                'title' => 'Tahsin Pemula Diperbarui',
                'category_id' => $category->id,
                'speaker_name' => 'Ustadzah Hafidzah Khairina',
                'speaker_role' => 'Pengajar Tahsin',
                'event_date' => '2026-05-13',
                'time_display' => '16:00 WIB',
                'location' => 'Aula Akhwat',
                'capacity' => 45,
                'status' => 'TAYANG',
                'description' => 'Materi baru',
            ])
            ->assertSet('toastMessage', 'Kegiatan berhasil diperbarui!')
            ->assertDispatched('close-event-modal');

        $fresh = $event->fresh();
        $this->assertEquals('Tahsin Pemula Diperbarui', $fresh->title);
        $this->assertEquals('Ustadzah Hafidzah Khairina', $fresh->speaker_name);
        $this->assertEquals('TAYANG', $fresh->status);
        $this->assertEquals(45, $fresh->capacity);
    }

    public function test_login_rate_limiter_throttles_after_repeated_failures(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Livewire::test(LoginPage::class)
                ->set('email', 'admin@masjidsalahuddin.id')
                ->set('password', 'wrong-password-attempt')
                ->call('login')
                ->assertSet('errorMessage', 'Kombinasi email dan kata sandi tidak cocok.');
        }

        // 6th attempt should be throttled
        Livewire::test(LoginPage::class)
            ->set('email', 'admin@masjidsalahuddin.id')
            ->set('password', 'wrong-password-attempt')
            ->call('login')
            ->assertSee('Terlalu banyak percobaan masuk yang gagal');
    }

    public function test_quick_login_is_blocked_in_production_environment(): void
    {
        $this->app['env'] = 'production';

        Livewire::test(LoginPage::class)
            ->call('quickLogin', 'master')
            ->assertForbidden();
    }

    public function test_csrf_token_meta_tag_is_rendered_in_layout(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('<meta name="csrf-token"', false);
    }

    public function test_lpj_upload_rejects_non_pdf_files(): void
    {
        $this->actingAs($this->admin);

        $fakeFile = \Illuminate\Http\UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        Livewire::test(AdminDashboard::class)
            ->set('agendaTitle', 'Kegiatan Maulid')
            ->set('agendaDate', '2026-06-01')
            ->set('agendaStatus', 'SELESAI')
            ->set('agendaReportPdf', $fakeFile)
            ->call('saveAgenda')
            ->assertHasErrors(['agendaReportPdf']);
    }

    public function test_finance_receipt_upload_rejects_unallowed_extensions(): void
    {
        $this->actingAs($this->admin);

        $fakeFile = \Illuminate\Http\UploadedFile::fake()->create('exploit.sh', 50, 'text/x-shellscript');

        Livewire::test(AdminDashboard::class)
            ->set('financeDate', '2026-06-01')
            ->set('financeType', 'pengeluaran')
            ->set('financeAmount', '500000')
            ->set('financeDescription', 'Pembelian Sound System')
            ->set('financeReceiptFile', $fakeFile)
            ->call('saveFinance')
            ->assertHasErrors(['financeReceiptFile']);
    }

    public function test_security_headers_are_present_in_responses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $this->assertFalse($response->headers->has('X-Powered-By'));

        $secureResponse = $this->get('https://localhost/');
        $secureResponse->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_direct_ngrok_access_is_redirected_when_origin_secret_configured(): void
    {
        putenv('ORIGIN_SECRET=test_secret_123');
        $this->app['env'] = 'production';

        // Direct request to ngrok without secret header should redirect 301 to official domain
        $response = $this->get('http://importer-dropper-panama.ngrok-free.dev/');
        $response->assertStatus(301);
        $response->assertRedirect('https://masjidsalahuddin.my.id');

        // Request with correct secret header passes through
        $validResponse = $this->withHeaders(['X-Origin-Verify' => 'test_secret_123'])
            ->get('http://importer-dropper-panama.ngrok-free.dev/');
        $validResponse->assertStatus(200);

        putenv('ORIGIN_SECRET');
        $this->app['env'] = 'testing';
    }
}

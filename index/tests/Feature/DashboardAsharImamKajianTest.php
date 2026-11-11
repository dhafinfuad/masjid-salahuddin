<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use App\Models\User;
use App\Models\Ustadz;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAsharImamKajianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Rabu, 11 November 2026 (pekan ke-2), sebelum waktu Ashar.
        Carbon::setTestNow(Carbon::create(2026, 11, 11, 10, 0, 0, 'Asia/Jakarta'));

        MasjidSetting::getActive();

        $this->admin = User::factory()->create([
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        PrayerDuty::create([
            'day_name' => 'Rabu',
            'prayer_time' => 'ashar',
            'week_pattern' => 'semua',
            'imam_name' => 'Muhammad Yahya',
            'muadzin_name' => 'Bilal Rabu',
            'tahun' => 2026,
        ]);

        Ustadz::create(['name' => "Imam Syafi'i Lc. MPd", 'photo' => 'ustadz-photos/imam-syafii.jpg']);
        Ustadz::create(['name' => 'M. Yasak Lc MA', 'photo' => 'ustadz-photos/m-yasak.jpg']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_dashboard_ashar_imam_follows_todays_kajian_speaker(): void
    {
        Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Pekanan Ashar',
            'speaker_name' => "Imam Syafi'i Lc. MPd",
            'speaker_photo' => 'ustadz-photos/imam-syafii.jpg',
            'date' => '2026-11-11',
            'time_display' => '15:30 WIB',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'dashboard'])
            ->assertSee("Imam : Imam Syafi'i Lc. MPd")
            ->assertDontSee('Imam : Muhammad Yahya');
    }

    public function test_changing_kajian_speaker_updates_dashboard_imam_and_speaker_photo(): void
    {
        $kajian = Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Pekanan Ashar',
            'speaker_name' => "Imam Syafi'i Lc. MPd",
            'speaker_photo' => 'ustadz-photos/imam-syafii.jpg',
            'date' => '2026-11-11',
            'time_display' => '15:30 WIB',
        ]);

        // Sama seperti openEditKajian() di browser: foto lama ikut terkirim sebagai kajianExistingPhoto.
        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kajianExistingPhoto', 'ustadz-photos/imam-syafii.jpg')
            ->call('saveKajian', [
                'id' => $kajian->id,
                'type' => 'pekanan',
                'date' => '2026-11-11',
                'time_display' => '15:30 WIB',
                'title' => 'Kajian Pekanan Ashar',
                'speaker_name' => 'M. Yasak Lc MA',
            ])
            ->assertHasNoErrors();

        $this->assertSame('M. Yasak Lc MA', $kajian->fresh()->speaker_name);
        $this->assertSame('ustadz-photos/m-yasak.jpg', $kajian->fresh()->speaker_photo);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'dashboard'])
            ->assertSee('Imam : M. Yasak Lc MA')
            ->assertDontSee('Imam : Muhammad Yahya');
    }

    public function test_changing_to_speaker_without_photo_clears_previous_speaker_photo(): void
    {
        $kajian = Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Pekanan Ashar',
            'speaker_name' => "Imam Syafi'i Lc. MPd",
            'speaker_photo' => 'ustadz-photos/imam-syafii.jpg',
            'date' => '2026-11-11',
            'time_display' => '15:30 WIB',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kajianExistingPhoto', 'ustadz-photos/imam-syafii.jpg')
            ->call('saveKajian', [
                'id' => $kajian->id,
                'type' => 'pekanan',
                'date' => '2026-11-11',
                'time_display' => '15:30 WIB',
                'title' => 'Kajian Pekanan Ashar',
                'speaker_name' => 'Ustadz Baru Tanpa Foto',
            ])
            ->assertHasNoErrors();

        $this->assertNull($kajian->fresh()->speaker_photo);
    }

    public function test_resaving_kajian_repairs_photo_left_over_from_previous_speaker(): void
    {
        // Data lama: pembicara sudah diganti, tetapi foto pembicara sebelumnya ikut tersimpan.
        $kajian = Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Pekanan Ashar',
            'speaker_name' => 'M. Yasak Lc MA',
            'speaker_photo' => 'ustadz-photos/imam-syafii.jpg',
            'date' => '2026-11-11',
            'time_display' => '15:30 WIB',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kajianExistingPhoto', 'ustadz-photos/imam-syafii.jpg')
            ->call('saveKajian', [
                'id' => $kajian->id,
                'type' => 'pekanan',
                'date' => '2026-11-11',
                'time_display' => '15:30 WIB',
                'title' => 'Kajian Pekanan Ashar',
                'speaker_name' => 'M. Yasak Lc MA',
            ])
            ->assertHasNoErrors();

        $this->assertSame('ustadz-photos/m-yasak.jpg', $kajian->fresh()->speaker_photo);
    }

    public function test_editing_kajian_without_changing_speaker_keeps_photo(): void
    {
        $kajian = Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Pekanan Ashar',
            'speaker_name' => "Imam Syafi'i Lc. MPd",
            'speaker_photo' => 'ustadz-photos/custom-upload.jpg',
            'date' => '2026-11-11',
            'time_display' => '15:30 WIB',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kajianExistingPhoto', 'ustadz-photos/custom-upload.jpg')
            ->call('saveKajian', [
                'id' => $kajian->id,
                'type' => 'pekanan',
                'date' => '2026-11-11',
                'time_display' => '15:30 WIB',
                'title' => 'Judul Baru',
                'speaker_name' => "Imam Syafi'i Lc. MPd",
            ])
            ->assertHasNoErrors();

        $this->assertSame('ustadz-photos/custom-upload.jpg', $kajian->fresh()->speaker_photo);
    }
}

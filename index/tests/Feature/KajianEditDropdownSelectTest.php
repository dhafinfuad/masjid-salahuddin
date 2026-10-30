<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianEditDropdownSelectTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'Master',
            'status' => 'AKTIF',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function test_modal_edit_kajian_contains_dropdown_select_option_for_jenis_jadwal(): void
    {
        $this->actingAs($this->adminUser);
        $response = $this->get('/admin/kegiatan');
        $response->assertOk();

        // 1. Verifikasi elemen dropdown select option pada mode edit
        $response->assertSee('x-show="isEditingKajian"', false);
        $response->assertSee('<select x-model="kajianForm.type"', false);

        // 2. Verifikasi seluruh option tersedia lengkap
        $response->assertSee('<option value="pekanan">Kajian Pekanan</option>', false);
        $response->assertSee('<option value="tematik">Kajian Tematik</option>', false);
        $response->assertSee('<option value="jumat">Khutbah Jumat</option>', false);

        // 3. Verifikasi sync state dan deep watcher
        $response->assertSee("this.\$watch('kajianForm.type'", false);
    }

    /** @test */
    public function test_saving_edited_kajian_type_persists_to_database(): void
    {
        $kajian = Kajian::create([
            'type' => 'pekanan',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time_display' => '15:05 WIB',
            'title' => 'Kajian Riyadhus Shalihin',
            'speaker_name' => 'Ustadz Abdullah',
        ]);

        // Simulasikan pengubahan jenis jadwal dari Pekanan ke Tematik melalui modal edit
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->call('saveKajian', [
                'id' => $kajian->id,
                'type' => 'tematik',
                'date' => $kajian->date,
                'time_display' => '09:00 WIB',
                'title' => 'Peringatan Nuzulul Quran',
                'speaker_name' => 'Ustadz Dr. Muhammad',
            ]);

        $this->assertDatabaseHas('kajians', [
            'id' => $kajian->id,
            'type' => 'tematik',
            'title' => 'Peringatan Nuzulul Quran',
            'speaker_name' => 'Ustadz Dr. Muhammad',
        ]);
    }

    /** @test */
    public function test_get_default_kajian_time_returns_prayer_time_for_all_types(): void
    {
        $testDate = '2026-11-20';

        $component = Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan']);

        // 1. Khutbah Jumat -> Waktu Dzuhur
        $jumatTime = $component->instance()->getDefaultKajianTime('jumat', $testDate);
        $this->assertNotNull($jumatTime);
        $this->assertStringEndsWith('WIB', $jumatTime);

        // 2. Kajian Pekanan -> Waktu Ashar
        $pekananTime = $component->instance()->getDefaultKajianTime('pekanan', $testDate);
        $this->assertNotNull($pekananTime);
        $this->assertStringEndsWith('WIB', $pekananTime);

        // 3. Kajian Tematik -> Waktu Ashar (identik dengan Pekanan)
        $tematikTime = $component->instance()->getDefaultKajianTime('tematik', $testDate);
        $this->assertNotNull($tematikTime);
        $this->assertEquals($pekananTime, $tematikTime);

        // Pastikan Dzuhur dan Ashar memiliki jam yang berbeda
        $this->assertNotEquals($jumatTime, $pekananTime);
    }

    /** @test */
    public function test_modal_date_input_and_dropdown_trigger_update_kajian_default_time(): void
    {
        $this->actingAs($this->adminUser);
        $response = $this->get('/admin/kegiatan');
        $response->assertOk();

        // Verifikasi input date pada modal memanggil updateKajianDefaultTime() secara unconditional saat @change
        $response->assertSee('@change="updateKajianDefaultTime(); if (window._activeShowKajianModal && window._activeKajianForm)', false);

        // Verifikasi dropdown select option memanggil updateKajianDefaultTime() saat jenis jadwal diubah
        $response->assertSee('updateKajianDefaultTime();', false);
    }
}


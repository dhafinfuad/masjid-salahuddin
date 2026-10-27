<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KajianModalStateTest extends TestCase
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
    public function test_kegiatan_subtab_switching_to_jumat_preserves_state(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->assertSet('currentTab', 'kegiatan')
            ->assertSet('kegiatanSubTab', 'pekanan')
            ->call('switchKegiatanSubTab', 'jumat')
            ->assertSet('kegiatanSubTab', 'jumat')
            ->assertSet('kajianSubTab', 'jumat');
    }

    /** @test */
    public function test_open_create_kajian_sets_modal_state_and_dispatches_event(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('kegiatanSubTab', 'jumat')
            ->call('openCreateKajian', 'jumat')
            ->assertSet('showKajianModal', true)
            ->assertSet('kajianType', 'jumat')
            ->assertDispatched('open-kajian-modal')
            ->assertSeeHtml("showKajianModal: true");
    }

    /** @test */
    public function test_close_kajian_modal_resets_state_and_dispatches_event(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('showKajianModal', true)
            ->call('closeKajianModal')
            ->assertSet('showKajianModal', false)
            ->assertDispatched('close-kajian-modal')
            ->assertSeeHtml("showKajianModal: false");
    }

    /** @test */
    public function test_save_kajian_closes_modal_and_resets_state(): void
    {
        $payload = [
            'type' => 'jumat',
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'time_display' => '11:45 - 12:45',
            'title' => 'Khutbah Jumat Berkah',
            'khatib_name' => 'Ustadz Ahmad',
            'mc_name' => 'Bambang',
            'muadzin_name' => 'Bilal',
            'khatib_phone' => '081234567890',
            'mc_notes' => 'Catatan DKM',
        ];

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->set('showKajianModal', true)
            ->call('saveKajian', $payload)
            ->assertSet('showKajianModal', false)
            ->assertDispatched('close-kajian-modal');

        $this->assertDatabaseHas('kajians', [
            'title' => 'Khutbah Jumat Berkah',
            'type' => 'jumat',
            'khatib_name' => 'Ustadz Ahmad',
        ]);
    }

    /** @test */
    public function test_open_and_close_import_kajian_modal_state_and_events(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            ->call('openImportKajian', 'jumat')
            ->assertSet('showImportKajianModal', true)
            ->assertSet('importKajianType', 'jumat')
            ->assertSet('importKajianTab', 'paste')
            ->assertDispatched('open-import-kajian-modal', type: 'jumat')
            ->assertSeeHtml('Import Jadwal Khutbah Jumat')
            ->assertSeeHtml('Impor perencanaan kegiatan dari Excel atau file CSV')
            ->assertDontSeeHtml('Jenis Kegiatan :')
            ->call('closeImportKajianModal')
            ->assertSet('showImportKajianModal', false)
            ->assertDispatched('close-import-kajian-modal')
            ->call('openImportKajian', 'pekanan')
            ->assertSet('showImportKajianModal', true)
            ->assertSet('importKajianType', 'pekanan')
            ->assertDispatched('open-import-kajian-modal', type: 'pekanan')
            ->assertSeeHtml('Import Jadwal Kajian')
            ->assertSeeHtml('Impor perencanaan kegiatan dari Excel atau file CSV')
            ->assertDontSeeHtml('Jenis Kegiatan :');
    }

    /** @test */
    public function test_opening_and_closing_ustadz_modal_preserves_editing_kajian_state(): void
    {
        $kajian = Kajian::create([
            'type' => 'pekanan',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time_display' => '16:00 WIB',
            'title' => 'Tafsir Jalalain',
            'speaker_name' => 'Ustadz Abdullah',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'kegiatan'])
            // Simulate editing a kajian
            ->set('showKajianModal', true)
            ->set('isEditingKajian', true)
            ->set('editingKajianId', $kajian->id)
            ->assertSet('isEditingKajian', true)
            ->assertSet('editingKajianId', $kajian->id)
            // Open ustadz modal from inside edit kajian
            ->call('openUstadzModal')
            ->assertSet('showUstadzModal', true)
            ->assertDispatched('open-ustadz-modal')
            // Crucial: showKajianModal and isEditingKajian must remain true and intact
            ->assertSet('showKajianModal', true)
            ->assertSet('isEditingKajian', true)
            ->assertSet('editingKajianId', $kajian->id)
            // Close ustadz modal
            ->call('closeUstadzModal')
            ->assertSet('showUstadzModal', false)
            ->assertDispatched('close-ustadz-modal')
            // Kajian modal remains in edit mode
            ->assertSet('showKajianModal', true)
            ->assertSet('isEditingKajian', true)
            ->assertSet('editingKajianId', $kajian->id);
    }

    /** @test */
    public function test_settings_database_ustadz_subtab_crud_operations(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'settings'])
            ->call('switchSettingsSubTab', 'ustadz')
            ->assertSet('settingsSubTab', 'ustadz')
            ->assertSeeHtml('Database Ustadz')
            ->assertSeeHtml('Direktori Ustadz')
            // Tambah Ustadz Baru
            ->set('ustadzName', 'Ust. Budi Hartono, M.Pd.')
            ->set('ustadzTitle', 'Kajian Fiqih Kontemporer')
            ->set('ustadzPhone', '081234567899')
            ->call('saveUstadz')
            ->assertSee('Ust. Budi Hartono, M.Pd.')
            ->assertSee('Kajian Fiqih Kontemporer');

        $this->assertDatabaseHas('ustadzs', [
            'name' => 'Ust. Budi Hartono, M.Pd.',
            'title' => 'Kajian Fiqih Kontemporer',
        ]);

        $ustadz = \App\Models\Ustadz::where('name', 'Ust. Budi Hartono, M.Pd.')->first();
        $this->assertNotNull($ustadz);

        // Edit Ustadz
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'settings'])
            ->call('switchSettingsSubTab', 'ustadz')
            ->call('editUstadz', $ustadz->id)
            ->assertSet('editingUstadzId', $ustadz->id)
            ->assertSet('ustadzName', 'Ust. Budi Hartono, M.Pd.')
            ->set('ustadzTitle', 'Tafsir Ayat Ahkam')
            ->call('saveUstadz')
            ->assertSee('Tafsir Ayat Ahkam');

        $this->assertDatabaseHas('ustadzs', [
            'id' => $ustadz->id,
            'title' => 'Tafsir Ayat Ahkam',
        ]);

        // Hapus Ustadz
        Livewire::actingAs($this->adminUser)
            ->test(AdminDashboard::class, ['tab' => 'settings'])
            ->call('switchSettingsSubTab', 'ustadz')
            ->call('deleteUstadz', $ustadz->id);

        $this->assertDatabaseMissing('ustadzs', [
            'id' => $ustadz->id,
        ]);
    }
}

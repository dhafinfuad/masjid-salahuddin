<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Agenda;
use App\Models\MasjidSetting;
use App\Models\OdojEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AgendaAndOdojTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->admin = User::factory()->create([
            'name' => 'Ustadz Admin DKM',
            'email' => 'admin@masjidsalahuddin.id',
            'role' => 'admin',
        ]);

        $this->viewer = User::factory()->create([
            'name' => 'Pak Viewer',
            'email' => 'viewer@masjidsalahuddin.id',
            'role' => 'viewer',
        ]);
    }

    public function test_admin_can_access_agenda_tab(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/agenda');
        $response->assertStatus(200);
        $response->assertSee('Agenda Masjid');
        $response->assertSee('One Day One Juz');
    }

    public function test_admin_can_create_agenda(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class)
            ->call('saveAgenda', [
                'title' => 'Buka Puasa Akbar 1448 H',
                'description' => 'Kegiatan buka puasa bersama 500 jamaah dan santunan dhuafa.',
                'event_date' => '2027-03-20',
                'budget' => '15000000',
                'status' => 'Direncanakan',
                'committee_members' => "Khudori (Ketua)\nJunaedi (Sekretaris)",
                'report_summary' => 'Draf rencana kegiatan buka puasa akbar.',
            ])
            ->assertSet('toastMessage', 'Agenda kegiatan baru berhasil disimpan.');

        $this->assertDatabaseHas('agendas', [
            'title' => 'Buka Puasa Akbar 1448 H',
            'status' => 'Direncanakan',
            'budget' => 15000000,
        ]);
    }

    public function test_admin_can_update_agenda(): void
    {
        $this->actingAs($this->admin);

        $agenda = Agenda::create([
            'title' => 'Tabligh Akbar Awal',
            'event_date' => '2026-10-10',
            'budget' => 5000000,
            'status' => 'Direncanakan',
        ]);

        Livewire::test(AdminDashboard::class)
            ->set('isEditingAgenda', true)
            ->set('editingAgendaId', $agenda->id)
            ->call('saveAgenda', [
                'title' => 'Tabligh Akbar Diperbarui',
                'description' => 'Tema: Menjaga Ukhuwah',
                'event_date' => '2026-10-12',
                'budget' => '8500000',
                'status' => 'Berjalan',
                'committee_members' => 'Eko (Ketua)',
                'report_summary' => 'Persiapan berjalan lancar.',
            ])
            ->assertSet('toastMessage', 'Agenda kegiatan berhasil diperbarui.');

        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'title' => 'Tabligh Akbar Diperbarui',
            'status' => 'Berjalan',
            'budget' => 8500000,
        ]);
    }

    public function test_admin_can_update_agenda_via_client_data_with_id_without_creating_new_record(): void
    {
        $this->actingAs($this->admin);

        $agenda = Agenda::create([
            'title' => 'Kajian Rutin Ahad Pagi',
            'description' => 'Kajian tafsir Al-Quran juz 30',
            'event_date' => '2026-11-01',
            'budget' => 2000000,
            'status' => 'Direncanakan',
            'committee_members' => 'Ustadz Ahmad',
            'report_summary' => '',
        ]);

        $initialCount = Agenda::count();

        // Simulating the exact client form submission from Alpine $wire.saveAgenda(agendaForm)
        Livewire::test(AdminDashboard::class)
            ->call('saveAgenda', [
                'id' => $agenda->id,
                'title' => 'Kajian Rutin Ahad Pagi (Revisi)',
                'description' => 'Kajian tafsir tematik bersama jamaah',
                'event_date' => '2026-11-08',
                'budget' => '3500000',
                'status' => 'Berjalan',
                'committee_members' => "Ustadz Ahmad (Pemateri)\nBudi (Koordinator)",
                'report_summary' => 'Jadwal dimundurkan 1 pekan atas kesepakatan DKM.',
            ])
            ->assertSet('toastMessage', 'Agenda kegiatan berhasil diperbarui.');

        // Verify count did not increase (did NOT create a duplicate record)
        $this->assertEquals($initialCount, Agenda::count());

        // Verify existing record was updated
        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'title' => 'Kajian Rutin Ahad Pagi (Revisi)',
            'description' => 'Kajian tafsir tematik bersama jamaah',
            'budget' => 3500000,
            'status' => 'Berjalan',
        ]);
        $this->assertEquals('2026-11-08', Carbon::parse($agenda->fresh()->event_date)->format('Y-m-d'));

        // Verify subsequent create operation creates a new record and does not overwrite
        Livewire::test(AdminDashboard::class)
            ->call('saveAgenda', [
                'id' => null,
                'title' => 'Santunan Anak Yatim',
                'description' => 'Santunan berkala',
                'event_date' => '2026-12-01',
                'budget' => '10000000',
                'status' => 'Direncanakan',
                'committee_members' => 'Dedy (Ketua)',
                'report_summary' => '',
            ])
            ->assertSet('toastMessage', 'Agenda kegiatan baru berhasil disimpan.');

        $this->assertEquals($initialCount + 1, Agenda::count());
        $this->assertDatabaseHas('agendas', [
            'title' => 'Santunan Anak Yatim',
            'status' => 'Direncanakan',
        ]);
    }

    public function test_admin_can_update_agenda_status_instantly(): void
    {
        $this->actingAs($this->admin);

        $agenda = Agenda::create([
            'title' => 'Kerja Bakti Masjid',
            'event_date' => '2026-10-01',
            'budget' => 1000000,
            'status' => 'Direncanakan',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('updateAgendaStatus', $agenda->id, 'SELESAI')
            ->assertSet('toastMessage', 'Status kegiatan diubah menjadi: SELESAI.');

        $this->assertEquals('SELESAI', $agenda->fresh()->status);
    }

    public function test_admin_can_delete_agenda(): void
    {
        $this->actingAs($this->admin);

        $agenda = Agenda::create([
            'title' => 'Agenda Dibatalkan',
            'event_date' => '2026-10-05',
            'budget' => 500000,
            'status' => 'Direncanakan',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('deleteAgenda', $agenda->id)
            ->assertSet('toastMessage', 'Agenda kegiatan berhasil dihapus.');

        $this->assertDatabaseMissing('agendas', ['id' => $agenda->id]);
    }

    public function test_admin_can_export_agenda_excel_and_pdf(): void
    {
        $this->actingAs($this->admin);

        Agenda::create([
            'title' => 'Agenda Export Test',
            'event_date' => '2026-10-20',
            'budget' => 2000000,
            'status' => 'Direncanakan',
        ]);

        $excelResponse = $this->get('/admin/agenda/export-excel');
        $excelResponse->assertStatus(200);
        $excelResponse->assertHeader('Content-Disposition');

        $pdfResponse = $this->get('/admin/agenda/export-pdf');
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('DAFTAR PERENCANAAN', false);
    }

    public function test_admin_can_print_agenda_lpj(): void
    {
        $this->actingAs($this->admin);

        $agenda = Agenda::create([
            'title' => 'Peringatan Nuzulul Quran',
            'event_date' => '2026-04-15',
            'budget' => 7500000,
            'status' => 'SELESAI',
            'committee_members' => "Fahmi (Ketua)\nJuli (Sekretaris)",
            'report_summary' => 'Pelaksanaan khidmat dihadiri 200 jamaah.',
        ]);

        $lpjResponse = $this->get("/admin/agenda/lpj/{$agenda->id}");
        $lpjResponse->assertStatus(200);
        $lpjResponse->assertSee('LAPORAN REALISASI AGENDA KEGIATAN');
        $lpjResponse->assertSee('Peringatan Nuzulul Quran');
        $lpjResponse->assertSee('Fahmi (Ketua)');
    }

    public function test_admin_can_view_uploaded_pdf_lpj(): void
    {
        $this->actingAs($this->admin);
        Storage::fake('public');

        $fakePdf = UploadedFile::fake()->createWithContent('laporan_kegiatan.pdf', '%PDF-1.4 sample content');
        $storedPath = $fakePdf->store('lpj', 'public');

        $agenda = Agenda::create([
            'title' => 'Peringatan Nuzulul Quran Akbar',
            'event_date' => '2026-04-15',
            'budget' => 7500000,
            'status' => 'SELESAI',
            'report_pdf_path' => $storedPath,
        ]);

        $response = $this->get("/admin/agenda/lpj/{$agenda->id}");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_uploading_non_pdf_file_for_agenda_lpj_is_rejected(): void
    {
        $this->actingAs($this->admin);
        Storage::fake('public');

        $fakeDoc = UploadedFile::fake()->createWithContent('document.docx', 'fake docx content');

        Livewire::test(AdminDashboard::class)
            ->set('agendaReportPdf', $fakeDoc)
            ->call('saveAgenda', [
                'title' => 'Kegiatan Uji Coba Dokumen',
                'event_date' => '2026-11-20',
                'budget' => '1000000',
                'status' => 'Direncanakan',
            ])
            ->assertHasErrors(['agendaReportPdf']);
    }

    public function test_admin_can_upload_pdf_lpj_when_saving_agenda(): void
    {
        $this->actingAs($this->admin);
        Storage::fake('public');

        $fakePdf = UploadedFile::fake()->createWithContent('berkas_resmi_lpj.pdf', '%PDF-1.4 sample content');

        Livewire::test(AdminDashboard::class)
            ->set('agendaReportPdf', $fakePdf)
            ->call('saveAgenda', [
                'title' => 'Kegiatan Syiar Akbar',
                'event_date' => '2026-12-01',
                'budget' => '5000000',
                'status' => 'SELESAI',
            ])
            ->assertHasNoErrors();

        $agenda = Agenda::where('title', 'Kegiatan Syiar Akbar')->first();
        $this->assertNotNull($agenda);
        $this->assertNotNull($agenda->report_pdf_path);
        Storage::disk('public')->assertExists($agenda->report_pdf_path);
    }

    public function test_admin_can_toggle_odoj_status(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->format('Y-m-d');
        $entry = OdojEntry::create([
            'group_name' => 'Laporan Madya Malang Bertilawah',
            'jamaah_name' => 'Khudori',
            'juz_number' => 1,
            'target_date' => $today,
            'status' => 'Belum',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('toggleOdojStatus', $entry->id);

        $this->assertEquals('Selesai', $entry->fresh()->status);
        $this->assertNotNull($entry->fresh()->completed_at);

        // Toggle back to Belum
        Livewire::test(AdminDashboard::class)
            ->call('toggleOdojStatus', $entry->id);

        $this->assertEquals('Belum', $entry->fresh()->status);
        $this->assertNull($entry->fresh()->completed_at);
    }

    public function test_admin_can_set_all_odoj_status(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->format('Y-m-d');
        for ($i = 1; $i <= 5; $i++) {
            OdojEntry::create([
                'group_name' => 'Laporan Madya Malang Bertilawah',
                'jamaah_name' => "Jamaah {$i}",
                'juz_number' => $i,
                'target_date' => $today,
                'status' => 'Belum',
            ]);
        }

        Livewire::test(AdminDashboard::class)
            ->set('odojDate', $today)
            ->call('setAllOdojStatus', 'Selesai');

        $this->assertEquals(5, OdojEntry::forDate($today)->where('status', 'Selesai')->count());
    }

    public function test_odoj_whatsapp_text_generator(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->format('Y-m-d');
        OdojEntry::create([
            'group_name' => 'Laporan Madya Malang Bertilawah',
            'jamaah_name' => 'Khudori',
            'juz_number' => 1,
            'target_date' => $today,
            'status' => 'Selesai',
        ]);

        $component = Livewire::test(AdminDashboard::class)
            ->set('odojDate', $today);

        $waText = $component->instance()->getOdojWhatsappText();
        $this->assertStringContainsString('LAPORAN MADYA MALANG BERTILAWAH', $waText);
        $this->assertStringContainsString('Khudori', $waText);
        $this->assertStringContainsString('SELESAI', $waText);
    }

    public function test_read_only_viewer_cannot_modify_agenda_or_odoj(): void
    {
        $this->actingAs($this->viewer);

        $agenda = Agenda::create([
            'title' => 'Agenda Dilindungi',
            'event_date' => '2026-11-01',
            'budget' => 3000000,
            'status' => 'Direncanakan',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('saveAgenda', [
                'title' => 'Hacked Agenda',
                'event_date' => '2026-11-02',
                'status' => 'Berjalan',
            ])
            ->assertSet('toastMessage', 'Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');

        Livewire::test(AdminDashboard::class)
            ->call('updateAgendaStatus', $agenda->id, 'SELESAI')
            ->assertSet('toastMessage', 'Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');

        Livewire::test(AdminDashboard::class)
            ->call('deleteAgenda', $agenda->id)
            ->assertSet('toastMessage', 'Akses ditolak: Akun viewer hanya memiliki izin baca (read-only).');

        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'title' => 'Agenda Dilindungi',
            'status' => 'Direncanakan',
        ]);
    }

    public function test_admin_can_set_single_odoj_status(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->format('Y-m-d');
        $entry = OdojEntry::create([
            'group_name' => 'Laporan Madya Malang Bertilawah',
            'jamaah_name' => 'Khudori',
            'juz_number' => 1,
            'target_date' => $today,
            'status' => 'Belum',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('setSingleOdojStatus', $entry->id, 'Selesai');

        $this->assertEquals('Selesai', $entry->fresh()->status);
        $this->assertNotNull($entry->fresh()->completed_at);

        Livewire::test(AdminDashboard::class)
            ->call('setSingleOdojStatus', $entry->id, 'Belum');

        $this->assertEquals('Belum', $entry->fresh()->status);
        $this->assertNull($entry->fresh()->completed_at);
    }

    public function test_admin_can_assign_up_to_two_employees_to_juz(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->format('Y-m-d');

        Livewire::test(AdminDashboard::class)
            ->set('odojDate', $today)
            ->set('assignJuzNumber', 5)
            ->set('assignPegawai1', 'Ahmad Fauzi')
            ->set('assignPegawai2', 'Budi Santoso')
            ->call('saveOdojAssignment')
            ->assertSet('toastMessage', 'Penugasan Juz 5 berhasil disimpan (Ahmad Fauzi / Budi Santoso).');

        $this->assertDatabaseHas('odoj_entries', [
            'juz_number' => 5,
            'jamaah_name' => 'Ahmad Fauzi / Budi Santoso',
        ]);
    }

    public function test_odoj_rotates_automatically_on_new_day(): void
    {
        $this->actingAs($this->admin);

        // 1. Setup base date: 2026-09-19
        $baseDate = '2026-09-19';
        OdojEntry::whereDate('target_date', $baseDate)->delete();

        for ($juz = 1; $juz <= 30; $juz++) {
            $name = "Jamaah {$juz}";
            if ($juz === 29) $name = 'Laila';
            if ($juz === 30) $name = 'Miswati';
            if ($juz === 1)  $name = 'Deril';
            if ($juz === 2)  $name = 'Bimo / Sukirman';

            OdojEntry::create([
                'group_name' => 'Laporan Madya Malang Bertilawah',
                'jamaah_name' => $name,
                'juz_number' => $juz,
                'target_date' => $baseDate,
                'status' => 'Selesai',
            ]);
        }

        // 2. Next day: 2026-09-20
        $nextDate = '2026-09-20';
        OdojEntry::whereDate('target_date', $nextDate)->delete();

        $rotatedEntries = OdojEntry::getEntriesForDate($nextDate)->keyBy('juz_number');

        $this->assertCount(30, $rotatedEntries);
        // Juz 30 = Laila (was Juz 29 on 19 Sep)
        $this->assertEquals('Laila', $rotatedEntries[30]->jamaah_name);
        // Juz 1 = Miswati (was Juz 30 on 19 Sep)
        $this->assertEquals('Miswati', $rotatedEntries[1]->jamaah_name);
        // Juz 2 = Deril (was Juz 1 on 19 Sep)
        $this->assertEquals('Deril', $rotatedEntries[2]->jamaah_name);
        // Juz 3 = Bimo / Sukirman (was Juz 2 on 19 Sep)
        $this->assertEquals('Bimo / Sukirman', $rotatedEntries[3]->jamaah_name);

        // Status for new day should be 'Belum'
        $this->assertEquals('Belum', $rotatedEntries[1]->status);
        $this->assertEquals('Belum', $rotatedEntries[30]->status);
    }

    public function test_admin_open_edit_agenda_modal_state_and_save_workflow(): void
    {
        $this->actingAs($this->admin);

        $agenda = Agenda::create([
            'title' => 'Rapat Pleno Ramadhan',
            'event_date' => '2026-03-01',
            'budget' => 2000000,
            'status' => 'Direncanakan',
            'youtube_url' => 'https://youtube.com/watch?v=sample123',
        ]);

        Livewire::test(AdminDashboard::class)
            ->call('editAgenda', $agenda->id)
            ->assertSet('showAgendaModal', true)
            ->assertSet('isEditingAgenda', true)
            ->assertSet('editingAgendaId', $agenda->id)
            ->assertDispatched('open-agenda-modal')
            ->call('saveAgenda', [
                'id' => $agenda->id,
                'title' => 'Rapat Pleno Ramadhan Terbuka',
                'event_date' => '2026-03-02',
                'budget' => 3500000,
                'status' => 'Berjalan',
                'youtube_url' => 'https://youtube.com/watch?v=sampleUpdated',
            ])
            ->assertSet('showAgendaModal', false)
            ->assertDispatched('close-agenda-modal');

        $updatedAgenda = $agenda->fresh();
        $this->assertEquals('Rapat Pleno Ramadhan Terbuka', $updatedAgenda->title);
        $this->assertEquals('2026-03-02', $updatedAgenda->event_date->format('Y-m-d'));
        $this->assertEquals(3500000, (int) $updatedAgenda->budget);
        $this->assertEquals('Berjalan', $updatedAgenda->status);
        $this->assertEquals('https://youtube.com/watch?v=sampleUpdated', $updatedAgenda->youtube_url);
    }
}


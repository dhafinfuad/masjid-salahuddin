<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Category;
use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\PrayerDuty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PdfExportConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $ketuaDkm;
    protected User $sekretaris;

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
            'name' => 'Administrator',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $this->ketuaDkm = User::create([
            'name' => 'Nazil Fuadi',
            'email' => 'ketua@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Ketua',
            'status' => 'AKTIF',
        ]);

        $this->sekretaris = User::create([
            'name' => 'Deril Amrizal Kholid',
            'email' => 'sekretaris@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Sekretaris',
            'status' => 'AKTIF',
        ]);

        MasjidSetting::create([
            'name' => 'Masjid Salahuddin',
            'address' => 'Jl. Merdeka No. 10',
            'phone' => '08123456789',
            'email' => 'dkm@masjidsalahuddin.id',
        ]);
    }

    public function test_agenda_export_pdf_matches_style_and_displays_real_names_and_qr(): void
    {
        $this->actingAs($this->admin);

        Agenda::create([
            'title' => 'Peringatan Nuzulul Quran 1447 H',
            'event_date' => '2026-03-25',
            'budget' => 5000000,
            'status' => 'Direncanakan',
            'committee_members' => "Pembina : Bimo Heriyanto\nKetua : Ichtiar Rachmatullah\nSekretaris : Deril Amrizal Kholid",
            'report_summary' => 'Persiapan acara berjalan lancar.',
        ]);

        $response = $this->get('/admin/agenda/export-pdf');
        $response->assertStatus(200);

        // Styling checks
        $response->assertSee('#06172e', false);
        $response->assertSee('table-header-blue', false);
        $response->assertSee('table-footer-blue', false);
        $response->assertSee('p-1.5', false);
        $response->assertSee('qrcode-container', false);
        $response->assertSee('TTE BSrE DISAHKAN', false);
        $response->assertSee('triggerPrintWithQr', false);

        // Signatures checks
        $response->assertSee('Nazil Fuadi', false);
        $response->assertSee('Ichtiar Rachmatullah', false);
        $response->assertDontSee('Ustadz Abdullah', false);
    }

    public function test_petugas_export_pdf_matches_style_and_displays_real_names_and_qr(): void
    {
        $this->actingAs($this->admin);

        PrayerDuty::create([
            'day_name' => 'Senin',
            'prayer_time' => 'dzuhur',
            'week_pattern' => 'semua',
            'imam_name' => 'Ust. Subhan',
            'muadzin_name' => 'Bilal Fauzan',
            'tahun' => 2026,
        ]);

        $response = $this->get('/admin/petugas/export-pdf');
        $response->assertStatus(200);

        // Styling checks
        $response->assertSee('#06172e', false);
        $response->assertSee('table-header-blue', false);
        $response->assertSee('p-1.5', false);
        $response->assertSee('qrcode-container', false);
        $response->assertSee('TTE BSrE DISAHKAN', false);
        $response->assertSee('triggerPrintWithQr', false);

        // Signatures checks
        $response->assertSee('Nazil Fuadi', false);
        $response->assertSee('Deril Amrizal Kholid', false);
    }

    public function test_kajian_pekanan_export_pdf_matches_style_and_displays_real_names_and_qr(): void
    {
        $this->actingAs($this->admin);

        Kajian::create([
            'type' => 'pekanan',
            'title' => 'Kajian Kitab Riyadhus Shalihin',
            'date' => Carbon::now()->addDays(2),
            'time_display' => '09:00 - 11:00 WIB',
            'speaker_name' => 'Ust. Dr. Firanda Andirja',
            'speaker_phone' => '08123456789',
        ]);

        $response = $this->get('/admin/kajian/export-pdf?type=pekanan');
        $response->assertStatus(200);

        // Styling checks
        $response->assertSee('#06172e', false);
        $response->assertSee('table-header-blue', false);
        $response->assertSee('p-1.5', false);
        $response->assertSee('qrcode-container', false);
        $response->assertSee('TTE BSrE DISAHKAN', false);
        $response->assertSee('triggerPrintWithQr', false);

        // Signatures checks
        $response->assertSee('Nazil Fuadi', false);
        $response->assertSee('Deril Amrizal Kholid', false);
    }

    public function test_kajian_jumat_export_pdf_matches_style_and_displays_real_names_and_qr(): void
    {
        $this->actingAs($this->admin);

        Kajian::create([
            'type' => 'jumat',
            'title' => 'Khutbah: Memakmurkan Masjid',
            'date' => Carbon::now()->next(Carbon::FRIDAY),
            'time_display' => '11:45 - 12:45 WIB',
            'khatib_name' => 'Ust. Dr. Syafiq Riza Basalamah',
            'mc_name' => 'Ust. Zaki',
            'muadzin_name' => 'Ust. Bilal',
        ]);

        $response = $this->get('/admin/kajian/export-pdf?type=jumat');
        $response->assertStatus(200);

        // Styling checks
        $response->assertSee('#06172e', false);
        $response->assertSee('table-header-blue', false);
        $response->assertSee('p-1.5', false);
        $response->assertSee('qrcode-container', false);
        $response->assertSee('TTE BSrE DISAHKAN', false);
        $response->assertSee('triggerPrintWithQr', false);

        // Signatures checks
        $response->assertSee('Nazil Fuadi', false);
        $response->assertSee('Deril Amrizal Kholid', false);
    }
}

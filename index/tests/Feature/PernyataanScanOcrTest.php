<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Category;
use App\Models\Employee;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use App\Services\PernyataanScanOcrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PernyataanScanOcrTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $jamaah;
    protected SocialProgram $programInfaq;
    protected SocialProgram $programYatim;
    protected SocialProgram $programZakat;
    protected SocialProgram $programQurban;
    protected SocialProgram $programDhuafa;

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
            'name' => 'Pengurus Masjid',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Ketua',
            'status' => 'AKTIF',
        ]);

        $this->jamaah = User::create([
            'name' => 'Jamaah Biasa',
            'email' => 'jamaah@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        Employee::create([
            'name' => 'Dilla Wahyu Permatasari',
            'nip_panjang' => '199712172018122001',
            'nip_pendek' => '122001',
            'jabatan' => 'Pelaksana',
            'status' => 'AKTIF',
        ]);

        $this->programInfaq = SocialProgram::firstOrCreate(['slug' => 'infaq-rutin'], [
            'name' => 'Infaq Rutin',
            'category' => 'infaq',
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
        ]);

        $this->programYatim = SocialProgram::firstOrCreate(['slug' => 'santunan-anak-yatim'], [
            'name' => 'Program Santunan Anak Yatim',
            'category' => 'yatim',
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
        ]);

        $this->programZakat = SocialProgram::firstOrCreate(['slug' => 'zakat-mal-rutin'], [
            'name' => 'Program Zakat Mal Rutin',
            'category' => 'zakat',
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
        ]);

        $this->programQurban = SocialProgram::firstOrCreate(['slug' => 'tabungan-qurban'], [
            'name' => 'Tabungan Qurban',
            'category' => 'qurban',
            'period_type' => 'tahunan',
            'status' => 'AKTIF',
        ]);

        $this->programDhuafa = SocialProgram::firstOrCreate(['slug' => 'santunan-dhuafa'], [
            'name' => 'Program Santunan Dhuafa',
            'category' => 'dhuafa',
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
        ]);
    }

    public function test_ocr_service_parses_form_text_accurately(): void
    {
        $service = new PernyataanScanOcrService();

        $sampleText = "
MASJID SALAHUDDIN KPP MADYA MALANG
Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja

Nama           Dilla Wahyu Permatasari
NIP            199712172018122001
Jabatan        Pelaksana
Seksi/Kelompok Seksi Pengawasan V
Nomor WhatsApp 085655560599

Saya bersedia dipotong Tunjangan Kinerja setiap bulan oleh Bendahara KPP Madya Malang, untuk disalurkan kepada Bendahara Masjid Salahuddin, dengan rincian berikut. Pemotongan berlaku mulai bulan November tahun 2026.

No  Program                 Nominal per Bulan
1   Zakat
2   Infaq & Sedekah         50.000
3   Tabungan Qurban
4   Santunan Anak Yatim     50.000
5   Santunan Dhuafa
";

        $parsed = $service->parseFormText($sampleText, 1);

        $this->assertEquals('Dilla Wahyu Permatasari', $parsed['nama']);
        $this->assertEquals('199712172018122001', $parsed['nip']);
        $this->assertEquals('Pelaksana', $parsed['jabatan']);
        $this->assertEquals('Seksi Pengawasan V', $parsed['seksi_kelompok']);
        $this->assertEquals('085655560599', $parsed['phone']);
        $this->assertEquals('Periode 11/2026', $parsed['effective_period']);
        $this->assertEquals(50000, $parsed['commitments']['infaq']);
        $this->assertEquals(50000, $parsed['commitments']['yatim']);
        $this->assertEquals(0, $parsed['commitments']['zakat']);
    }

    public function test_admin_can_process_scan_pernyataan_pdf(): void
    {
        $this->actingAs($this->admin);

        $fakePdf = UploadedFile::fake()->create('scan_mesin.pdf', 500, 'application/pdf');

        $test = Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('scanPernyataanFile', $fakePdf)
            ->call('processScanPernyataan')
            ->assertHasNoErrors()
            ->assertCount('scanPages', 5)
            ->assertSee('Dilla Wahyu Permatasari');

        $this->assertEquals('Moh. Lukman Hakim', $test->get('scanPages')[1]['nama']);
        $this->assertEquals('198404172006021003', $test->get('scanPages')[1]['nip']);
    }

    public function test_admin_can_save_scan_pernyataan_commitments_to_database(): void
    {
        $this->actingAs($this->admin);

        $fakePdf = UploadedFile::fake()->create('scan_mesin.pdf', 500, 'application/pdf');

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('scanPernyataanFile', $fakePdf)
            ->call('processScanPernyataan')
            ->call('saveScanPernyataanCommitments')
            ->assertHasNoErrors();

        // Verifikasi tabel program_participants
        $this->assertDatabaseHas('program_participants', [
            'name' => 'Dilla Wahyu Permatasari',
            'program_name' => 'Infaq Rutin',
            'monthly_amount' => 50000,
            'period' => 'Periode 11/2026',
        ]);

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Dilla Wahyu Permatasari',
            'program_name' => 'Program Santunan Anak Yatim',
            'monthly_amount' => 50000,
            'period' => 'Periode 11/2026',
        ]);

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Moh. Lukman Hakim',
            'program_name' => 'Infaq Rutin',
            'monthly_amount' => 200000,
            'period' => 'Periode 11/2026',
        ]);

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Alfan Machrus Afandi',
            'program_name' => 'Program Zakat Mal Rutin',
            'monthly_amount' => 100000,
            'period' => 'Periode 10/2026',
        ]);

        // Verifikasi tabel employees
        $this->assertDatabaseHas('employees', [
            'nip_panjang' => '199712172018122001',
            'name' => 'Dilla Wahyu Permatasari',
            'jabatan' => 'Pelaksana',
        ]);
    }

    public function test_non_admin_cannot_process_scan_pernyataan(): void
    {
        $this->actingAs($this->jamaah);

        $fakePdf = UploadedFile::fake()->create('scan_mesin.pdf', 500, 'application/pdf');

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('scanPernyataanFile', $fakePdf)
            ->call('processScanPernyataan')
            ->assertSet('scanError', 'Akses ditolak: Anda tidak memiliki wewenang mengimpor data komitmen.');
    }
}

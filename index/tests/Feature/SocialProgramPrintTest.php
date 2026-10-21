<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialProgramPrintTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Employee $employee;
    protected SocialProgram $yatimProgram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'Master',
            'status' => 'Aktif',
            'email' => 'admin.print@masjid.id',
        ]);

        $this->employee = Employee::create([
            'name' => 'Ichtiar Rachmatullah',
            'nip_panjang' => '198101012006041001',
            'nip_pendek' => '060098765',
            'jabatan' => 'Pelaksana (Seksi Pengawasan IV)',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
        ]);

        $this->yatimProgram = SocialProgram::create([
            'name' => 'Program Santunan Anak Yatim',
            'slug' => 'program-santunan-anak-yatim',
            'category' => 'yatim',
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
        ]);
    }

    public function test_guest_cannot_access_print_surat_pernyataan(): void
    {
        $response = $this->get('/admin/programs/print-surat-pernyataan');
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_print_surat_pernyataan_all_mode(): void
    {
        $this->actingAs($this->admin);

        ProgramParticipant::create([
            'name' => $this->employee->name,
            'program_name' => $this->yatimProgram->name,
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 150000,
            'period' => 'Periode 3/2026',
        ]);

        $response = $this->get('/admin/programs/print-surat-pernyataan?mode=all&year=2026');

        $response->assertStatus(200);
        $response->assertSee('Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja');
        $response->assertSee($this->employee->name);
        $response->assertSee('198101012006041001');
        $response->assertSee('Pelaksana');
        $response->assertSee('Seksi Pengawasan IV');
        $response->assertSee('Riwayat Potongan');
        $response->assertSee('Deril Amrizal Kholid');
        $response->assertSee('Seksi Penjaminan Kualitas Data');
        // Kolom Nominal per Bulan wajib kosong untuk diisi manual dengan bolpen
        $response->assertDontSee('Rp 150.000');
    }

    public function test_nominal_per_bulan_column_is_blank_for_manual_pen_filling(): void
    {
        $this->actingAs($this->admin);

        ProgramParticipant::create([
            'name' => $this->employee->name,
            'program_name' => $this->yatimProgram->name,
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 250000,
            'period' => 'Periode 1/2026',
        ]);

        $response = $this->get('/admin/programs/print-surat-pernyataan?mode=single&employee_id=' . $this->employee->id);

        $response->assertStatus(200);
        $response->assertSee('Nominal per Bulan');
        $response->assertDontSee('Rp 250.000');
        // Kolom harus berupa td kosong tanpa teks nominal
        $response->assertSee('<td></td>', false);
    }

    public function test_admin_can_print_single_employee_with_history(): void
    {
        $this->actingAs($this->admin);

        ProgramParticipant::create([
            'name' => $this->employee->name,
            'program_name' => $this->yatimProgram->name,
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 100000,
            'period' => 'Periode 1/2026',
        ]);

        $response = $this->get('/admin/programs/print-surat-pernyataan?mode=single&year=2026&employee_id=' . $this->employee->id . '&date_letter=15 Oktober 2026&effective_month=Januari&effective_year=2026');

        $response->assertStatus(200);
        $response->assertSee($this->employee->name);
        $response->assertSee('15 Oktober 2026');
        $response->assertSee('Januari');
        $response->assertSee('100k');
        $response->assertSee('Riwayat Potongan Tahun 2026');
    }

    public function test_admin_can_print_new_employee_form_without_history(): void
    {
        $this->actingAs($this->admin);

        $newEmp = Employee::create([
            'name' => 'Budi Santoso',
            'nip_panjang' => '199001012015021002',
            'jabatan' => 'Fungsional Pemeriksa (Seksi Pemeriksaan)',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
        ]);

        $response = $this->get('/admin/programs/print-surat-pernyataan?mode=new&employee_id=' . $newEmp->id);

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('199001012015021002');
        $response->assertSee('Fungsional Pemeriksa');
        $response->assertSee('Seksi Pemeriksaan');
        // Riwayat Potongan harus disembunyikan dan tanpa banner placeholder
        $response->assertDontSee('Riwayat Potongan Tahun');
        $response->assertDontSee('Belum Tercatat Riwayat Pemotongan Sebelumnya');
    }

    public function test_admin_can_print_blank_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/programs/print-surat-pernyataan?mode=blank');

        $response->assertStatus(200);
        $response->assertSee('Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja');
        $response->assertDontSee('Riwayat Potongan Tahun');
        // Tanggal menggunakan garis lurus solid (bukan underscore terpisah)
        $response->assertSee('Malang,');
        $response->assertSee('border-b border-slate-700', false);
        $response->assertDontSee('______________ 20 ______________');
        // Footer tidak lagi menampilkan kutipan ayat
        $response->assertDontSee('At-Taubah: 103');
        $response->assertDontSee('Al-Baqarah: 245');
    }

    public function test_fuzzy_matching_and_title_casing_for_employee_data(): void
    {
        $this->actingAs($this->admin);

        // Buat employee dengan input yang mengandung gelar akademik
        $emp = Employee::create([
            'name' => 'Bimo Heriyanto, S.E.',
            'nip_panjang' => '198501152010121001',
            'jabatan' => 'Account Representative (Seksi Pengawasan II)',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
        ]);

        // Verifikasi pada database nama otomatis dibersihkan dari gelar
        $this->assertEquals('Bimo Heriyanto', $emp->fresh()->name);

        // Buat participant dengan nama huruf kapital semua tanpa gelar (seperti hasil import Excel potongan)
        ProgramParticipant::create([
            'name' => 'BIMO HERIYANTO',
            'program_name' => $this->yatimProgram->name,
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 200000,
            'period' => 'Periode 2/2026',
        ]);

        $response = $this->get('/admin/programs/print-surat-pernyataan?mode=all&year=2026');

        $response->assertStatus(200);
        // Nama pegawai diformat Title Case Kapital per kata TANPA GELAR
        $response->assertSee('Bimo Heriyanto');
        $response->assertDontSee('S.E.');
        // NIP, Jabatan, dan Seksi/Kelompok otomatis terisi
        $response->assertSee('198501152010121001');
        $response->assertSee('Account Representative');
        $response->assertSee('Seksi Pengawasan II');
        // Tanggal format garis lurus solid
        $response->assertSee('Malang,');
        $response->assertSee('border-b border-slate-700', false);
        // Header Riwayat Potongan tidak ada uppercase tracking
        $response->assertDontSee('uppercase tracking-[0.1em]', false);
        $response->assertSee('Riwayat Potongan Tahun 2026');
        // Top action bar seragam dengan style UI proyek dan tanpa badge count
        $response->assertSee('no-print bg-slate-900', false);
        $response->assertDontSee('Total:');
        // Tidak ada kutipan ayat pada footer
        $response->assertDontSee('At-Taubah: 103');
        $response->assertDontSee('Al-Baqarah: 245');
    }

    public function test_all_mode_only_includes_currently_active_employees(): void
    {
        $this->actingAs($this->admin);

        // 1. Pegawai Aktif
        $activeEmp = Employee::create([
            'name' => 'Ahmad Fajar',
            'nip_panjang' => '199001012015011001',
            'jabatan' => 'Pelaksana',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
        ]);
        ProgramParticipant::create([
            'name' => 'Ahmad Fajar',
            'program_name' => $this->yatimProgram->name,
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 100000,
            'period' => 'Periode 1/2026',
        ]);

        // 2. Pegawai Mutasi
        $mutatedEmp = Employee::create([
            'name' => 'Bambang Mutasi',
            'nip_panjang' => '198002022005021002',
            'jabatan' => 'Fungsional',
            'unit_kerja' => 'KPP Pratama Batu',
            'status' => 'MUTASI',
        ]);
        ProgramParticipant::create([
            'name' => 'Bambang Mutasi',
            'program_name' => $this->yatimProgram->name,
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 100000,
            'period' => 'Periode 1/2026',
        ]);

        // 3. Pegawai Nonaktif
        $inactiveEmp = Employee::create([
            'name' => 'Cahyo Pensiun',
            'nip_panjang' => '196003031985031003',
            'jabatan' => 'Purna Tugas',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'NONAKTIF',
        ]);
        ProgramParticipant::create([
            'name' => 'Cahyo Pensiun',
            'program_name' => $this->yatimProgram->name,
            'social_program_id' => $this->yatimProgram->id,
            'monthly_amount' => 100000,
            'period' => 'Periode 1/2026',
        ]);

        $response = $this->get('/admin/programs/print-surat-pernyataan?mode=all&year=2026');

        $response->assertStatus(200);
        // Pegawai AKTIF wajib tercetak
        $response->assertSee('Ahmad Fajar');
        // Pegawai MUTASI & NONAKTIF TIDAK BOLEH tercetak pada mode Semua Pegawai
        $response->assertDontSee('Bambang Mutasi');
        $response->assertDontSee('Cahyo Pensiun');
    }

    public function test_default_effective_month_and_year_in_admin_modal_is_next_month(): void
    {
        $this->actingAs($this->admin);

        // Kasus 1: Misal saat ini bulan Oktober 2026, maka default adalah bulan November 2026
        \Carbon\Carbon::setTestNow('2026-10-21 12:00:00');
        $response = \Livewire\Livewire::test(\App\Livewire\Admin\AdminDashboard::class, ['tab' => 'programs']);
        $response->assertSee("printPernyataanEffectiveMonth: 'November'", false);
        $response->assertSee("printPernyataanEffectiveYear: '2026'", false);

        // Kasus 2: Misal saat ini bulan Desember 2026, maka tertampil secara otomatis adalah bulan Januari 2027
        \Carbon\Carbon::setTestNow('2026-12-15 12:00:00');
        $responseDec = \Livewire\Livewire::test(\App\Livewire\Admin\AdminDashboard::class, ['tab' => 'programs']);
        $responseDec->assertSee("printPernyataanEffectiveMonth: 'Januari'", false);
        $responseDec->assertSee("printPernyataanEffectiveYear: '2027'", false);

        \Carbon\Carbon::setTestNow();
    }
}


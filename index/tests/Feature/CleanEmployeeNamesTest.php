<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Kajian;
use App\Models\PrayerDuty;
use App\Models\ProgramParticipant;
use App\Models\User;
use App\Services\EmployeeImportService;
use App\Services\EmployeeStatusSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanEmployeeNamesTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_model_mutator_automatically_strips_titles_and_capitalizes(): void
    {
        $emp = Employee::create([
            'name' => 'NOBIYANTORO, A.Md.',
            'nip_pendek' => '808320185',
            'status' => 'AKTIF',
        ]);

        $this->assertEquals('Nobiyantoro', $emp->fresh()->name);

        $emp2 = Employee::create([
            'name' => 'dani sulistiono, s.m.',
            'nip_pendek' => '808320114',
            'status' => 'AKTIF',
        ]);

        $this->assertEquals('Dani Sulistiono', $emp2->fresh()->name);
    }

    public function test_clean_employee_names_command_cleans_all_tables(): void
    {
        // 1. Employee with titles (bypass mutator using direct DB insert)
        \DB::table('employees')->insert([
            'name' => 'NOBIYANTORO, A.Md.',
            'nip_pendek' => '808320185',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('employees')->insert([
            'name' => 'BAYU HENDRA KURNIAWAN, S.Ak.',
            'nip_pendek' => '808320094',
            'unit_kerja' => 'KPP Madya Malang',
            'status' => 'AKTIF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. User with titles
        $user = User::factory()->create([
            'name' => 'ADELINA KHOLIFAH PUTRI, A.Md.Pjk.',
            'email' => 'adelina@test.com',
        ]);

        // 3. Prayer duties with uppercase titles
        $duty = PrayerDuty::create([
            'prayer_time' => 'dzuhur',
            'day_name' => 'Senin',
            'week_pattern' => 'semua',
            'imam_name' => 'JOKO DWI SAPUTRO, S.S.T., Ak., M.A.',
            'muadzin_name' => 'Syaiful Muslimin, S.E., M.m.',
        ]);

        // 4. Kajian with title
        $kajian = Kajian::create([
            'type' => 'jumat',
            'date' => now()->toDateString(),
            'title' => 'Sholat Jumat',
            'mc_name' => 'JULI ARDIANTO, S.Ak.',
            'muadzin_name' => 'NOBIYANTORO, A.Md.',
        ]);

        // 5. Program participant
        $part = ProgramParticipant::create([
            'name' => 'NOBIYANTORO, A.Md.',
            'program_name' => 'Infaq Rutin',
            'monthly_amount' => 50000,
            'period' => 'Periode 10/2026',
        ]);

        // Execute command
        $this->artisan('masjid:clean-employee-names')
            ->assertSuccessful();

        // Verify employees
        $this->assertEquals('Nobiyantoro', \DB::table('employees')->where('nip_pendek', '808320185')->value('name'));
        $this->assertEquals('Bayu Hendra Kurniawan', \DB::table('employees')->where('nip_pendek', '808320094')->value('name'));

        // Verify users
        $this->assertEquals('Adelina Kholifah Putri', $user->fresh()->name);

        // Verify prayer_duties
        $this->assertEquals('Joko Dwi Saputro', $duty->fresh()->imam_name);
        $this->assertEquals('Syaiful Muslimin', $duty->fresh()->muadzin_name);

        // Verify kajians
        $this->assertEquals('Juli Ardianto', $kajian->fresh()->mc_name);
        $this->assertEquals('Nobiyantoro', $kajian->fresh()->muadzin_name);

        // Verify program_participants
        $this->assertEquals('Nobiyantoro', $part->fresh()->name);
    }

    public function test_employee_import_service_prefers_pure_nama_pegawai_and_strips_titles(): void
    {
        $rows = [
            ['NIP', 'IP SIKKA', 'NAMA PEGAWAI', 'GELAR BELAKANG', 'NAMA LENGKAP', 'AGAMA', 'GENDER'],
            ['199011122012101001', '808320185', 'NOBIYANTORO', 'A.Md.', 'NOBIYANTORO, A.Md.', 'Islam', 'L'],
            ['199006172012101003', '808320114', 'DANI SULISTIONO', 'S.m.', 'Dani Sulistiono, S.m.', 'Islam', 'L'],
        ];

        $parsed = EmployeeImportService::parseRows($rows, true);

        $this->assertCount(2, $parsed['items']);
        $this->assertEquals('Nobiyantoro', $parsed['items'][0]['name']);
        $this->assertEquals('Dani Sulistiono', $parsed['items'][1]['name']);
    }
}

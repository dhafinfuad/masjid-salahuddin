<?php

namespace Tests\Feature;

use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use App\Services\PotonganMasjidSyncService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncPotonganMasjidTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 9, 15, 12, 0, 0, 'Asia/Jakarta'));

        // Seed social programs
        SocialProgram::create([
            'id' => 1,
            'name' => 'Program Santunan Anak Yatim',
            'slug' => 'santunan-anak-yatim',
            'category' => 'yatim',
            'description' => 'Program Santunan Anak Yatim',
            'target_amount' => 33325000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'heart-handshake',
            'color' => 'emerald',
        ]);

        SocialProgram::create([
            'id' => 2,
            'name' => 'Infaq Rutin',
            'slug' => 'infaq-rutin',
            'category' => 'infaq',
            'description' => 'Infaq Rutin',
            'target_amount' => 43575000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'wallet',
            'color' => 'blue',
        ]);

        SocialProgram::create([
            'id' => 3,
            'name' => 'Program Zakat Mal Rutin',
            'slug' => 'zakat-mal-rutin',
            'category' => 'zakat',
            'description' => 'Program Zakat Mal Rutin',
            'target_amount' => 29500000,
            'period_type' => 'bulanan',
            'status' => 'AKTIF',
            'icon' => 'coins',
            'color' => 'amber',
        ]);

        SocialProgram::create([
            'id' => 4,
            'name' => 'Tabungan Qurban',
            'slug' => 'tabungan-qurban',
            'category' => 'qurban',
            'description' => 'Tabungan Qurban',
            'target_amount' => 112400000,
            'period_type' => 'tahunan',
            'status' => 'AKTIF',
            'icon' => 'sparkles',
            'color' => 'teal',
        ]);
    }

    public function test_sync_potongan_dry_run_does_not_persist(): void
    {
        $this->artisan('masjid:sync-potongan', ['--dry-run' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('SIMULASI (DRY-RUN)');

        $this->assertEquals(0, ProgramParticipant::count());
    }

    public function test_sync_potongan_executes_and_populates_participants(): void
    {
        $this->artisan('masjid:sync-potongan')
            ->assertSuccessful()
            ->expectsOutputToContain('EKSEKUSI DATABASE');

        $this->assertEquals(1606, ProgramParticipant::count());
        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        $this->assertEquals(130, ProgramParticipant::where('period', $currentPeriod)->count());
        $this->assertEquals(19600000.00, (float) ProgramParticipant::where('period', $currentPeriod)->sum('monthly_amount'));

        // Check idempotency: running again should not duplicate records
        $this->artisan('masjid:sync-potongan')
            ->assertSuccessful();

        $this->assertEquals(1606, ProgramParticipant::count());
        $this->assertEquals(130, ProgramParticipant::where('period', $currentPeriod)->count());
    }

    public function test_livewire_import_potongan_modal_flow(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Administrator Masjid',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        \App\Models\Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
            'description' => 'Kajian rutin pekanan',
        ]);

        $filePath = public_path('resources/Rekapitulasi Potongan Masjid 2.0.xlsx');
        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'Rekapitulasi Potongan Masjid 2.0.xlsx',
            file_get_contents($filePath)
        );

        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\AdminDashboard::class)
            ->set('currentTab', 'programs')
            ->call('openImportPotonganModal')
            ->assertSet('showImportPotonganModal', true)
            ->assertDispatched('open-import-potongan-modal')
            ->set('potonganFile', $uploadedFile)
            ->assertSet('importPotonganPreview.total_excel_unique', 1606)
            ->assertSet('importPotonganPreview.current_period_count', 130)
            ->assertSet('importPotonganPreview.current_period_amount', 19600000.0)
            ->call('processConfirmImportPotongan')
            ->assertDispatched('close-import-potongan-modal')
            ->assertSet('showImportPotonganModal', false);

        $this->assertEquals(1606, ProgramParticipant::count());
        $this->assertEquals(130, ProgramParticipant::where('period', $currentPeriod)->count());
    }

    public function test_import_potongan_replaces_existing_month_data_completely(): void
    {
        // 1. Seed existing data for October 2026 (old total = 18.850.000 with 3 participants)
        ProgramParticipant::create([
            'social_program_id' => 2,
            'name' => 'Pegawai Lama A',
            'program_name' => 'Infaq Rutin',
            'monthly_amount' => 8850000,
            'period' => 'Periode 10/2026',
        ]);
        ProgramParticipant::create([
            'social_program_id' => 3,
            'name' => 'Pegawai Lama B',
            'program_name' => 'Program Zakat Mal Rutin',
            'monthly_amount' => 5000000,
            'period' => 'Periode 10/2026',
        ]);
        ProgramParticipant::create([
            'social_program_id' => 1,
            'name' => 'Pegawai Lama C',
            'program_name' => 'Program Santunan Anak Yatim',
            'monthly_amount' => 5000000,
            'period' => 'Periode 10/2026',
        ]);

        // Seed data for September 2026 (should remain intact)
        ProgramParticipant::create([
            'social_program_id' => 2,
            'name' => 'Pegawai September X',
            'program_name' => 'Infaq Rutin',
            'monthly_amount' => 2000000,
            'period' => 'Periode 9/2026',
        ]);

        $this->assertEquals(3, ProgramParticipant::where('period', 'Periode 10/2026')->count());
        $this->assertEquals(18850000.0, (float) ProgramParticipant::where('period', 'Periode 10/2026')->sum('monthly_amount'));

        // 2. Create new Excel for October with total 18.450.000 (2 participants)
        $excelPath = $this->createTestExcelFile([
            [2026, 'OKTOBER', 'Pegawai Baru 1', 'Infaq Rutin', 10000000],
            [2026, 'OKTOBER', 'Pegawai Baru 2', 'Program Zakat Mal Rutin', 8450000],
        ]);

        // 3. Execute sync
        $syncService = app(PotonganMasjidSyncService::class);
        $result = $syncService->sync($excelPath, false);

        // 4. Assertions
        $this->assertEquals(3, $result['deleted_count']);
        $this->assertEquals(2, $result['inserted_count']);
        $this->assertEquals(18450000.0, (float) $result['total_amount']);

        // Check October records in DB: old ones must be completely replaced
        $this->assertEquals(2, ProgramParticipant::where('period', 'Periode 10/2026')->count());
        $this->assertEquals(18450000.0, (float) ProgramParticipant::where('period', 'Periode 10/2026')->sum('monthly_amount'));
        $this->assertFalse(ProgramParticipant::where('name', 'Pegawai Lama A')->exists());
        $this->assertFalse(ProgramParticipant::where('name', 'Pegawai Lama B')->exists());
        $this->assertFalse(ProgramParticipant::where('name', 'Pegawai Lama C')->exists());
        $this->assertTrue(ProgramParticipant::where('name', 'Pegawai Baru 1')->exists());
        $this->assertTrue(ProgramParticipant::where('name', 'Pegawai Baru 2')->exists());

        // Check September records: MUST BE UNTOUCHED!
        $this->assertEquals(1, ProgramParticipant::where('period', 'Periode 9/2026')->count());
        $this->assertEquals(2000000.0, (float) ProgramParticipant::where('period', 'Periode 9/2026')->sum('monthly_amount'));

        if (file_exists($excelPath)) {
            @unlink($excelPath);
        }
    }

    protected function createTestExcelFile(array $rows): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'test_pot_') . '.xlsx';
        $zip = new \ZipArchive();
        $zip->open($filePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $headers = ['TAHUN', 'BULAN', 'NAMA', 'JENIS POTONGAN', 'JUMLAH POTONGAN'];
        $sharedStrings = [];
        $addString = function (string $s) use (&$sharedStrings) {
            $idx = array_search($s, $sharedStrings, true);
            if ($idx === false) {
                $idx = count($sharedStrings);
                $sharedStrings[] = $s;
            }
            return $idx;
        };

        $sheetRows = [];
        $hCells = [];
        $cols = ['A', 'B', 'C', 'D', 'E'];
        foreach ($headers as $cIdx => $h) {
            $sIdx = $addString($h);
            $hCells[] = '<c r="' . $cols[$cIdx] . '1" t="s"><v>' . $sIdx . '</v></c>';
        }
        $sheetRows[] = '<row r="1">' . implode('', $hCells) . '</row>';

        $rNum = 2;
        foreach ($rows as $r) {
            $cXml = [];
            $cXml[] = '<c r="A' . $rNum . '"><v>' . $r[0] . '</v></c>';
            $cXml[] = '<c r="B' . $rNum . '" t="s"><v>' . $addString((string)$r[1]) . '</v></c>';
            $cXml[] = '<c r="C' . $rNum . '" t="s"><v>' . $addString((string)$r[2]) . '</v></c>';
            $cXml[] = '<c r="D' . $rNum . '" t="s"><v>' . $addString((string)$r[3]) . '</v></c>';
            $cXml[] = '<c r="E' . $rNum . '"><v>' . $r[4] . '</v></c>';
            $sheetRows[] = '<row r="' . $rNum . '">' . implode('', $cXml) . '</row>';
            $rNum++;
        }

        $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
        foreach ($sharedStrings as $str) {
            $ssXml .= '<si><t>' . htmlspecialchars($str) . '</t></si>';
        }
        $ssXml .= '</sst>';

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . implode('', $sheetRows) . '</sheetData></worksheet>';
        $wbXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Potongan" sheetId="1" r:id="rId1"/></sheets></workbook>';
        $wbRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>';
        $rootRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRelsXml);
        $zip->addFromString('xl/workbook.xml', $wbXml);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRelsXml);
        $zip->addFromString('xl/sharedStrings.xml', $ssXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        return $filePath;
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}

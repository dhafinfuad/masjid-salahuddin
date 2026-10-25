<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\MasjidSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTakmirKepTest extends TestCase
{
    use RefreshDatabase;

    protected User $master;
    protected User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->master = User::create([
            'name' => 'Dr. H. Bambang Irawan',
            'email' => 'master@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);

        $this->jamaah = User::create([
            'name' => 'Haji Mansyur',
            'email' => 'jamaah@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);
    }

    public function test_can_access_print_official_sk_route(): void
    {
        $this->actingAs($this->master);

        $response = $this->get(route('admin.kepengurusan.print-sk'));
        $response->assertStatus(200);
        $response->assertSee('KEPUTUSAN KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG');
        $response->assertSee('SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN');
        $response->assertSee('PENJABARAN TUGAS POKOK DAN FUNGSI');
    }

    public function test_can_open_and_close_template_kep_modal(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class)
            ->call('openTemplateKepModal')
            ->assertSet('showTemplateKepModal', true)
            ->assertDispatched('open-template-kep-modal')
            ->call('closeTemplateKepModal')
            ->assertSet('showTemplateKepModal', false)
            ->assertDispatched('close-template-kep-modal');
    }

    public function test_save_template_kep_validates_required_fields(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class)
            ->call('openTemplateKepModal')
            ->set('docSkNumber', '')
            ->set('docSkDate', '')
            ->set('docSkPeriode', '')
            ->set('pembinaName', '')
            ->set('ketuaName', '')
            ->set('wakilKetuaName', '')
            ->set('sekretarisNames', '')
            ->set('bendaharaNames', '')
            ->call('saveTemplateKep')
            ->assertHasErrors([
                'docSkNumber',
                'docSkDate',
                'docSkPeriode',
                'pembinaName',
                'ketuaName',
                'wakilKetuaName',
                'sekretarisNames',
                'bendaharaNames',
            ]);
    }

    public function test_save_template_kep_updates_database(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class)
            ->call('openTemplateKepModal')
            ->set('docSkNumber', 'KEP-99/KPP.1209/2027')
            ->set('docSkDate', '25 Mei 2027')
            ->set('docSkPeriode', '2027-2030')
            ->set('docSkTitle', 'Surat Keputusan Kepala KPP Madya Malang 2027')
            ->set('docSkDescription', 'Perubahan Susunan Pengurus Periode 2027-2030')
            ->set('pembinaName', 'Dr. Pembina Baru, M.Si.')
            ->set('ketuaName', 'Ketua Terpilih Baru')
            ->set('wakilKetuaName', 'Wakil Ketua Baru')
            ->set('sekretarisNames', "Sekretaris Satu\nSekretaris Dua")
            ->set('bendaharaNames', "Bendahara Satu\nBendahara Dua")
            ->set('bidang0Name', 'Bidang Dakwah & Syiar Islam')
            ->set('bidang0Pengelola', 'Koordinator Dakwah')
            ->set('bidang0Anggota', "Anggota 1\nAnggota 2")
            ->set('bidang0Tupoksi', "Mengatur kajian pekanan\nMenyelenggarakan PHBI")
            ->call('saveTemplateKep')
            ->assertHasNoErrors()
            ->assertSet('showTemplateKepModal', false)
            ->assertDispatched('close-template-kep-modal')
            ->assertDispatched('download-kep-pdf');

        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();
        $struct = $settings->getTakmirStructure();

        $this->assertEquals('KEP-99/KPP.1209/2027', $docs['sk']['number']);
        $this->assertEquals('25 Mei 2027', $docs['sk']['date']);
        $this->assertEquals('Dr. Pembina Baru, M.Si.', $struct['pembina']['name']);
        $this->assertEquals('Ketua Terpilih Baru', $struct['ketua']['name']);
        $this->assertEquals('Wakil Ketua Baru', $struct['wakil_ketua']['name']);
        $this->assertEquals(['Sekretaris Satu', 'Sekretaris Dua'], $struct['sekretaris']['names']);
        $this->assertEquals(['Bendahara Satu', 'Bendahara Dua'], $struct['bendahara']['names']);
        $this->assertEquals('Bidang Dakwah & Syiar Islam', $struct['bidang'][0]['name']);
        $this->assertEquals(['Koordinator Dakwah'], $struct['bidang'][0]['pengelola']);
        $this->assertEquals(['Anggota 1', 'Anggota 2'], $struct['bidang'][0]['anggota']);
        $this->assertEquals(['Mengatur kajian pekanan', 'Menyelenggarakan PHBI'], $struct['bidang'][0]['tupoksi']);
    }

    public function test_jamaah_cannot_save_template_kep(): void
    {
        $this->actingAs($this->jamaah);

        Livewire::test(AdminDashboard::class)
            ->call('saveTemplateKep');

        // Nilai default tidak boleh berubah
        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();
        $this->assertNotEquals('KEP-999/KPP.1209/2099', $docs['sk']['number'] ?? '');
    }

    public function test_can_open_upload_doc_modal_for_sk_lengkap(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class)
            ->call('openUploadDoc', 'sk_lengkap')
            ->assertSet('uploadDocTarget', 'sk_lengkap')
            ->assertSet('uploadDocTitle', 'Dokumen Resmi SK Takmir & Seluruh Lampiran')
            ->assertSet('showUploadDocModal', true)
            ->assertDispatched('open-upload-doc-modal');
    }

    public function test_can_download_official_sk_pdf_route(): void
    {
        $this->actingAs($this->master);

        $response = $this->get(route('admin.kepengurusan.download-sk-pdf'));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string)$response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string)$response->headers->get('Content-Disposition'));
    }

    public function test_can_download_fresh_dynamic_sk_pdf_even_if_uploaded_file_exists(): void
    {
        $this->actingAs($this->master);

        // Pasang dummy file sk_lengkap di settings
        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();
        $docs['sk_lengkap']['file'] = 'uploaded_scan.pdf';
        $settings->takmir_documents = $docs;
        $settings->save();

        // Buat file fisik sementara
        $dummyPath = public_path('resources/uploaded_scan.pdf');
        if (!is_dir(dirname($dummyPath))) {
            mkdir(dirname($dummyPath), 0755, true);
        }
        file_put_contents($dummyPath, '%PDF-1.4 dummy static file');

        try {
            // Panggilan tanpa fresh=1 mengunduh file pindaian lama
            $responseStatic = $this->get(route('admin.kepengurusan.download-sk-pdf'));
            $responseStatic->assertStatus(200);
            $this->assertStringContainsString('uploaded_scan.pdf', (string)$responseStatic->headers->get('Content-Disposition'));

            // Panggilan dengan fresh=1 men-generate dokumen dinamis baru (Dokumen_Resmi_SK_Takmir_...)
            $responseFresh = $this->get(route('admin.kepengurusan.download-sk-pdf', ['fresh' => 1]));
            $responseFresh->assertStatus(200);
            $this->assertStringContainsString('Dokumen_Resmi_SK_Takmir_', (string)$responseFresh->headers->get('Content-Disposition'));
            $this->assertStringContainsString('application/pdf', (string)$responseFresh->headers->get('Content-Type'));
        } finally {
            if (file_exists($dummyPath)) {
                @unlink($dummyPath);
            }
        }
    }

    public function test_can_upload_doc_file_and_updates_database(): void
    {
        $this->actingAs($this->master);

        $file = UploadedFile::fake()->create('dokumen_sk.pdf', 100, 'application/pdf');

        Livewire::test(AdminDashboard::class)
            ->call('openUploadDoc', 'sk_lengkap')
            ->set('uploadDocFile', $file)
            ->set('uploadDocNumber', 'KEP-777/TEST/2026')
            ->set('uploadDocDate', '2026-08-17')
            ->call('saveUploadDoc')
            ->assertHasNoErrors()
            ->assertSet('showUploadDocModal', false)
            ->assertSet('docSkNumber', 'KEP-777/TEST/2026')
            ->assertSet('docSkDate', '17 Agustus 2026');

        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();

        $this->assertEquals('KEP-777/TEST/2026', $docs['sk_lengkap']['number'] ?? '');
        $this->assertEquals('KEP-777/TEST/2026', $docs['sk']['number'] ?? '');
        $this->assertEquals('17 Agustus 2026', $docs['sk_lengkap']['date'] ?? '');
        $this->assertEquals('17 Agustus 2026', $docs['sk']['date'] ?? '');
        $this->assertNotEmpty($docs['sk_lengkap']['file'] ?? '');

        // Cleanup fake test file
        $testFilePath = public_path('resources/' . ($docs['sk_lengkap']['file'] ?? ''));
        if (file_exists($testFilePath)) {
            @unlink($testFilePath);
        }
    }

    public function test_upload_doc_requires_pdf_file(): void
    {
        $this->actingAs($this->master);

        Livewire::test(AdminDashboard::class)
            ->call('openUploadDoc', 'sk_lengkap')
            ->set('uploadDocFile', null)
            ->call('saveUploadDoc')
            ->assertHasErrors(['uploadDocFile' => 'required']);

        $imageFile = UploadedFile::fake()->image('avatar.jpg');

        Livewire::test(AdminDashboard::class)
            ->call('openUploadDoc', 'sk_lengkap')
            ->set('uploadDocFile', $imageFile)
            ->call('saveUploadDoc')
            ->assertHasErrors(['uploadDocFile' => 'mimes']);
    }
}

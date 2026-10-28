<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FormValidationStandardizationTest extends TestCase
{
    /**
     * Pastikan konfigurasi locale id menghasilkan pesan standar "{Label} wajib diisi."
     * untuk seluruh field form di proyek Masjid Salahuddin.
     */
    public function test_indonesian_validation_messages_are_standardized_and_correct(): void
    {
        App::setLocale('id');

        $testCases = [
            'kajianTitle' => 'Judul / tema kajian',
            'kajianSpeakerName' => 'Nama pembicara',
            'agendaTitle' => 'Nama kegiatan',
            'agendaEventDate' => 'Tanggal kegiatan',
            'financeAmount' => 'Nominal transaksi',
            'financeDate' => 'Tanggal transaksi',
            'financeDescription' => 'Keterangan transaksi',
            'socialProgramName' => 'Nama program sosial',
            'employeeFormName' => 'Nama lengkap pegawai',
            'userName' => 'Nama pengguna',
            'userEmail' => 'Alamat email',
            'participantName' => 'Nama penerima manfaat',
            'galleryTitle' => 'Judul kegiatan',
            'ustadzName' => 'Nama ustadz',
            'message' => 'Isi saran/pesan',
        ];

        foreach ($testCases as $field => $expectedLabel) {
            $validator = Validator::make([$field => ''], [$field => 'required']);
            $this->assertTrue($validator->fails(), "Validasi untuk field {$field} harus gagal bila kosong.");
            $errorMessage = $validator->errors()->first($field);
            $this->assertSame(
                "{$expectedLabel} wajib diisi.",
                $errorMessage,
                "Pesan validasi untuk {$field} harus berformat '{$expectedLabel} wajib diisi.'"
            );
        }
    }

    /**
     * Verifikasi bahwa seluruh form modal di _modals.blade.php memiliki atribut novalidate
     * untuk mencegah munculnya tooltip browser bawaan seperti 'Fill out this field'.
     */
    public function test_modals_forms_have_novalidate_attribute(): void
    {
        $modalsContent = file_get_contents(resource_path('views/livewire/admin/tabs/_modals.blade.php'));

        preg_match_all('/<form[^>]+>/', $modalsContent, $matches);
        $forms = $matches[0];

        $this->assertNotEmpty($forms, 'Harus ada form di dalam _modals.blade.php.');

        foreach ($forms as $formTag) {
            $this->assertStringContainsString(
                'novalidate',
                $formTag,
                "Form tag '{$formTag}' di _modals.blade.php wajib memiliki atribut 'novalidate'."
            );
        }
    }

    /**
     * Verifikasi bahwa input yang divalidasi tidak memiliki atribut required bawaan browser
     * di dalam file _modals.blade.php.
     */
    public function test_modals_inputs_do_not_have_native_required_attribute(): void
    {
        $modalsContent = file_get_contents(resource_path('views/livewire/admin/tabs/_modals.blade.php'));

        // Pastikan field-field utama tidak lagi memiliki atribut required
        $this->assertDoesNotMatchRegularExpression('/name="title"[^>]*required/', $modalsContent);
        $this->assertDoesNotMatchRegularExpression('/x-model="agendaForm\.title"[^>]*required/', $modalsContent);
        $this->assertDoesNotMatchRegularExpression('/x-model="agendaForm\.event_date"[^>]*required/', $modalsContent);
        $this->assertDoesNotMatchRegularExpression('/wire:model="employeeFormName"[^>]*required/', $modalsContent);
        $this->assertDoesNotMatchRegularExpression('/wire:model="socialProgramName"[^>]*required/', $modalsContent);
    }

    /**
     * Verifikasi bahwa admin-dashboard.blade.php memiliki fungsi validasi Alpine real-time on-blur
     * untuk Kajian dan Agenda.
     */
    public function test_admin_dashboard_has_realtime_alpine_validators(): void
    {
        $dashboardContent = file_get_contents(resource_path('views/livewire/admin/admin-dashboard.blade.php'));

        $this->assertStringContainsString('kajianErrors', $dashboardContent);
        $this->assertStringContainsString('validateKajianField', $dashboardContent);
        $this->assertStringContainsString('validateKajianAll', $dashboardContent);

        $this->assertStringContainsString('agendaErrors', $dashboardContent);
        $this->assertStringContainsString('validateAgendaField', $dashboardContent);
        $this->assertStringContainsString('validateAgendaAll', $dashboardContent);
    }
}

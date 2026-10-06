<?php

use App\Http\Controllers\AgendaExportController;
use App\Http\Controllers\FinanceExportController;
use App\Http\Controllers\KajianExportController;
use App\Http\Controllers\PrayerDutyExportController;
use App\Http\Controllers\SocialProgramExportController;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Auth\SetPasswordPage;
use App\Livewire\Portal\JadwalSholatPage;
use App\Livewire\Portal\KegiatanMasjidPage;
use App\Livewire\Portal\PetugasSholatPage;
use App\Livewire\Portal\PortalPage;
use App\Livewire\Portal\ProfilMasjidPage;
use App\Livewire\Portal\SaranKritikPage;
use App\Livewire\Tv\TvDisplayPage;
use Illuminate\Support\Facades\Route;

// Public Surfaces
Route::get('/', PortalPage::class)->name('portal');
Route::get('/kegiatan', KegiatanMasjidPage::class)->name('portal.kegiatan');
Route::get('/kegiatan-masjid', fn() => redirect()->route('portal.kegiatan'));
Route::get('/jadwal-sholat', JadwalSholatPage::class)->name('portal.jadwal-sholat');
Route::get('/petugas-sholat', PetugasSholatPage::class)->name('portal.petugas-sholat');
Route::get('/petugas-sholat/cetak', [PrayerDutyExportController::class, 'printMonthlyPdf'])->name('portal.petugas-sholat.print');
Route::get('/profil', ProfilMasjidPage::class)->name('portal.profil');
Route::get('/profil-masjid', fn() => redirect()->route('portal.profil'));
Route::get('/saran', SaranKritikPage::class)->name('portal.saran');
Route::get('/saran-kritik', fn() => redirect()->route('portal.saran'));
Route::get('/aspirasi', fn() => redirect()->route('portal.saran'));
Route::get('/admin/kajian/teks-mc/{id}', [KajianExportController::class, 'printMcText'])->name('admin.kajian.teks-mc');
Route::get('/display', TvDisplayPage::class)->name('display');
Route::get('/offline', fn() => response()->view('portal.offline'))->name('portal.offline');
Route::get('/resources/QRIS.webp', fn() => response()->file(public_path('images/QRIS.webp'), ['Content-Type' => 'image/webp']));
Route::get('/masjid-salahuddin/resources/QRIS.webp', fn() => response()->file(public_path('images/QRIS.webp'), ['Content-Type' => 'image/webp']));

// Authentication
if (app()->environment('local')) {
    Route::get('/auth/dev-login/{id?}', function ($id = 1) {
        $user = \App\Models\User::find($id) ?? \App\Models\User::first();
        if ($user) {
            \Illuminate\Support\Facades\Auth::login($user);
        }
        return redirect()->route('admin.dashboard');
    })->name('dev.login');
}
Route::get('/auth/login', LoginPage::class)->name('login');
Route::get('/auth/register', RegisterPage::class)->name('register');
Route::get('/auth/set-password', SetPasswordPage::class)->name('password.set');
Route::get('/auth/forgot-password', ForgotPasswordPage::class)->name('password.request');
Route::get('/auth/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');

// Email Verification Link
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request, string $id, string $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Tautan verifikasi tidak valid.');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return redirect()->route('login')->with('status', 'Email dinas Anda berhasil diverifikasi! Silakan masuk ke backoffice.');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

// Document Print & TTE Verification Views (Public so QR Code scanned from printed documents can be viewed/verified)
Route::get('/admin/kajian/export-pdf', [KajianExportController::class, 'printPdf'])->name('admin.kajian.export-pdf');
Route::get('/admin/petugas/export-pdf', [PrayerDutyExportController::class, 'printPdf'])->name('admin.petugas.export-pdf');
Route::get('/admin/agenda/export-pdf', [AgendaExportController::class, 'printPdf'])->name('admin.agenda.export-pdf');
Route::get('/admin/agenda/lpj/{id}', [AgendaExportController::class, 'printLpj'])->name('admin.agenda.lpj');
Route::get('/admin/finance/export-pdf', [FinanceExportController::class, 'printPdf'])->name('admin.finance.export-pdf');

// CMS Admin (Protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/admin/kajian/export-excel', [KajianExportController::class, 'exportExcel'])->name('admin.kajian.export-excel');
    Route::get('/admin/petugas/export-excel', [PrayerDutyExportController::class, 'exportExcel'])->name('admin.petugas.export-excel');
    Route::get('/admin/agenda/export-excel', [AgendaExportController::class, 'exportExcel'])->name('admin.agenda.export-excel');
    Route::get('/admin/finance/export-excel', [FinanceExportController::class, 'exportExcel'])->name('admin.finance.export-excel');
    Route::get('/admin/programs/export-excel', [SocialProgramExportController::class, 'exportExcel'])->name('admin.programs.export-excel');

    Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/poster-setting', \App\Livewire\Admin\PosterSettingManager::class)->name('admin.poster-setting');
    Route::get('/admin/{tab}', AdminDashboard::class)->name('admin.tab');
});




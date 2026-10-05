<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Login Pengurus DKM — Masjid Salahuddin')]
class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $errorMessage = '';

    public bool $isMigrationAccount = false;
    public string $migrationEmail = '';

    protected function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required|min:4',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Alamat email atau ID email dinas wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 4 karakter.',
        ];
    }

    public function mount(): void
    {
        if (session()->has('error')) {
            $this->errorMessage = (string) session('error');
        }
    }

    public function login(): void
    {
        $this->errorMessage = '';
        $this->isMigrationAccount = false;
        $this->email = strtolower(trim($this->email));
        $this->validate();

        $input = $this->email;
        $resolvedEmail = $input;

        // Jika user hanya menginput ID Email (tanpa domain @), resolusikan ke email terdaftar
        if (! str_contains($input, '@')) {
            $pajakEmail = $input . '@pajak.go.id';
            if (User::where('email', $pajakEmail)->exists()) {
                $resolvedEmail = $pajakEmail;
            } else {
                // Coba cari akun dengan prefix sebelum tanda @ (misal admin atau domain kementerian lain)
                $userByPrefix = User::where('email', 'like', $input . '@%')->first();
                $resolvedEmail = $userByPrefix ? $userByPrefix->email : $pajakEmail;
            }
        }

        $throttleKey = Str::transliterate(Str::lower($resolvedEmail).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->errorMessage = "Terlalu banyak percobaan masuk yang gagal. Silakan coba kembali dalam {$seconds} detik.";
            return;
        }

        // Cek apakah email terdaftar di database
        $user = User::where('email', $resolvedEmail)->first();

        if (! $user) {
            RateLimiter::hit($throttleKey, 60);
            $this->errorMessage = 'Email belum terdaftar. Silakan lakukan pendaftaran akun.';
            return;
        }

        // Cek status keaktifan pegawai
        if (strtoupper($user->status ?? 'AKTIF') !== 'AKTIF') {
            RateLimiter::hit($throttleKey, 60);
            $this->errorMessage = 'Anda sudah bukan lagi pegawai KPP Madya Malang.';
            return;
        }

        // Cek apakah akun hasil migrasi lama yang belum memiliki password (password NULL atau kosong)
        if (is_null($user->password) || blank($user->password)) {
            $this->isMigrationAccount = true;
            $this->migrationEmail = $user->email;
            $this->errorMessage = 'Akun Anda terdaftar dari sistem lama namun belum memiliki kata sandi. Silakan klik tombol di bawah untuk membuat kata sandi baru via email dinas Anda.';
            return;
        }

        // Coba autentikasi jika akun memiliki password
        if (Auth::attempt(['email' => $resolvedEmail, 'password' => $this->password], $this->remember)) {
            if (strtoupper(Auth::user()->status ?? 'AKTIF') !== 'AKTIF') {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->errorMessage = 'Anda sudah bukan lagi pegawai KPP Madya Malang.';
                return;
            }

            RateLimiter::clear($throttleKey);
            session()->regenerate();
            $this->redirectIntended(route('admin.dashboard'), navigate: true);
            return;
        }

        RateLimiter::hit($throttleKey, 60);
        $this->errorMessage = 'Kombinasi email dan kata sandi tidak cocok.';
    }

    public function quickLogin(string $role): void
    {
        // Hanya aktif di environment lokal / testing untuk keamanan
        if (! app()->environment('local', 'testing')) {
            abort(403, 'Fitur login demo cepat dinonaktifkan pada lingkungan publik demi keamanan.');
        }

        $roleMap = [
            'admin' => ['Master', 'Ketua', 'admin'],
            'master' => ['Master', 'admin'],
            'ketua' => ['Ketua', 'admin', 'Master'],
            'sekretaris' => ['Sekretaris', 'operator'],
            'operator' => ['Sekretaris', 'operator'],
            'bendahara' => ['Bendahara'],
            'jamaah' => ['Jamaah', 'viewer'],
            'viewer' => ['Jamaah', 'viewer'],
        ];

        $targetRoles = $roleMap[strtolower($role)] ?? [$role];
        $user = User::whereIn('role', $targetRoles)->first();
        if ($user) {
            if (strtoupper($user->status ?? 'AKTIF') !== 'AKTIF') {
                $this->errorMessage = 'Anda sudah bukan lagi pegawai KPP Madya Malang.';
                return;
            }
            Auth::login($user, true);
            session()->regenerate();
            $this->redirectIntended(route('admin.dashboard'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }
}

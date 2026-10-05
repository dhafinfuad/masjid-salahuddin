<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pendaftaran Akun Pengurus — Masjid Salahuddin')]
class RegisterPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $isSubmitted = false;
    public string $registeredEmail = '';
    public string $directVerifyUrl = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'ends_with:@pajak.go.id', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap pegawai wajib diisi.',
            'name.min' => 'Nama lengkap minimal terdiri dari 3 karakter.',
            'email.required' => 'Alamat email dinas wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.ends_with' => 'Pendaftaran akun jamaah wajib menggunakan email dinas resmi dengan domain @pajak.go.id.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem. Silakan gunakan menu Masuk atau Lupa Kata Sandi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi yang dimasukkan.',
        ];
    }

    public function register(): void
    {
        $this->email = strtolower(trim($this->email));

        $this->validate();

        $user = User::create([
            'name' => trim($this->name),
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
            'email_verified_at' => null,
        ]);

        $this->directVerifyUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        // Kirim email verifikasi resmi Laravel (Queued ShouldQueue) dengan perlindungan timeout
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('SMTP Verification dispatch failed: ' . $e->getMessage());
        }

        $this->registeredEmail = $user->email;
        $this->isSubmitted = true;
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->isSubmitted = false;
        $this->registeredEmail = '';
    }

    public function render()
    {
        return view('livewire.auth.register-page');
    }
}

<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Reset Kata Sandi — Masjid Salahuddin')]
class ResetPasswordPage extends Component
{
    public string $token = '';
    public string $email = '';
    public string $userName = '';

    public string $password = '';
    public string $password_confirmation = '';

    public bool $isValidToken = false;
    public string $errorMessage = '';
    public bool $isSuccess = false;

    public function mount(?string $token = null): void
    {
        $this->token = (string) ($token ?? request()->route('token') ?? request()->query('token', ''));
        $this->email = strtolower(trim((string) request()->query('email', '')));

        if (! $this->token || ! $this->email) {
            $this->isValidToken = false;
            $this->errorMessage = 'Tautan reset kata sandi tidak lengkap atau tidak valid.';
            return;
        }

        $user = User::where('email', $this->email)->first();
        if (! $user) {
            $this->isValidToken = false;
            $this->errorMessage = 'Pengguna dengan alamat email ini tidak ditemukan di sistem.';
            return;
        }

        if (! Password::broker()->tokenExists($user, $this->token)) {
            $this->isValidToken = false;
            $this->errorMessage = 'Tautan reset kata sandi telah kedaluwarsa atau sudah pernah digunakan sebelumnya. Silakan ajukan permintaan reset kata sandi baru.';
            return;
        }

        $this->userName = $user->name;
        $this->isValidToken = true;
    }

    protected function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                PasswordRule::min(8)
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
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi baru.',
        ];
    }

    public function resetPassword(): void
    {
        if (! $this->isValidToken) {
            return;
        }

        $this->validate();

        $user = User::where('email', $this->email)->first();
        if (! $user || ! Password::broker()->tokenExists($user, $this->token)) {
            $this->isValidToken = false;
            $this->errorMessage = 'Tautan sudah tidak valid atau telah kedaluwarsa.';
            return;
        }

        $user->password = $this->password;

        // Otomatis isi email_verified_at jika tadinya NULL agar akun langsung aktif
        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
        }

        $user->status = 'AKTIF';
        $user->save();

        Password::broker()->deleteToken($user);

        $this->isSuccess = true;
    }

    public function render()
    {
        return view('livewire.auth.reset-password-page');
    }
}

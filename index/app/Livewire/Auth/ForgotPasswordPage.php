<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Lupa Kata Sandi — Masjid Salahuddin')]
class ForgotPasswordPage extends Component
{
    public string $email = '';
    public bool $isSubmitted = false;
    public string $errorMessage = '';
    public string $submittedEmail = '';
    public string $directResetUrl = '';

    public function mount(): void
    {
        // Pre-fill email from query parameter if provided (e.g. from login redirect)
        $queryEmail = request()->query('email');
        if ($queryEmail && is_string($queryEmail)) {
            $this->email = strtolower(trim($queryEmail));
        }
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'ends_with:@pajak.go.id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Alamat email dinas wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.ends_with' => 'Pemulihan kata sandi hanya berlaku untuk alamat email dinas resmi dengan domain @pajak.go.id.',
        ];
    }

    public function sendResetLink(): void
    {
        $this->errorMessage = '';
        $this->email = strtolower(trim($this->email));
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->errorMessage = 'Alamat email belum terdaftar di sistem. Silakan lakukan pendaftaran akun terlebih dahulu.';
            return;
        }

        // Create token & build reset URL
        $token = Password::broker()->createToken($user);
        $this->directResetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // Send queued reset notification gracefully
        try {
            $user->sendPasswordResetNotification($token);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('SMTP Mail dispatch failed: ' . $e->getMessage());
        }

        $this->submittedEmail = $this->email;
        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-page');
    }
}

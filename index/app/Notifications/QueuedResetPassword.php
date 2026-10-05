<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedResetPassword extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $name = $notifiable->name ?? 'Pegawai DJP';
        $count = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Permintaan Reset Kata Sandi Akun — Masjid Salahuddin')
            ->greeting("Assalamu'alaikum Warahmatullahi Wabarakatuh, Yth. {$name}")
            ->line('Kami menerima permintaan untuk menyetel ulang kata sandi bagi akun Anda di portal Masjid Salahuddin.')
            ->line('Silakan klik tombol di bawah ini untuk membuat kata sandi baru:')
            ->action('Reset Kata Sandi', $resetUrl)
            ->line("Tautan reset kata sandi ini hanya berlaku selama {$count} menit.")
            ->line('Jika Anda tidak merasa meminta reset kata sandi, tidak ada tindakan lebih lanjut yang perlu Anda lakukan.')
            ->salutation("Wassalamu'alaikum Warahmatullahi Wabarakatuh,\nPengurus DKM Masjid Salahuddin");
    }
}

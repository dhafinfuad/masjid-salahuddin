<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
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
        $verificationUrl = $this->verificationUrl($notifiable);
        $name = $notifiable->name ?? 'Pegawai DJP';

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Akun — Masjid Salahuddin')
            ->greeting("Assalamu'alaikum Warahmatullahi Wabarakatuh, Yth. {$name}")
            ->line('Terima kasih telah mendaftarkan akun di sistem Masjid Salahuddin KPP Madya Malang.')
            ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email dinas Anda dan mengaktifkan akun:')
            ->action('Verifikasi Alamat Email', $verificationUrl)
            ->line('Jika Anda tidak pernah merasa membuat akun ini, Anda dapat mengabaikan email ini dengan aman.')
            ->salutation("Wassalamu'alaikum Warahmatullahi Wabarakatuh,\nPengurus DKM Masjid Salahuddin");
    }
}

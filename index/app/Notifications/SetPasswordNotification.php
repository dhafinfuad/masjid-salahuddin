<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $activationUrl,
        public int $expireMinutes = 60
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->name ?? 'Pegawai DJP';

        return (new MailMessage)
            ->subject('Aktivasi Akun Jamaah & Pembuatan Kata Sandi — Masjid Salahuddin')
            ->greeting("Assalamu'alaikum Warahmatullahi Wabarakatuh, Yth. {$name}")
            ->line('Terima kasih telah mendaftarkan akun jamaah Masjid Salahuddin menggunakan alamat email kedinasan Ditjen Pajak (@pajak.go.id).')
            ->line('Untuk mengaktifkan akun dan membuat kata sandi baru, silakan klik tombol di bawah ini:')
            ->action('Buat Kata Sandi Baru', $this->activationUrl)
            ->line("Tautan aktivasi ini hanya berlaku selama {$this->expireMinutes} menit sejak email ini dikirimkan demi keamanan akun Anda.")
            ->line('Jika Anda tidak pernah merasa melakukan pendaftaran akun ini, Anda dapat mengabaikan email ini dengan aman.')
            ->salutation("Wassalamu'alaikum Warahmatullahi Wabarakatuh,\nPengurus DKM Masjid Salahuddin");
    }
}

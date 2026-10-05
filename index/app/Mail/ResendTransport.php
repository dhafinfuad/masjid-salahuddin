<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class ResendTransport extends AbstractTransport
{
    public function __construct(protected string $key)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $from = ! empty($email->getFrom()) 
            ? $email->getFrom()[0]->toString() 
            : (config('mail.from.name') ? config('mail.from.name') . ' <' . config('mail.from.address') . '>' : config('mail.from.address'));

        $payload = [
            'from' => $from,
            'to' => array_map(fn ($addr) => $addr->getAddress(), $email->getTo()),
            'subject' => $email->getSubject() ?: 'Notifikasi Masjid Salahuddin',
        ];

        if ($html = $email->getHtmlBody()) {
            $payload['html'] = $html;
        }

        if ($text = $email->getTextBody()) {
            $payload['text'] = $text;
        }

        if (! isset($payload['html']) && ! isset($payload['text'])) {
            $payload['html'] = (string) $email->getBody();
        }

        if ($replyTo = $email->getReplyTo()) {
            $payload['reply_to'] = array_map(fn ($addr) => $addr->getAddress(), $replyTo);
        }

        $response = Http::withoutVerifying()
            ->withToken($this->key)
            ->timeout(15)
            ->post('https://api.resend.com/emails', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Resend API Error (' . $response->status() . '): ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'resend';
    }
}

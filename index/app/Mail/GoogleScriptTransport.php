<?php

namespace App\Mail;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class GoogleScriptTransport extends AbstractTransport
{
    public function __construct(protected string $webhookUrl)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $toAddresses = array_map(fn ($addr) => $addr->getAddress(), $email->getTo());

        $payload = [
            'to' => implode(',', $toAddresses),
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

        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        unset($ch);

        if ($code < 200 || $code >= 400 || $curlError) {
            throw new \RuntimeException('Google Script Mailer Error (' . $code . '): ' . ($curlError ?: $res));
        }
    }

    public function __toString(): string
    {
        return 'googlescript';
    }
}

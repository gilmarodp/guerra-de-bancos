<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppClient
{
    public function sendAccessCredentials(string $name, string $phone, string $email, string $plainPassword): void
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');

        if (! is_string($accountSid) || $accountSid === '' || ! is_string($authToken) || $authToken === '') {
            throw new RuntimeException('Twilio credentials not configured.');
        }

        $messagingServiceSid = config('services.twilio.messaging_service_sid');
        $from = config('services.twilio.whatsapp_from');

        if ((! is_string($from) || $from === '') && (! is_string($messagingServiceSid) || $messagingServiceSid === '')) {
            throw new RuntimeException('Twilio WhatsApp sender not configured.');
        }

        $payload = [
            'To' => $this->normalizeWhatsappNumber($phone),
            'Body' => $this->buildMessage($name, $email, $plainPassword),
        ];

        if (is_string($messagingServiceSid) && $messagingServiceSid !== '') {
            $payload['MessagingServiceSid'] = $messagingServiceSid;
        } else {
            $payload['From'] = $this->normalizeWhatsappNumber($from);
        }

        $response = Http::asForm()
            ->withBasicAuth($accountSid, $authToken)
            ->timeout(10)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", $payload);

        if ($response->failed()) {
            throw new RuntimeException('Twilio WhatsApp request failed: '.$response->status());
        }
    }

    private function buildMessage(string $name, string $email, string $plainPassword): string
    {
        return sprintf(
            'Ola %s, seu acesso ao sistema: email %s, senha %s.',
            $name,
            $email,
            $plainPassword
        );
    }

    private function normalizeWhatsappNumber(?string $number): string
    {
        $value = is_string($number) ? trim($number) : '';

        if ($value === '') {
            return '';
        }

        if (stripos($value, 'whatsapp:') === 0) {
            return $value;
        }

        return 'whatsapp:'.$value;
    }
}


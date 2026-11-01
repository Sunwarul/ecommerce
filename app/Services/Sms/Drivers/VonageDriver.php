<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;

class VonageDriver extends SmsDriver
{
    public static function label(): string
    {
        return 'Vonage (Nexmo)';
    }

    public static function fields(): array
    {
        return [
            ['key' => 'api_key', 'label' => 'API Key', 'type' => 'text'],
            ['key' => 'api_secret', 'label' => 'API Secret', 'type' => 'password'],
        ];
    }

    public function send(string $phone, string $message): SmsResult
    {
        $unicode = preg_match('/[^\x00-\x7F]/', $message) === 1;

        $res = $this->http()->asForm()->post('https://rest.nexmo.com/sms/json', [
            'api_key' => $this->cred('api_key'),
            'api_secret' => $this->cred('api_secret'),
            'from' => $this->senderId,
            'to' => $phone,
            'text' => $message,
            'type' => $unicode ? 'unicode' : 'text',
        ]);

        $first = $res->json('messages.0', []);
        if ((string) ($first['status'] ?? '') === '0') {
            return SmsResult::ok($first['message-id'] ?? null, $res->body());
        }

        return SmsResult::fail((string) ($first['error-text'] ?? 'HTTP '.$res->status()), $res->body());
    }

    public static function supportsBalance(): bool
    {
        return true;
    }

    public function balance(): ?float
    {
        $res = $this->http()->get('https://rest.nexmo.com/account/get-balance', [
            'api_key' => $this->cred('api_key'),
            'api_secret' => $this->cred('api_secret'),
        ]);

        return $this->extractNumber($res->json(), ['value']);
    }
}

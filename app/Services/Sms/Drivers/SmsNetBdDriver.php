<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;

/** Alpha SMS / sms.net.bd */
class SmsNetBdDriver extends SmsDriver
{
    public static function label(): string
    {
        return 'Alpha SMS (sms.net.bd)';
    }

    public static function fields(): array
    {
        return [
            ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password'],
            ['key' => 'base_url', 'label' => 'Base URL', 'type' => 'text', 'help' => 'Default: https://api.sms.net.bd'],
        ];
    }

    private function base(): string
    {
        return rtrim($this->cred('base_url') ?: 'https://api.sms.net.bd', '/');
    }

    public function send(string $phone, string $message): SmsResult
    {
        $payload = [
            'api_key' => $this->cred('api_key'),
            'msg' => $message,
            'to' => $phone,
        ];
        if ($this->senderId) {
            $payload['sender_id'] = $this->senderId;
        }

        $res = $this->http()->asForm()->post($this->base().'/sendsms', $payload);
        $json = $res->json();

        if ($res->successful() && (int) data_get($json, 'error', 1) === 0) {
            return SmsResult::ok((string) data_get($json, 'data.request_id'), $res->body());
        }

        return SmsResult::fail((string) (data_get($json, 'msg') ?? 'HTTP '.$res->status()), $res->body());
    }

    public static function supportsBalance(): bool
    {
        return true;
    }

    public function balance(): ?float
    {
        $res = $this->http()->get($this->base().'/user/balance/', ['api_key' => $this->cred('api_key')]);

        return $this->extractNumber($res->json() ?? $res->body(), ['data.balance', 'balance']);
    }
}

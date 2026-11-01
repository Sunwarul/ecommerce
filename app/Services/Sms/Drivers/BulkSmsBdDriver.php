<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;

/** bulksmsbd.net */
class BulkSmsBdDriver extends SmsDriver
{
    public static function label(): string
    {
        return 'BulkSMSBD (bulksmsbd.net)';
    }

    public static function fields(): array
    {
        return [
            ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password'],
            ['key' => 'base_url', 'label' => 'Base URL', 'type' => 'text', 'help' => 'Default: http://bulksmsbd.net/api'],
        ];
    }

    private function base(): string
    {
        return rtrim($this->cred('base_url') ?: 'http://bulksmsbd.net/api', '/');
    }

    public function send(string $phone, string $message): SmsResult
    {
        $res = $this->http()->asForm()->post($this->base().'/smsapi', [
            'api_key' => $this->cred('api_key'),
            'type' => 'text',
            'number' => $phone,
            'senderid' => $this->senderId,
            'message' => $message,
        ]);

        $json = $res->json();
        if ((int) data_get($json, 'response_code') === 202) {
            return SmsResult::ok((string) data_get($json, 'message_id'), $res->body());
        }

        return SmsResult::fail((string) (data_get($json, 'error_message') ?? data_get($json, 'success_message') ?? 'HTTP '.$res->status()), $res->body());
    }

    public static function supportsBalance(): bool
    {
        return true;
    }

    public function balance(): ?float
    {
        $res = $this->http()->get($this->base().'/getBalanceApi', ['api_key' => $this->cred('api_key')]);

        return $this->extractNumber($res->json() ?? $res->body(), ['balance', 'data.balance']);
    }
}

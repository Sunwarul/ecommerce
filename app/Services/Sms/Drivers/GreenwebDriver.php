<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;
use Illuminate\Support\Str;

/** greenweb.com.bd */
class GreenwebDriver extends SmsDriver
{
    public static function label(): string
    {
        return 'Greenweb (greenweb.com.bd)';
    }

    public static function fields(): array
    {
        return [
            ['key' => 'token', 'label' => 'API Token', 'type' => 'password'],
            ['key' => 'base_url', 'label' => 'Base URL', 'type' => 'text', 'help' => 'Default: https://api.greenweb.com.bd'],
        ];
    }

    private function base(): string
    {
        return rtrim($this->cred('base_url') ?: 'https://api.greenweb.com.bd', '/');
    }

    public function send(string $phone, string $message): SmsResult
    {
        $res = $this->http()->asForm()->post($this->base().'/api.php', [
            'token' => $this->cred('token'),
            'to' => $phone,
            'message' => $message,
        ]);

        $body = trim($res->body());
        if ($res->successful() && Str::startsWith(Str::lower($body), 'ok')) {
            return SmsResult::ok(null, $body);
        }

        return SmsResult::fail($body ?: 'HTTP '.$res->status(), $body);
    }

    public static function supportsBalance(): bool
    {
        return true;
    }

    public function balance(): ?float
    {
        $res = $this->http()->get($this->base().'/g_api.php', ['token' => $this->cred('token'), 'balance' => '']);

        return $this->extractNumber($res->json() ?? $res->body(), ['balance']);
    }
}

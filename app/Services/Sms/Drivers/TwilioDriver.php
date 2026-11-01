<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;

class TwilioDriver extends SmsDriver
{
    public static function label(): string
    {
        return 'Twilio';
    }

    public static function fields(): array
    {
        return [
            ['key' => 'account_sid', 'label' => 'Account SID', 'type' => 'text'],
            ['key' => 'auth_token', 'label' => 'Auth Token', 'type' => 'password'],
        ];
    }

    private function base(): string
    {
        return 'https://api.twilio.com/2010-04-01/Accounts/'.$this->cred('account_sid');
    }

    public function send(string $phone, string $message): SmsResult
    {
        $res = $this->http()
            ->withBasicAuth((string) $this->cred('account_sid'), (string) $this->cred('auth_token'))
            ->asForm()
            ->post($this->base().'/Messages.json', [
                'From' => $this->senderId,
                'To' => '+'.$phone,
                'Body' => $message,
            ]);

        if ($res->successful()) {
            return SmsResult::ok((string) $res->json('sid'), $res->body());
        }

        return SmsResult::fail((string) ($res->json('message') ?? 'HTTP '.$res->status()), $res->body());
    }

    public static function supportsBalance(): bool
    {
        return true;
    }

    public function balance(): ?float
    {
        $res = $this->http()
            ->withBasicAuth((string) $this->cred('account_sid'), (string) $this->cred('auth_token'))
            ->get($this->base().'/Balance.json');

        return $this->extractNumber($res->json(), ['balance']);
    }
}

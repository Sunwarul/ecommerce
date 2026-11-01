<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;
use Illuminate\Support\Facades\Log;

/** Writes messages to the application log instead of sending. Useful for testing. */
class LogDriver extends SmsDriver
{
    public static function label(): string
    {
        return 'Log only (testing)';
    }

    public static function fields(): array
    {
        return [];
    }

    public function send(string $phone, string $message): SmsResult
    {
        Log::info('SMS (log driver)', ['to' => $phone, 'sender' => $this->senderId, 'message' => $message]);

        return SmsResult::ok('log-'.uniqid(), 'logged');
    }

    public static function supportsBalance(): bool
    {
        return true;
    }

    public function balance(): ?float
    {
        return 9999;
    }
}

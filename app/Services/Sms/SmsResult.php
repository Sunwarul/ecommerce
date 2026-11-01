<?php

namespace App\Services\Sms;

class SmsResult
{
    public function __construct(
        public bool $success,
        public ?string $messageId = null,
        public ?string $response = null,
        public ?string $error = null,
    ) {}

    public static function ok(?string $messageId = null, ?string $response = null): self
    {
        return new self(true, $messageId, $response);
    }

    public static function fail(string $error, ?string $response = null): self
    {
        return new self(false, null, $response, $error);
    }
}

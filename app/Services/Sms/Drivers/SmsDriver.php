<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class SmsDriver
{
    public function __construct(
        protected array $credentials = [],
        protected ?string $senderId = null,
    ) {}

    /** Human label shown in the gateway form. */
    abstract public static function label(): string;

    /**
     * Credential fields rendered by the gateway form.
     * Each: ['key' => .., 'label' => .., 'type' => 'text|password|textarea|select', 'options' => [..], 'help' => ..]
     */
    abstract public static function fields(): array;

    /** $phone is digits only, with country code (e.g. 8801712345678). */
    abstract public function send(string $phone, string $message): SmsResult;

    public static function supportsBalance(): bool
    {
        return false;
    }

    public function balance(): ?float
    {
        throw new \RuntimeException('Balance check is not supported by this gateway.');
    }

    protected function cred(string $key, $default = null)
    {
        return Arr::get($this->credentials, $key, $default);
    }

    protected function http()
    {
        return Http::timeout(20)->acceptJson();
    }

    /** Pull the first numeric value found under any of the given keys (dot notation) or in a plain-text body. */
    protected function extractNumber($body, array $paths = []): ?float
    {
        if (is_array($body)) {
            foreach ($paths as $path) {
                $v = data_get($body, $path);
                if (is_numeric($v)) {
                    return (float) $v;
                }
            }

            return null;
        }

        if (is_string($body) && preg_match('/-?\d+(?:\.\d+)?/', str_replace(',', '', $body), $m)) {
            return (float) $m[0];
        }

        return null;
    }
}

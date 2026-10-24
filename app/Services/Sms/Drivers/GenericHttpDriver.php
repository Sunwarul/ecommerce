<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Works with most HTTP SMS vendors: configure URL, method and parameter names from the vendor docs.
 */
class GenericHttpDriver extends SmsDriver
{
    public static function label(): string
    {
        return 'Generic HTTP API (any vendor)';
    }

    public static function fields(): array
    {
        return [
            ['key' => 'url', 'label' => 'Send URL', 'type' => 'text', 'help' => 'e.g. https://vendor.com/api/send'],
            ['key' => 'method', 'label' => 'Method', 'type' => 'select', 'options' => ['GET', 'POST', 'POST_JSON']],
            ['key' => 'phone_param', 'label' => 'Phone parameter name', 'type' => 'text', 'help' => 'e.g. to, number, msisdn'],
            ['key' => 'message_param', 'label' => 'Message parameter name', 'type' => 'text', 'help' => 'e.g. message, msg, sms'],
            ['key' => 'sender_param', 'label' => 'Sender ID parameter name', 'type' => 'text', 'help' => 'Leave empty if not needed'],
            ['key' => 'extra_params', 'label' => 'Extra parameters', 'type' => 'textarea', 'help' => 'One per line: key=value (e.g. api_key=XXXX)'],
            ['key' => 'headers', 'label' => 'Headers', 'type' => 'textarea', 'help' => 'One per line: Header-Name: value'],
            ['key' => 'phone_prefix', 'label' => 'Phone prefix', 'type' => 'text', 'help' => 'Prepended to number, e.g. + (leave empty for 8801XXXXXXXXX)'],
            ['key' => 'success_keyword', 'label' => 'Success keyword', 'type' => 'text', 'help' => 'Text that appears in a successful response body. Empty = any HTTP 2xx'],
            ['key' => 'balance_url', 'label' => 'Balance URL', 'type' => 'text', 'help' => 'Full URL incl. credentials (optional)'],
            ['key' => 'balance_path', 'label' => 'Balance JSON path', 'type' => 'text', 'help' => 'e.g. data.balance (empty = first number in response)'],
        ];
    }

    private function lines(?string $text, string $sep): array
    {
        $out = [];
        foreach (preg_split('/\r?\n/', (string) $text) as $line) {
            if (! str_contains($line, $sep)) {
                continue;
            }
            [$k, $v] = array_map('trim', explode($sep, $line, 2));
            if ($k !== '') {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    public function send(string $phone, string $message): SmsResult
    {
        $params = $this->lines($this->cred('extra_params'), '=');
        $params[$this->cred('phone_param') ?: 'to'] = ($this->cred('phone_prefix') ?? '').$phone;
        $params[$this->cred('message_param') ?: 'message'] = $message;
        if ($this->cred('sender_param') && $this->senderId) {
            $params[$this->cred('sender_param')] = $this->senderId;
        }

        $req = Http::timeout(20)->withHeaders($this->lines($this->cred('headers'), ':'));
        $res = match (strtoupper((string) $this->cred('method', 'GET'))) {
            'POST' => $req->asForm()->post($this->cred('url'), $params),
            'POST_JSON' => $req->asJson()->post($this->cred('url'), $params),
            default => $req->get($this->cred('url'), $params),
        };

        $body = $res->body();
        $keyword = trim((string) $this->cred('success_keyword'));
        $ok = $res->successful() && ($keyword === '' || Str::contains(Str::lower($body), Str::lower($keyword)));

        return $ok ? SmsResult::ok(null, $body) : SmsResult::fail('Unexpected response (HTTP '.$res->status().')', $body);
    }

    public static function supportsBalance(): bool
    {
        return true;
    }

    public function balance(): ?float
    {
        if (! $this->cred('balance_url')) {
            throw new \RuntimeException('Balance URL is not configured for this gateway.');
        }
        $res = Http::timeout(20)->withHeaders($this->lines($this->cred('headers'), ':'))->get($this->cred('balance_url'));
        $path = $this->cred('balance_path');

        return $path
            ? $this->extractNumber($res->json() ?? [], [$path])
            : $this->extractNumber($res->body());
    }
}

<?php

namespace App\Services\Sms;

use App\Jobs\SendSmsJob;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\SmsGateway;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Services\Sms\Drivers\BulkSmsBdDriver;
use App\Services\Sms\Drivers\GenericHttpDriver;
use App\Services\Sms\Drivers\GreenwebDriver;
use App\Services\Sms\Drivers\LogDriver;
use App\Services\Sms\Drivers\SmsDriver;
use App\Services\Sms\Drivers\SmsNetBdDriver;
use App\Services\Sms\Drivers\TwilioDriver;
use App\Services\Sms\Drivers\VonageDriver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SmsManager
{
    /** Event keys that can be toggled on/off in SMS settings. */
    public const EVENTS = [
        'sale_completed' => 'Sale completed',
        'payment_received' => 'Due / installment payment received',
        'installment_created' => 'Installment agreement created',
        'installment_reminder' => 'Upcoming installment reminder',
        'installment_overdue' => 'Overdue installment notice',
    ];

    public static function drivers(): array
    {
        return [
            'bulksmsbd' => BulkSmsBdDriver::class,
            'smsnetbd' => SmsNetBdDriver::class,
            'greenweb' => GreenwebDriver::class,
            'twilio' => TwilioDriver::class,
            'vonage' => VonageDriver::class,
            'generic' => GenericHttpDriver::class,
            'log' => LogDriver::class,
        ];
    }

    /** Driver metadata for the gateway form. */
    public static function driverOptions(): array
    {
        return collect(self::drivers())->map(fn ($class, $key) => [
            'value' => $key,
            'label' => $class::label(),
            'fields' => $class::fields(),
            'supports_balance' => $class::supportsBalance(),
        ])->values()->all();
    }

    public function driverFor(SmsGateway $gateway): SmsDriver
    {
        $class = self::drivers()[$gateway->driver] ?? null;
        if (! $class) {
            throw new \InvalidArgumentException("Unknown SMS driver [{$gateway->driver}]");
        }

        return new $class($gateway->credentials ?? [], $gateway->sender_id);
    }

    public function enabled(): bool
    {
        return (string) Setting::get('sms_enabled', '0') === '1';
    }

    public function eventEnabled(string $event): bool
    {
        return $this->enabled() && (string) Setting::get("sms_event_{$event}", '0') === '1';
    }

    /** Normalise to digits with country code: 01712-345678 => 8801712345678 */
    public static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }
        $cc = preg_replace('/\D+/', '', (string) Setting::get('sms_country_code', '880'));

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif ($cc !== '' && ! str_starts_with($digits, $cc)) {
            $digits = str_starts_with($digits, '0') ? $cc.substr($digits, 1) : $cc.$digits;
        }

        return (strlen($digits) >= 8 && strlen($digits) <= 15) ? $digits : null;
    }

    /** GSM-7: 160 / 153 per part, Unicode (e.g. Bangla): 70 / 67 per part. */
    public static function segments(string $message): int
    {
        $unicode = preg_match('/[^\x00-\x7F]/', $message) === 1;
        $len = mb_strlen($message);
        if ($len === 0) {
            return 0;
        }
        [$single, $multi] = $unicode ? [70, 67] : [160, 153];

        return $len <= $single ? 1 : (int) ceil($len / $multi);
    }

    public static function render(string $body, array $vars): string
    {
        $replace = [];
        foreach ($vars as $k => $v) {
            $replace['{'.$k.'}'] = (string) $v;
        }

        return trim(strtr($body, $replace));
    }

    public static function money($amount): string
    {
        return number_format((float) $amount, 2);
    }

    /**
     * Queue an SMS. Returns the log row (status queued / failed when the number is invalid).
     *
     * $options: type, event, customer_id, smsable (Model), campaign_id, gateway_id, sent_by
     */
    public function send(string $phone, string $message, array $options = []): SmsLog
    {
        $normalized = self::normalizePhone($phone);
        $gateway = ! empty($options['gateway_id'])
            ? SmsGateway::find($options['gateway_id'])
            : SmsGateway::default();

        /** @var Model|null $smsable */
        $smsable = $options['smsable'] ?? null;

        $log = SmsLog::create([
            'sms_gateway_id' => $gateway?->id,
            'sms_campaign_id' => $options['campaign_id'] ?? null,
            'customer_id' => $options['customer_id'] ?? null,
            'phone' => $normalized ?? (string) $phone,
            'message' => $message,
            'type' => $options['type'] ?? 'transactional',
            'event' => $options['event'] ?? null,
            'smsable_type' => $smsable?->getMorphClass(),
            'smsable_id' => $smsable?->getKey(),
            'segments' => self::segments($message),
            'status' => 'queued',
            'sent_by' => $options['sent_by'] ?? auth()->id(),
        ]);

        if (! $normalized) {
            $log->update(['status' => 'failed', 'error' => 'Invalid phone number']);

            return $log;
        }
        if (! $gateway) {
            $log->update(['status' => 'failed', 'error' => 'No active SMS gateway configured']);

            return $log;
        }

        $this->dispatch($log);

        return $log;
    }

    public function dispatch(SmsLog $log): void
    {
        match (Setting::get('sms_delivery_mode', 'after_response')) {
            'queue' => SendSmsJob::dispatch($log->id),
            'sync' => $this->deliver($log),
            default => SendSmsJob::dispatchAfterResponse($log->id),
        };
    }

    /** Actually hit the vendor API and record the result. */
    public function deliver(SmsLog $log): SmsLog
    {
        $gateway = $log->gateway ?? SmsGateway::default();
        if (! $gateway) {
            $log->update(['status' => 'failed', 'error' => 'No active SMS gateway configured']);

            return $log;
        }

        try {
            $result = $this->driverFor($gateway)->send($log->phone, $log->message);
        } catch (\Throwable $e) {
            $result = SmsResult::fail($e->getMessage());
        }

        $log->update([
            'sms_gateway_id' => $gateway->id,
            'status' => $result->success ? 'sent' : 'failed',
            'provider_message_id' => $result->messageId,
            'response' => $result->response ? mb_substr($result->response, 0, 2000) : null,
            'error' => $result->error ? mb_substr($result->error, 0, 1000) : null,
            'sent_at' => $result->success ? now() : null,
        ]);

        if ($log->sms_campaign_id) {
            $log->campaign?->increment($result->success ? 'sent_count' : 'failed_count');
            $campaign = $log->campaign?->fresh();
            if ($campaign && $campaign->sent_count + $campaign->failed_count >= $campaign->recipients_count) {
                $campaign->update(['status' => 'completed']);
            }
        }

        return $log;
    }

    public function balance(SmsGateway $gateway): ?float
    {
        $balance = $this->driverFor($gateway)->balance();
        $gateway->update(['balance' => $balance, 'balance_checked_at' => now()]);

        return $balance;
    }

    /**
     * Send an event SMS (sale_completed, payment_received, ...) to a customer if the event is enabled.
     * Never throws: SMS problems must not break a sale.
     */
    public function notify(string $event, ?Customer $customer, array $vars = [], ?Model $smsable = null): ?SmsLog
    {
        try {
            if (! $customer || ! $customer->phone || ! $this->eventEnabled($event)) {
                return null;
            }
            $template = SmsTemplate::where('key', $event)->where('is_active', true)->first();
            if (! $template) {
                return null;
            }

            $message = self::render($template->body, array_merge([
                'customer_name' => $customer->name,
                'phone' => $customer->phone,
                'shop_name' => Setting::get('general_application_name', config('app.name')),
                'shop_phone' => Setting::get('general_phone', ''),
                'date' => now()->format('d-m-Y'),
            ], $vars));

            return $this->send($customer->phone, $message, [
                'type' => 'transactional',
                'event' => $event,
                'customer_id' => $customer->id,
                'smsable' => $smsable,
            ]);
        } catch (\Throwable $e) {
            Log::warning('SMS notify failed', ['event' => $event, 'error' => $e->getMessage()]);

            return null;
        }
    }
}

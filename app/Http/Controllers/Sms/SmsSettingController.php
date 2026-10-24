<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SmsGateway;
use App\Models\SmsTemplate;
use App\Services\Sms\SmsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SmsSettingController extends Controller
{
    public const MASK = '******';

    public function __construct(protected SmsManager $sms) {}

    public function index()
    {
        $events = collect(SmsManager::EVENTS)->map(fn ($label, $key) => [
            'key' => $key,
            'label' => $label,
            'enabled' => (string) Setting::get("sms_event_{$key}", '0') === '1',
        ])->values();

        return Inertia::render('Admin/Sms/Settings', [
            'settings' => [
                'sms_enabled' => (string) Setting::get('sms_enabled', '0') === '1',
                'sms_country_code' => Setting::get('sms_country_code', '880'),
                'sms_delivery_mode' => Setting::get('sms_delivery_mode', 'after_response'),
                'sms_installment_reminder_days' => (int) Setting::get('sms_installment_reminder_days', 3),
                'installment_rounding' => Setting::get('installment_rounding', 'integer'),
            ],
            'events' => $events,
            'templates' => SmsTemplate::orderBy('id')->get(),
            'gateways' => SmsGateway::orderByDesc('is_default')->get()->map(fn ($g) => $this->presentGateway($g)),
            'drivers' => SmsManager::driverOptions(),
            'placeholders' => [
                '{customer_name}', '{phone}', '{shop_name}', '{shop_phone}', '{date}', '{invoice_no}', '{total}', '{paid}', '{due}',
                '{amount}', '{items_count}', '{agreement_no}', '{installments}', '{installment_no}', '{installment_amount}', '{next_due_date}',
            ],
        ]);
    }

    /** Gateway for the UI, with secrets masked. */
    protected function presentGateway(SmsGateway $g): array
    {
        $class = SmsManager::drivers()[$g->driver] ?? null;
        $fields = $class ? $class::fields() : [];
        $creds = $g->credentials ?? [];
        foreach ($fields as $f) {
            if (($f['type'] ?? '') === 'password' && ! empty($creds[$f['key']])) {
                $creds[$f['key']] = self::MASK;
            }
        }

        return Arr::except($g->toArray(), ['credentials']) + ['credentials' => $creds];
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sms_enabled' => ['required', 'boolean'],
            'sms_country_code' => ['nullable', 'regex:/^\+?\d{0,4}$/'],
            'sms_delivery_mode' => ['required', 'in:after_response,queue,sync'],
            'sms_installment_reminder_days' => ['required', 'integer', 'min:0', 'max:30'],
            'installment_rounding' => ['required', 'in:integer,none'],
            'events' => ['array'],
            'events.*' => ['boolean'],
        ]);

        Setting::set('sms_enabled', $data['sms_enabled'] ? '1' : '0');
        Setting::set('sms_country_code', ltrim((string) ($data['sms_country_code'] ?? ''), '+'));
        Setting::set('sms_delivery_mode', $data['sms_delivery_mode']);
        Setting::set('sms_installment_reminder_days', (string) $data['sms_installment_reminder_days']);
        Setting::set('installment_rounding', $data['installment_rounding']);

        foreach (array_keys(SmsManager::EVENTS) as $key) {
            Setting::set("sms_event_{$key}", ! empty($data['events'][$key]) ? '1' : '0');
        }

        return back()->with('success', 'SMS settings saved.');
    }

    public function updateTemplate(Request $request, SmsTemplate $template)
    {
        $template->update($request->validate([
            'name' => ['required', 'string', 'max:191'],
            'body' => ['required', 'string', 'max:1600'],
            'is_active' => ['boolean'],
        ]));

        return back()->with('success', 'Template saved.');
    }

    public function storeTemplate(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'body' => ['required', 'string', 'max:1600'],
        ]);
        SmsTemplate::create($data + ['key' => 'custom_'.uniqid(), 'is_active' => true]);

        return back()->with('success', 'Template created.');
    }

    public function destroyTemplate(SmsTemplate $template)
    {
        if (array_key_exists($template->key, SmsManager::EVENTS)) {
            throw ValidationException::withMessages(['template' => ['System templates cannot be deleted; turn them off instead.']]);
        }
        $template->delete();

        return back()->with('success', 'Template deleted.');
    }

    // ------------------------------------------------------------------
    // Gateways
    // ------------------------------------------------------------------

    protected function gatewayData(Request $request, ?SmsGateway $existing = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'driver' => ['required', 'in:'.implode(',', array_keys(SmsManager::drivers()))],
            'sender_id' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
            'credentials' => ['nullable', 'array'],
        ]);

        // keep stored secrets when the masked placeholder comes back
        $creds = $data['credentials'] ?? [];
        $old = $existing?->credentials ?? [];
        foreach ($creds as $k => $v) {
            if ($v === self::MASK) {
                $creds[$k] = $old[$k] ?? null;
            }
        }
        $data['credentials'] = $creds;

        return $data;
    }

    protected function applyDefault(SmsGateway $gateway): void
    {
        if ($gateway->is_default || SmsGateway::where('is_default', true)->doesntExist()) {
            SmsGateway::where('id', '!=', $gateway->id)->update(['is_default' => false]);
            $gateway->update(['is_default' => true]);
        }
    }

    public function storeGateway(Request $request)
    {
        $gateway = SmsGateway::create($this->gatewayData($request));
        $this->applyDefault($gateway);

        return back()->with('success', 'Gateway added.');
    }

    public function updateGateway(Request $request, SmsGateway $gateway)
    {
        $gateway->update($this->gatewayData($request, $gateway));
        $this->applyDefault($gateway->fresh());

        return back()->with('success', 'Gateway updated.');
    }

    public function destroyGateway(SmsGateway $gateway)
    {
        $wasDefault = $gateway->is_default;
        $gateway->delete();
        if ($wasDefault && ($next = SmsGateway::where('is_active', true)->first())) {
            $next->update(['is_default' => true]);
        }

        return back()->with('success', 'Gateway removed.');
    }

    public function balance(SmsGateway $gateway)
    {
        try {
            $balance = $this->sms->balance($gateway);
        } catch (\Throwable $e) {
            return back()->with('error', 'Balance check failed: '.$e->getMessage());
        }

        return back()->with('success', $balance === null ? 'Could not read balance from the response.' : 'Balance: '.number_format($balance, 2));
    }

    public function test(Request $request, SmsGateway $gateway)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $log = $this->sms->send($data['phone'], $data['message'] ?: 'Test SMS from '.Setting::get('general_application_name', config('app.name')), [
            'type' => 'test',
            'event' => 'test',
            'gateway_id' => $gateway->id,
        ]);

        // test messages are delivered immediately so the result can be shown
        if ($log->status === 'queued') {
            $log = $this->sms->deliver($log);
        }

        return $log->status === 'sent'
            ? back()->with('success', 'Test SMS sent to '.$log->phone.'.')
            : back()->with('error', 'Test failed: '.($log->error ?: 'unknown error'));
    }
}

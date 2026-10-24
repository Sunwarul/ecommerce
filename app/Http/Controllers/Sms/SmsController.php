<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\SmsCampaign;
use App\Models\SmsGateway;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Services\Sms\SmsManager;
use App\Support\ReportExporter;
use App\Support\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SmsController extends Controller
{
    public function __construct(protected SmsManager $sms) {}

    // ------------------------------------------------------------------
    // Dashboard & reports
    // ------------------------------------------------------------------

    public function dashboard(Request $request)
    {
        $from = $request->date('date_from') ?? today()->subDays(29);
        $to = $request->date('date_to') ?? today();
        $range = [$from->copy()->startOfDay(), $to->copy()->endOfDay()];

        $inRange = fn () => SmsLog::whereBetween('sms_logs.created_at', $range);

        $totals = $inRange()
            ->selectRaw("COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'queued' THEN 1 ELSE 0 END) as queued,
                SUM(CASE WHEN status = 'sent' THEN segments ELSE 0 END) as segments")
            ->first();

        // Daily trend
        $daily = $inRange()
            ->selectRaw("DATE(sms_logs.created_at) as day,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed")
            ->groupBy('day')->orderBy('day')->get()->keyBy('day');

        $labels = $sent = $failed = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $labels[] = $d->format('d M');
            $sent[] = (int) ($daily[$key]->sent ?? 0);
            $failed[] = (int) ($daily[$key]->failed ?? 0);
        }

        $byType = $inRange()->selectRaw("COALESCE(event, type) as label, COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed")
            ->groupBy('label')->orderByDesc('total')->get();

        $byGateway = $inRange()->leftJoin('sms_gateways', 'sms_gateways.id', '=', 'sms_logs.sms_gateway_id')
            ->selectRaw("COALESCE(sms_gateways.name, 'None') as label, COUNT(*) as total,
                SUM(CASE WHEN sms_logs.status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN sms_logs.status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN sms_logs.status = 'sent' THEN sms_logs.segments ELSE 0 END) as segments")
            ->groupBy('label')->get();

        $topErrors = $inRange()->where('status', 'failed')->whereNotNull('error')
            ->selectRaw('error, COUNT(*) as total')->groupBy('error')->orderByDesc('total')->limit(5)->get();

        if ($request->filled('export')) {
            abort_unless($request->user()->can('Report Export'), 403);
            $rows = collect($labels)->map(fn ($l, $i) => [$l, $sent[$i], $failed[$i], $sent[$i] + $failed[$i]])->all();

            return ReportExporter::download($request->get('export'), 'SMS Delivery Report', ['Day', 'Sent', 'Failed', 'Total'], $rows, [
                'Total' => (int) $totals->total,
                'Sent' => (int) $totals->sent,
                'Failed' => (int) $totals->failed,
                'Segments used' => (int) $totals->segments,
            ], ['Period' => $from->format('d M Y').' - '.$to->format('d M Y')]);
        }

        return Inertia::render('Admin/Sms/Dashboard', [
            'filters' => ['date_from' => $from->toDateString(), 'date_to' => $to->toDateString()],
            'totals' => [
                'total' => (int) $totals->total,
                'sent' => (int) $totals->sent,
                'failed' => (int) $totals->failed,
                'queued' => (int) $totals->queued,
                'segments' => (int) $totals->segments,
                'success_rate' => $totals->total ? round($totals->sent / $totals->total * 100, 1) : 0,
            ],
            'chart' => ['labels' => $labels, 'sent' => $sent, 'failed' => $failed],
            'byType' => $byType,
            'byGateway' => $byGateway,
            'topErrors' => $topErrors,
            'gateways' => SmsGateway::orderByDesc('is_default')->get(['id', 'name', 'driver', 'is_default', 'is_active', 'balance', 'balance_checked_at']),
            'campaigns' => SmsCampaign::with('creator:id,name')->latest('id')->limit(10)->get(),
            'enabled' => $this->sms->enabled(),
        ]);
    }

    // ------------------------------------------------------------------
    // Compose & send
    // ------------------------------------------------------------------

    public function compose()
    {
        return Inertia::render('Admin/Sms/Send', [
            'templates' => SmsTemplate::where('is_active', true)->orderBy('name')->get(['id', 'key', 'name', 'body']),
            'gateways' => SmsGateway::where('is_active', true)->orderByDesc('is_default')->get(['id', 'name', 'is_default']),
            'audiences' => $this->audienceCounts(),
            'enabled' => $this->sms->enabled(),
        ]);
    }

    protected function audienceQuery(?string $audience)
    {
        $q = Customer::query()->whereNotNull('phone')->where('phone', '!=', '');

        return match ($audience) {
            'due' => $q->whereHas('orders', fn ($o) => $o->where('status', 'completed')->where('due_amount', '>', 0)),
            'installment' => $q->whereHas('installmentSales', fn ($s) => $s->where('status', 'active')),
            'overdue' => $q->whereHas('installmentSales', fn ($s) => $s->overdue()),
            'buyers' => $q->whereHas('orders', fn ($o) => $o->where('status', 'completed')),
            default => $q,
        };
    }

    protected function audienceCounts(): array
    {
        return collect([
            'all' => 'All customers',
            'buyers' => 'Customers who purchased',
            'due' => 'Customers with due',
            'installment' => 'Active installment customers',
            'overdue' => 'Overdue installment customers',
        ])->map(fn ($label, $key) => [
            'value' => $key,
            'label' => $label,
            'count' => $this->audienceQuery($key)->count(),
        ])->values()->all();
    }

    /** JSON customer picker (partial name/phone search). */
    public function customers(Request $request)
    {
        $query = Search::customers($this->audienceQuery($request->get('audience')), $request->get('q'))
            ->select('id', 'name', 'phone')
            ->orderBy('name');

        return response()->json($query->paginate(50));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:191'],
            'message' => ['required', 'string', 'max:1600'],
            'customer_ids' => ['nullable', 'array'],
            'customer_ids.*' => ['integer'],
            'audience' => ['nullable', 'in:all,buyers,due,installment,overdue'],
            'numbers' => ['nullable', 'string', 'max:20000'],
            'gateway_id' => ['nullable', 'exists:sms_gateways,id'],
        ]);

        if (! $this->sms->enabled()) {
            throw ValidationException::withMessages(['message' => [__('SMS is turned off. Enable it in SMS Settings first.')]]);
        }

        // Collect recipients: [normalized phone => [customer|null, raw phone]]
        $recipients = [];
        $customers = collect();
        if (! empty($data['audience'])) {
            $customers = $this->audienceQuery($data['audience'])->get(['id', 'name', 'phone']);
        } elseif (! empty($data['customer_ids'])) {
            $customers = Customer::whereIn('id', $data['customer_ids'])->get(['id', 'name', 'phone']);
        }
        foreach ($customers as $c) {
            if ($p = SmsManager::normalizePhone($c->phone)) {
                $recipients[$p] ??= [$c, $c->phone];
            }
        }
        foreach (preg_split('/[\s,;]+/', (string) ($data['numbers'] ?? '')) as $raw) {
            if ($raw !== '' && ($p = SmsManager::normalizePhone($raw))) {
                $recipients[$p] ??= [null, $raw];
            }
        }

        if (empty($recipients)) {
            throw ValidationException::withMessages(['customer_ids' => [__('Select at least one customer or enter a valid number.')]]);
        }

        $campaign = DB::transaction(function () use ($data, $recipients) {
            $campaign = SmsCampaign::create([
                'name' => ($data['name'] ?? null) ?: 'Campaign '.now()->format('d M Y h:i A'),
                'message' => $data['message'],
                'recipients_count' => count($recipients),
                'created_by' => Auth::id(),
            ]);

            $shop = Setting::get('general_application_name', config('app.name'));
            foreach ($recipients as [$customer, $raw]) {
                $this->sms->send($raw, SmsManager::render($data['message'], [
                    'customer_name' => $customer?->name ?? '',
                    'name' => $customer?->name ?? '',
                    'phone' => $raw,
                    'shop_name' => $shop,
                    'date' => now()->format('d-m-Y'),
                ]), [
                    'type' => 'promotional',
                    'event' => 'campaign',
                    'campaign_id' => $campaign->id,
                    'customer_id' => $customer?->id,
                    'gateway_id' => $data['gateway_id'] ?? null,
                ]);
            }

            return $campaign;
        });

        // Invalid numbers are marked failed immediately
        $failedNow = $campaign->logs()->where('status', 'failed')->count();
        if ($failedNow) {
            $campaign->increment('failed_count', $failedNow);
        }
        $campaign->refresh();
        if ($campaign->sent_count + $campaign->failed_count >= $campaign->recipients_count) {
            $campaign->update(['status' => 'completed']);
        }

        return redirect()->route('sms.logs', ['campaign_id' => $campaign->id])
            ->with('success', __(':count SMS queued for sending.', ['count' => count($recipients)]));
    }

    // ------------------------------------------------------------------
    // History
    // ------------------------------------------------------------------

    protected function logsQuery(Request $request)
    {
        return SmsLog::query()
            ->with(['gateway:id,name', 'customer:id,name', 'campaign:id,name', 'sender:id,name'])
            ->when($request->get('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->get('type'), fn ($q, $v) => $q->where('type', $v))
            ->when($request->get('event'), fn ($q, $v) => $q->where('event', $v))
            ->when($request->get('gateway_id'), fn ($q, $v) => $q->where('sms_gateway_id', $v))
            ->when($request->get('campaign_id'), fn ($q, $v) => $q->where('sms_campaign_id', $v))
            ->when($request->date('date_from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date('date_to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->filled('search'), fn ($q) => Search::apply($q, $request->get('search'), ['phone', 'message'], ['phone']))
            ->latest('id');
    }

    public function logs(Request $request)
    {
        if ($request->filled('export')) {
            abort_unless($request->user()->can('Report Export'), 403);
            $rows = $this->logsQuery($request)->limit(20000)->get()->map(fn ($l) => [
                $l->created_at?->format('d-m-Y H:i'),
                $l->phone,
                $l->customer?->name ?? '',
                $l->event ?? $l->type,
                $l->gateway?->name ?? '',
                $l->segments,
                $l->status,
                $l->error ?? '',
                $l->message,
            ])->all();

            return ReportExporter::download($request->get('export'), 'SMS History', [
                'Date', 'Phone', 'Customer', 'Type', 'Gateway', 'Parts', 'Status', 'Error', 'Message',
            ], $rows, [
                'Total' => count($rows),
                'Sent' => collect($rows)->where(6, 'sent')->count(),
                'Failed' => collect($rows)->where(6, 'failed')->count(),
            ]);
        }

        return Inertia::render('Admin/Sms/Logs', [
            'logs' => $this->logsQuery($request)->paginate(25)->withQueryString(),
            'filters' => $request->only(['status', 'type', 'event', 'gateway_id', 'campaign_id', 'date_from', 'date_to', 'search']),
            'gateways' => SmsGateway::get(['id', 'name']),
            'campaign' => $request->get('campaign_id') ? SmsCampaign::find($request->get('campaign_id')) : null,
            'events' => collect(SmsManager::EVENTS)->map(fn ($l, $k) => ['value' => $k, 'label' => $l])->values()
                ->push(['value' => 'campaign', 'label' => 'Campaign'])
                ->push(['value' => 'test', 'label' => 'Test']),
        ]);
    }

    public function resend(Request $request)
    {
        $data = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer']]);

        $logs = SmsLog::whereIn('id', $data['ids'])->where('status', 'failed')->get();
        foreach ($logs as $log) {
            $this->sms->send($log->phone, $log->message, [
                'type' => $log->type,
                'event' => $log->event,
                'customer_id' => $log->customer_id,
                'smsable' => $log->smsable_type ? $log->smsable : null,
            ]);
        }

        return back()->with('success', __(':count SMS re-queued.', ['count' => $logs->count()]));
    }

    /** Re-check queued messages older than a few minutes (e.g. if the queue worker was down). */
    public function retryStuck()
    {
        $logs = SmsLog::where('status', 'queued')->where('created_at', '<', Carbon::now()->subMinutes(5))->limit(500)->get();
        foreach ($logs as $log) {
            $this->sms->dispatch($log);
        }

        return back()->with('success', __(':count queued SMS dispatched again.', ['count' => $logs->count()]));
    }
}

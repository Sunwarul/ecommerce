<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\InstallmentSale;
use App\Models\InstallmentSchedule;
use App\Models\PaymentMethod;
use App\Models\PosOrder;
use App\Services\InstallmentService;
use App\Support\ReportExporter;
use App\Support\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class InstallmentController extends Controller
{
    public function __construct(protected InstallmentService $service) {}

    protected function branchScope($query, string $column = 'installment_sales.branch_id')
    {
        $branchId = session('current_branch_id');

        return $query->when($branchId, fn ($q) => $q->where($column, $branchId));
    }

    /** Agreements filtered by tab/search/date. */
    protected function salesQuery(Request $request)
    {
        $tab = $request->get('tab', 'active');
        $today = today()->toDateString();

        return $this->branchScope(InstallmentSale::query())
            ->with(['customer:id,name,phone', 'order:id,invoice_no,total_amount,paid_amount', 'nextSchedule'])
            ->when($request->filled('search'), function ($q) use ($request) {
                foreach (Search::tokens($request->get('search')) as $token) {
                    $q->where(fn ($qq) => $qq->where('agreement_no', 'like', Search::like($token))
                        ->orWhereHas('order', fn ($o) => $o->where('invoice_no', 'like', Search::like($token)))
                        ->orWhereHas('customer', fn ($c) => Search::customers($c, $token))
                        ->orWhere('guarantor_phone', 'like', Search::like($token)));
                }
            })
            ->when($request->date('date_from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date('date_to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($tab === 'active', fn ($q) => $q->where('status', 'active'))
            ->when($tab === 'overdue', fn ($q) => $q->overdue())
            ->when($tab === 'due_today', fn ($q) => $q->where('status', 'active')
                ->whereHas('schedules', fn ($s) => $s->unpaid()->whereDate('due_date', $today)))
            ->when($tab === 'upcoming', fn ($q) => $q->where('status', 'active')
                ->whereHas('schedules', fn ($s) => $s->unpaid()->whereBetween('due_date', [$today, today()->addDays(7)->toDateString()])))
            ->when(in_array($tab, ['completed', 'cancelled', 'defaulted'], true), fn ($q) => $q->where('status', $tab))
            ->latest('id');
    }

    /** Individual installments due in a date range (collection sheet). */
    protected function schedulesQuery(Request $request)
    {
        $from = $request->date('date_from') ?? today()->startOfMonth();
        $to = $request->date('date_to') ?? today()->endOfMonth();

        return InstallmentSchedule::query()
            ->select('installment_schedules.*')
            ->join('installment_sales', 'installment_sales.id', '=', 'installment_schedules.installment_sale_id')
            ->tap(fn ($q) => $this->branchScope($q))
            ->whereIn('installment_sales.status', ['active', 'completed'])
            ->whereBetween('installment_schedules.due_date', [$from->toDateString(), $to->toDateString()])
            ->when($request->get('schedule_status'), function ($q, $st) {
                if ($st === 'overdue') {
                    $q->whereIn('installment_schedules.status', ['pending', 'partial'])->whereDate('installment_schedules.due_date', '<', today());
                } else {
                    $q->where('installment_schedules.status', $st);
                }
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('sale', function ($s) use ($request) {
                    foreach (Search::tokens($request->get('search')) as $token) {
                        $s->where(fn ($qq) => $qq->where('agreement_no', 'like', Search::like($token))
                            ->orWhereHas('customer', fn ($c) => Search::customers($c, $token)));
                    }
                });
            })
            ->with(['sale:id,agreement_no,customer_id,order_id,status', 'sale.customer:id,name,phone'])
            ->orderBy('installment_schedules.due_date');
    }

    public function index(Request $request)
    {
        $mode = $request->get('mode', 'agreements'); // agreements | schedules

        if ($request->filled('export')) {
            abort_unless($request->user()->can('Report Export'), 403);

            return $this->export($request, $mode);
        }

        $today = today()->toDateString();
        $activeIds = $this->branchScope(InstallmentSale::where('status', 'active'))->pluck('id');

        $unpaid = fn () => InstallmentSchedule::whereIn('installment_sale_id', $activeIds)->unpaid();
        $remainingSql = 'COALESCE(SUM(amount - paid_amount + late_fee - late_fee_paid), 0)';

        $stats = [
            'active_count' => $activeIds->count(),
            'outstanding' => (float) $unpaid()->selectRaw("{$remainingSql} as v")->value('v'),
            'overdue_amount' => (float) $unpaid()->whereDate('due_date', '<', $today)->selectRaw("{$remainingSql} as v")->value('v'),
            'overdue_count' => $this->branchScope(InstallmentSale::query())->overdue()->count(),
            'due_today_amount' => (float) $unpaid()->whereDate('due_date', $today)->selectRaw("{$remainingSql} as v")->value('v'),
            'collected_this_month' => (float) DB::table('installment_payments')
                ->join('installment_sales', 'installment_sales.id', '=', 'installment_payments.installment_sale_id')
                ->tap(fn ($q) => $this->branchScope($q))
                ->whereBetween('installment_payments.created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->selectRaw('COALESCE(SUM(installment_payments.amount + installment_payments.late_fee), 0) as v')
                ->value('v'),
        ];

        return Inertia::render('Admin/Installments/Index', [
            'mode' => $mode,
            'sales' => $mode === 'agreements' ? $this->salesQuery($request)->paginate(15)->withQueryString() : null,
            'schedules' => $mode === 'schedules' ? $this->schedulesQuery($request)->paginate(25)->withQueryString() : null,
            'stats' => $stats,
            'filters' => [
                'tab' => $request->get('tab', 'active'),
                'search' => $request->get('search'),
                'date_from' => $request->date('date_from')?->format('Y-m-d'),
                'date_to' => $request->date('date_to')?->format('Y-m-d'),
                'schedule_status' => $request->get('schedule_status'),
            ],
        ]);
    }

    protected function export(Request $request, string $mode)
    {
        $format = $request->get('export');

        if ($mode === 'schedules') {
            $rows = $this->schedulesQuery($request)->get()->map(fn ($s) => [
                $s->due_date?->format('d-m-Y'),
                $s->sale?->agreement_no,
                $s->sale?->customer?->name,
                $s->sale?->customer?->phone,
                $s->installment_no,
                (float) $s->amount,
                (float) $s->late_fee,
                (float) $s->paid_amount + (float) $s->late_fee_paid,
                $s->remaining,
                $s->is_overdue ? 'overdue' : $s->status,
            ])->all();

            return ReportExporter::download($format, 'Installment Collection Sheet', [
                'Due Date', 'Agreement', 'Customer', 'Phone', '#', 'Amount', 'Late Fee', 'Paid', 'Remaining', 'Status',
            ], $rows, [
                'Installments' => count($rows),
                'Total Due' => number_format(array_sum(array_column($rows, 8)), 2),
            ]);
        }

        $rows = $this->salesQuery($request)->get()->map(fn ($s) => [
            $s->agreement_no,
            $s->created_at?->format('d-m-Y'),
            $s->order?->invoice_no,
            $s->customer?->name,
            $s->customer?->phone,
            (float) $s->sale_amount,
            (float) $s->down_payment,
            (float) $s->total_payable,
            (float) $s->paid_amount,
            $s->balance,
            $s->number_of_installments.' x '.number_format($s->installment_amount, 2),
            $s->nextSchedule?->due_date?->format('d-m-Y') ?? '-',
            $s->status,
        ])->all();

        return ReportExporter::download($format, 'Installment Agreements', [
            'Agreement', 'Date', 'Invoice', 'Customer', 'Phone', 'Sale', 'Down Pay', 'Payable', 'Paid', 'Balance', 'Plan', 'Next Due', 'Status',
        ], $rows, [
            'Agreements' => count($rows),
            'Outstanding' => number_format(array_sum(array_column($rows, 9)), 2),
        ]);
    }

    public function show(InstallmentSale $installment)
    {
        $this->service->applyLateFees($installment);

        $installment->load([
            'customer',
            'plan',
            'creator:id,name',
            'schedules',
            'order.items',
            'order.user:id,name',
            'order.payments.paymentMethod:id,name',
            'payments' => fn ($q) => $q->latest('id'),
            'payments.posPayment.paymentMethod:id,name',
            'payments.receiver:id,name',
            'payments.schedule:id,installment_no',
        ]);

        return Inertia::render('Admin/Installments/Show', [
            'sale' => $installment,
            'paymentMethods' => PaymentMethod::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /** JSON: calculate a schedule without saving (used by POS and convert dialog). */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'sale_amount' => ['required', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'installment' => ['required', 'array'],
            ...InstallmentService::rules(),
        ]);

        $config = $this->service->withPlanDefaults($data['installment']);
        $calc = $this->service->calculate((float) $data['sale_amount'], (float) ($data['down_payment'] ?? 0), $config);
        $min = (float) ($config['_min_down_payment_percent'] ?? 0);
        $calc['min_down_payment'] = round((float) $data['sale_amount'] * $min / 100, 2);

        return response()->json($calc);
    }

    /** Convert an existing completed order with due into an installment agreement. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:pos_orders,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'installment' => ['required', 'array'],
            ...InstallmentService::rules(),
        ]);

        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($data['order_id']);
        if ($order->status !== 'completed') {
            throw ValidationException::withMessages(['order_id' => [__('Only completed orders can be converted to installments.')]]);
        }

        $sale = DB::transaction(function () use ($order, $data) {
            if (! empty($data['customer_id'])) {
                $order->update(['customer_id' => $data['customer_id']]);
            }

            return $this->service->createForOrder($order, $data['installment'], Auth::id());
        });

        return redirect()->route('installments.show', $sale->id)->with('success', __('Installment agreement created.'));
    }

    public function collect(Request $request, InstallmentSale $installment)
    {
        $data = $request->validate([
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.transaction_ref' => ['nullable', 'string', 'max:100'],
            'payments.*.notes' => ['nullable', 'string', 'max:500'],
            'payments.*.meta' => ['nullable', 'array'],
        ]);

        $result = DB::transaction(fn () => $this->service->collect($installment, $data['payments'], Auth::id()));

        return back()->with('success', __('Collected :amount.', ['amount' => number_format($result['collected'], 2)]));
    }

    public function waiveLateFee(InstallmentSchedule $schedule)
    {
        $amount = DB::transaction(fn () => $this->service->waiveLateFee($schedule));

        return back()->with('success', __('Late fee waived: :amount', ['amount' => number_format($amount, 2)]));
    }

    public function updateStatus(Request $request, InstallmentSale $installment)
    {
        $data = $request->validate(['status' => ['required', 'in:active,defaulted,cancelled']]);

        DB::transaction(function () use ($installment, $data) {
            if ($data['status'] === 'cancelled') {
                $this->service->cancel($installment);
            } else {
                $installment->update(['status' => $data['status']]);
                if ($data['status'] === 'active') {
                    $installment->schedules()->where('status', 'cancelled')->update(['status' => 'pending']);
                }
            }
        });

        return back()->with('success', __('Agreement status updated.'));
    }

    public function update(Request $request, InstallmentSale $installment)
    {
        $data = $request->validate([
            'customer_nid' => ['nullable', 'string', 'max:50'],
            'guarantor_name' => ['nullable', 'string', 'max:191'],
            'guarantor_phone' => ['nullable', 'string', 'max:30'],
            'guarantor_nid' => ['nullable', 'string', 'max:50'],
            'guarantor_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $installment->update($data);

        return back()->with('success', __('Agreement updated.'));
    }
}

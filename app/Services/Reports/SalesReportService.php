<?php

namespace App\Services\Reports;

use App\Http\Controllers\Admin\PosOrderController;
use App\Models\PosOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    public const PRESETS = [
        'today', 'yesterday', 'last_7_days', 'this_week', 'last_week',
        'last_30_days', 'this_month', 'last_month', 'this_quarter', 'this_year', 'last_year', 'custom',
    ];

    /** @return array{0: Carbon, 1: Carbon, 2: string} */
    public function range(Request $request): array
    {
        $preset = $request->get('preset', 'this_month');
        $now = now();

        [$from, $to] = match ($preset) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'last_7_days' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'last_30_days' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_quarter' => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'last_year' => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            'custom' => [
                ($request->date('date_from') ?? $now->copy()->startOfMonth())->copy()->startOfDay(),
                ($request->date('date_to') ?? $now->copy())->copy()->endOfDay(),
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to, in_array($preset, self::PRESETS, true) ? $preset : 'this_month'];
    }

    public function groupBy(Request $request, Carbon $from, Carbon $to): string
    {
        $g = $request->get('group_by', 'auto');
        if (in_array($g, ['day', 'week', 'month'], true)) {
            return $g;
        }
        $days = $from->diffInDays($to);

        return $days <= 45 ? 'day' : ($days <= 200 ? 'week' : 'month');
    }

    public static function isAdmin(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasRole('admin') || $user->hasRole('Super Admin') || $user->hasRole('manager'));
    }

    /** Admins may pick any branch or "all"; everyone else is locked to the current branch. */
    public function branchId(Request $request): ?int
    {
        $param = $request->get('branch_id');
        if (self::isAdmin() && $param === 'all') {
            return null;
        }
        if (self::isAdmin() && is_numeric($param)) {
            return (int) $param;
        }
        $current = session('current_branch_id');

        return $current ? (int) $current : null;
    }

    /** Completed orders matching all filters within [from, to]. */
    public function ordersQuery(Request $request, Carbon $from, Carbon $to)
    {
        $branchId = $this->branchId($request);

        return PosOrder::query()
            ->withoutGlobalScope('branch')
            ->where('pos_orders.status', 'completed')
            ->whereBetween('pos_orders.created_at', [$from, $to])
            ->when($branchId, fn ($q) => $q->where('pos_orders.branch_id', $branchId))
            ->when($request->get('user_id'), fn ($q, $v) => $q->where('pos_orders.user_id', $v))
            ->when($request->get('sale_type'), fn ($q, $v) => $q->where('pos_orders.sale_type', $v))
            ->when($request->get('payment_status'), fn ($q, $v) => $q->where('pos_orders.payment_status', $v))
            ->when($request->get('payment_method_id'), fn ($q, $v) => $q->whereHas('payments', fn ($p) => $p->where('payment_method_id', $v)))
            ->when($request->get('category_id'), fn ($q, $v) => $q->whereHas('items.product', fn ($p) => $p->where('category_id', $v)))
            ->when($request->get('brand_id'), fn ($q, $v) => $q->whereHas('items.product', fn ($p) => $p->where('brand_id', $v)))
            ->when($request->get('product_id'), fn ($q, $v) => $q->whereHas('items', fn ($i) => $i->where('product_id', $v)))
            ->when($request->get('customer_id'), fn ($q, $v) => $q->where('pos_orders.customer_id', $v))
            ->when(trim((string) $request->get('search')), fn ($q, $s) => PosOrderController::applyOrderSearch($q, $s));
    }

    public function summary($query): array
    {
        $row = (clone $query)->toBase()->selectRaw('
            COUNT(*) as orders,
            COUNT(DISTINCT pos_orders.customer_id) as customers,
            COALESCE(SUM(subtotal), 0) as gross,
            COALESCE(SUM(discount_amount), 0) as discount,
            COALESCE(SUM(tax_amount), 0) as tax,
            COALESCE(SUM(emi_charge_amount + installment_charge_amount + late_fee_amount), 0) as charges,
            COALESCE(SUM(total_amount), 0) as net,
            COALESCE(SUM(paid_amount - change_amount), 0) as collected,
            COALESCE(SUM(CASE WHEN total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as due
        ')->first();

        $items = (float) DB::table('pos_order_items')
            ->whereIn('order_id', (clone $query)->select('pos_orders.id'))
            ->sum('quantity');

        $orders = (int) $row->orders;

        return [
            'orders' => $orders,
            'customers' => (int) $row->customers,
            'gross' => round((float) $row->gross, 2),
            'discount' => round((float) $row->discount, 2),
            'tax' => round((float) $row->tax, 2),
            'charges' => round((float) $row->charges, 2),
            'net' => round((float) $row->net, 2),
            'collected' => round((float) $row->collected, 2),
            'due' => round((float) $row->due, 2),
            'items' => $items,
            'avg_order' => $orders ? round((float) $row->net / $orders, 2) : 0,
        ];
    }

    public function trend($query, Carbon $from, Carbon $to, string $groupBy): array
    {
        $daily = (clone $query)->toBase()
            ->selectRaw('DATE(pos_orders.created_at) as day, COUNT(*) as orders, SUM(total_amount) as net,
                SUM(paid_amount - change_amount) as collected, SUM(discount_amount) as discount')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $buckets = [];
        for ($d = $from->copy()->startOfDay(); $d->lte($to); $d->addDay()) {
            [$key, $label] = match ($groupBy) {
                'week' => [$d->copy()->startOfWeek()->toDateString(), $d->copy()->startOfWeek()->format('d M').' - '.$d->copy()->endOfWeek()->format('d M')],
                'month' => [$d->format('Y-m'), $d->format('M Y')],
                default => [$d->toDateString(), $d->format('d M')],
            };
            $buckets[$key] ??= ['key' => $key, 'label' => $label, 'orders' => 0, 'net' => 0, 'collected' => 0, 'discount' => 0, 'due' => 0];
            if ($r = $daily->get($d->toDateString())) {
                $buckets[$key]['orders'] += (int) $r->orders;
                $buckets[$key]['net'] += (float) $r->net;
                $buckets[$key]['collected'] += (float) $r->collected;
                $buckets[$key]['discount'] += (float) $r->discount;
            }
        }

        return array_values(array_map(function ($b) {
            $b['net'] = round($b['net'], 2);
            $b['collected'] = round($b['collected'], 2);
            $b['discount'] = round($b['discount'], 2);
            $b['due'] = round(max(0, $b['net'] - $b['collected']), 2);
            $b['avg_order'] = $b['orders'] ? round($b['net'] / $b['orders'], 2) : 0;

            return $b;
        }, $buckets));
    }

    protected function hourExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%H', pos_orders.created_at) AS INTEGER)",
            'pgsql' => 'EXTRACT(HOUR FROM pos_orders.created_at)',
            default => 'HOUR(pos_orders.created_at)',
        };
    }

    public function byHour($query): array
    {
        $rows = (clone $query)->toBase()
            ->selectRaw($this->hourExpression().' as hour, COUNT(*) as orders, SUM(total_amount) as net')
            ->groupBy('hour')->get()->keyBy('hour');

        return collect(range(0, 23))->map(fn ($h) => [
            'hour' => $h,
            'label' => Carbon::createFromTime($h)->format('h A'),
            'orders' => (int) ($rows[$h]->orders ?? 0),
            'net' => round((float) ($rows[$h]->net ?? 0), 2),
        ])->all();
    }

    public function paymentMethods($query): array
    {
        return DB::table('pos_payments')
            ->join('payment_methods', 'payment_methods.id', '=', 'pos_payments.payment_method_id')
            ->whereIn('pos_payments.order_id', (clone $query)->select('pos_orders.id'))
            ->groupBy('payment_methods.name')
            ->selectRaw('payment_methods.name as label, COUNT(*) as count, SUM(pos_payments.amount) as amount')
            ->orderByDesc('amount')
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'count' => (int) $r->count, 'amount' => round((float) $r->amount, 2)])
            ->all();
    }

    /** Money received during the period, incl. dues & installments of older sales. */
    public function collectionsInPeriod(Request $request, Carbon $from, Carbon $to): array
    {
        $branchId = $this->branchId($request);

        return DB::table('pos_payments')
            ->join('payment_methods', 'payment_methods.id', '=', 'pos_payments.payment_method_id')
            ->join('pos_orders', 'pos_orders.id', '=', 'pos_payments.order_id')
            ->whereNull('pos_orders.deleted_at')
            ->where('pos_orders.status', 'completed')
            ->whereBetween('pos_payments.paid_at', [$from, $to])
            ->when($branchId, fn ($q) => $q->where('pos_orders.branch_id', $branchId))
            ->groupBy('payment_methods.name')
            ->selectRaw('payment_methods.name as label, COUNT(*) as count, SUM(pos_payments.amount) as amount')
            ->orderByDesc('amount')
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'count' => (int) $r->count, 'amount' => round((float) $r->amount, 2)])
            ->all();
    }

    protected function itemsBase($query)
    {
        return DB::table('pos_order_items')
            ->whereIn('pos_order_items.order_id', (clone $query)->select('pos_orders.id'));
    }

    public function topProducts($query, int $limit = 10): array
    {
        return $this->itemsBase($query)
            ->groupBy('pos_order_items.product_id', 'pos_order_items.name')
            ->selectRaw('pos_order_items.product_id, pos_order_items.name as label, SUM(quantity) as qty, SUM(line_total) as amount, COUNT(DISTINCT order_id) as orders')
            ->orderByDesc('amount')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => ['product_id' => $r->product_id, 'label' => $r->label, 'qty' => (float) $r->qty, 'amount' => round((float) $r->amount, 2), 'orders' => (int) $r->orders])
            ->all();
    }

    public function byCategory($query): array
    {
        return $this->itemsBase($query)
            ->leftJoin('products', 'products.id', '=', 'pos_order_items.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.name')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as label, SUM(quantity) as qty, SUM(line_total) as amount")
            ->orderByDesc('amount')
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'qty' => (float) $r->qty, 'amount' => round((float) $r->amount, 2)])
            ->all();
    }

    public function byBrand($query): array
    {
        return $this->itemsBase($query)
            ->leftJoin('products', 'products.id', '=', 'pos_order_items.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->groupBy('brands.name')
            ->selectRaw("COALESCE(brands.name, 'No brand') as label, SUM(quantity) as qty, SUM(line_total) as amount")
            ->orderByDesc('amount')
            ->limit(15)
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'qty' => (float) $r->qty, 'amount' => round((float) $r->amount, 2)])
            ->all();
    }

    public function byCashier($query): array
    {
        return (clone $query)->toBase()
            ->leftJoin('users', 'users.id', '=', 'pos_orders.user_id')
            ->groupBy('users.name')
            ->selectRaw("COALESCE(users.name, 'Unknown') as label, COUNT(*) as orders, SUM(total_amount) as amount")
            ->orderByDesc('amount')
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'orders' => (int) $r->orders, 'amount' => round((float) $r->amount, 2)])
            ->all();
    }

    public function bySaleType($query): array
    {
        return (clone $query)->toBase()
            ->groupBy('sale_type')
            ->selectRaw('sale_type as label, COUNT(*) as orders, SUM(total_amount) as amount, SUM(total_amount - paid_amount) as due')
            ->orderByDesc('amount')
            ->get()
            ->map(fn ($r) => ['label' => $r->label ?: 'regular', 'orders' => (int) $r->orders, 'amount' => round((float) $r->amount, 2), 'due' => round(max(0, (float) $r->due), 2)])
            ->all();
    }

    public function topCustomers($query, int $limit = 10): array
    {
        return (clone $query)->toBase()
            ->join('customers', 'customers.id', '=', 'pos_orders.customer_id')
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->selectRaw('customers.id, customers.name as label, customers.phone, COUNT(*) as orders, SUM(total_amount) as amount,
                SUM(CASE WHEN total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END) as due')
            ->orderByDesc('amount')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => ['id' => $r->id, 'label' => $r->label, 'phone' => $r->phone, 'orders' => (int) $r->orders, 'amount' => round((float) $r->amount, 2), 'due' => round((float) $r->due, 2)])
            ->all();
    }

    public static function growth(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current > 0 ? null : 0.0;
        }

        return round(($current - $previous) / abs($previous) * 100, 1);
    }
}

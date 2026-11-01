<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Services\Reports\SalesReportService;
use App\Support\ReportExporter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesReportController extends Controller
{
    public function __construct(protected SalesReportService $reports) {}

    public function index(Request $request)
    {
        [$from, $to, $preset] = $this->reports->range($request);
        $groupBy = $this->reports->groupBy($request, $from, $to);
        $query = $this->reports->ordersQuery($request, $from, $to);

        if ($request->filled('export')) {
            abort_unless($request->user()->can('Report Export'), 403);

            return $this->export($request, $query, $from, $to, $groupBy);
        }

        // Previous period of the same length, for growth figures
        $days = $from->diffInDays($to) + 1;
        $prevFrom = $from->copy()->subDays((int) round($days));
        $prevTo = $from->copy()->subSecond();
        $previous = $this->reports->summary($this->reports->ordersQuery($request, $prevFrom, $prevTo));
        $summary = $this->reports->summary($query);

        $orders = (clone $query)
            ->with(['customer:id,name,phone', 'user:id,name', 'branch:id,name'])
            ->latest('pos_orders.id')
            ->paginate($request->integer('per_page', 20) ?: 20)
            ->withQueryString();

        return Inertia::render('Admin/Reports/Sales', [
            'filters' => [
                'preset' => $preset,
                'date_from' => $from->toDateString(),
                'date_to' => $to->toDateString(),
                'group_by' => $request->get('group_by', 'auto'),
                'resolved_group_by' => $groupBy,
                'branch_id' => $request->get('branch_id'),
                'user_id' => $request->get('user_id'),
                'sale_type' => $request->get('sale_type'),
                'payment_status' => $request->get('payment_status'),
                'payment_method_id' => $request->get('payment_method_id'),
                'category_id' => $request->get('category_id'),
                'brand_id' => $request->get('brand_id'),
                'product_id' => $request->get('product_id'),
                'search' => $request->get('search'),
                'per_page' => $request->integer('per_page', 20) ?: 20,
            ],
            'summary' => $summary,
            'previous' => $previous,
            'growth' => [
                'net' => SalesReportService::growth($summary['net'], $previous['net']),
                'orders' => SalesReportService::growth($summary['orders'], $previous['orders']),
                'collected' => SalesReportService::growth($summary['collected'], $previous['collected']),
                'avg_order' => SalesReportService::growth($summary['avg_order'], $previous['avg_order']),
            ],
            'trend' => $this->reports->trend($query, $from, $to, $groupBy),
            'hourly' => $this->reports->byHour($query),
            'paymentMethods' => $this->reports->paymentMethods($query),
            'collections' => $this->reports->collectionsInPeriod($request, $from, $to),
            'topProducts' => $this->reports->topProducts($query),
            'categories' => $this->reports->byCategory($query),
            'brands' => $this->reports->byBrand($query),
            'cashiers' => $this->reports->byCashier($query),
            'saleTypes' => $this->reports->bySaleType($query),
            'topCustomers' => $this->reports->topCustomers($query),
            'orders' => $orders,
            'options' => [
                'isAdmin' => SalesReportService::isAdmin(),
                'branches' => Branch::select('id', 'name')->orderBy('name')->get(),
                'users' => User::select('id', 'name')->orderBy('name')->get(),
                'paymentMethods' => PaymentMethod::select('id', 'name')->orderBy('name')->get(),
                'categories' => Category::select('id', 'name')->orderBy('name')->get(),
                'brands' => Brand::select('id', 'name')->orderBy('name')->get(),
                'products' => Product::select('id', 'name')->orderBy('name')->get(),
            ],
        ]);
    }

    protected function export(Request $request, $query, $from, $to, string $groupBy)
    {
        $format = $request->get('export');
        $section = $request->get('section', 'orders');
        $s = $this->reports->summary($query);
        $money = fn ($v) => number_format((float) $v, 2);
        $summary = [
            'Orders' => $s['orders'],
            'Net Sales' => $money($s['net']),
            'Collected' => $money($s['collected']),
            'Due' => $money($s['due']),
            'Discount' => $money($s['discount']),
            'Tax' => $money($s['tax']),
            'Charges' => $money($s['charges']),
            'Avg. Order' => $money($s['avg_order']),
        ];
        $meta = ['Period' => $from->format('d M Y').' - '.$to->format('d M Y')];

        return match ($section) {
            'summary' => ReportExporter::download($format, 'Sales Summary ('.ucfirst($groupBy).')',
                ['Period', 'Orders', 'Net Sales', 'Collected', 'Due', 'Discount', 'Avg. Order'],
                array_map(fn ($r) => [$r['label'], $r['orders'], $r['net'], $r['collected'], $r['due'], $r['discount'], $r['avg_order']],
                    $this->reports->trend($query, $from, $to, $groupBy)),
                $summary, $meta),

            'products' => ReportExporter::download($format, 'Product Sales',
                ['Product', 'Qty', 'Orders', 'Amount'],
                array_map(fn ($r) => [$r['label'], $r['qty'], $r['orders'], $r['amount']], $this->reports->topProducts($query, 1000)),
                $summary, $meta),

            'categories' => ReportExporter::download($format, 'Category Sales',
                ['Category', 'Qty', 'Amount'],
                array_map(fn ($r) => [$r['label'], $r['qty'], $r['amount']], $this->reports->byCategory($query)),
                $summary, $meta),

            'customers' => ReportExporter::download($format, 'Customer Sales',
                ['Customer', 'Phone', 'Orders', 'Amount', 'Due'],
                array_map(fn ($r) => [$r['label'], $r['phone'], $r['orders'], $r['amount'], $r['due']], $this->reports->topCustomers($query, 5000)),
                $summary, $meta),

            'payments' => ReportExporter::download($format, 'Payment Method Summary',
                ['Method', 'Payments', 'Amount'],
                array_map(fn ($r) => [$r['label'], $r['count'], $r['amount']], $this->reports->paymentMethods($query)),
                $summary, $meta),

            'cashiers' => ReportExporter::download($format, 'Cashier Sales',
                ['Cashier', 'Orders', 'Amount'],
                array_map(fn ($r) => [$r['label'], $r['orders'], $r['amount']], $this->reports->byCashier($query)),
                $summary, $meta),

            default => ReportExporter::download($format, 'Sales Orders',
                ['Invoice', 'Date', 'Customer', 'Phone', 'Cashier', 'Branch', 'Type', 'Subtotal', 'Discount', 'Tax', 'Charges', 'Total', 'Paid', 'Due', 'Payment'],
                (clone $query)->with(['customer:id,name,phone', 'user:id,name', 'branch:id,name'])->latest('pos_orders.id')->limit(20000)->get()
                    ->map(fn ($o) => [
                        $o->invoice_no,
                        $o->created_at?->format('d-m-Y h:i A'),
                        $o->customer?->name ?? 'Walk-in',
                        $o->customer?->phone ?? '',
                        $o->user?->name ?? '',
                        $o->branch?->name ?? '',
                        $o->sale_type,
                        (float) $o->subtotal,
                        (float) $o->discount_amount,
                        (float) $o->tax_amount,
                        (float) $o->emi_charge_amount + (float) $o->installment_charge_amount + (float) $o->late_fee_amount,
                        (float) $o->total_amount,
                        (float) $o->paid_amount,
                        max(0, (float) $o->total_amount - (float) $o->paid_amount),
                        $o->payment_status,
                    ])->all(),
                $summary, $meta),
        };
    }
}

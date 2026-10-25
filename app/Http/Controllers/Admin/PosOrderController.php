<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\EmiBank;
use App\Models\InstallmentPlan;
use App\Models\PaymentMethod;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosPayment;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\WarrantyGuarantee;
use App\Services\EmiService;
use App\Services\InstallmentService;
use App\Services\Sms\SmsManager;
use App\Services\StockService;
use App\Support\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PosOrderController extends Controller
{
    public function __construct(
        protected EmiService $emi,
        protected InstallmentService $installments,
        protected SmsManager $sms,
    ) {}

    public function index()
    {
        return Inertia::render('Admin/POS/Index', $this->posProps());
    }

    /** Props shared by the POS screen (new sale + edit draft). */
    protected function posProps(): array
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'barcode', 'base_price', 'base_discount_price', 'thumbnail', 'type', 'category_id')
            ->with([
                'variations:id,product_id,sku,price,discount_price',
                'stocks:id,product_id,variation_id,warehouse_id,branch_id,quantity',
                'stocks.warehouse:id,name',
            ])
            ->orderBy('name')
            ->get();

        return [
            'products' => $products,
            'customers' => Customer::select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'currentSession' => PosSession::where('user_id', Auth::id())->where('status', 'open')->latest('id')->first(),
            'branches' => Branch::select('id', 'name')->orderBy('id')->get(),
            'warehouses' => Warehouse::select('id', 'name')->orderBy('id')->get(),
            'lastWarrantyInfo' => PosOrder::where('user_id', Auth::id())
                ->where('warranty_info', '!=', '')
                ->whereNotNull('warranty_info')
                ->latest('id')
                ->first()?->warranty_info ?? '',
            'warrantyTemplates' => WarrantyGuarantee::where('is_active', true)->select('id', 'name', 'description', 'category_id')->get(),
            ...self::creditSaleProps(),
        ];
    }

    /** Installment plans + EMI banks, used by POS and the orders page. */
    public static function creditSaleProps(): array
    {
        return [
            'installmentPlans' => InstallmentPlan::where('is_active', true)->orderBy('number_of_installments')->get(),
            'emiBanks' => EmiBank::where('is_active', true)
                ->with(['tenures' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get(),
            'emiPaymentMethodId' => EmiService::paymentMethodId(),
        ];
    }

    public function customerSearch(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $customers = Search::customers(Customer::query()->select(['id', 'name', 'phone', 'email']), $q)
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json(['data' => $customers]);
    }

    // ------------------------------------------------------------------
    // Validation
    // ------------------------------------------------------------------

    protected function paymentRules(string $prefix = 'payments.*.'): array
    {
        return [
            $prefix.'payment_method_id' => ['required_with:'.$prefix.'amount', 'exists:payment_methods,id'],
            $prefix.'amount' => ['required_with:'.$prefix.'payment_method_id', 'numeric', 'min:0'],
            $prefix.'transaction_ref' => ['nullable', 'string', 'max:100'],
            $prefix.'notes' => ['nullable', 'string', 'max:500'],

            $prefix.'meta' => ['nullable', 'array'],
            $prefix.'meta.customer_bank_name' => ['nullable', 'string', 'max:100'],
            $prefix.'meta.customer_account_no' => ['nullable', 'string', 'max:50'],
            $prefix.'meta.received_to_bank_account_id' => ['nullable', 'integer'],
            $prefix.'meta.txn_ref' => ['nullable', 'string', 'max:100'],

            // Bank card EMI
            $prefix.'emi' => ['nullable', 'array'],
            $prefix.'emi.emi_bank_tenure_id' => ['nullable', 'exists:emi_bank_tenures,id'],
            $prefix.'emi.card_holder_name' => ['nullable', 'string', 'max:191'],
            $prefix.'emi.card_last4' => ['nullable', 'digits:4'],
            $prefix.'emi.approval_code' => ['nullable', 'string', 'max:50'],
            $prefix.'emi.terminal_ref' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** Validates the optional installment block; returns null when not requested. */
    protected function validateInstallment(Request $request): ?array
    {
        if (! $request->boolean('installment.enabled')) {
            return null;
        }

        return $request->validate(InstallmentService::rules())['installment'];
    }

    protected function filterPayments(array $payments): array
    {
        return collect($payments)
            ->filter(fn ($p) => ! empty($p['payment_method_id']) && (float) ($p['amount'] ?? 0) > 0)
            ->values()
            ->all();
    }

    // ------------------------------------------------------------------
    // Create / update sale
    // ------------------------------------------------------------------

    public function store(Request $request, StockService $stockService)
    {
        return $this->saveSale($request, $stockService, null);
    }

    public function update(Request $request, $orderId, StockService $stockService)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status !== 'draft') {
            return back()->with('error', __('Cannot update non-draft order.'));
        }

        return $this->saveSale($request, $stockService, $order);
    }

    protected function saveSale(Request $request, StockService $stockService, ?PosOrder $order)
    {
        $data = $request->validate([
            'action' => ['required', 'in:draft,complete,complete_print'],

            'pos_session_id' => ['required', 'exists:pos_sessions,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],

            'discount_id' => ['nullable', 'exists:discounts,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variation_id' => ['nullable', 'exists:product_variations,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount' => ['nullable', 'numeric', 'min:0'],

            // payments only required for complete
            'payments' => ['nullable', 'array'],
            ...$this->paymentRules(),

            // manual order discount fallback (optional)
            'order_discount_type' => ['nullable', 'in:none,percent,fixed'],
            'order_discount_value' => ['nullable', 'numeric', 'min:0'],
            'warranty_info' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],

            'installment' => ['nullable', 'array'],
            'installment.enabled' => ['nullable', 'boolean'],
        ]);

        $isDraft = $data['action'] === 'draft';
        $installment = $isDraft ? null : $this->validateInstallment($request);

        if ($installment && empty($data['customer_id'])) {
            throw ValidationException::withMessages(['customer_id' => [__('Select a customer for installment sales.')]]);
        }

        $userId = Auth::id();

        return DB::transaction(function () use ($data, $userId, $stockService, $order, $isDraft, $installment) {
            $warehouseId = (int) $data['warehouse_id'];

            [$preparedItems, $subtotal, $lineDiscountTotal, $taxTotal] = $this->prepareItems($data['items']);

            $discount = ! empty($data['discount_id']) ? Discount::find($data['discount_id']) : null;
            $orderDiscountAmount = $discount
                ? $this->discountAmount($subtotal, $discount->type, (float) $discount->value)
                : $this->discountAmount($subtotal, $data['order_discount_type'] ?? 'none', (float) ($data['order_discount_value'] ?? 0));

            $discountTotal = $lineDiscountTotal + $orderDiscountAmount;

            // payments: only on complete
            $payments = $isDraft ? [] : $this->filterPayments($data['payments'] ?? []);
            [$payments, $emiCharge] = $this->emi->preparePayments($payments);

            if (! $isDraft && empty($payments) && ! $installment) {
                throw ValidationException::withMessages([
                    'payments' => [__('At least one payment is required to complete the order.')],
                ]);
            }

            $total = $subtotal - $discountTotal + $taxTotal + $emiCharge;

            $attributes = [
                'pos_session_id' => $data['pos_session_id'],
                'branch_id' => $data['branch_id'] ?? session('current_branch_id') ?? Auth::user()->branch_id ?? $order?->branch_id ?? 1,
                'warehouse_id' => $warehouseId,
                'customer_id' => $data['customer_id'] ?? null,
                'discount_id' => $data['discount_id'] ?? null,

                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'tax_amount' => $taxTotal,
                'emi_charge_amount' => $emiCharge,
                'installment_charge_amount' => 0,
                'late_fee_amount' => 0,
                'total_amount' => $total,

                'paid_amount' => 0,
                'due_amount' => $isDraft ? 0 : $total,
                'change_amount' => 0,
                'payment_status' => 'unpaid',
                'status' => $isDraft ? 'draft' : 'completed',
                'sale_type' => collect($payments)->contains(fn ($p) => ! empty($p['emi'])) ? 'emi' : 'regular',

                'warranty_info' => $data['warranty_info'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];

            if ($order) {
                $order->update($attributes);
                $order->items()->delete();
                $order->payments()->delete();
            } else {
                $order = PosOrder::create($attributes + ['user_id' => $userId]);
            }

            // invoice number only for completed (order id keeps it unique)
            if (! $isDraft && ! $order->invoice_no) {
                $order->update(['invoice_no' => PosOrder::generateInvoiceNo($order->id)]);
            }

            foreach ($preparedItems as $it) {
                $product = $it['product'];
                $variation = $it['variation'];

                $lineSub = $it['unit_price'] * $it['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id,
                    'sku' => $variation?->sku ?? $product->sku,
                    'name' => $product->name,
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'discount_amount' => $it['discount_amount'],
                    'tax_amount' => $it['tax_amount'],
                    'line_total' => $lineSub - $it['discount_amount'] + $it['tax_amount'],
                ]);

                // stock out only when completed (warehouse-based, not branch-dependent)
                if (! $isDraft) {
                    $stockService->stockOut([
                        'type' => 'out',
                        'product_id' => $product->id,
                        'variation_id' => $variation?->id,
                        'quantity' => $it['quantity'],
                        'from_warehouse_id' => $warehouseId,
                        'reference' => $order->invoice_no,
                        'note' => 'POS sale',
                        'created_by' => $userId,
                    ]);
                }
            }

            if ($isDraft) {
                return redirect()->back()->with('success', __('Order saved as draft successfully'));
            }

            $this->savePayments($order, $payments);
            $this->finalizeCompletedSale($order, $installment, $userId);

            return redirect()->route('pos.orders.invoice', array_filter([
                'order' => $order->id,
                'autoprint' => $data['action'] === 'complete_print' ? 1 : null,
            ]));
        });
    }

    /**
     * @return array{0: array, 1: float, 2: float, 3: float} [items, subtotal, line discount, tax]
     */
    protected function prepareItems(array $items): array
    {
        $productIds = collect($items)->pluck('product_id')->unique()->values()->all();
        $products = Product::whereIn('id', $productIds)
            ->with(['variations:id,product_id,sku,price,discount_price'])
            ->get()
            ->keyBy('id');

        $subtotal = 0;
        $lineDiscountTotal = 0;
        $taxTotal = 0;
        $prepared = [];

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (! $product) {
                throw ValidationException::withMessages(['items' => [__('Invalid product.')]]);
            }

            // variable => variation required
            if ($product->type === 'variable' && empty($item['variation_id'])) {
                throw ValidationException::withMessages([
                    'items' => [__('Variation is required for: :name', ['name' => $product->name])],
                ]);
            }

            $variation = null;
            if (! empty($item['variation_id'])) {
                $variation = $product->variations->firstWhere('id', (int) $item['variation_id']);
                if (! $variation) {
                    throw ValidationException::withMessages([
                        'items' => [__('Invalid variation for: :name', ['name' => $product->name])],
                    ]);
                }
            }

            $unitPrice =
                $item['unit_price'] ??
                ($variation
                    ? ($variation->discount_price ?? $variation->price)
                    : ($product->base_discount_price ?? $product->base_price));

            $qty = (int) $item['quantity'];
            $lineDiscount = (float) ($item['discount_amount'] ?? 0);
            $lineTax = (float) ($item['tax_amount'] ?? 0);

            $subtotal += (float) $unitPrice * $qty;
            $lineDiscountTotal += $lineDiscount;
            $taxTotal += $lineTax;

            $prepared[] = [
                'product' => $product,
                'variation' => $variation,
                'quantity' => $qty,
                'unit_price' => (float) $unitPrice,
                'discount_amount' => $lineDiscount,
                'tax_amount' => $lineTax,
            ];
        }

        return [$prepared, $subtotal, $lineDiscountTotal, $taxTotal];
    }

    protected function discountAmount(float $subtotal, ?string $type, float $value): float
    {
        if ($type === 'percent') {
            return ($subtotal * min(max($value, 0), 100)) / 100;
        }
        if ($type === 'fixed') {
            return min(max($value, 0), $subtotal);
        }

        return 0;
    }

    /** Persist payment rows (and their EMI records). Expects rows already passed through EmiService::preparePayments. */
    protected function savePayments(PosOrder $order, array $payments): void
    {
        foreach ($payments as $p) {
            $payment = PosPayment::create([
                'order_id' => $order->id,
                'branch_id' => $order->branch_id ?? session('current_branch_id') ?? Auth::user()->branch_id ?? 1,
                'payment_method_id' => $p['payment_method_id'],
                'amount' => $p['amount'],
                'paid_at' => now(),
                'transaction_ref' => $p['transaction_ref'] ?? ($p['emi']['approval_code'] ?? null),
                'notes' => $p['notes'] ?? null,
                'meta' => ! empty($p['emi'])
                    ? array_filter(['type' => 'emi', 'months' => $p['emi']['months'], 'card_last4' => $p['emi']['card_last4'] ?? null])
                    : ($p['meta'] ?? null),
            ]);

            if (! empty($p['emi'])) {
                $this->emi->record($order, $payment, $p['emi']);
            }
        }
    }

    protected function finalizeCompletedSale(PosOrder $order, ?array $installment, ?int $userId): void
    {
        $order->recalculatePayments();

        if ($installment) {
            $this->installments->createForOrder($order, $installment, $userId);
            $order->refresh();
        }

        $order->loadMissing('customer');
        DB::afterCommit(fn () => $this->sms->notify('sale_completed', $order->customer, [
            'invoice_no' => $order->invoice_no,
            'total' => SmsManager::money($order->total_amount),
            'paid' => SmsManager::money($order->paid_amount),
            'due' => SmsManager::money($order->due_amount),
            'items_count' => $order->items()->count(),
        ], $order));
    }

    public function invoice($orderId, Request $request)
    {
        $order = PosOrder::withoutGlobalScope('branch')
            ->findOrFail($orderId);

        $order->load([
            'items',
            'payments.paymentMethod',
            'customer',
            'user',
            'branch',
            'warehouse',
            'installmentSale.schedules',
            'emiTransactions.bank',
        ]);

        $mode = $request->get('mode', 'a4'); // 'a4' or 'thermal'

        return Inertia::render('Admin/POS/Order/Invoice', [
            'order' => $order,
            'mode' => $mode,
            'thermalWidth' => 80, // or config('pos.printer_width', 80)
            'shop' => [
                'name' => Setting::get('general_application_name'),
                'address' => Setting::get('general_address'),
                'phone' => Setting::get('general_phone'),
                'footer' => Setting::get('general_footer_text'),
            ],
        ]);
    }

    /** Apply the orders-list filters (shared with exports). */
    public static function filteredOrdersQuery(Request $request)
    {
        $search = trim($request->string('search'));
        $status = trim($request->string('status'));
        $paymentStatus = trim($request->string('payment_status'));
        $saleType = trim($request->string('sale_type'));
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');
        $trashed = $request->boolean('trashed');

        $category_id = $request->get('category_id');
        $brand_id = $request->get('brand_id');
        $product_id = $request->get('product_id');

        // Branch filtering - filter by current branch unless admin requests all
        $currentBranchId = session('current_branch_id');
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('Super Admin') || $user->hasRole('manager');
        $showAllBranches = $request->boolean('all_branches') && $isAdmin;

        return PosOrder::query()
            ->when(! $showAllBranches && $currentBranchId, fn ($q) => $q->where('branch_id', $currentBranchId))
            ->when($trashed, fn ($q) => $q->onlyTrashed())
            ->when($search, fn ($q) => self::applyOrderSearch($q, $search))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus))
            ->when($saleType, fn ($q) => $q->where('sale_type', $saleType))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($category_id, function ($q) use ($category_id) {
                $q->whereHas('items.product', fn ($p) => $p->where('category_id', $category_id));
            })
            ->when($brand_id, function ($q) use ($brand_id) {
                $q->whereHas('items.product', fn ($p) => $p->where('brand_id', $brand_id));
            })
            ->when($product_id, function ($q) use ($product_id) {
                $q->whereHas('items', fn ($i) => $i->where('product_id', $product_id));
            });
    }

    /**
     * Invoice no / customer name / customer phone (partial, digits-only) / cashier.
     * Each word must match something; "*" works as a wildcard.
     */
    public static function applyOrderSearch($query, string $search)
    {
        foreach (Search::tokens($search) as $token) {
            $like = Search::like($token);
            $query->where(function ($qq) use ($token, $like) {
                $qq->where('pos_orders.invoice_no', 'like', $like)
                    ->orWhereHas('customer', fn ($c) => Search::customers($c, $token))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like));
            });
        }

        return $query;
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('Super Admin') || $user->hasRole('manager');
        $currentBranchId = session('current_branch_id');

        $query = self::filteredOrdersQuery($request)
            ->with(['customer', 'user', 'branch', 'session', 'items.product.category', 'items.product.brand', 'installmentSale:id,order_id,agreement_no,status'])
            ->latest('id');

        // Handle Exports
        if ($request->has('export')) {
            $export = $request->get('export');
            if ($export === 'pdf') {
                return $this->exportPdf($query->get());
            } elseif ($export === 'excel') {
                return $this->exportExcel($query->get());
            } elseif ($export === 'csv') {
                return $this->exportExcel($query->get(), 'csv');
            }
        }

        $orders = $query->paginate($request->integer('per_page', 15) ?: 15)->withQueryString();

        // Insights (Top Selling Products)
        $topProducts = PosOrderItem::join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.id')
            ->whereNull('pos_orders.deleted_at')
            ->where('pos_orders.status', 'completed')
            ->select('pos_order_items.product_id', 'pos_order_items.name', DB::raw('SUM(pos_order_items.quantity) as total_qty'), DB::raw('SUM(pos_order_items.line_total) as total_amount'))
            ->groupBy('pos_order_items.product_id', 'pos_order_items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Insights (Sales by Brand)
        $brandSales = PosOrderItem::join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.id')
            ->whereNull('pos_orders.deleted_at')
            ->where('pos_orders.status', 'completed')
            ->join('products', 'pos_order_items.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->select('brands.name', DB::raw('SUM(pos_order_items.quantity) as total_qty'), DB::raw('SUM(pos_order_items.line_total) as total_amount'))
            ->groupBy('brands.name')
            ->orderByDesc('total_amount')
            ->get();

        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        return Inertia::render('Admin/POS/Order/Orders', [
            'orders' => $orders,
            'filters' => [
                'search' => trim($request->string('search')),
                'status' => trim($request->string('status')),
                'payment_status' => trim($request->string('payment_status')),
                'sale_type' => trim($request->string('sale_type')),
                'date_from' => $dateFrom?->format('Y-m-d'),
                'date_to' => $dateTo?->format('Y-m-d'),
                'category_id' => $request->get('category_id'),
                'brand_id' => $request->get('brand_id'),
                'product_id' => $request->get('product_id'),
                'trashed' => $request->boolean('trashed'),
                'all_branches' => $request->boolean('all_branches') && $isAdmin,
            ],
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'brands' => Brand::select('id', 'name')->orderBy('name')->get(),
            'available_products' => Product::select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', 1)->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name')->get(),
            'currentBranchId' => $currentBranchId,
            'isAdmin' => $isAdmin,
            'insights' => [
                'top_products' => $topProducts,
                'brand_sales' => $brandSales,
            ],
            ...self::creditSaleProps(),
        ]);
    }

    protected function exportPdf($orders)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pos_orders_pdf', [
            'orders' => $orders,
            'shop' => [
                'name' => Setting::get('general_application_name'),
                'address' => Setting::get('general_address'),
                'phone' => Setting::get('general_phone'),
                'footer' => Setting::get('general_footer_text'),
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('pos_orders_report.pdf');
    }

    protected function exportExcel($orders, string $format = 'xlsx')
    {
        return $format === 'csv'
            ? \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OrderExport($orders), 'pos_orders_report.csv', \Maatwebsite\Excel\Excel::CSV)
            : \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OrderExport($orders), 'pos_orders_report.xlsx');
    }

    public function void($orderId, StockService $stockService)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status === 'void') {
            return back()->with('success', __('Already voided'));
        }

        DB::transaction(function () use ($order, $stockService) {
            // restore stock only if it was completed
            if ($order->status === 'completed') {
                $warehouseId = $order->warehouse_id ?? $order->session?->warehouse_id;

                foreach ($order->items as $it) {
                    $stockService->stockIn([
                        'type' => 'in',
                        'product_id' => $it->product_id,
                        'variation_id' => $it->variation_id,
                        'branch_id' => $order->branch_id ?? session('current_branch_id') ?? Auth::user()->branch_id ?? 1,
                        'quantity' => $it->quantity,
                        'to_warehouse_id' => $warehouseId,
                        'reference' => $order->invoice_no,
                        'note' => 'VOID POS order',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            if ($order->installmentSale) {
                $this->installments->cancel($order->installmentSale);
            }
            $order->emiTransactions()->where('settlement_status', 'pending')->update(['settlement_status' => 'cancelled']);

            $order->update([
                'status' => 'void',
            ]);
        });

        return back()->with('success', __('Order voided'));
    }

    public function completeDraft(Request $request, $orderId, StockService $stockService)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status !== 'draft') {
            return back()->with('success', __('Order already completed/void.'));
        }

        $data = $request->validate([
            'payments' => ['nullable', 'array'],
            ...$this->paymentRules(),
            'customer_id' => ['nullable', 'exists:customers,id'],
            'installment' => ['nullable', 'array'],
            'installment.enabled' => ['nullable', 'boolean'],
        ]);
        $installment = $this->validateInstallment($request);

        if (! empty($data['customer_id'])) {
            $order->customer_id = $data['customer_id'];
        }
        if ($installment && ! $order->customer_id) {
            throw ValidationException::withMessages(['customer_id' => [__('Select a customer for installment sales.')]]);
        }

        return DB::transaction(function () use ($order, $data, $stockService, $installment) {
            [$payments, $emiCharge] = $this->emi->preparePayments($this->filterPayments($data['payments'] ?? []));

            if (empty($payments) && ! $installment) {
                throw ValidationException::withMessages(['payments' => [__('At least one payment is required to complete the order.')]]);
            }

            $invoiceNo = $order->invoice_no ?: PosOrder::generateInvoiceNo($order->id);

            $order->forceFill([
                'invoice_no' => $invoiceNo,
                'status' => 'completed',
                'emi_charge_amount' => $emiCharge,
                'total_amount' => round((float) $order->total_amount - (float) $order->emi_charge_amount + $emiCharge, 2),
                'sale_type' => collect($payments)->contains(fn ($p) => ! empty($p['emi'])) ? 'emi' : 'regular',
            ])->save();

            // stockOut now (draft had no stockOut before) - warehouse-based, not branch-dependent
            $warehouseId = $order->warehouse_id ?? $order->session?->warehouse_id;
            $order->loadMissing('items');
            foreach ($order->items as $it) {
                $stockService->stockOut([
                    'type' => 'out',
                    'product_id' => $it->product_id,
                    'variation_id' => $it->variation_id,
                    'quantity' => $it->quantity,
                    'from_warehouse_id' => $warehouseId,
                    'reference' => $invoiceNo,
                    'note' => 'POS sale (completed from draft)',
                    'created_by' => Auth::id(),
                ]);
            }

            $this->savePayments($order, $payments);
            $this->finalizeCompletedSale($order, $installment, Auth::id());

            return redirect()
                ->route('pos.orders.invoice', $order->id)
                ->with('success', __('Draft completed successfully'));
        });
    }

    public function addPayment(Request $request, $orderId)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status === 'void') {
            return back()->with('error', __('Cannot take payment for void order.'));
        }

        // Accept { payments: [...] } or a single payment payload
        if (! $request->has('payments')) {
            $request->merge(['payments' => [$request->only(['payment_method_id', 'amount', 'transaction_ref', 'notes', 'meta', 'emi'])]]);
        }

        $data = $request->validate([
            'payments' => ['required', 'array', 'min:1'],
            ...$this->paymentRules(),
            'payments.*.payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        // Installment orders: allocate against the schedule so both stay in sync
        if ($order->installmentSale && $order->installmentSale->status === 'active') {
            DB::transaction(fn () => $this->installments->collect($order->installmentSale, $data['payments'], Auth::id()));

            return back()->with('success', __('Installment payment collected.'));
        }

        DB::transaction(function () use ($order, $data) {
            [$payments, $emiCharge] = $this->emi->preparePayments($data['payments']);

            if ($emiCharge > 0) {
                $order->forceFill([
                    'emi_charge_amount' => round((float) $order->emi_charge_amount + $emiCharge, 2),
                    'total_amount' => round((float) $order->total_amount + $emiCharge, 2),
                ])->save();
            }

            $this->savePayments($order, $payments);
            $order->recalculatePayments();

            $order->loadMissing('customer');
            $paid = collect($payments)->sum('amount');
            DB::afterCommit(fn () => $this->sms->notify('payment_received', $order->customer, [
                'amount' => SmsManager::money($paid),
                'invoice_no' => $order->invoice_no,
                'due' => SmsManager::money($order->due_amount),
            ], $order));
        });

        return back()->with('success', __('Payment added successfully.'));
    }

    public function edit($orderId)
    {
        $order = PosOrder::withoutGlobalScope('branch')
            ->findOrFail($orderId);

        if ($order->status !== 'draft') {
            return to_route('pos.index')->with('error', __('Only draft orders can be edited.'));
        }

        $order->load(['items.product.variations', 'items.product.stocks.warehouse', 'customer']);

        return Inertia::render('Admin/POS/Index', [
            ...$this->posProps(),
            'lastWarrantyInfo' => $order->warranty_info ?? '',
            'order' => $order, // Pass order for editing
        ]);
    }

    public function destroy($orderId)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);
        $order->delete();

        return back()->with('success', __('Order moved to trash.'));
    }

    public function restore($orderId)
    {
        $order = PosOrder::onlyTrashed()->withoutGlobalScope('branch')->findOrFail($orderId);
        $order->restore();

        return back()->with('success', __('Order restored.'));
    }

    public function forceDelete($orderId)
    {
        $order = PosOrder::onlyTrashed()->withoutGlobalScope('branch')->findOrFail($orderId);

        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->payments()->delete();
            $order->forceDelete();
        });

        return back()->with('success', __('Order permanently deleted.'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:trash,restore,force_delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if ($action === 'trash') {
            PosOrder::withoutGlobalScope('branch')->whereIn('id', $ids)->delete();

            return back()->with('success', __(':count orders moved to trash.', ['count' => count($ids)]));
        }

        if ($action === 'restore') {
            PosOrder::onlyTrashed()->withoutGlobalScope('branch')->whereIn('id', $ids)->restore();

            return back()->with('success', __(':count orders restored.', ['count' => count($ids)]));
        }

        if ($action === 'force_delete') {
            DB::transaction(function () use ($ids) {
                PosOrder::onlyTrashed()->withoutGlobalScope('branch')->whereIn('id', $ids)->each(function ($order) {
                    $order->items()->delete();
                    $order->payments()->delete();
                    $order->forceDelete();
                });
            });

            return back()->with('success', __(':count orders permanently deleted.', ['count' => count($ids)]));
        }

        return back();
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'target_branch_id' => ['required', 'exists:branches,id'],
        ]);

        $ids = $request->ids;
        $targetBranchId = (int) $request->target_branch_id;

        $orders = PosOrder::withoutGlobalScope('branch')
            ->whereIn('id', $ids)
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', __('No orders found.'));
        }

        $completedCount = 0;
        $skippedCount = 0;

        foreach ($orders as $order) {
            if ($order->status === 'completed') {
                DB::transaction(function () use ($order, $targetBranchId) {
                    $order->update(['branch_id' => $targetBranchId]);

                    $order->payments()->update(['branch_id' => $targetBranchId]);
                    $order->installmentSale()->update(['branch_id' => $targetBranchId]);
                    $order->emiTransactions()->update(['branch_id' => $targetBranchId]);
                });
                $completedCount++;
            } else {
                $skippedCount++;
            }
        }

        $message = __(':count order(s) transferred to the selected branch.', ['count' => $completedCount]);
        if ($skippedCount > 0) {
            $message .= ' '.__(':count order(s) skipped (not completed).', ['count' => $skippedCount]);
        }

        return back()->with('success', $message);
    }

    public function updateOrderDate(Request $request, $orderId)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        $user = $request->user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('Super Admin') || $user->hasRole('manager');

        if (! $isAdmin) {
            return back()->with('error', __('Only admins can edit order date.'));
        }

        $data = $request->validate([
            'sale_date' => ['required', 'date'],
        ]);

        $oldDate = $order->created_at->format('Y-m-d H:i:s');
        $newDate = $data['sale_date'];

        $order->update([
            'created_at' => $newDate,
        ]);

        Log::channel('order-updates')->info('Order date updated', [
            'order_id' => $order->id,
            'invoice_no' => $order->invoice_no,
            'old_date' => $oldDate,
            'new_date' => $newDate,
            'updated_by' => Auth::id(),
            'user_name' => Auth::user()->name,
        ]);

        return back()->with('success', __('Order date updated successfully.'));
    }
}

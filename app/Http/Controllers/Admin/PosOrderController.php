<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosPayment;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\WarrantyGuarantee;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

use function Symfony\Component\String\s;

class PosOrderController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'barcode', 'base_price', 'base_discount_price', 'thumbnail', 'type')
            ->with([
                'variations:id,product_id,sku,price,discount_price',
                'stocks:id,product_id,variation_id,warehouse_id,branch_id,quantity',
                'stocks.warehouse:id,name'
            ])
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/POS/Index', [
            'products' => $products,
            'customers' => Customer::select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'currentSession' => PosSession::where('user_id', Auth::id())->where('status', 'open')->latest('id')->first(),

            // ✅ add these
            'branches' => Branch::select('id', 'name')->orderBy('id')->get(),
            'warehouses' => Warehouse::select('id', 'name')->orderBy('id')->get(),
            'lastWarrantyInfo' => PosOrder::where('user_id', Auth::id())
                ->where('warranty_info', '!=', '')
                ->whereNotNull('warranty_info')
                ->latest('id')
                ->first()?->warranty_info ?? '',
            'warrantyTemplates' => WarrantyGuarantee::where('is_active', true)->select('id', 'name', 'description', 'category_id')->get(),
        ]);
    }


    public function customerSearch(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $customers = Customer::query()
            ->select(['id', 'name', 'phone', 'email'])
            ->where(function ($query) use ($q) {
                $query->whereAny(['name', 'phone', 'email'], 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json(['data' => $customers]);
    }

    public function store(Request $request, StockService $stockService)
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
            'payments.*.payment_method_id' => ['required_with:payments.*.amount', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required_with:payments.*.payment_method_id', 'numeric', 'min:0'],
            'payments.*.transaction_ref' => ['nullable', 'string', 'max:100'],
            'payments.*.notes' => ['nullable', 'string', 'max:500'],

            'payments.*.meta' => ['nullable', 'array'],
            'payments.*.meta.customer_bank_name' => ['nullable', 'string', 'max:100'],
            'payments.*.meta.customer_account_no' => ['nullable', 'string', 'max:50'],
            'payments.*.meta.received_to_bank_account_id' => ['nullable', 'integer'],
            'payments.*.meta.txn_ref' => ['nullable', 'string', 'max:100'],


            // manual order discount fallback (optional)
            'order_discount_type' => ['nullable', 'in:none,percent,fixed'],
            'order_discount_value' => ['nullable', 'numeric', 'min:0'],
            'warranty_info' => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = Auth::id();

        return DB::transaction(function () use ($data, $userId, $stockService) {

            $isDraft = $data['action'] === 'draft';
            $warehouseId = (int) $data['warehouse_id'];

            // load products once
            $productIds = collect($data['items'])->pluck('product_id')->unique()->values()->all();
            $products = Product::whereIn('id', $productIds)
                ->with(['variations:id,product_id,sku,price,discount_price'])
                ->get()
                ->keyBy('id');

            $discount = !empty($data['discount_id'])
                ? Discount::find($data['discount_id'])
                : null;

            $subtotal = 0;
            $lineDiscountTotal = 0;
            $taxTotal = 0;

            $preparedItems = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);
                if (!$product) {
                    throw ValidationException::withMessages(['items' => ['Invalid product.']]);
                }

                // variable => variation required
                if ($product->type === 'variable' && empty($item['variation_id'])) {
                    throw ValidationException::withMessages([
                        'items' => ["Variation is required for: {$product->name}"],
                    ]);
                }

                $variation = null;
                if (!empty($item['variation_id'])) {
                    $variation = $product->variations->firstWhere('id', (int) $item['variation_id']);
                    if (!$variation) {
                        throw ValidationException::withMessages([
                            'items' => ["Invalid variation for: {$product->name}"],
                        ]);
                    }
                }

                // price choose
                $unitPrice =
                    $item['unit_price'] ??
                    ($variation
                        ? ($variation->discount_price ?? $variation->price)
                        : ($product->base_discount_price ?? $product->base_price));

                $qty = (int) $item['quantity'];

                $lineSub = (float) $unitPrice * $qty;
                $lineDiscount = (float) ($item['discount_amount'] ?? 0);
                $lineTax = (float) ($item['tax_amount'] ?? 0);

                $subtotal += $lineSub;
                $lineDiscountTotal += $lineDiscount;
                $taxTotal += $lineTax;

                $preparedItems[] = [
                    'product' => $product,
                    'variation' => $variation,
                    'quantity' => $qty,
                    'unit_price' => (float) $unitPrice,
                    'discount_amount' => $lineDiscount,
                    'tax_amount' => $lineTax,
                ];
            }

            // order-level discount (auto discount_id first)
            $orderDiscountAmount = 0;

            if ($discount) {
                // adjust to your columns: type/value
                $dtype = $discount->type;     // percent|fixed
                $dval = (float) $discount->value;

                if ($dtype === 'percent') {
                    $p = min(max($dval, 0), 100);
                    $orderDiscountAmount = ($subtotal * $p) / 100;
                } elseif ($dtype === 'fixed') {
                    $orderDiscountAmount = min(max($dval, 0), $subtotal);
                }
            } else {
                $t = $data['order_discount_type'] ?? 'none';
                $v = (float) ($data['order_discount_value'] ?? 0);

                if ($t === 'percent') {
                    $p = min(max($v, 0), 100);
                    $orderDiscountAmount = ($subtotal * $p) / 100;
                } elseif ($t === 'fixed') {
                    $orderDiscountAmount = min(max($v, 0), $subtotal);
                }
            }

            $discountTotal = $lineDiscountTotal + $orderDiscountAmount;
            $total = $subtotal - $discountTotal + $taxTotal;

            // payments: only on complete
            $payments = collect($data['payments'] ?? [])
                ->filter(fn($p) => !empty($p['payment_method_id']) && (float) ($p['amount'] ?? 0) > 0)
                ->values();

            $paidAmount = $isDraft ? 0 : (float) $payments->sum('amount');
            $change = $isDraft ? 0 : max(0, $paidAmount - $total);

            $paymentStatus = $isDraft
                ? 'unpaid'
                : ($paidAmount >= $total ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'));

            // if complete -> require at least one payment (recommended)
            if (!$isDraft && $payments->isEmpty()) {
                throw ValidationException::withMessages([
                    'payments' => ['At least one payment is required to complete the order.'],
                ]);
            }

            // invoice number only for completed (recommended)
            $invoiceNo = $isDraft ? null : ('POS-' . now()->format('YmdHis') . '-' . $userId);

            $order = PosOrder::create([
                'pos_session_id' => $data['pos_session_id'],
                'branch_id' => $data['branch_id'] ?? 1,
                'warehouse_id' => $warehouseId,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $userId,

                'invoice_no' => $invoiceNo,

                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'tax_amount' => $taxTotal,
                'total_amount' => $total,

                'paid_amount' => $paidAmount,
                'change_amount' => $change,

                'payment_status' => $paymentStatus,
                'status' => $isDraft ? 'draft' : 'completed',

                // optional if you have columns
                'discount_id' => $data['discount_id'] ?? null,
                'warranty_info' => $data['warranty_info'] ?? null,
            ]);

            foreach ($preparedItems as $it) {
                $product = $it['product'];
                $variation = $it['variation'];

                $lineSub = $it['unit_price'] * $it['quantity'];
                $lineTotal = $lineSub - $it['discount_amount'] + $it['tax_amount'];

                PosOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id,
                    'sku' => $variation?->sku ?? $product->sku,
                    'name' => $product->name,
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'discount_amount' => $it['discount_amount'],
                    'tax_amount' => $it['tax_amount'],
                    'line_total' => $lineTotal,
                ]);

                // ✅ stock out only when completed
                if (!$isDraft) {
                    $stockService->stockOut([
                        'type' => 'out',
                        'product_id' => $product->id,
                        'variation_id' => $variation?->id,
                        'branch_id' => $order?->branch_id ?? session('current_branch_id'),
                        'quantity' => $it['quantity'],
                        'from_warehouse_id' => $warehouseId,
                        'reference' => $order->invoice_no,
                        'note' => 'POS sale',
                        'created_by' => $userId,
                    ]);
                }
            }

            // payments only when completed
            if (!$isDraft) {
                foreach ($payments as $payment) {
                    PosPayment::create([
                        'order_id' => $order->id,
                        'branch_id' => $order->branch_id ?? session('current_branch_id') ?? 1,
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount' => $payment['amount'],
                        'paid_at' => now(),
                        'transaction_ref' => $payment['transaction_ref'] ?? null,
                        'notes' => $payment['notes'] ?? null,
                        'meta' => $payment['meta'] ?? null,
                    ]);
                }
            }

            if ($isDraft) {
                return redirect()->back()->with('success', 'Order saved as draft successfully');
            }

            return redirect()->route('pos.orders.invoice', $order->id);



            // return response()->json([
            //     'success' => true,
            //     'order_id' => $order->id,
            //     'invoice_no' => $order->invoice_no,
            //     'status' => $order->status,
            //     'total' => $total,
            //     'paid_amount' => $paidAmount,
            //     'change' => $change,
            //     'redirect' => !$isDraft ? route('pos.orders.invoice', $order->id) : null,
            // ]);
        });
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
            ],
        ]);
    }



    public function orders(Request $request)
    {
        $search = trim($request->string('search'));
        $status = trim($request->string('status'));
        $paymentStatus = trim($request->string('payment_status'));
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');
        $trashed = $request->boolean('trashed');

        // Advanced Filters
        $category_id = $request->get('category_id');
        $brand_id = $request->get('brand_id');
        $product_id = $request->get('product_id');

        $query = PosOrder::with(['customer', 'user', 'session', 'items.product.category', 'items.product.brand'])
            ->when($trashed, fn($q) => $q->onlyTrashed())
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas(
                            'customer',
                            fn($c) =>
                            $c->where('name', 'like', "%{$search}%")
                        )
                        ->orWhereHas(
                            'user',
                            fn($u) =>
                            $u->where('name', 'like', "%{$search}%")
                        );
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($paymentStatus, fn($q) => $q->where('payment_status', $paymentStatus))
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($category_id, function ($q) use ($category_id) {
                $q->whereHas('items.product', fn($p) => $p->where('category_id', $category_id));
            })
            ->when($brand_id, function ($q) use ($brand_id) {
                $q->whereHas('items.product', fn($p) => $p->where('brand_id', $brand_id));
            })
            ->when($product_id, function ($q) use ($product_id) {
                $q->whereHas('items', fn($i) => $i->where('product_id', $product_id));
            })
            ->latest('id');

        // Handle Exports
        if ($request->has('export')) {
            if ($request->get('export') === 'pdf') {
                return $this->exportPdf($query->get());
            } elseif ($request->get('export') === 'excel') {
                return $this->exportExcel($query->get());
            }
        }

        $orders = $query->paginate(15)->withQueryString();

        // Insights (Top Selling Products)
        $topProducts = PosOrderItem::join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.id')
            ->whereNull('pos_orders.deleted_at')
            ->select('pos_order_items.product_id', 'pos_order_items.name', DB::raw('SUM(pos_order_items.quantity) as total_qty'), DB::raw('SUM(pos_order_items.line_total) as total_amount'))
            ->groupBy('pos_order_items.product_id', 'pos_order_items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Insights (Sales by Brand)
        $brandSales = PosOrderItem::join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.id')
            ->whereNull('pos_orders.deleted_at')
            ->join('products', 'pos_order_items.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->select('brands.name', DB::raw('SUM(pos_order_items.quantity) as total_qty'), DB::raw('SUM(pos_order_items.line_total) as total_amount'))
            ->groupBy('brands.name')
            ->orderByDesc('total_amount')
            ->get();

        return Inertia::render('Admin/POS/Order/Orders', [
            'orders' => $orders,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'date_from' => $dateFrom?->format('Y-m-d'),
                'date_to' => $dateTo?->format('Y-m-d'),
                'category_id' => $category_id,
                'brand_id' => $brand_id,
                'product_id' => $product_id,
                'trashed' => $trashed,
            ],
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'brands' => Brand::select('id', 'name')->orderBy('name')->get(),
            'available_products' => Product::select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', 1)->get(),
            'insights' => [
                'top_products' => $topProducts,
                'brand_sales' => $brandSales
            ]
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
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('pos_orders_report.pdf');
    }

    protected function exportExcel($orders)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OrderExport($orders), 'pos_orders_report.xlsx');
    }


    public function void($orderId, StockService $stockService)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status === 'void') {
            return back()->with('success', 'Already voided');
        }

        DB::transaction(function () use ($order, $stockService) {
            // restore stock only if it was completed
            if ($order->status === 'completed') {
                foreach ($order->items as $it) {
                    $stockService->stockIn([
                        'type' => 'in',
                        'product_id' => $it->product_id,
                        'variation_id' => $it->variation_id,
                        'branch_id' => $order?->branch_id ?? session('current_branch_id') ?? 1,
                        'quantity' => $it->quantity,
                        'to_warehouse_id' => $order->warehouse_id,
                        'reference' => $order->invoice_no,
                        'note' => 'VOID POS order',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            // mark order void
            $order->update([
                'status' => 'void',
            ]);

            // optional: store a void log fields if you have them:
            // $order->update(['voided_at'=>now(), 'voided_by'=>Auth::id()]);
        });

        return back()->with('success', 'Order voided');
    }

    public function completeDraft(Request $request, $orderId, StockService $stockService)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status !== 'draft') {
            return back()->with('success', 'Order already completed/void.');
        }

        $data = $request->validate([
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.transaction_ref' => ['nullable', 'string', 'max:100'],
            'payments.*.notes' => ['nullable', 'string', 'max:500'],

            // meta (bank info)
            'payments.*.meta' => ['nullable', 'array'],
            'payments.*.meta.customer_bank_name' => ['nullable', 'string', 'max:100'],
            'payments.*.meta.customer_account_no' => ['nullable', 'string', 'max:50'],
            'payments.*.meta.received_to_bank_account_id' => ['nullable', 'integer'],
            'payments.*.meta.txn_ref' => ['nullable', 'string', 'max:100'],
        ]);

        return DB::transaction(function () use ($order, $data, $stockService) {

            $total = (float) $order->total_amount;

            $paid = collect($data['payments'])->sum(fn($p) => (float) $p['amount']);
            $change = max(0, $paid - $total);

            $paymentStatus = $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');

            // ✅ generate invoice no now
            $invoiceNo = $order->invoice_no ?: ('POS-' . now()->format('YmdHis') . '-' . $order->id);

            // ✅ create payment rows now
            foreach ($data['payments'] as $p) {
                PosPayment::create([
                    'order_id' => $order->id,
                    'branch_id' => $order?->branch_id ?? session('current_branch_id') ?? 1,
                    'payment_method_id' => $p['payment_method_id'],
                    'amount' => $p['amount'],
                    'paid_at' => now(),
                    'transaction_ref' => $p['transaction_ref'] ?? null,
                    'notes' => $p['notes'] ?? null,
                    'meta' => $p['meta'] ?? null, // make sure PosPayment model casts meta as array/json
                ]);
            }

            // ✅ stockOut now (draft had no stockOut before)
            $order->loadMissing('items');
            foreach ($order->items as $it) {
                $stockService->stockOut([
                    'type' => 'out',
                    'product_id' => $it->product_id,
                    'variation_id' => $it->variation_id,
                    'branch_id' => $order?->branch_id ?? session('current_branch_id') ?? 1,
                    'quantity' => $it->quantity,
                    'from_warehouse_id' => $order->warehouse_id,
                    'reference' => $invoiceNo,
                    'note' => 'POS sale (completed from draft)',
                    'created_by' => Auth::id(),
                ]);
            }

            // ✅ update order
            $order->update([
                'invoice_no' => $invoiceNo,
                'status' => 'completed',
                'paid_amount' => $paid,
                'change_amount' => $change,
                'payment_status' => $paymentStatus,
            ]);

            return redirect()
                ->route('pos.orders.invoice', $order->id)
                ->with('success', 'Draft completed successfully');
        });
    }


    public function addPayment(Request $request, $orderId)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status === 'void') {
            return back()->with('error', 'Cannot take payment for void order.');
        }

        // ✅ If frontend sends { payments: [...] }
        if ($request->has('payments')) {
            $data = $request->validate([
                'payments' => ['required', 'array', 'min:1'],

                'payments.*.payment_method_id' => ['required', 'exists:payment_methods,id'],
                'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
                'payments.*.transaction_ref' => ['nullable', 'string', 'max:100'],
                'payments.*.notes' => ['nullable', 'string', 'max:500'],

                'payments.*.meta' => ['nullable', 'array'],
                'payments.*.meta.customer_bank_name' => ['nullable', 'string', 'max:100'],
                'payments.*.meta.customer_account_no' => ['nullable', 'string', 'max:50'],
                'payments.*.meta.received_to_bank_account_id' => ['nullable', 'integer'],
                'payments.*.meta.txn_ref' => ['nullable', 'string', 'max:100'],
            ]);

            DB::transaction(function () use ($order, $data) {
                foreach ($data['payments'] as $p) {
                    PosPayment::create([
                        'order_id' => $order->id,
                        'branch_id' => $order?->branch_id ?? session('current_branch_id') ?? 1,
                        'payment_method_id' => $p['payment_method_id'],
                        'amount' => $p['amount'],
                        'paid_at' => now(),
                        'transaction_ref' => $p['transaction_ref'] ?? null,
                        'notes' => $p['notes'] ?? null,
                        'meta' => $p['meta'] ?? null,
                    ]);
                }

                $total = (float) $order->total_amount;
                $paid = (float) $order->payments()->sum('amount');
                $due = max(0, $total - $paid);

                $paymentStatus = $paid <= 0 ? 'unpaid' : ($due <= 0 ? 'paid' : 'partial');

                $order->update([
                    'paid_amount' => $paid,
                    'due_amount' => $due,
                    'payment_status' => $paymentStatus,
                ]);
            });

            return back()->with('success', 'Payments added successfully.');
        }

        // ✅ Otherwise handle single payment payload
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_ref' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],

            'meta' => ['nullable', 'array'],
            'meta.customer_bank_name' => ['nullable', 'string', 'max:100'],
            'meta.customer_account_no' => ['nullable', 'string', 'max:50'],
            'meta.received_to_bank_account_id' => ['nullable', 'integer'],
            'meta.txn_ref' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($order, $data) {
            PosPayment::create([
                'order_id' => $order->id,
                'branch_id' => $order?->branch_id ?? session('current_branch_id') ?? 1,
                'payment_method_id' => $data['payment_method_id'],
                'amount' => $data['amount'],
                'paid_at' => now(),
                'transaction_ref' => $data['transaction_ref'] ?? null,
                'notes' => $data['notes'] ?? null,
                'meta' => $data['meta'] ?? null,
            ]);

            $total = (float) $order->total_amount;
            $paid = (float) $order->payments()->sum('amount');
            $due = max(0, $total - $paid);

            $paymentStatus = $paid <= 0 ? 'unpaid' : ($due <= 0 ? 'paid' : 'partial');

            $order->update([
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_status' => $paymentStatus,
            ]);
        });

        return back()->with('success', 'Payment added successfully.');
    }


    public function edit($orderId)
    {
        $order = PosOrder::withoutGlobalScope('branch')
            ->findOrFail($orderId);

        if ($order->status !== 'draft') {
            return to_route('pos.index')->with('error', 'Only draft orders can be edited.');
        }

        $order->load(['items.product.variations', 'items.product.stocks.warehouse', 'customer']);

        // share same data as index
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'barcode', 'base_price', 'base_discount_price', 'thumbnail', 'type')
            ->with([
                'variations:id,product_id,sku,price,discount_price',
                'stocks:id,product_id,variation_id,warehouse_id,branch_id,quantity',
                'stocks.warehouse:id,name'
            ])
            ->orderBy('name')
            ->get();

        // Last warranty info for pre-filling
        $lastOrder = PosOrder::where('user_id', Auth::id())
            ->where('warranty_info', '!=', '')
            ->whereNotNull('warranty_info')
            ->latest('id')
            ->first();

        return Inertia::render('Admin/POS/Index', [
            'products' => $products,
            'customers' => Customer::select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'currentSession' => PosSession::where('user_id', Auth::id())->where('status', 'open')->latest('id')->first(),
            'branches' => Branch::select('id', 'name')->orderBy('name')->get(),
            'warehouses' => Warehouse::select('id', 'name')->orderBy('name')->get(),
            'lastWarrantyInfo' => $lastOrder?->warranty_info ?? '',
            'warrantyTemplates' => WarrantyGuarantee::where('is_active', true)->select('id', 'name', 'description', 'category_id')->get(),
            'order' => $order, // ✅ Pass order for editing
        ]);
    }

    public function update(Request $request, $orderId, StockService $stockService)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);

        if ($order->status !== 'draft') {
            return back()->with('error', 'Cannot update non-draft order.');
        }

        // validation same as store
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

            'payments' => ['nullable', 'array'],
            'payments.*.payment_method_id' => ['required_with:payments.*.amount', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required_with:payments.*.payment_method_id', 'numeric', 'min:0'],
            'payments.*.transaction_ref' => ['nullable', 'string', 'max:100'],
            'payments.*.notes' => ['nullable', 'string', 'max:500'],
            'payments.*.meta' => ['nullable', 'array'],

            'order_discount_type' => ['nullable', 'in:none,percent,fixed'],
            'order_discount_value' => ['nullable', 'numeric', 'min:0'],
            'warranty_info' => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = Auth::id();

        return DB::transaction(function () use ($data, $userId, $stockService, $order) {
            $isDraft = $data['action'] === 'draft';
            $warehouseId = (int) $data['warehouse_id'];

            // products
            $productIds = collect($data['items'])->pluck('product_id')->unique()->values()->all();
            $products = Product::whereIn('id', $productIds)
                ->with(['variations:id,product_id,sku,price,discount_price'])
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $lineDiscountTotal = 0;
            $taxTotal = 0;
            $preparedItems = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);
                if (!$product) continue;

                $variation = null;
                if (!empty($item['variation_id'])) {
                    $variation = $product->variations->firstWhere('id', (int) $item['variation_id']);
                }

                $unitPrice = $item['unit_price'] ?? ($variation ? ($variation->discount_price ?? $variation->price) : ($product->base_discount_price ?? $product->base_price));
                $qty = (int) $item['quantity'];

                $lineSub = (float) $unitPrice * $qty;
                $lineDiscount = (float) ($item['discount_amount'] ?? 0);
                $lineTax = (float) ($item['tax_amount'] ?? 0);

                $subtotal += $lineSub;
                $lineDiscountTotal += $lineDiscount;
                $taxTotal += $lineTax;

                $preparedItems[] = [
                    'product' => $product,
                    'variation' => $variation,
                    'quantity' => $qty,
                    'unit_price' => (float) $unitPrice,
                    'discount_amount' => $lineDiscount,
                    'tax_amount' => $lineTax,
                ];
            }

            // order discount
            $orderDiscountAmount = 0;
            $t = $data['order_discount_type'] ?? 'none';
            $v = (float) ($data['order_discount_value'] ?? 0);
            if ($t === 'percent') {
                $p = min(max($v, 0), 100);
                $orderDiscountAmount = ($subtotal * $p) / 100;
            } elseif ($t === 'fixed') {
                $orderDiscountAmount = min(max($v, 0), $subtotal);
            }

            $discountTotal = $lineDiscountTotal + $orderDiscountAmount;
            $total = $subtotal - $discountTotal + $taxTotal;

            // payments
            $payments = collect($data['payments'] ?? [])
                ->filter(fn($p) => !empty($p['payment_method_id']) && (float) ($p['amount'] ?? 0) > 0)
                ->values();

            $paidAmount = $isDraft ? 0 : (float) $payments->sum('amount');
            $change = $isDraft ? 0 : max(0, $paidAmount - $total);

            // validate payment if complete
            if (!$isDraft && $payments->isEmpty()) {
                throw ValidationException::withMessages(['payments' => ['At least one payment is required.']]);
            }

            $invoiceNo = (!$isDraft && !$order->invoice_no) ? ('POS-' . now()->format('YmdHis') . '-' . $userId) : $order->invoice_no;

            // Update Order
            $order->update([
                'pos_session_id' => $data['pos_session_id'], // update session to current?
                'branch_id' => $data['branch_id'] ?? null,
                'warehouse_id' => $warehouseId,
                'customer_id' => $data['customer_id'] ?? null,
                'invoice_no' => $invoiceNo,
                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'tax_amount' => $taxTotal,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => $change,
                'status' => $isDraft ? 'draft' : 'completed',
                'payment_status' => $isDraft ? 'unpaid' : ($paidAmount >= $total ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid')),
                'warranty_info' => $data['warranty_info'] ?? null,
            ]);

            // Sync Items (delete all and recreate)
            $order->items()->delete();
            foreach ($preparedItems as $it) {
                $order->items()->create([
                    'product_id' => $it['product']->id,
                    'variation_id' => $it['variation']?->id,
                    'sku' => $it['variation']?->sku ?? $it['product']->sku,
                    'name' => $it['product']->name,
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'discount_amount' => $it['discount_amount'],
                    'tax_amount' => $it['tax_amount'],
                    'line_total' => ($it['unit_price'] * $it['quantity']) - $it['discount_amount'] + $it['tax_amount'],
                ]);

                if (!$isDraft) {
                    $stockService->stockOut([
                        'type' => 'out',
                        'product_id' => $it['product']->id,
                        'variation_id' => $it['variation']?->id,
                        'branch_id' => $order->branch_id ?? session('current_branch_id'),
                        'quantity' => $it['quantity'],
                        'from_warehouse_id' => $warehouseId,
                        'reference' => $order->invoice_no,
                        'note' => 'POS sale (update)',
                        'created_by' => $userId,
                    ]);
                }
            }

            // Sync Payments (delete old drafts? no, draft has no payments usually. delete all and recreate)
            $order->payments()->delete();
            if (!$isDraft) {
                foreach ($payments as $payment) {
                    $order->payments()->create([
                        'branch_id' => $order->branch_id ?? session('current_branch_id'),
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount' => $payment['amount'],
                        'paid_at' => now(),
                        'transaction_ref' => $payment['transaction_ref'] ?? null,
                        'notes' => $payment['notes'] ?? null,
                        'meta' => $payment['meta'] ?? null,
                    ]);
                }
            }

            if ($isDraft) {
                return redirect()->back()->with('success', 'Draft updated successfully');
            }

            return redirect()->route('pos.orders.invoice', $order->id);
        });
    }

    public function destroy($orderId)
    {
        $order = PosOrder::withoutGlobalScope('branch')->findOrFail($orderId);
        $order->delete();

        return back()->with('success', 'Order moved to trash.');
    }

    public function restore($orderId)
    {
        $order = PosOrder::onlyTrashed()->withoutGlobalScope('branch')->findOrFail($orderId);
        $order->restore();

        return back()->with('success', 'Order restored.');
    }

    public function forceDelete($orderId)
    {
        $order = PosOrder::onlyTrashed()->withoutGlobalScope('branch')->findOrFail($orderId);

        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->payments()->delete();
            $order->forceDelete();
        });

        return back()->with('success', 'Order permanently deleted.');
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
            return back()->with('success', count($ids) . ' orders moved to trash.');
        }

        if ($action === 'restore') {
            PosOrder::onlyTrashed()->withoutGlobalScope('branch')->whereIn('id', $ids)->restore();
            return back()->with('success', count($ids) . ' orders restored.');
        }

        if ($action === 'force_delete') {
            DB::transaction(function () use ($ids) {
                PosOrder::onlyTrashed()->withoutGlobalScope('branch')->whereIn('id', $ids)->each(function ($order) {
                    $order->items()->delete();
                    $order->payments()->delete();
                    $order->forceDelete();
                });
            });
            return back()->with('success', count($ids) . ' orders permanently deleted.');
        }

        return back();
    }
}

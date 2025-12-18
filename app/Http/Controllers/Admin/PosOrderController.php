<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosPayment;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PosOrderController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'barcode', 'base_price', 'base_discount_price', 'thumbnail', 'type')
            ->with(['variations:id,product_id,sku,price,discount_price'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/POS/Index', [
            'products' => $products,
            'customers' => Customer::select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'currentSession' => PosSession::where('user_id', Auth::id())->where('status', 'open')->latest('id')->first(),

            // ✅ add these
            'branches' => Branch::select('id', 'name')->orderBy('name')->get(),
            'warehouses' => Warehouse::select('id', 'name')->orderBy('name')->get(),
        ]);
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

            // manual order discount fallback (optional)
            'order_discount_type' => ['nullable', 'in:none,percent,fixed'],
            'order_discount_value' => ['nullable', 'numeric', 'min:0'],

            'notes' => ['nullable', 'string'],
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
                'branch_id' => $data['branch_id'] ?? null,
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
                'notes' => $data['notes'] ?? null,

                // optional if you have columns
                // 'discount_id' => $data['discount_id'] ?? null,
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
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount' => $payment['amount'],
                        'paid_at' => now(),
                        'transaction_ref' => $payment['transaction_ref'] ?? null,
                        'notes' => $payment['notes'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'status' => $order->status,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change' => $change,
                'redirect' => !$isDraft ? route('pos.orders.invoice', $order->id) : null,
            ]);
        });
    }



    public function orders(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $paymentStatus = $request->get('payment_status');

        $query = PosOrder::with(['customer', 'user', 'session'])
            ->when($search, function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($paymentStatus, function ($q) use ($paymentStatus) {
                $q->where('payment_status', $paymentStatus);
            })
            ->latest('id');

        $orders = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/POS/Order/Orders', [
            'orders' => $orders,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'payment_status' => $paymentStatus,
            ],
        ]);
    }
}

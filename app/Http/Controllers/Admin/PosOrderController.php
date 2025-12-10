<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosPayment;
use App\Models\PosSession;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PosOrderController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'barcode', 'base_price', 'stock_quantity')
            ->orderBy('name')
            ->get();

        $customers = Customer::select('id', 'name')->orderBy('name')->get();

        $paymentMethods = PaymentMethod::where('is_active', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $currentSession = PosSession::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest('id')
            ->first();

        return Inertia::render('Admin/POS/Index', [
            'products' => $products,
            'customers' => $customers,
            'paymentMethods' => $paymentMethods,
            'currentSession' => $currentSession,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'pos_session_id' => ['required', 'exists:pos_sessions,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variation_id' => ['nullable', 'exists:product_variations,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount' => ['nullable', 'numeric', 'min:0'],

            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.transaction_ref' => ['nullable', 'string', 'max:100'],
            'payments.*.notes' => ['nullable', 'string', 'max:500'],

            // 🔥 order-level discount (from POS UI)
            'order_discount_type' => ['nullable', Rule::in(['none', 'percent', 'fixed'])],
            'order_discount_value' => ['nullable', 'numeric', 'min:0'],

            'notes' => ['nullable', 'string'],
        ]);

        $userId = Auth::id();

        return DB::transaction(function () use ($data, $userId) {

            $subtotal = 0;
            $lineDiscountTotal = 0;
            $taxTotal = 0;

            // ------------------ ITEMS CALC ------------------
            foreach ($data['items'] as $item) {
                $lineSub = $item['unit_price'] * $item['quantity'];
                $lineDiscount = $item['discount_amount'] ?? 0;
                $lineTax = $item['tax_amount'] ?? 0;

                $subtotal += $lineSub;
                $lineDiscountTotal += $lineDiscount;
                $taxTotal += $lineTax;
            }

            // ------------------ ORDER DISCOUNT ------------------
            $orderDiscountType = $data['order_discount_type'] ?? 'none';
            $orderDiscountValue = (float) ($data['order_discount_value'] ?? 0);
            $orderDiscountAmount = 0;

            if ($orderDiscountType === 'percent') {
                // 0–100 এর মধ্যে clamp করি
                $p = min(max($orderDiscountValue, 0), 100);
                $orderDiscountAmount = ($subtotal * $p) / 100;
            } elseif ($orderDiscountType === 'fixed') {
                // subtotal এর বেশি discount allow করব না
                $orderDiscountAmount = min(max($orderDiscountValue, 0), $subtotal);
            }

            // মোট discount = line discount + order-level discount
            $discountTotal = $lineDiscountTotal + $orderDiscountAmount;

            // ------------------ TOTAL CALC ------------------
            $total = $subtotal - $discountTotal + $taxTotal;
            $paidAmount = collect($data['payments'])->sum('amount');
            $change = max(0, $paidAmount - $total);

            $paymentStatus = $paidAmount >= $total
                ? 'paid'
                : ($paidAmount > 0 ? 'partial' : 'unpaid');

            // (চাইলে backend থেকেও check করতে পারো যে partial allow করবে নাকি error দেবে)
            // উদাহরণ:
            // if ($paidAmount < $total && config('pos.disallow_partial', false)) {
            //     throw ValidationException::withMessages([
            //         'payments' => ['Total paid must be at least payable amount.'],
            //     ]);
            // }

            // ------------------ CREATE ORDER ------------------
            $order = PosOrder::create([
                'pos_session_id' => $data['pos_session_id'],
                'branch_id' => $data['branch_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $userId,

                'invoice_no' => 'POS-' . now()->format('YmdHis') . '-' . $userId,
                'reference_no' => null,

                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,        // ✅ line + order discount total
                'tax_amount' => $taxTotal,
                'total_amount' => $total,

                'paid_amount' => $paidAmount,
                'change_amount' => $change,

                'payment_status' => $paymentStatus,
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,

                // 🔥 শুধুমাত্র যদি এই কলামগুলো PosOrder টেবিলে / মডেলে যোগ করে থাকো:
                // 'order_discount_type'  => $orderDiscountType,
                // 'order_discount_value' => $orderDiscountValue,
                // 'order_discount_amount'=> $orderDiscountAmount,
            ]);

            // ------------------ ITEMS + STOCK UPDATE ------------------
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $lineSub = $item['unit_price'] * $item['quantity'];
                $lineDiscount = $item['discount_amount'] ?? 0;
                $lineTax = $item['tax_amount'] ?? 0;
                $lineTotal = $lineSub - $lineDiscount + $lineTax;

                PosOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variation_id' => $item['variation_id'] ?? null,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $lineDiscount,
                    'tax_amount' => $lineTax,
                    'line_total' => $lineTotal,
                ]);

                // TODO: এখানে তোমার ProductStock / Inventory logic প্লাগ ইন করো
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // ------------------ PAYMENTS SAVE (multiple methods) ------------------
            foreach ($data['payments'] as $payment) {
                PosPayment::create([
                    'order_id' => $order->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount'],
                    'paid_at' => now(),
                    'transaction_ref' => $payment['transaction_ref'] ?? null,
                    'notes' => $payment['notes'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change' => $change,
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

<?php

use App\Models\Branch;
use App\Models\Customer;
use App\Models\EmiBank;
use App\Models\InstallmentSale;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\PosOrder;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SmsGateway;
use App\Models\SmsLog;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InstallmentService;
use App\Services\Sms\SmsManager;

beforeEach(function () {
    $this->user = User::factory()->create();
    $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
    $role->syncPermissions(Permission::all());
    $this->user->assignRole($role);

    $this->branch = Branch::factory()->create(['is_active' => true]);
    $this->warehouse = Warehouse::create(['name' => 'WH', 'address' => 'Dhaka', 'phone' => '0100', 'status' => 1]);
    $this->session = PosSession::create([
        'user_id' => $this->user->id,
        'branch_id' => $this->branch->id,
        'warehouse_id' => $this->warehouse->id,
        'status' => 'open',
        'opened_at' => now(),
    ]);
    $this->product = Product::create([
        'name' => 'Air Conditioner 1.5 Ton',
        'slug' => 'ac-15',
        'sku' => 'AC-15',
        'base_price' => 60000,
        'type' => 'simple',
        'is_active' => true,
    ]);
    ProductStock::create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'branch_id' => $this->branch->id,
        'quantity' => 10,
    ]);
    $this->cash = PaymentMethod::create(['name' => 'Cash in Hand', 'is_active' => true]);
    $this->customer = Customer::create(['name' => 'Rahim Uddin', 'phone' => '01712-345678']);

    Setting::set('sms_enabled', '1');
    Setting::set('sms_event_sale_completed', '1');
    Setting::set('sms_event_payment_received', '1');
    Setting::set('sms_delivery_mode', 'sync');
    SmsGateway::create(['name' => 'Test', 'driver' => 'log', 'is_default' => true, 'is_active' => true]);

    $this->actingAs($this->user);
});

function salePayload(array $overrides = []): array
{
    return array_merge([
        'action' => 'complete',
        'pos_session_id' => test()->session->id,
        'branch_id' => test()->branch->id,
        'warehouse_id' => test()->warehouse->id,
        'customer_id' => test()->customer->id,
        'items' => [['product_id' => test()->product->id, 'quantity' => 1, 'unit_price' => 60000]],
        'payments' => [['payment_method_id' => test()->cash->id, 'amount' => 60000]],
    ], $overrides);
}

test('regular sale stores warehouse, due and sends sale SMS', function () {
    $this->post(route('pos.orders.store'), salePayload([
        'payments' => [['payment_method_id' => $this->cash->id, 'amount' => 20000]],
    ]))->assertRedirect();

    $order = PosOrder::first();
    expect($order->warehouse_id)->toBe($this->warehouse->id)
        ->and((float) $order->due_amount)->toBe(40000.0)
        ->and($order->payment_status)->toBe('partial')
        ->and($order->invoice_no)->toEndWith('-'.$order->id);

    $sms = SmsLog::where('event', 'sale_completed')->first();
    expect($sms)->not->toBeNull()
        ->and($sms->phone)->toBe('8801712345678')
        ->and($sms->status)->toBe('sent');
});

test('installment sale builds schedule and keeps order totals consistent', function () {
    $this->post(route('pos.orders.store'), salePayload([
        'payments' => [['payment_method_id' => $this->cash->id, 'amount' => 12000]],
        'installment' => [
            'enabled' => true,
            'number_of_installments' => 6,
            'frequency' => 'monthly',
            'interest_type' => 'flat',
            'interest_rate' => 10,
            'processing_fee_type' => 'fixed',
            'processing_fee' => 500,
            'late_fee_type' => 'fixed',
            'late_fee_amount' => 200,
            'grace_days' => 3,
            'first_due_date' => today()->addMonth()->toDateString(),
        ],
    ]))->assertSessionHasNoErrors()->assertRedirect();

    $order = PosOrder::first();
    $sale = InstallmentSale::first();

    // financed = 60000 + 500 - 12000 = 48500; interest 10% = 4850
    expect((float) $sale->financed_amount)->toBe(48500.0)
        ->and((float) $sale->interest_amount)->toBe(4850.0)
        ->and((float) $sale->total_payable)->toBe(53350.0)
        ->and($sale->schedules()->count())->toBe(6)
        ->and((float) $sale->schedules()->sum('amount'))->toBe(53350.0)
        ->and($order->sale_type)->toBe('installment')
        ->and((float) $order->total_amount)->toBe(65350.0)
        ->and((float) $order->due_amount)->toBe(53350.0);

    // Collect slightly more than one installment: second becomes partial
    $first = (float) $sale->schedules()->first()->amount;
    $this->post(route('installments.collect', $sale), [
        'payments' => [['payment_method_id' => $this->cash->id, 'amount' => $first + 100]],
    ])->assertSessionHasNoErrors();

    $sale->refresh();
    $statuses = $sale->schedules()->pluck('status')->take(2)->all();
    expect($statuses)->toBe(['paid', 'partial'])
        ->and((float) $sale->paid_amount)->toBe($first + 100)
        ->and((float) $order->fresh()->due_amount)->toBe(round(53350 - $first - 100, 2));

    expect(SmsLog::where('event', 'payment_received')->exists())->toBeTrue();

    // Over-collection is rejected
    $this->post(route('installments.collect', $sale), [
        'payments' => [['payment_method_id' => $this->cash->id, 'amount' => 999999]],
    ])->assertSessionHasErrors('payments');
});

test('late fee is applied after grace period and is collected first', function () {
    $this->post(route('pos.orders.store'), salePayload([
        'payments' => [],
        'installment' => [
            'enabled' => true,
            'number_of_installments' => 3,
            'frequency' => 'monthly',
            'interest_type' => 'none',
            'late_fee_type' => 'fixed',
            'late_fee_amount' => 300,
            'grace_days' => 2,
            'first_due_date' => today()->subDays(5)->toDateString(),
        ],
    ]))->assertSessionHasNoErrors();

    $sale = InstallmentSale::first();
    app(InstallmentService::class)->applyLateFees($sale);
    $sale->refresh();

    expect((float) $sale->late_fee_charged)->toBe(300.0)
        ->and((float) $sale->order->late_fee_amount)->toBe(300.0)
        ->and((float) $sale->order->total_amount)->toBe(60300.0);

    // applying again does not double-charge
    app(InstallmentService::class)->applyLateFees($sale->fresh());
    expect((float) $sale->fresh()->late_fee_charged)->toBe(300.0);

    $this->post(route('installments.collect', $sale), [
        'payments' => [['payment_method_id' => $this->cash->id, 'amount' => 300]],
    ])->assertSessionHasNoErrors();

    $schedule = $sale->schedules()->first();
    expect((float) $schedule->late_fee_paid)->toBe(300.0)
        ->and((float) $schedule->paid_amount)->toBe(0.0);
});

test('installment requires a customer', function () {
    $this->post(route('pos.orders.store'), salePayload([
        'customer_id' => null,
        'installment' => [
            'enabled' => true,
            'number_of_installments' => 3,
            'frequency' => 'monthly',
            'interest_type' => 'none',
            'first_due_date' => today()->addMonth()->toDateString(),
        ],
    ]))->assertSessionHasErrors('customer_id');
});

test('reducing balance interest matches amortization formula', function () {
    $calc = app(InstallmentService::class)->calculate(100000, 0, [
        'number_of_installments' => 12,
        'frequency' => 'monthly',
        'interest_type' => 'reducing',
        'interest_rate' => 12,
        'first_due_date' => '2026-01-31',
    ]);

    // P=100000, r=1%/month, n=12 => EMI 8884.88, total interest ~6618.55
    expect($calc['interest_amount'])->toBe(6618.55)
        ->and(array_sum(array_column($calc['schedules'], 'amount')))->toEqualWithDelta(106618.55, 0.001)
        ->and($calc['schedules'][1]['due_date'])->toBe('2026-02-28'); // month-end without overflow
});

test('bank EMI sale adds customer charge and records receivable', function () {
    $bank = EmiBank::create(['name' => 'City Bank', 'is_active' => true]);
    $tenure = $bank->tenures()->create(['months' => 6, 'customer_charge_percent' => 3, 'merchant_charge_percent' => 2, 'is_active' => true]);
    $emiMethod = PaymentMethod::create(['name' => 'Card EMI', 'is_active' => true]);

    $this->post(route('pos.orders.store'), salePayload([
        'payments' => [[
            'payment_method_id' => $emiMethod->id,
            'amount' => 60000,
            'emi' => ['emi_bank_tenure_id' => $tenure->id, 'card_last4' => '4242', 'approval_code' => 'A1B2'],
        ]],
    ]))->assertSessionHasNoErrors();

    $order = PosOrder::first();
    $tx = $order->emiTransactions()->first();

    expect($order->sale_type)->toBe('emi')
        ->and((float) $order->emi_charge_amount)->toBe(1800.0)
        ->and((float) $order->total_amount)->toBe(61800.0)
        ->and($order->payment_status)->toBe('paid')
        ->and((float) $tx->card_amount)->toBe(61800.0)
        ->and((float) $tx->merchant_charge_amount)->toBe(1236.0)
        ->and((float) $tx->net_receivable)->toBe(60564.0)
        ->and((float) $tx->monthly_amount)->toBe(10300.0);

    $this->post(route('emi.settle'), ['ids' => [$tx->id], 'settled_at' => today()->toDateString()])->assertSessionHasNoErrors();
    expect($tx->fresh()->settlement_status)->toBe('settled');
});

test('void restores stock to the sale warehouse and cancels installments', function () {
    $this->post(route('pos.orders.store'), salePayload([
        'payments' => [],
        'installment' => [
            'enabled' => true,
            'number_of_installments' => 2,
            'frequency' => 'monthly',
            'interest_type' => 'none',
            'first_due_date' => today()->addMonth()->toDateString(),
        ],
    ]));
    expect((float) ProductStock::sum('quantity'))->toBe(9.0);

    $order = PosOrder::first();
    $this->post(route('pos.orders.void', $order->id))->assertRedirect();

    expect((float) ProductStock::where('warehouse_id', $this->warehouse->id)->sum('quantity'))->toBe(10.0)
        ->and(InstallmentSale::first()->status)->toBe('cancelled');
});

test('insufficient stock returns a validation error instead of a server error', function () {
    $this->post(route('pos.orders.store'), salePayload([
        'items' => [['product_id' => $this->product->id, 'quantity' => 50, 'unit_price' => 60000]],
    ]))->assertSessionHasErrors('items');

    expect(PosOrder::count())->toBe(0);
});

test('order search matches partial and formatted phone numbers', function () {
    $this->post(route('pos.orders.store'), salePayload());

    foreach (['1712345', '01712345678', '+880 1712-345678', 'rahim', 'ra*din', '0171*678'] as $term) {
        $this->get(route('pos.orders.index', ['search' => $term]))
            ->assertInertia(fn ($page) => $page->where('orders.total', 1));
    }
    $this->get(route('pos.orders.index', ['search' => '01999']))
        ->assertInertia(fn ($page) => $page->where('orders.total', 0));
});

test('phone normalisation handles local formats', function () {
    expect(SmsManager::normalizePhone('01712-345678'))->toBe('8801712345678')
        ->and(SmsManager::normalizePhone('+8801712345678'))->toBe('8801712345678')
        ->and(SmsManager::normalizePhone('008801712345678'))->toBe('8801712345678')
        ->and(SmsManager::normalizePhone('12'))->toBeNull()
        ->and(SmsManager::segments(str_repeat('a', 160)))->toBe(1)
        ->and(SmsManager::segments(str_repeat('a', 161)))->toBe(2)
        ->and(SmsManager::segments(str_repeat('ক', 71)))->toBe(2);
});

test('sms campaign sends to selected customers and manual numbers', function () {
    $other = Customer::create(['name' => 'Karim', 'phone' => '01812345678']);

    $this->post(route('sms.send'), [
        'message' => 'Eid Mubarak {customer_name}!',
        'customer_ids' => [$this->customer->id, $other->id],
        'numbers' => "01912345678, 01712345678\nbad",
    ])->assertSessionHasNoErrors()->assertRedirect();

    // 01712345678 is a duplicate of the first customer; "bad" is not a number
    expect(SmsLog::where('event', 'campaign')->count())->toBe(3)
        ->and(SmsLog::where('phone', '8801712345678')->first()->message)->toBe('Eid Mubarak Rahim Uddin!');
});

test('sms disabled blocks campaigns and event messages', function () {
    Setting::set('sms_enabled', '0');

    $this->post(route('sms.send'), ['message' => 'hi', 'customer_ids' => [$this->customer->id]])
        ->assertSessionHasErrors('message');

    $this->post(route('pos.orders.store'), salePayload());
    expect(SmsLog::count())->toBe(0);
});

test('report and module pages render', function () {
    $this->post(route('pos.orders.store'), salePayload());

    $this->get(route('reports.sales', ['preset' => 'this_month']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Admin/Reports/Sales', false)
            ->where('summary.orders', 1)
            ->where('summary.net', 60000));

    foreach (['csv', 'excel', 'pdf'] as $format) {
        $this->get(route('reports.sales', ['export' => $format, 'section' => 'orders']))->assertOk();
    }

    $this->get(route('installments.index'))->assertOk();
    $this->get(route('installment-plans.index'))->assertOk();
    $this->get(route('emi.index'))->assertOk();
    $this->get(route('sms.dashboard'))->assertOk();
    $this->get(route('sms.compose'))->assertOk();
    $this->get(route('sms.logs'))->assertOk();
    $this->get(route('sms.settings'))->assertOk();
});

test('users without permission cannot open new modules', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('reports.sales'))->assertForbidden();
    $this->get(route('sms.settings'))->assertForbidden();
    $this->get(route('installments.index'))->assertForbidden();
});

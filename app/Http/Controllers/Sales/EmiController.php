<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\EmiBank;
use App\Models\EmiBankTenure;
use App\Models\EmiTransaction;
use App\Services\EmiService;
use App\Support\ReportExporter;
use App\Support\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmiController extends Controller
{
    protected function query(Request $request)
    {
        $branchId = session('current_branch_id');

        return EmiTransaction::query()
            ->with(['bank:id,name,short_name', 'order:id,invoice_no,customer_id,created_at', 'order.customer:id,name,phone'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($request->get('bank_id'), fn ($q, $v) => $q->where('emi_bank_id', $v))
            ->when($request->get('settlement_status'), fn ($q, $v) => $q->where('settlement_status', $v))
            ->when($request->get('months'), fn ($q, $v) => $q->where('months', $v))
            ->when($request->date('date_from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date('date_to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->filled('search'), function ($q) use ($request) {
                foreach (Search::tokens($request->get('search')) as $token) {
                    $like = Search::like($token);
                    $q->where(fn ($qq) => $qq->where('approval_code', 'like', $like)
                        ->orWhere('card_last4', 'like', $like)
                        ->orWhere('card_holder_name', 'like', $like)
                        ->orWhereHas('order', fn ($o) => $o->where('invoice_no', 'like', $like)
                            ->orWhereHas('customer', fn ($c) => Search::customers($c, $token))));
                }
            })
            ->latest('id');
    }

    public function index(Request $request)
    {
        if ($request->filled('export')) {
            $rows = $this->query($request)->get()->map(fn ($t) => [
                $t->created_at?->format('d-m-Y'),
                $t->order?->invoice_no,
                $t->order?->customer?->name ?? 'Walk-in',
                $t->bank?->name,
                $t->months,
                (float) $t->principal_amount,
                (float) $t->customer_charge_amount,
                (float) $t->card_amount,
                (float) $t->merchant_charge_amount,
                (float) $t->net_receivable,
                $t->approval_code,
                $t->settlement_status,
                $t->settled_amount !== null ? (float) $t->settled_amount : '',
            ])->all();

            return ReportExporter::download($request->get('export'), 'Bank EMI Transactions', [
                'Date', 'Invoice', 'Customer', 'Bank', 'Months', 'Principal', 'Cust. Charge', 'Card Amount', 'Bank Charge', 'Receivable', 'Approval', 'Settlement', 'Settled',
            ], $rows, [
                'Transactions' => count($rows),
                'Card Amount' => number_format(array_sum(array_column($rows, 7)), 2),
                'Receivable' => number_format(array_sum(array_column($rows, 9)), 2),
            ]);
        }

        $base = fn () => $this->query(new Request)->reorder();

        $byBank = EmiTransaction::query()
            ->join('emi_banks', 'emi_banks.id', '=', 'emi_transactions.emi_bank_id')
            ->when(session('current_branch_id'), fn ($q, $b) => $q->where('emi_transactions.branch_id', $b))
            ->where('emi_transactions.settlement_status', '!=', 'cancelled')
            ->groupBy('emi_banks.name')
            ->selectRaw('emi_banks.name, COUNT(*) as count, SUM(card_amount) as card_amount, SUM(net_receivable) as receivable')
            ->orderByDesc('card_amount')
            ->get();

        return Inertia::render('Admin/Emi/Index', [
            'transactions' => $this->query($request)->paginate(20)->withQueryString(),
            'banks' => EmiBank::with('tenures')->orderBy('name')->get(),
            'stats' => [
                'pending_count' => $base()->where('settlement_status', 'pending')->count(),
                'pending_receivable' => (float) $base()->where('settlement_status', 'pending')->sum('net_receivable'),
                'month_card_amount' => (float) $base()->where('settlement_status', '!=', 'cancelled')
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('card_amount'),
                'month_bank_charges' => (float) $base()->where('settlement_status', '!=', 'cancelled')
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('merchant_charge_amount'),
            ],
            'byBank' => $byBank,
            'filters' => $request->only(['bank_id', 'settlement_status', 'months', 'date_from', 'date_to', 'search']),
        ]);
    }

    public function quote(Request $request, EmiService $emi)
    {
        $data = $request->validate([
            'emi_bank_tenure_id' => ['required', 'exists:emi_bank_tenures,id'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        return response()->json($emi->quote(EmiBankTenure::findOrFail($data['emi_bank_tenure_id']), (float) $data['amount']));
    }

    public function settle(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:emi_transactions,id'],
            'settled_at' => ['required', 'date'],
            'settlement_ref' => ['nullable', 'string', 'max:100'],
            'settled_amount' => ['nullable', 'numeric', 'min:0'], // only honoured for a single transaction
        ]);

        DB::transaction(function () use ($data) {
            $single = count($data['ids']) === 1;
            EmiTransaction::whereIn('id', $data['ids'])->where('settlement_status', 'pending')->get()
                ->each(fn ($t) => $t->update([
                    'settlement_status' => 'settled',
                    'settled_at' => $data['settled_at'],
                    'settlement_ref' => $data['settlement_ref'] ?? null,
                    'settled_amount' => $single && isset($data['settled_amount']) ? $data['settled_amount'] : $t->net_receivable,
                ]));
        });

        return back()->with('success', 'Settlement recorded.');
    }

    public function unsettle(EmiTransaction $transaction)
    {
        $transaction->update(['settlement_status' => 'pending', 'settled_amount' => null, 'settled_at' => null, 'settlement_ref' => null]);

        return back()->with('success', 'Settlement reverted.');
    }

    protected function bankRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'tenures' => ['array'],
            'tenures.*.id' => ['nullable', 'integer'],
            'tenures.*.months' => ['required', 'integer', 'min:1', 'max:120', 'distinct'],
            'tenures.*.customer_charge_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tenures.*.merchant_charge_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tenures.*.min_amount' => ['nullable', 'numeric', 'min:0'],
            'tenures.*.is_active' => ['boolean'],
        ];
    }

    protected function syncTenures(EmiBank $bank, array $tenures): void
    {
        $keep = [];
        foreach ($tenures as $t) {
            $tenure = $bank->tenures()->updateOrCreate(
                ['months' => $t['months']],
                [
                    'customer_charge_percent' => $t['customer_charge_percent'] ?? 0,
                    'merchant_charge_percent' => $t['merchant_charge_percent'] ?? 0,
                    'min_amount' => $t['min_amount'] ?? 0,
                    'is_active' => $t['is_active'] ?? true,
                ]
            );
            $keep[] = $tenure->id;
        }
        // Tenures used by past transactions are deactivated rather than deleted
        $bank->tenures()->whereNotIn('id', $keep)->get()->each(function ($t) {
            EmiTransaction::where('emi_bank_tenure_id', $t->id)->exists() ? $t->update(['is_active' => false]) : $t->delete();
        });
    }

    public function storeBank(Request $request)
    {
        $data = $request->validate($this->bankRules());

        DB::transaction(function () use ($data) {
            $bank = EmiBank::create(collect($data)->except('tenures')->all());
            $this->syncTenures($bank, $data['tenures'] ?? []);
        });

        return back()->with('success', 'Bank saved.');
    }

    public function updateBank(Request $request, EmiBank $bank)
    {
        $data = $request->validate($this->bankRules());

        DB::transaction(function () use ($bank, $data) {
            $bank->update(collect($data)->except('tenures')->all());
            $this->syncTenures($bank, $data['tenures'] ?? []);
        });

        return back()->with('success', 'Bank updated.');
    }

    public function destroyBank(EmiBank $bank)
    {
        $bank->delete();

        return back()->with('success', 'Bank removed.');
    }
}

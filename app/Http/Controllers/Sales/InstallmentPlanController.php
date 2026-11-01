<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\InstallmentPlan;
use App\Services\InstallmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstallmentPlanController extends Controller
{
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:1000'],
            'number_of_installments' => ['required', 'integer', 'min:1', 'max:120'],
            'frequency' => ['required', 'in:'.implode(',', InstallmentService::FREQUENCIES)],
            'interval_days' => ['nullable', 'required_if:frequency,custom', 'integer', 'min:1', 'max:365'],
            'interest_type' => ['required', 'in:'.implode(',', InstallmentService::INTEREST_TYPES)],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'min_down_payment_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'processing_fee_type' => ['required', 'in:fixed,percent'],
            'processing_fee' => ['nullable', 'numeric', 'min:0'],
            'late_fee_type' => ['required', 'in:fixed,percent'],
            'late_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'grace_days' => ['nullable', 'integer', 'min:0', 'max:90'],
            'is_active' => ['boolean'],
        ];
    }

    protected function normalize(array $data): array
    {
        foreach (['interest_rate', 'min_down_payment_percent', 'processing_fee', 'late_fee_amount', 'grace_days'] as $k) {
            $data[$k] = $data[$k] ?? 0;
        }
        if (($data['frequency'] ?? null) !== 'custom') {
            $data['interval_days'] = null;
        }

        return $data;
    }

    public function index()
    {
        return Inertia::render('Admin/Installments/Plans', [
            'plans' => InstallmentPlan::orderByDesc('is_active')->orderBy('number_of_installments')->get(),
        ]);
    }

    public function store(Request $request)
    {
        InstallmentPlan::create($this->normalize($request->validate($this->rules())));

        return back()->with('success', __('Plan created.'));
    }

    public function update(Request $request, InstallmentPlan $plan)
    {
        $plan->update($this->normalize($request->validate($this->rules())));

        return back()->with('success', __('Plan updated.'));
    }

    public function destroy(InstallmentPlan $plan)
    {
        $plan->delete();

        return back()->with('success', __('Plan deleted.'));
    }
}

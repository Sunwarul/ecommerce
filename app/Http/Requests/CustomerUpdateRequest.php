<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_type' => ['required', 'in:Retailer,Wholesaler'],
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'mobile' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'tax_number' => ['nullable', 'string'],
            'currency_id' => ['nullable', 'string'],
            'status' => ['required'],
            'billing_address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'opening_balance' => ['required', 'string'],
            'opening_balance_date' => ['nullable', 'date'],
            'opening_balance_type' => ['required', 'in:to_pay,to_receive'],
            'credit_limit' => ['nullable', 'string'],
            'has_credit_limit' => ['required'],
            'photo' => ['nullable', 'string'],
            'file' => ['nullable', 'string'],
            'created_by' => ['nullable', 'string'],
        ];
    }
}

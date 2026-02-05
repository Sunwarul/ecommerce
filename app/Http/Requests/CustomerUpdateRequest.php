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
            'customer_type' => ['nullable', 'in:retailer,wholesaler'],
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'max:20', 'string'],
            'whatsapp_number' => ['nullable', 'string'],
            'tax_number' => ['nullable', 'string'],
            'currency_id' => ['nullable'],
            'status' => ['nullable'],
            'address' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'string'],
            'opening_balance_date' => ['nullable', 'date'],
            'opening_balance_type' => ['nullable', 'in:to_pay,to_receive'],
            'credit_limit' => ['nullable', 'string'],
            'has_credit_limit' => ['nullable'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:7048'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:7048'],
            'created_by' => ['nullable', 'string'],
        ];
    }
}

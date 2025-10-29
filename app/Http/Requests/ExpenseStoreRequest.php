<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseStoreRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'date' => ['required', 'date:Y-m-d'],
            'amount' => ['required', 'numeric'],
            'details' => ['nullable', 'string'],
            'status' => ['nullable'],
            'expense_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'attachment' => ['nullable', 'file', 'mimes:png,jpg,pdf,xlsx,csv'],
        ];
    }
}

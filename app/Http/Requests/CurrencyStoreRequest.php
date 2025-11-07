<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyStoreRequest extends FormRequest
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
            'short_code' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'symbol' => ['required', 'string'],
            'exchange_rate' => ['required', 'numeric'],
            'status' => ['nullable'],
        ];
    }
}

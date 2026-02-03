<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttributeValueStoreRequest extends FormRequest
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
            'attribute_id' => ['required', 'integer', 'exists:product_attributes.id,id'],
            'value' => ['required', 'string'],
            'display_value' => ['nullable', 'string'],
            'color_code' => ['nullable', 'string'],
            'deleted_at' => ['required'],
        ];
    }
}

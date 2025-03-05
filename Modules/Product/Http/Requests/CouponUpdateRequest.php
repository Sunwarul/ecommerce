<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'code' => 'required|min:6|unique:coupons,code' . $this->id,
            'categories' => 'nullable|string',
            'valid_from' => 'date',
            'valid_to' => 'required|date',
            'type' => 'string|nullable',
            'discount_type' => 'required',
            'amount' => 'required',
            'minimum_order' => 'required',
            'use_limit' => 'required',
            'is_active' => 'boolean',
        ];
    }
}

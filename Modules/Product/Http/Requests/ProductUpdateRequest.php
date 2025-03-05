<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'tax_id' => 'required|exists:taxes,id',
            'brand_id' => 'nullable|exists:brands,id',
            'tag_id' => 'nullable|exists:tags,id',
            'added_by' => 'nullable|exists:users,id',
            'product_name' => 'required' . $this->id,
            'price' => 'required',
            'discount_price' => 'nullable',
            'title' => 'nullable|string',
            'code' => 'nullable',
            'slug' => 'nullable',
            'dimantion' => 'nullable',
            'weight' => 'nullable',
            'sku' => 'nullable',
            'meterials' => 'nullable',
            'description' => 'nullable',
            'other_info' => 'nullable',
            'pro_photo' => 'nullable',
            'is_active' => 'nullable|boolean',
        ];
    }
}

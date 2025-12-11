<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? null;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'tax_id' => ['nullable', 'exists:taxes,id'],

            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:7048'],

            'images' => ['nullable', 'array'],
            'images.*' => ['string'],

            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')->ignore($productId),
            ],
            'code' => ['nullable', 'string', 'max:255'],

            'base_price' => ['required', 'numeric', 'min:0'],
            'base_discount_price' => ['nullable', 'numeric', 'min:0'],

            'stock_status' => ['required', 'string'],
            'stock_quantity' => ['nullable', 'numeric'],

            'type' => ['required', 'in:simple,variable'],
            'warehouse_id' => ['required_if:type,simple', 'nullable', 'exists:warehouses,id'],

            'weight' => ['nullable', 'numeric'],
            'dimensions' => ['nullable', 'array'],
            'materials' => ['nullable', 'array'],

            'description' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],

            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],

            'variations' => ['nullable', 'array'],
            'variations.*.sku' => ['required_if:type,variable', 'string'],
            'variations.*.price' => ['required_if:type,variable', 'numeric'],
            'variations.*.discount_price' => ['nullable', 'numeric'],
            'variations.*.stock_quantity' => ['required_if:type,variable', 'integer'],
            'variations.*.stock_status' => ['required_if:type,variable', 'string'],
            'variations.*.image' => ['nullable', 'string'],
            'variations.*.attribute_value_ids' => ['required_if:type,variable', 'array', 'min:1'],
            'variations.*.attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],
        ];
    }
}

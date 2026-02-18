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
        $product = $this->route('product');
        $productId = $product?->id;

        return [
            // Basic relations
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'tax_id' => ['nullable', 'exists:taxes,id'],

            // Basic info
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
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'code')->ignore($productId),
            ],

            // Pricing
            'base_price' => ['required', 'numeric', 'min:0'],
            'base_discount_price' => ['nullable', 'numeric', 'min:0'],

            // Product type
            'type' => ['required', 'in:simple,variable'],

            // ✅ SIMPLE: warehouse stocks
            'stocks' => ['required_if:type,simple', 'array', 'min:1'],
            'stocks.*.warehouse_id' => ['required', 'exists:warehouses,id'],
            'stocks.*.quantity' => ['required', 'numeric', 'min:0'],
            'stocks.*.alert_quantity' => ['nullable', 'numeric', 'min:0'],

            // Other fields
            'weight' => ['nullable', 'numeric'],
            'dimensions' => ['nullable', 'array'],
            'materials' => ['nullable', 'array'],
            'description' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],

            // SEO
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],

            // Tags
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],

            // ✅ VARIABLE: variations
            'variations' => ['required_if:type,variable', 'array', 'min:1'],

            // Optional variation id for update
            'variations.*.id' => [
                'nullable',
                'integer',
                Rule::exists('product_variations', 'id')->where(function ($q) use ($productId) {
                    return $q->where('product_id', $productId);
                }),
            ],

            'variations.*.sku' => ['required_if:type,variable', 'string', 'max:255'],
            'variations.*.price' => ['required_if:type,variable', 'numeric', 'min:0'],
            'variations.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.image' => ['nullable', 'string'],

            // attribute values
            'variations.*.attribute_value_ids' => ['required_if:type,variable', 'array', 'min:1'],
            'variations.*.attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],

            // ✅ VARIABLE: warehouse stocks per variation
            'variations.*.stocks' => ['required_if:type,variable', 'array', 'min:1'],
            'variations.*.stocks.*.warehouse_id' => ['required', 'exists:warehouses,id'],
            'variations.*.stocks.*.quantity' => ['required', 'numeric', 'min:0'],
            'variations.*.stocks.*.alert_quantity' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

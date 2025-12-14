<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Basic relations
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'tax_id' => ['nullable', 'exists:taxes,id'],

            // Basic info
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:7048'],

            'images' => ['nullable', 'array'],
            'images.*' => ['string'],

            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:products,barcode'],
            'code' => ['nullable', 'string', 'max:255', 'unique:products,code'],

            // Pricing
            'base_price' => ['required', 'numeric', 'min:0'],
            'base_discount_price' => ['nullable', 'numeric', 'min:0'],

            // Stock (for simple product)
            'stock_status' => ['required', 'string'],
            'stock_quantity' => ['nullable', 'numeric'],

            // Product type
            'type' => ['required', 'in:simple,variable'],

            // warehouse usage for simple product
            // 'warehouse_id' => ['required_if:type,simple', 'nullable', 'exists:warehouses,id'],

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

            // TAGS (must always be array when present)
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],

            // VARIATIONS (only when type = variable)
            'variations' => ['required_if:type,variable', 'array'],
            'variations.*.sku' => ['required_if:type,variable', 'string'],
            'variations.*.price' => ['required_if:type,variable', 'numeric'],
            'variations.*.discount_price' => ['nullable', 'numeric'],
            'variations.*.stock_quantity' => ['required_if:type,variable', 'integer'],
            'variations.*.stock_status' => ['required_if:type,variable', 'string'],
            'variations.*.image' => ['nullable', 'string'],

            // attribute_value_ids MUST be array, each id must be integer + exist
            'variations.*.attribute_value_ids' => ['required_if:type,variable', 'array', 'min:1'],
            'variations.*.attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],
        ];
    }
}

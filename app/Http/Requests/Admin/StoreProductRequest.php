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
            "category_id" => "required|exists:categories,id",
            "brand_id" => "nullable|exists:brands,id",
            "tax_id" => "nullable|exists:taxes,id",

            "name" => "required|string|max:255",
            "slug" => "nullable|string|max:255|unique:products,slug",
            "thumbnail" => "nullable|string",
            "images" => "nullable|array",
            "images.*" => "string",

            "sku" => "nullable|string|max:255|unique:products,sku",
            "barcode" => "nullable|string|max:255|unique:products,barcode",
            "code" => "nullable|string|max:255",

            "base_price" => "required|numeric|min:0",
            "base_discount_price" => "nullable|numeric|min:0",

            "stock_status" => "required|string",
            "stock_quantity" => "nullable|numeric",

            "type" => "required|in:simple,variable",

            "weight" => "nullable|numeric",
            "dimensions" => "nullable|array",
            "materials" => "nullable|array",

            "description" => "nullable|string",
            "additional_info" => "nullable|string",

            "is_active" => "nullable|boolean",

            "tag_ids" => "nullable|array",
            "tag_ids.*" => "exists:tags,id",

            // VARIATIONS IF VARIABLE
            "variations" => "required_if:type,variable|array",
            "variations.*.sku" => "required|string",
            "variations.*.price" => "required|numeric",
            "variations.*.discount_price" => "nullable|numeric",
            "variations.*.stock_quantity" => "required|integer",
            "variations.*.stock_status" => "required|string",
            "variations.*.image" => "nullable|string",
            "variations.*.attribute_value_ids" => "required|array|min:1",
            "variations.*.attribute_value_ids.*" => "exists:product_attribute_values,id",
        ];
    }
}

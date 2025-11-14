<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'category_id' => 'nullable|exists:categories,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'brand_id' => 'nullable|exists:brands,id',

            'name' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($productId)],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('products', 'code')->ignore($productId)],

            'base_price' => 'sometimes|required|numeric|min:0',
            'base_discount_price' => 'nullable|numeric|min:0|lt:base_price',

            'stock_quantity' => 'nullable|integer|min:0',
            'stock_status' => ['nullable', Rule::in(['in_stock', 'out_of_stock', 'pre_order'])],

            'type' => ['sometimes', 'required', Rule::in(['simple', 'variable'])],

            'weight' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'materials' => 'nullable|string|max:255',

            'description' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'is_active' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'base_price.required' => 'The product price is required.',
            'base_price.min' => 'The product price must be at least 0.',
            'base_discount_price.lt' => 'The discount price must be less than the regular price.',
            'slug.unique' => 'This product slug is already in use.',
            'sku.unique' => 'This SKU is already in use by another product.',
            'barcode.unique' => 'This barcode is already in use by another product.',
            'code.unique' => 'This product code is already in use.',
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.mimes' => 'The thumbnail must be a file of type: jpeg, png, jpg, gif.',
            'thumbnail.max' => 'The thumbnail may not be greater than 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name') && ! $this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }

        if ($this->has('is_active') && is_string($this->is_active)) {
            $this->merge([
                'is_active' => $this->is_active === 'true' || $this->is_active === '1',
            ]);
        }
    }
}

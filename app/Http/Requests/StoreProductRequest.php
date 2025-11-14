<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // adjust if you use policies/permissions
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'has_variants' => ['sometimes', 'boolean'],
            'base_unit_id' => ['nullable', 'integer', 'exists:units,id'],
            'tax_class_id' => ['nullable', 'integer', 'exists:tax_classes,id'],
            'meta_json' => ['nullable', 'array'],
            // attributes used on this product
            'attribute_ids' => ['nullable', 'array'],
            'attribute_ids.*' => ['integer', 'exists:attributes,id'],
            // optional images (if you handle uploads here)
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:2048'], // 2MB each (change if needed)
// OPTIONAL: SKUs + inventory (for product with variants)
            'skus' => ['nullable', 'array'],
            'skus.*.code' => ['required_with:skus', 'string', 'max:255'],
            'skus.*.barcode' => ['nullable', 'string', 'max:255'],
            'skus.*.price' => ['required_with:skus', 'numeric', 'min:0'],
            'skus.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.currency' => ['nullable', 'string', 'size:3'],
            'skus.*.is_active' => ['sometimes', 'boolean'],
            // SKU attribute values (variant combo)
            'skus.*.attribute_value_ids' => ['nullable', 'array'],
            'skus.*.attribute_value_ids.*' => ['integer', 'exists:attribute_values,id'],
            // initial inventory
            'skus.*.inventory' => ['nullable', 'array'],

            'skus.*.inventory.warehouse_id' => [
                'required_with:skus.*.inventory',
                'integer',
                'exists:warehouses,id'
            ],
            'skus.*.inventory.qty_on_hand' => [
                'required_with:skus.*.inventory',
                'integer'
            ],
            'skus.*.inventory.low_stock_threshold' => ['nullable', 'integer'],
        ];
    }
    public function messages(): array
    {
        return [
            'skus.*.code.required_with' => 'Each SKU must have a code.',
            'skus.*.price.required_with' => 'Each SKU must have a price.',
            'skus.*.inventory.warehouse_id.required_with' => 'Warehouse is required when setting
inventory.',
            'skus.*.inventory.qty_on_hand.required_with' => 'Initial quantity is required when
setting inventory.',
        ];
    }
}
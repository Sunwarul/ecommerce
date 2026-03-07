<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductStock;
use App\Models\Tax;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder5 extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCatalogue();
        });
    }

    private function seedCatalogue()
    {
        $data = $this->getProductData();
        $productsData = $data['data'] ?? [];

        $tax = Tax::first();
        $warehouses = Warehouse::take(2)->get();

        // Handle Brand
        $brandName = 'Whirlpool';
        $brand = Brand::withTrashed()->where('name', $brandName)->first();
        if (! $brand) {
            $brand = Brand::create(['name' => $brandName, 'is_active' => true]);
        } elseif ($brand->trashed()) {
            $brand->restore();
        }

        // Handle Category
        $categoryName = 'Microwave Oven';
        $slug = 'microwave-oven';
        $category = Category::withTrashed()->where('slug', $slug)->first();
        if (! $category) {
            $category = Category::create([
                'name' => $categoryName,
                'slug' => $slug,
                'parent_id' => 33, // Kitchen Appliances from CategorySeeder
                'is_active' => true,
            ]);
        } elseif ($category->trashed()) {
            $category->restore();
        }

        foreach ($productsData as $item) {
            $matCode = $item['mat_code'];
            $name = $item['material_description'];
            $sku = "WHIRLPOOL-{$matCode}";

            $mrp = (float) ($item['mrp'] ?? 0);
            $mop = (float) ($item['mop'] ?? 0);
            $capacity = $item['capacity'] ?? 'N/A';

            // Check existence (including soft deleted)
            $existingProduct = Product::withTrashed()->where('sku', $sku)->first();
            if ($existingProduct) {
                if ($existingProduct->trashed()) {
                    $existingProduct->restore();
                }
                $existingProduct->update([
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'tax_id' => $tax?->id,
                    'name' => $name,
                    'base_price' => $mrp,
                    'base_discount_price' => $mop,
                    'description' => "<ul><li><strong>Capacity:</strong> {$capacity}</li><li><strong>Model:</strong> {$matCode}</li></ul>",
                    'additional_info' => json_encode($item),
                    'is_active' => true,
                ]);
                $product = $existingProduct;
            } else {
                $productSlug = Str::slug($name);
                if (Product::withTrashed()->where('slug', $productSlug)->exists()) {
                    $productSlug .= '-'.Str::random(4);
                }

                $product = Product::create([
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'tax_id' => $tax?->id,
                    'name' => $name,
                    'slug' => $productSlug,
                    'sku' => $sku,
                    'barcode' => (string) $matCode,
                    'code' => (string) $matCode,
                    'base_price' => $mrp,
                    'base_discount_price' => $mop,
                    'type' => 'simple',
                    'description' => "<ul><li><strong>Capacity:</strong> {$capacity}</li><li><strong>Model:</strong> {$matCode}</li></ul>",
                    'additional_info' => json_encode($item),
                    'is_active' => true,
                ]);
            }

            // Dynamic Attributes
            $attributesToSeed = [
                'capacity' => 'Capacity',
            ];

            $attrValues = [
                'capacity' => $capacity,
            ];

            foreach ($attributesToSeed as $key => $attrName) {
                $value = $attrValues[$key] ?? '';
                if (empty($value) || $value === 'N/A') {
                    continue;
                }

                // Create Attribute
                $attribute = ProductAttribute::withTrashed()->where('name', Str::slug($attrName))->first();
                if (! $attribute) {
                    $attribute = ProductAttribute::create([
                        'name' => Str::slug($attrName),
                        'display_name' => $attrName,
                        'type' => 'text',
                        'is_active' => true,
                    ]);
                } elseif ($attribute->trashed()) {
                    $attribute->restore();
                }

                // Create Value
                $attributeValue = ProductAttributeValue::withTrashed()
                    ->where('attribute_id', $attribute->id)
                    ->where('value', (string) $value)
                    ->first();

                if (! $attributeValue) {
                    $attributeValue = ProductAttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => (string) $value,
                        'display_value' => (string) $value,
                    ]);
                } elseif ($attributeValue->trashed()) {
                    $attributeValue->restore();
                }

                // Attach to Simple Product (variation_id = null)
                $exists = DB::table('product_variation_attributes')
                    ->where('product_id', $product->id)
                    ->where('attribute_id', $attribute->id)
                    ->where('attribute_value_id', $attributeValue->id)
                    ->whereNull('variation_id')
                    ->exists();

                if (! $exists) {
                    $product->attributes()->attach($attribute->id, [
                        'attribute_value_id' => $attributeValue->id,
                        'variation_id' => null,
                    ]);
                }
            }

            // Stock
            foreach ($warehouses as $warehouse) {
                $stockExists = ProductStock::where('product_id', $product->id)
                    ->where('warehouse_id', $warehouse->id)
                    ->whereNull('variation_id')
                    ->exists();

                if (! $stockExists) {
                    ProductStock::create([
                        'branch_id' => 1,
                        'product_id' => $product->id,
                        'variation_id' => null,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => rand(5, 20),
                        'alert_quantity' => 3,
                    ]);
                }
            }
        }
    }

    private function getProductData(): array
    {

        return [
            'title' => 'Microwave Oven',
            'headers' => [
                'mat_code' => 'MAT CODE',
                'cat' => 'Cat',
                'material_description' => 'Material Description',
                'capacity' => 'Capacity',
                'mrp' => 'MRP',
                'qty' => 'QTY',
                'total_mrp' => 'TOTAL MRP',
                'discount_percent' => 'Discount %',
                'discount_tk' => 'Discount (Tk.)',
                'mop' => 'MOP',
            ],
            'data' => [
                [
                    'mat_code' => 50039,
                    'cat' => 'MW',
                    'material_description' => 'MAGICOOK 30L BLACK MIRROR',
                    'capacity' => '30 L',
                    'mrp' => 29490,
                    'qty' => 1,
                    'total_mrp' => 29490,
                    'discount_percent' => '27%',
                    'discount_tk' => 7962,
                    'mop' => 21528,
                ],
                [
                    'mat_code' => 50093,
                    'cat' => 'MW-Air Fryer',
                    'material_description' => 'Magicook Pro 31CES Rotisserie BD',
                    'capacity' => '29 L',
                    'mrp' => 32990,
                    'qty' => 1,
                    'total_mrp' => 32990,
                    'discount_percent' => '27%',
                    'discount_tk' => 8907,
                    'mop' => 24083,
                ],
                [
                    'mat_code' => 50094,
                    'cat' => 'MW',
                    'material_description' => '26CE',
                    'capacity' => '24 L',
                    'mrp' => 21990,
                    'qty' => 1,
                    'total_mrp' => 21990,
                    'discount_percent' => '27%',
                    'discount_tk' => 5937,
                    'mop' => 16053,
                ],
                [
                    'mat_code' => 50092,
                    'cat' => 'MW',
                    'material_description' => 'Whirlpool Supremechef 35L',
                    'capacity' => '35 L',
                    'mrp' => 61990,
                    'qty' => 1,
                    'total_mrp' => 61990,
                    'discount_percent' => '27%',
                    'discount_tk' => 16737,
                    'mop' => 45253,
                ],
                [
                    'mat_code' => 50054,
                    'cat' => 'MW',
                    'material_description' => 'MAGICOOK PRO 30 GE (GRILL)',
                    'capacity' => '30 L',
                    'mrp' => 21990,
                    'qty' => 1,
                    'total_mrp' => 21990,
                    'discount_percent' => '28%',
                    'discount_tk' => 6157,
                    'mop' => 15833,
                ],
                [
                    'mat_code' => 50050,
                    'cat' => 'MW',
                    'material_description' => 'MAGICOOK PRO 25GE BLACK',
                    'capacity' => '25 L',
                    'mrp' => 18990,
                    'qty' => 1,
                    'total_mrp' => 18990,
                    'discount_percent' => '28%',
                    'discount_tk' => 5317,
                    'mop' => 13673,
                ],
                [
                    'mat_code' => 50031,
                    'cat' => 'MW',
                    'material_description' => 'MAGICOOK 20L CLASSIC KNOB BLACK',
                    'capacity' => '20 L',
                    'mrp' => 13690,
                    'qty' => 1,
                    'total_mrp' => 13690,
                    'discount_percent' => '30%',
                    'discount_tk' => 4107,
                    'mop' => 9583,
                ],
                [
                    'mat_code' => 50015,
                    'cat' => 'MW',
                    'material_description' => 'MAGICOOK 20L CLASSIC-BLACK(NEW)',
                    'capacity' => '20 L',
                    'mrp' => 14690,
                    'qty' => 1,
                    'total_mrp' => 14690,
                    'discount_percent' => '30%',
                    'discount_tk' => 4407,
                    'mop' => 10283,
                ],
            ],
        ];
    }
}

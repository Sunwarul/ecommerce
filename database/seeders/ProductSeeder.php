<?php

namespace Database\Seeders;

use App\Models\{
    Product,
    ProductVariation,
    ProductStock,
    ProductAttribute,
    ProductAttributeValue,
    Category,
    Brand,
    Tax,
    Tag,
    Warehouse
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $category = Category::first();
            $brand = Brand::first();
            $tax = Tax::first();
            $tags = Tag::take(3)->pluck('id')->toArray();
            $warehouses = Warehouse::take(2)->get();

            if (!$category || !$warehouses->count()) {
                $this->command->warn('Missing category or warehouses. Seeder skipped.');
                return;
            }

            /* =========================================================
             | SIMPLE PRODUCT (all fields + stocks)
             |=========================================================*/
            $simpleProduct = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand?->id,
                'tax_id' => $tax?->id,

                'name' => 'Simple Cotton T-Shirt',
                'slug' => Str::slug('Simple Cotton T-Shirt'),
                'thumbnail' => null,
                'images' => [
                    'products/gallery/simple-1.jpg',
                    'products/gallery/simple-2.jpg',
                ],

                'sku' => 'SIMPLE-TSHIRT-001',
                'barcode' => '123456789001',
                'code' => 'TSHIRT-001',

                'base_price' => 25,
                'base_discount_price' => 20,

                'type' => 'simple',

                'weight' => 0.35,
                'dimensions' => ['length' => 30, 'width' => 20, 'height' => 2],
                'materials' => ['Cotton', 'Polyester'],

                'description' => '<p>High quality simple cotton t-shirt.</p>',
                'additional_info' => '<p>Wash cold, do not bleach.</p>',

                'is_active' => true,

                'meta_title' => 'Simple Cotton T-Shirt',
                'meta_description' => 'A premium simple cotton t-shirt.',
                'meta_keywords' => 'tshirt,cotton,simple',
            ]);

            // Tags
            if (!empty($tags)) {
                $simpleProduct->tags()->sync($tags);
            }

            // Stocks (simple: variation_id = null)
            foreach ($warehouses as $warehouse) {
                ProductStock::create([
                    'product_id' => $simpleProduct->id,
                    'variation_id' => null,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => rand(20, 50),
                    'alert_quantity' => 5,
                ]);
            }

            /* =========================================================
             | VARIABLE PRODUCT (all fields + variations + pivot + stocks)
             |=========================================================*/

            // Attributes
            $colorAttr = ProductAttribute::firstOrCreate(
                ['name' => 'color'],
                ['display_name' => 'Color', 'type' => 'select', 'is_active' => true]
            );

            $sizeAttr = ProductAttribute::firstOrCreate(
                ['name' => 'size'],
                ['display_name' => 'Size', 'type' => 'select', 'is_active' => true]
            );

            // Attribute Values
            $red = ProductAttributeValue::firstOrCreate([
                'attribute_id' => $colorAttr->id,
                'value' => 'red',
                'display_value' => 'Red',
            ]);

            $blue = ProductAttributeValue::firstOrCreate([
                'attribute_id' => $colorAttr->id,
                'value' => 'blue',
                'display_value' => 'Blue',
            ]);

            $small = ProductAttributeValue::firstOrCreate([
                'attribute_id' => $sizeAttr->id,
                'value' => 'S',
                'display_value' => 'Small',
            ]);

            $medium = ProductAttributeValue::firstOrCreate([
                'attribute_id' => $sizeAttr->id,
                'value' => 'M',
                'display_value' => 'Medium',
            ]);

            // Variable Product
            $variableProduct = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand?->id,
                'tax_id' => $tax?->id,

                'name' => 'Variable Hoodie',
                'slug' => Str::slug('Variable Hoodie'),
                'thumbnail' => null,
                'images' => [
                    'products/gallery/hoodie-1.jpg',
                    'products/gallery/hoodie-2.jpg',
                ],

                'sku' => 'HOODIE-MASTER-001',
                'barcode' => '123456789002',
                'code' => 'HOODIE-001',

                'base_price' => 60,
                'base_discount_price' => 55,

                'type' => 'variable',

                'weight' => 1.2,
                'dimensions' => ['length' => 40, 'width' => 35, 'height' => 8],
                'materials' => ['Cotton', 'Fleece'],

                'description' => '<p>Comfortable hoodie with multiple variations.</p>',
                'additional_info' => '<p>Unisex. Warm and soft.</p>',

                'is_active' => true,

                'meta_title' => 'Variable Hoodie',
                'meta_description' => 'A hoodie with size & color options.',
                'meta_keywords' => 'hoodie,variable,size,color',
            ]);

            // Tags
            if (!empty($tags)) {
                $variableProduct->tags()->sync($tags);
            }

            // Variations combinations
            $combinations = [
                [$red, $small],
                [$red, $medium],
                [$blue, $small],
                [$blue, $medium],
            ];

            foreach ($combinations as $combo) {
                $skuParts = collect($combo)->pluck('value')->implode('-');

                $variation = ProductVariation::create([
                    'product_id' => $variableProduct->id,
                    'sku' => 'HOODIE-' . strtoupper($skuParts),
                    'price' => 60,
                    'discount_price' => 55,
                    'image' => null,
                    'is_active' => true,
                ]);

                // ✅ Pivot attach (must include product_id because your table requires it)
                $attachData = [];
                foreach ($combo as $value) {
                    $attachData[$value->id] = [
                        'attribute_id' => $value->attribute_id,
                        'product_id' => $variableProduct->id,
                    ];
                }
                $variation->attributeValues()->attach($attachData);

                // Variation stocks per warehouse
                foreach ($warehouses as $warehouse) {
                    ProductStock::create([
                        'product_id' => $variableProduct->id,
                        'variation_id' => $variation->id,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => rand(5, 15),
                        'alert_quantity' => 3,
                    ]);
                }
            }
        });
    }
}

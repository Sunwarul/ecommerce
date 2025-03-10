<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\ProductAttributeValue;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory(20)->create();
        $colorAttribute = ProductAttribute::create([
            'name' => 'color',
            'display_name' => 'Color',
            'type' => 'color'
        ]);

        $sizeAttribute = ProductAttribute::create([
            'name' => 'size',
            'display_name' => 'Size',
            'type' => 'select'
        ]);

        $colors = [
            ['value' => 'red', 'display_value' => 'Ruby Red', 'color_code' => '#FF0000'],
            ['value' => 'blue', 'display_value' => 'Ocean Blue', 'color_code' => '#0000FF'],
            ['value' => 'black', 'display_value' => 'Jet Black', 'color_code' => '#000000'],
        ];

        $colorValues = collect();
        foreach ($colors as $color) {
            $colorValues->push(ProductAttributeValue::create([
                'attribute_id' => $colorAttribute->id,
                ...$color
            ]));
        }

        $sizes = [
            ['value' => 'S', 'display_value' => 'Small'],
            ['value' => 'M', 'display_value' => 'Medium'],
            ['value' => 'L', 'display_value' => 'Large'],
            ['value' => 'XL', 'display_value' => 'Extra Large'],
        ];

        $sizeValues = collect();
        foreach ($sizes as $size) {
            $sizeValues->push(ProductAttributeValue::create([
                'attribute_id' => $sizeAttribute->id,
                ...$size
            ]));
        }

        $product = Product::create([
            'name' => 'Classic T-Shirt',
            'slug' => Str::slug('Classic T-Shirt'),
            'type' => 'variable',
            'base_price' => 19.99,
            'description' => 'A comfortable classic t-shirt available in multiple colors and sizes.',
            'meta_title' => 'Classic T-Shirt | Your Store',
            'meta_description' => 'Shop our classic t-shirt available in multiple colors and sizes.',
            'is_active' => true,
        ]);

        // $product = Product::first();
        // $colorValues = ProductAttributeValue::where('attribute_id', 1)->get();
        // $sizeValues = ProductAttributeValue::where('attribute_id', 2)->get();
        foreach ($colorValues as $color) {
            foreach ($sizeValues as $size) {
                $sku = sprintf('TSH-%s-%s', strtoupper($color->value), $size->value);

                $variation = ProductVariation::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'price' => 19.99,
                    'stock_quantity' => 100,
                    'stock_status' => 'in_stock',
                    'is_active' => true,
                ]);

                // Attach color and size attributes to the variation
                $variation->attributeValues()->attach([
                    $color->id,
                    $size->id
                ]);
            }
        }
    }
}
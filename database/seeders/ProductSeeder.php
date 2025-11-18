<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Tag;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------
        // FETCH REQUIRED EXISTING DATA
        // --------------------------------------------

        $category = Category::first();
        $brand = Brand::first();
        $tax = \App\Models\Tax::first();
        $tags = Tag::take(3)->pluck('id')->toArray();
        $userId = 1; // created_by

        // Attributes (created from your ProductAttributeSeeder)
        $colorAttr = ProductAttribute::where('name', 'color')->first();
        $sizeAttr = ProductAttribute::where('name', 'size')->first();

        $red = ProductAttributeValue::where('value', 'red')->first();
        $blue = ProductAttributeValue::where('value', 'blue')->first();
        $sizeS = ProductAttributeValue::where('value', 'S')->first();
        $sizeM = ProductAttributeValue::where('value', 'M')->first();

        $warehouse = Warehouse::first();

        // --------------------------------------------
        // SIMPLE PRODUCT
        // --------------------------------------------

        $simpleProduct = Product::create([
            'category_id' => $category->id,
            'tax_id' => $tax->id ?? null,
            'brand_id' => $brand->id ?? null,
            'created_by' => $userId,

            'name' => 'Basic T-Shirt',
            'slug' => Str::slug('Basic T-Shirt'),
            'thumbnail' => 'products/tshirt/thumb.jpg',
            'images' => ['products/tshirt/img1.jpg'],

            'sku' => 'TSHIRT-1001',
            'barcode' => '100200300400',
            'code' => 'BT001',

            'base_price' => 500,
            'base_discount_price' => 450,

            'stock_quantity' => 100,
            'stock_status' => 'in_stock',
            'type' => 'simple',

            'weight' => 0.25,
            'dimensions' => ['length' => 30, 'width' => 25, 'height' => 2],
            'materials' => ['cotton'],

            'description' => 'High-quality cotton basic T-shirt.',
            'additional_info' => 'Machine wash cold.',
            'is_active' => true,
        ]);

        // Simple Stock Entry
        ProductStock::create([
            'product_id' => $simpleProduct->id,
            'warehouse_id' => $warehouse?->id ?? 1,
            'quantity' => 100,
            'alert_quantity' => 10,
        ]);

        // Sync some tags
        $simpleProduct->tags()->sync($tags);

        // --------------------------------------------
        // VARIABLE PRODUCT
        // --------------------------------------------

        $variableProduct = Product::create([
            'category_id' => $category->id,
            'tax_id' => $tax->id ?? null,
            'brand_id' => $brand->id ?? null,
            'created_by' => $userId,

            'name' => 'Premium Hoodie',
            'slug' => Str::slug('Premium Hoodie'),
            'thumbnail' => 'products/hoodie/thumb.jpg',
            'images' => ['products/hoodie/img1.jpg', 'products/hoodie/img2.jpg'],

            'sku' => 'HOODIE-2001',
            'barcode' => '200300400500',
            'code' => 'PH001',

            'base_price' => 2000,
            'base_discount_price' => 1800,

            'stock_quantity' => 0, // variations track stock
            'stock_status' => 'in_stock',
            'type' => 'variable',

            'weight' => 0.80,
            'dimensions' => ['length' => 40, 'width' => 30, 'height' => 10],
            'materials' => ['cotton', 'polyester'],

            'description' => 'Soft and warm premium hoodie.',
            'additional_info' => 'Available in multiple sizes/colors.',
            'is_active' => true,
        ]);

        $variableProduct->tags()->sync($tags);

        // --------------------------------------------
        // VARIATION 1: Red / Size S
        // --------------------------------------------

        $v1 = ProductVariation::create([
            'product_id' => $variableProduct->id,
            'sku' => 'HOODIE-RED-S',
            'price' => 2000,
            'discount_price' => 1800,
            'stock_quantity' => 25,
            'stock_status' => 'in_stock',
            'image' => 'products/hoodie/red-s.jpg',
            'is_active' => true,
        ]);

        $v1->attributeValues()->attach([
            $red->id => ['attribute_id' => $colorAttr->id, 'product_id' => $variableProduct->id],
            $sizeS->id => ['attribute_id' => $sizeAttr->id, 'product_id' => $variableProduct->id],
        ]);

        // --------------------------------------------
        // VARIATION 2: Red / Size M
        // --------------------------------------------

        $v2 = ProductVariation::create([
            'product_id' => $variableProduct->id,
            'sku' => 'HOODIE-RED-M',
            'price' => 2000,
            'discount_price' => 1800,
            'stock_quantity' => 15,
            'stock_status' => 'in_stock',
            'image' => 'products/hoodie/red-m.jpg',
            'is_active' => true,
        ]);

        $v2->attributeValues()->attach([
            $red->id => ['attribute_id' => $colorAttr->id, 'product_id' => $variableProduct->id],
            $sizeM->id => ['attribute_id' => $sizeAttr->id, 'product_id' => $variableProduct->id],
        ]);

        // --------------------------------------------
        // VARIATION 3: Blue / Size M
        // --------------------------------------------

        $v3 = ProductVariation::create([
            'product_id' => $variableProduct->id,
            'sku' => 'HOODIE-BLUE-M',
            'price' => 2000,
            'discount_price' => null,
            'stock_quantity' => 10,
            'stock_status' => 'in_stock',
            'image' => 'products/hoodie/blue-m.jpg',
            'is_active' => true,
        ]);

        $v3->attributeValues()->attach([
            $blue->id => ['attribute_id' => $colorAttr->id, 'product_id' => $variableProduct->id],
            $sizeM->id => ['attribute_id' => $sizeAttr->id, 'product_id' => $variableProduct->id],
        ]);
    }
}

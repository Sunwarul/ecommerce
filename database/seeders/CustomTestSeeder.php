<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Tag;
use App\Utils\FakeImageUtil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedCustom();
    }

    private function seedCustom(): void
    {
        Category::create([
            'name' => 'Electronics',
            'is_active' => true,
            'photo' => fake()->imageUrl(),
        ]);

        Brand::create([
            'name' => 'Apple',
            'is_active' => true,
            'photo' => fake()->imageUrl(),
        ]);
        Brand::create([
            'name' => 'Samsung',
            'is_active' => true,
            'photo' => fake()->imageUrl(),
        ]);
        Brand::create([
            'name' => 'Dell',
            'is_active' => true,
            'photo' => fake()->imageUrl(),
        ]);

        Tag::create([
            'name' => 'New',
            'is_active' => true,
        ]);
        Tag::create([
            'name' => 'Popular',
            'is_active' => true,
        ]);
        Tag::create([
            'name' => 'Trending',
            'is_active' => true,
        ]);

        $productAttr = [
            'category_id' => 1,
            'brand_id' => 1,
            'name' => 'iPhone 13',
            'description' => 'The iPhone 13 is a smartphone designed, developed, and marketed by Apple Inc. It is the fourteenth generation of the iPhone, succeeding the iPhone 12 and preceding the iPhone 14.',
            'base_price' => 999.99,
            'stock_status' => 'in_stock',
            'is_active' => true,
            'thumbnail' => FakeImageUtil::imagePath(),
            'images' => [
                FakeImageUtil::imagePath(),
                FakeImageUtil::imagePath(),
                FakeImageUtil::imagePath(),
            ],
        ];
        $productAttr['slug'] = Str::slug($productAttr['name']);
        $productAttr['sku'] = 'IP13'.random_int(1000, 9999);
        $productAttr['barcode'] = 'IP13'.random_int(1000, 9999);
        $productAttr['code'] = 'IP13'.random_int(1000, 9999);
        $product = Product::create($productAttr);
        $product->tags()->attach([Tag::first()->id, Tag::skip(1)->first()->id, Tag::skip(2)->first()->id]);
    }
}

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

class ProductSeeder2 extends Seeder
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

        // Handle Brand
        $brand = Brand::withTrashed()->where('name', 'Haier')->first();
        if (!$brand) {
            $brand = Brand::create(['name' => 'Haier', 'is_active' => true]);
        } elseif ($brand->trashed()) {
            $brand->restore();
        }

        $tax = Tax::first();
        $warehouses = Warehouse::take(2)->get();

        foreach ($data as $categoryKey => $products) {
            $categoryName = "Air Conditioner"; // Specific for this seeder
            $slug = Str::slug($categoryName);

            // Handle Category with Soft Deletes
            $category = Category::withTrashed()->where('slug', $slug)->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $categoryName,
                    'slug' => $slug,
                    'is_active' => true
                ]);
            } elseif ($category->trashed()) {
                $category->restore();
            }

            foreach ($products as $modelName => $item) {
                $name = "Haier {$modelName}";
                $sku = "HAIER-" . Str::upper(Str::slug($modelName));

                // Check existence (including soft deleted)
                $existingProduct = Product::withTrashed()->where('sku', $sku)->first();
                if ($existingProduct) {
                    if ($existingProduct->trashed()) {
                        $existingProduct->restore();
                    }
                    $product = $existingProduct;
                } else {
                    $mrp = $item['mrp'] ?? 0;
                    $netPrice = $item['net_price'] ?? null;

                    $productSlug = Str::slug($name);
                    if (Product::withTrashed()->where('slug', $productSlug)->exists()) {
                        $productSlug .= '-' . Str::random(4);
                    }

                    $product = Product::create([
                        'category_id' => $category->id,
                        'brand_id' => $brand->id,
                        'tax_id' => $tax?->id,
                        'name' => $name,
                        'slug' => $productSlug,
                        'sku' => $sku,
                        'barcode' => $sku,
                        'code' => $modelName,
                        'base_price' => $mrp,
                        'base_discount_price' => $netPrice,
                        'type' => 'simple',
                        'description' => "<ul><li><strong>Capacity:</strong> {$item['capacity']}</li><li><strong>Type:</strong> {$item['category']}</li></ul>",
                        'additional_info' => json_encode($item),
                        'is_active' => true,
                    ]);
                }

                // Dynamic Attributes
                $attributesToSeed = [
                    'category' => 'AC Type',
                    'capacity' => 'Capacity',
                ];

                foreach ($attributesToSeed as $key => $attrName) {
                    $value = $item[$key] ?? '';
                    if (empty($value)) continue;

                    // Create Attribute
                    $attribute = ProductAttribute::withTrashed()->where('name', Str::slug($attrName))->first();
                    if (!$attribute) {
                        $attribute = ProductAttribute::create([
                            'name' => Str::slug($attrName),
                            'display_name' => $attrName,
                            'type' => 'text',
                            'is_active' => true
                        ]);
                    } elseif ($attribute->trashed()) {
                        $attribute->restore();
                    }

                    // Create Value
                    $attributeValue = ProductAttributeValue::withTrashed()
                        ->where('attribute_id', $attribute->id)
                        ->where('value', (string)$value)
                        ->first();

                    if (!$attributeValue) {
                        $attributeValue = ProductAttributeValue::create([
                            'attribute_id' => $attribute->id,
                            'value' => (string)$value,
                            'display_value' => (string)$value
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

                    if (!$exists) {
                        $product->attributes()->attach($attribute->id, [
                            'attribute_value_id' => $attributeValue->id,
                            'variation_id' => null
                        ]);
                    }
                }

                // Stock
                foreach ($warehouses as $warehouse) {
                    $stockExists = ProductStock::where('product_id', $product->id)
                        ->where('warehouse_id', $warehouse->id)
                        ->whereNull('variation_id')
                        ->exists();

                    if (!$stockExists) {
                        ProductStock::create([
                            'branch_id' => 1,
                            'product_id' => $product->id,
                            'variation_id' => null,
                            'warehouse_id' => $warehouse->id,
                            'quantity' => rand(5, 20),
                            'alert_quantity' => 2,
                        ]);
                    }
                }
            }
        }
    }

    private function getProductData(): array
    {
        return [
            'air_conditioners' => [
                "HSU-24UVCool:(INV)(Pro)-WiFi*UV" => [
                    "category" => "INV",
                    "capacity" => "2.0 TON",
                    "mrp" => 94990,
                    "discount_amount" => 22490,
                    "net_price" => 72500
                ],
                "HSU-24AntirustCool:(INV)(Pro)-WiFi" => [
                    "category" => "INV",
                    "capacity" => "2.0 TON",
                    "mrp" => 92990,
                    "discount_amount" => 21990,
                    "net_price" => 71000
                ],
                "HSU-24CleanCool:(INV)(Pro)" => [
                    "category" => "INV",
                    "capacity" => "2.0 TON",
                    "mrp" => 91990,
                    "discount_amount" => 21490,
                    "net_price" => 70500
                ],
                "HSU-24EnergyCool:(INV)(Pro)" => [
                    "category" => "INV",
                    "capacity" => "2.0 TON",
                    "mrp" => 91990,
                    "discount_amount" => 21490,
                    "net_price" => 70500
                ],
                "HSU-19UltimateCool:(INV)(Pro)/T3-WiFi*UV" => [
                    "category" => "INV",
                    "capacity" => "1.6 TON",
                    "mrp" => 99990,
                    "discount_amount" => 19490,
                    "net_price" => 80500
                ],
                "HSU-19PuriCool:(INV)(WiFi)(IFD)(Jade)*AirPurifier" => [
                    "category" => "INV",
                    "capacity" => "1.6 TON",
                    "mrp" => 99990,
                    "discount_amount" => 19490,
                    "net_price" => 80500
                ],
                "HSU-19HeatCool:(INV)(Pro)-WiFi" => [
                    "category" => "INV",
                    "capacity" => "1.6 TON",
                    "mrp" => 78990,
                    "discount_amount" => 12490,
                    "net_price" => 66500
                ],
                "HSU-19UVCool:(INV)(Pro)-WiFi*UV" => [
                    "category" => "INV",
                    "capacity" => "1.5 Ton",
                    "mrp" => 77990,
                    "discount_amount" => 15490,
                    "net_price" => 62500
                ],
                "HSU-19AntirustCool:(INV)(Pro)-WiFi" => [
                    "category" => "INV",
                    "capacity" => "1.6 TON",
                    "mrp" => 75990,
                    "discount_amount" => 16990,
                    "net_price" => 59000
                ],
                "HSU-19CleanCool:(INV)(Pro)" => [
                    "category" => "INV",
                    "capacity" => "1.6 TON",
                    "mrp" => 74990,
                    "discount_amount" => 16490,
                    "net_price" => 58500
                ],
                "HSU-19EnergyCool:(INV)(Pro)" => [
                    "category" => "INV",
                    "capacity" => "1.6 TON",
                    "mrp" => 74990,
                    "discount_amount" => 16490,
                    "net_price" => 58500
                ],
                "HSU-18CleanCool:(INV)(3DF)(QA)" => [
                    "category" => "INV",
                    "capacity" => "1.6 TON",
                    "mrp" => 71990,
                    "discount_amount" => 15490,
                    "net_price" => 56500
                ],
                "HSU-18WifiCool:(INV)(3DF)(QCG)-WiFi" => [
                    "category" => "INV",
                    "capacity" => "1.5 TON",
                    "mrp" => 72990,
                    "discount_amount" => 16490,
                    "net_price" => 56500
                ],
                "HSU-12UVCool:(INV)(Pro)-WiFi*UV" => [
                    "category" => "INV",
                    "capacity" => "1.0 TON",
                    "mrp" => 60990,
                    "discount_amount" => 10490,
                    "net_price" => 50500
                ],
                "HSU-12Antirustcool:(INV)(Pro)-WiFi" => [
                    "category" => "INV",
                    "capacity" => "1.0 TON",
                    "mrp" => 58990,
                    "discount_amount" => 10990,
                    "net_price" => 48000
                ],
                "HSU-12CleanCool:(INV)(Pro)" => [
                    "category" => "INV",
                    "capacity" => "1.0 TON",
                    "mrp" => 57990,
                    "discount_amount" => 10490,
                    "net_price" => 47500
                ],
                "HSU-24TurboAqua:(FIX)(Pro)" => [
                    "category" => "Non-INV",
                    "capacity" => "2.0 TON",
                    "mrp" => 77990,
                    "discount_amount" => 15990,
                    "net_price" => 62000
                ],
                "HSU-18TurboAqua:(FIX)(Pro)" => [
                    "category" => "Non-INV",
                    "capacity" => "1.5 TON",
                    "mrp" => 63990,
                    "discount_amount" => 12990,
                    "net_price" => 51000
                ],
                "HSU-18TurboCool:(FIX)(Pro)" => [
                    "category" => "Non-INV",
                    "capacity" => "1.5 TON",
                    "mrp" => 62990,
                    "discount_amount" => 12490,
                    "net_price" => 50500
                ],
                "HSU-12TurboAqua:(FIX)(Pro)" => [
                    "category" => "Non-INV",
                    "capacity" => "1.0 TON",
                    "mrp" => 50990,
                    "discount_amount" => 8990,
                    "net_price" => 42000
                ]
            ]
        ];
    }
}

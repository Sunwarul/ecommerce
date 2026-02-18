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

class ProductSeeder3 extends Seeder
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

        $tax = Tax::first();
        $warehouses = Warehouse::take(2)->get();

        foreach ($data as $item) {
            $brandName = $item['brand'] ?? 'Haier';
            // Handle Brand
            $brand = Brand::withTrashed()->where('name', $brandName)->first();
            if (!$brand) {
                $brand = Brand::create(['name' => $brandName, 'is_active' => true]);
            } elseif ($brand->trashed()) {
                $brand->restore();
            }

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

            $modelName = $item['model'];
            $name = "{$brandName} {$modelName}";
            $sku = strtoupper(Str::slug($brandName)) . "-" . strtoupper(Str::slug($modelName));

            // Clean price strings (remove commas)
            $mrp = (float) str_replace(',', '', $item['mrp_bdt'] ?? '0');
            $netPrice = (float) str_replace(',', '', $item['net_price'] ?? '0');

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
                    'base_discount_price' => $netPrice,
                    'description' => "<ul><li><strong>Capacity:</strong> {$item['capacity']}</li><li><strong>Series:</strong> " . ($item['series'] ?? 'N/A') . "</li><li><strong>Type:</strong> {$item['category']}</li></ul>",
                    'additional_info' => json_encode($item),
                    'is_active' => true,
                ]);
                $product = $existingProduct;
            } else {
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
                    'description' => "<ul><li><strong>Capacity:</strong> {$item['capacity']}</li><li><strong>Series:</strong> " . ($item['series'] ?? 'N/A') . "</li><li><strong>Type:</strong> {$item['category']}</li></ul>",
                    'additional_info' => json_encode($item),
                    'is_active' => true,
                ]);
            }

            // Dynamic Attributes
            $attributesToSeed = [
                'category' => 'AC Type',
                'capacity' => 'Capacity',
                'series' => 'Series',
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
                        'alert_quantity' => 3,
                    ]);
                }
            }
        }
    }

    private function getProductData(): array
    {
        $hisense_ac_price_list = array_map(function ($item) {
            $item['brand'] = 'Hisense';
            return $item;
        }, [
            [
                'category'   => 'INV',
                'capacity'   => '1.5 TON',
                'series'     => 'Smart Compact',
                'model'      => 'AS18TW4RGSKB02DU',
                'mrp_bdt'    => '73,900',
                'dis_amount' => '23,900',
                'net_price'  => '50,000',
            ],
            [
                'category'   => 'INV',
                'capacity'   => '1.0 TON',
                'series'     => 'Smart Cool',
                'model'      => 'AS12TW4RYETD00BU',
                'mrp_bdt'    => '59,900',
                'dis_amount' => '13,178',
                'net_price'  => '46,722',
            ],
            [
                'category'   => 'INV',
                'capacity'   => '1.5 TON',
                'series'     => 'Smart Cool',
                'model'      => 'AS18TW4RMATD01BU',
                'mrp_bdt'    => '79,900',
                'dis_amount' => '24,900',
                'net_price'  => '55,000',
            ],
            [
                'category'   => 'INV',
                'capacity'   => '2.0 TON',
                'series'     => 'Smart Cool',
                'model'      => 'AS22TW4RXBTD00BU',
                'mrp_bdt'    => '95,900',
                'dis_amount' => '30,100',
                'net_price'  => '65,800',
            ],
            [
                'category'   => 'INV',
                'capacity'   => '1.5 TON',
                'series'     => 'Smart WIFI',
                'model'      => 'AS18TZ4RMATD01AU',
                'mrp_bdt'    => '81,900',
                'dis_amount' => '18,018',
                'net_price'  => '63,882',
            ],
            [
                'category'   => 'INV',
                'capacity'   => '2.0 TON',
                'series'     => 'Smart WIFI',
                'model'      => 'AS22TZ4RXBTD00AU',
                'mrp_bdt'    => '99,900',
                'dis_amount' => '21,978',
                'net_price'  => '77,922',
            ],
            [
                'category'   => 'Non-INV',
                'capacity'   => '1.0 TON',
                'series'     => 'Smart Comfort',
                'model'      => 'AS12CW4RGRKF01BU',
                'mrp_bdt'    => '49,900',
                'dis_amount' => '10,978',
                'net_price'  => '38,922',
            ],
            [
                'category'   => 'Non-INV',
                'capacity'   => '1.5 TON',
                'series'     => 'Smart Comfort',
                'model'      => 'AS18CW4RXSKF00AU',
                'mrp_bdt'    => '63,900',
                'dis_amount' => '19,900',
                'net_price'  => '44,000',
            ],
            [
                'category'   => 'Non-INV',
                'capacity'   => '2.0 TON',
                'series'     => 'Smart Comfort',
                'model'      => 'AS24CW4RBTKF00AU',
                'mrp_bdt'    => '77,900',
                'dis_amount' => '17,138',
                'net_price'  => '60,762',
            ],
            [
                'category'   => 'INV',
                'capacity'   => '4.0 TON',
                'series'     => 'LCAC Cassette',
                'model'      => 'AUC48TRFRQKA2U',
                'mrp_bdt'    => '2,23,900',
                'dis_amount' => '35,824',
                'net_price'  => '1,88,076',
            ],
        ]);

        $gree_haiko_non_inv_list = array_map(function ($item) {
            $item['brand'] = (strpos($item['model'], 'HA-') === 0) ? 'Haiko' : 'Gree';
            return $item;
        }, [
            // GREE Non-Inverter Section
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XCOA5', 'mrp_bdt' => '54,000', 'dis_amount' => '8,100', 'net_price' => '45,900'],
            ['category' => 'Non-INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XCOA5', 'mrp_bdt' => '69,490', 'dis_amount' => '16,623', 'net_price' => '52,867'],
            ['category' => 'Non-INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XCOA5', 'mrp_bdt' => '85,790', 'dis_amount' => '12,868', 'net_price' => '72,922'],
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XSMA1', 'mrp_bdt' => '54,000', 'dis_amount' => '8,100', 'net_price' => '45,900'],
            ['category' => 'Non-INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XSMA1', 'mrp_bdt' => '69,490', 'dis_amount' => '10,423', 'net_price' => '59,067'],
            ['category' => 'Non-INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XSMA1', 'mrp_bdt' => '85,790', 'dis_amount' => '12,868', 'net_price' => '72,922'],
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XCOA4', 'mrp_bdt' => '54,000', 'dis_amount' => '8,100', 'net_price' => '45,900'],
            ['category' => 'Non-INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XCOA4', 'mrp_bdt' => '69,490', 'dis_amount' => '10,423', 'net_price' => '59,067'],
            ['category' => 'Non-INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XCOA4', 'mrp_bdt' => '85,790', 'dis_amount' => '19,068', 'net_price' => '66,722'],
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'GSH-12XFA410', 'mrp_bdt' => '54,000', 'dis_amount' => '8,100', 'net_price' => '45,900'],
            ['category' => 'Non-INV', 'capacity' => '1.5 TON', 'model' => 'GSH-18XFA410', 'mrp_bdt' => '70,890', 'dis_amount' => '10,633', 'net_price' => '60,257'],
            ['category' => 'Non-INV', 'capacity' => '2.0 TON', 'model' => 'GSH-24XFA410', 'mrp_bdt' => '85,290', 'dis_amount' => '12,793', 'net_price' => '72,497'],
            ['category' => 'Non-INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XFA32 G', 'mrp_bdt' => '69,990', 'dis_amount' => '10,498', 'net_price' => '59,492'],
            ['category' => 'Non-INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XNF32 G', 'mrp_bdt' => '85,190', 'dis_amount' => '12,778', 'net_price' => '72,412'],
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'GS-12FA410 G', 'mrp_bdt' => '54,000', 'dis_amount' => '8,100', 'net_price' => '45,900'],
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XLM32', 'mrp_bdt' => '52,000', 'dis_amount' => '7,800', 'net_price' => '44,200'],
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XCM32', 'mrp_bdt' => '52,000', 'dis_amount' => '7,800', 'net_price' => '44,200'],

            // HAIKO Non-Inverter Section
            ['category' => 'Non-INV', 'capacity' => '1.0 TON', 'model' => 'HA-12KT410', 'mrp_bdt' => '42,500', 'dis_amount' => '6,375', 'net_price' => '36,125'],
            ['category' => 'Non-INV', 'capacity' => '1.5 TON', 'model' => 'HA-18KT410', 'mrp_bdt' => '53,990', 'dis_amount' => '8,098', 'net_price' => '45,892'],
            ['category' => 'Non-INV', 'capacity' => '2.0 TON', 'model' => 'HA-24KT 410', 'mrp_bdt' => '70,500', 'dis_amount' => '10,575', 'net_price' => '59,925'],
        ]);

        $gree_inv_list = array_map(function ($item) {
            $item['brand'] = 'Gree';
            return $item;
        }, [
            // 1.5 - 3.0 TON Section
            ['category' => 'INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XZNA3V', 'mrp_bdt' => '79,500', 'dis_amount' => '21,125', 'net_price' => '58,375'],
            ['category' => 'INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XZNA3V', 'mrp_bdt' => '94,000', 'dis_amount' => '14,100', 'net_price' => '79,900'],
            ['category' => 'INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XSMA4V', 'mrp_bdt' => '79,500', 'dis_amount' => '11,925', 'net_price' => '67,575'],
            ['category' => 'INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XSMA4V', 'mrp_bdt' => '94,000', 'dis_amount' => '14,100', 'net_price' => '79,900'],
            ['category' => 'INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XCOA1V', 'mrp_bdt' => '79,500', 'dis_amount' => '11,925', 'net_price' => '67,575'],
            ['category' => 'INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XCOA1V', 'mrp_bdt' => '94,800', 'dis_amount' => '14,220', 'net_price' => '80,580'],
            ['category' => 'INV', 'capacity' => '1.5 TON', 'model' => 'GS-18XCOA3V', 'mrp_bdt' => '79,500', 'dis_amount' => '11,925', 'net_price' => '67,575'],
            ['category' => 'INV', 'capacity' => '2.0 TON', 'model' => 'GS-24XCOA3V', 'mrp_bdt' => '94,800', 'dis_amount' => '14,220', 'net_price' => '80,580'],
            ['category' => 'INV', 'capacity' => '2.5 TON', 'model' => 'GS-30XPUV32', 'mrp_bdt' => '1,41,000', 'dis_amount' => '21,150', 'net_price' => '1,19,850'],
            ['category' => 'INV', 'capacity' => '2.5 TON', 'model' => 'GS-30XFV32', 'mrp_bdt' => '1,42,000', 'dis_amount' => '21,300', 'net_price' => '1,20,700'],
            ['category' => 'INV', 'capacity' => '3.0 TON', 'model' => 'GS-36XCZV32', 'mrp_bdt' => '1,64,000', 'dis_amount' => '24,600', 'net_price' => '1,39,400'],
            ['category' => 'INV', 'capacity' => '1.5 TON', 'model' => 'GSH-18XCLV32', 'mrp_bdt' => '99,000', 'dis_amount' => '14,850', 'net_price' => '84,150'],
            ['category' => 'INV', 'capacity' => '1.5 TON', 'model' => 'GSH-18XFV32', 'mrp_bdt' => '83,890', 'dis_amount' => '12,583', 'net_price' => '71,307'],
            ['category' => 'INV', 'capacity' => '2.0 TON', 'model' => 'GSH-24XCLV32', 'mrp_bdt' => '1,15,000', 'dis_amount' => '17,250', 'net_price' => '97,750'],
            ['category' => 'INV', 'capacity' => '2.0 TON', 'model' => 'GSH-24XFV32', 'mrp_bdt' => '96,290', 'dis_amount' => '14,443', 'net_price' => '81,847'],

            // 1.0 TON Section
            ['category' => 'INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XZNA3V', 'mrp_bdt' => '61,290', 'dis_amount' => '15,293', 'net_price' => '45,997'],
            ['category' => 'INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XSMA4V', 'mrp_bdt' => '61,290', 'dis_amount' => '9,193', 'net_price' => '52,097'],
            ['category' => 'INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XCOA1V', 'mrp_bdt' => '61,790', 'dis_amount' => '9,268', 'net_price' => '52,522'],
            ['category' => 'INV', 'capacity' => '1.0 TON', 'model' => 'GS-12XCOA3V', 'mrp_bdt' => '61,790', 'dis_amount' => '9,268', 'net_price' => '52,522'],
            ['category' => 'INV', 'capacity' => '1.0 TON', 'model' => 'GSH-12XFV32', 'mrp_bdt' => '61,300', 'dis_amount' => '9,195', 'net_price' => '52,105'],
            ['category' => 'INV', 'capacity' => '1.0 TON', 'model' => 'GSH-12XCLV32', 'mrp_bdt' => '77,500', 'dis_amount' => '11,625', 'net_price' => '65,875'],
        ]);

        return array_merge($hisense_ac_price_list, $gree_haiko_non_inv_list, $gree_inv_list);
    }
}

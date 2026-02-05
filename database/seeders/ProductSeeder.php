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

        $productTypeMap = [
            'WH' => 'Water Heater',
            'HRF' => 'Refrigerator',
            'HCF' => 'Chest Freezer',
            'TV' => 'TV',
            'HWD' => 'Washer Dryer',
            'HWM' => 'Washing Machine',
        ];

        foreach ($data as $categoryKey => $products) {
            $categoryName = Str::title(str_replace('_', ' ', $categoryKey));
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

            foreach ($products as $item) {
                // Determine Name and Codes
                $model = $item['model'] ?? 'Unknown';
                $productCode = $item['product'] ?? '';
                $productTypeName = $productTypeMap[$productCode] ?? Str::singular($categoryName);

                $name = "Haier {$productTypeName} {$model}";
                // Use material as SKU if available, otherwise generated SKU
                $sku = $item['material'] ?? ("HAIER-" . Str::slug($model));

                // Check existence (including soft deleted)
                $existingProduct = Product::withTrashed()->where('sku', $sku)->first();
                if ($existingProduct) {
                    if ($existingProduct->trashed()) {
                        $existingProduct->restore();
                    }
                    // Update existing product if needed, or just skip
                    // For now, we assume if it exists, it's fine.
                    $product = $existingProduct;
                    // Ensure attributes are synced below?
                    // Continue to next iteration if we don't want to update attributes every time
                    // But to be safe properly ensuring existence:
                } else {
                    $mrp = $item['mrp'] ?? 0;
                    $showroomPrice = $item['showroom_price'] ?? null;

                    // Description
                    $desc = "<ul>";
                    if (!empty($item['remarks'])) $desc .= "<li><strong>Remarks:</strong> {$item['remarks']}</li>";
                    if (!empty($item['specification'])) $desc .= "<li><strong>Specification:</strong> {$item['specification']}</li>";
                    $desc .= "</ul>";

                    // Ensure unique slug if SKU was unique but slug collided (unlikely with random, but possible)
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
                        'code' => $model,
                        'base_price' => $mrp,
                        'base_discount_price' => $showroomPrice,
                        'type' => 'simple',
                        'description' => $desc,
                        'additional_info' => json_encode($item),
                        'is_active' => true,
                        'materials' => isset($item['material']) ? [$item['material']] : null,
                    ]);
                }

                // Dynamic Attributes
                foreach ($item as $key => $value) {
                    if (in_array($key, ['product', 'material', 'mrp', 'showroom_price', 'dealer_price', 'remarks'])) continue;

                    // Map key to display name
                    $attrName = match ($key) {
                        'model' => 'Model',
                        'product_category' => 'Category Type',
                        'capacity' => 'Capacity',
                        'size' => 'Size',
                        'type' => 'Type',
                        'specification' => 'Specification',
                        default => Str::title(str_replace('_', ' ', $key))
                    };

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
                    $attributeValue = ProductAttributeValue::withTrashed() // Assuming Value supports soft deletes too? Migration check: yes
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
                    // Check if attached?
                    $exists = DB::table('product_variation_attributes')
                        ->where('product_id', $product->id)
                        ->where('attribute_id', $attribute->id)
                        ->where('attribute_value_id', $attributeValue->id)
                        ->whereNull('variation_id') // Important
                        ->exists();

                    if (!$exists) {
                        $product->attributes()->attach($attribute->id, [
                            'attribute_value_id' => $attributeValue->id,
                            'variation_id' => null
                        ]);
                    }
                }

                // Stock - Check if stock exists?
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

        $haierPriceList2026 = [
            'water_heaters' => [
                [
                    'product' => 'WH',
                    'material' => 'GA0U7FZ00',
                    'model' => 'ES15V-SPA(U)',
                    'capacity' => '15L',
                    'mrp' => 12300,
                    'showroom_price' => 11070,
                    'dealer_price' => 10500,
                ],
                [
                    'product' => 'WH',
                    'material' => 'GA0SJHE00',
                    'model' => 'ES30H-CK3 (BD)',
                    'capacity' => '30L',
                    'mrp' => 12800,
                    'showroom_price' => 11520,
                    'dealer_price' => 11000,
                ],
                [
                    'product' => 'WH',
                    'material' => 'GA0UAAE02',
                    'model' => 'ES30V-C2UG(BD)',
                    'capacity' => '30L',
                    'mrp' => 13800,
                    'showroom_price' => 12420,
                    'dealer_price' => 12000,
                ],
                [
                    'product' => 'WH',
                    'material' => 'GA0G8WE00',
                    'model' => 'ES40H-CK3(BD) ESWH',
                    'capacity' => '40L',
                    'mrp' => 14300,
                    'showroom_price' => 12870,
                    'dealer_price' => 12500,
                ],
                [
                    'product' => 'WH',
                    'material' => 'GA0G8VE00',
                    'model' => 'ES50H-CK3(BD) ESWH',
                    'capacity' => '50L',
                    'mrp' => 15800,
                    'showroom_price' => 14220,
                    'dealer_price' => 13000,
                ],
            ],
            'refrigerators' => [
                [
                    'product' => 'HRF',
                    'product_category' => 'T Door',
                    'model' => 'HRF-578TPB',
                    'mrp' => 149900,
                    'showroom_price' => 142405,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'T Door',
                    'model' => 'HRF-578TPB(BD)',
                    'mrp' => 149900,
                    'showroom_price' => 142405,
                    'remarks' => 'New Arrival',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'SBS',
                    'model' => 'HRF-622IBG (SKD)',
                    'mrp' => 145900,
                    'showroom_price' => 138605,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'SBS',
                    'model' => 'HRF-622ICG (SKD)',
                    'mrp' => 144900,
                    'showroom_price' => 137655,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'SBS',
                    'model' => 'HRF-680BG',
                    'mrp' => 155900,
                    'showroom_price' => 132515,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'NF',
                    'model' => 'HRF-386TBG',
                    'mrp' => 79900,
                    'showroom_price' => 75905,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'NF',
                    'model' => 'HRF-362TBG',
                    'mrp' => 75900,
                    'showroom_price' => 72105,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'NF',
                    'model' => 'HRF-300WMBG',
                    'mrp' => 57900,
                    'showroom_price' => 34740,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'NF',
                    'model' => 'HRF-270WMBG',
                    'mrp' => 54900,
                    'showroom_price' => 32940,
                    'remarks' => '',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'NF',
                    'model' => 'HRF-518IFFB',
                    'mrp' => 114900,
                    'showroom_price' => 109155,
                    'remarks' => 'New Arrival',
                ],
                [
                    'product' => 'HRF',
                    'product_category' => 'NF',
                    'model' => 'HRF-488IFFB',
                    'mrp' => 107900,
                    'showroom_price' => 102505,
                    'remarks' => 'New Arrival',
                ],
            ],
            'chest_freezers' => [
                ['model' => 'HCF-175SGE', 'capacity' => '142L', 'mrp' => 43900, 'showroom_price' => 37754],
                ['model' => 'HCF-175SG', 'capacity' => '142L', 'mrp' => 40400, 'showroom_price' => 34340],
                ['model' => 'HCF-175SGM', 'capacity' => '142L', 'mrp' => 41400, 'showroom_price' => 35190],
                ['model' => 'HCF-230SG', 'capacity' => '200L', 'mrp' => 47400, 'showroom_price' => 40290],
                ['model' => 'HCF-230SGM', 'capacity' => '200L', 'mrp' => 48400, 'showroom_price' => 41140],
                ['model' => 'HCF-230SGE', 'capacity' => '200L', 'mrp' => 51400, 'showroom_price' => 45232],
                ['model' => 'HCF-290SM', 'capacity' => '251L', 'mrp' => 53400, 'showroom_price' => 46992],
                ['model' => 'HCF-340SM', 'capacity' => '301L', 'mrp' => 59400, 'showroom_price' => 52272],
            ],
            'televisions' => [
                [
                    'product' => 'TV',
                    'size' => '32',
                    'material' => 'DH1ZMAD00',
                    'model' => 'H32K85FX',
                    'specification' => 'HD Google TV, 1.5G+8G',
                    'mrp' => 26900,
                    'showroom_price' => 24900,
                ],
                [
                    'product' => 'TV',
                    'size' => '32',
                    'material' => 'DH1U64D06',
                    'model' => 'H32S80EFX',
                    'specification' => 'QLED FHD, Google TV, 1.5G+8G',
                    'mrp' => 29900,
                    'showroom_price' => 27900,
                ],
                [
                    'product' => 'TV',
                    'size' => '43',
                    'material' => 'DH1ZNBD00',
                    'model' => 'H43K85FFX',
                    'specification' => 'FHD Google TV, 1.5G+8G',
                    'mrp' => 39900,
                    'showroom_price' => 36900,
                ],
                [
                    'product' => 'TV',
                    'size' => '43',
                    'material' => 'DH1YVTD01',
                    'model' => 'H43S80EFX',
                    'specification' => 'QLED 4K Google TV, 2G+32G',
                    'mrp' => 47900,
                    'showroom_price' => 44900,
                ],
                [
                    'product' => 'TV',
                    'size' => '43',
                    'material' => 'DH1U8XD05',
                    'model' => 'H43P7UX',
                    'specification' => 'HQLED, Google TV, 2G+32G, Hands Free Voice',
                    'mrp' => 51900,
                    'showroom_price' => 48900,
                ],
                [
                    'product' => 'TV',
                    'size' => '50',
                    'material' => 'DH1WKDDOO',
                    'model' => 'H50P7UX',
                    'specification' => 'HQLED, Google TV, 2G+32G, Hands Free Voice',
                    'mrp' => 67900,
                    'showroom_price' => 63900,
                ],
                [
                    'product' => 'TV',
                    'size' => '55',
                    'material' => 'DH1VVKD02',
                    'model' => 'H55P7UX',
                    'specification' => 'HQLED, Google TV, 2G+32G, Hands Free Voice',
                    'mrp' => 78900,
                    'showroom_price' => 73900,
                ],
                [
                    'product' => 'TV',
                    'size' => '65',
                    'material' => 'DH1VWLD04',
                    'model' => 'H65P7UX',
                    'specification' => 'HQLED, Google TV, 2G+32G, Hands Free Voice',
                    'mrp' => 104900,
                    'showroom_price' => 98900,
                ],
                [
                    'product' => 'TV',
                    'size' => '55',
                    'material' => 'DH1ZH8M00',
                    'model' => 'H55M80FUX',
                    'specification' => 'MiniLED, 120Hz, KEF Sound',
                    'mrp' => 107900,
                    'showroom_price' => 99900,
                ],
                [
                    'product' => 'TV',
                    'size' => '65',
                    'material' => 'DH1ZK9M00',
                    'model' => 'H65M80FUX',
                    'specification' => 'MiniLED, 120Hz, KEF Sound',
                    'mrp' => 134900,
                    'showroom_price' => 124900,
                ],
                [
                    'product' => 'TV',
                    'size' => '75',
                    'material' => 'DH1ZLBMOO',
                    'model' => 'H75M80FUX',
                    'specification' => 'MiniLED, 120Hz, KEF Sound',
                    'mrp' => 194900,
                    'showroom_price' => 179900,
                ],
            ],
            'washing_machines' => [
                [
                    'product' => 'HWD',
                    'model' => 'HWD140-BPD14387GNU1',
                    'type' => 'Front Loading',
                    'mrp' => 114900,
                    'showroom_price' => 109155,
                    'remarks' => 'New Arrival',
                ],
                [
                    'product' => 'HWD',
                    'model' => 'HWD105-B14959S8U1',
                    'type' => 'Front Loading',
                    'mrp' => 98900,
                    'showroom_price' => 93955,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HW120-BP12357S8',
                    'type' => 'Front Loading',
                    'mrp' => 84900,
                    'showroom_price' => 80655,
                    'remarks' => 'New Arrival',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HW120:BP14959S8',
                    'type' => 'Front Loading',
                    'mrp' => 84900,
                    'showroom_price' => 80655,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HW105-B14959S8U1',
                    'type' => 'Front Loading',
                    'mrp' => 79900,
                    'showroom_price' => 75905,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HW90-BP12357S8',
                    'type' => 'Front Loading',
                    'mrp' => 68900,
                    'showroom_price' => 65455,
                    'remarks' => 'New Arrival',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HW90:BP14959S8',
                    'type' => 'Front Loading',
                    'mrp' => 68900,
                    'showroom_price' => 65455,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HW80-BP12929S6',
                    'type' => 'Front Loading',
                    'mrp' => 62900,
                    'showroom_price' => 59755,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HWM130-B1678ES8',
                    'type' => 'Top Loading',
                    'mrp' => 53900,
                    'showroom_price' => 51205,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HWM120-316S6',
                    'type' => 'Top Loading',
                    'mrp' => 49900,
                    'showroom_price' => 47405,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HWM100-316S6',
                    'type' => 'Top Loading',
                    'mrp' => 43900,
                    'showroom_price' => 41705,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HWM90-316S6',
                    'type' => 'Top Loading',
                    'mrp' => 38400,
                    'showroom_price' => 36480,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HWM80-316S6',
                    'type' => 'Top Loading',
                    'mrp' => 33900,
                    'showroom_price' => 32205,
                    'remarks' => '',
                ],
                [
                    'product' => 'HWM',
                    'model' => 'HWM80-1269S6',
                    'type' => 'Top Loading',
                    'mrp' => 29900,
                    'showroom_price' => 26312,
                    'remarks' => '',
                ],
            ],
        ];

        return $haierPriceList2026;
    }
}

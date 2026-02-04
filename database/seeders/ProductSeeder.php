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
                    'branch_id' => 1,
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
                        'branch_id' => 1,
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

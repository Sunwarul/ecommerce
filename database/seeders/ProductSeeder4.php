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

class ProductSeeder4 extends Seeder
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
            $brandName = 'Hisense';
            // Handle Brand
            $brand = Brand::withTrashed()->where('name', $brandName)->first();
            if (!$brand) {
                $brand = Brand::create(['name' => $brandName, 'is_active' => true]);
            } elseif ($brand->trashed()) {
                $brand->restore();
            }

            // Category "Refrigerator" exists with ID 6 and slug "refrigerator"
            $categoryName = "Refrigerator";
            $slug = 'refrigerator';

            // Handle Category with Soft Deletes
            $category = Category::withTrashed()->where('slug', $slug)->first();
            if (!$category) {
                // If for some reason it's missing, create it under "Home Appliances" (ID 2)
                $category = Category::create([
                    'name' => $categoryName,
                    'slug' => $slug,
                    'parent_id' => 2,
                    'is_active' => true
                ]);
            } elseif ($category->trashed()) {
                $category->restore();
            }

            $modelName = $item['model'];
            $name = $item['description'] ?? "{$brandName} {$modelName}";
            $sku = strtoupper(Str::slug($brandName)) . "-" . strtoupper(Str::slug($modelName));

            // Clean price strings (remove commas)
            $mrp = (float)($item['mrp'] ?? 0);
            $lifting = (float)($item['lifting'] ?? 0);

            // Extract attributes from description if possible
            preg_match('/(\d+L)/i', $name, $capacityMatches);
            $capacity = $capacityMatches[1] ?? 'N/A';

            preg_match('/(TMF|BMF)/i', $name, $typeMatches);
            $type = $typeMatches[1] ?? 'Refrigerator';

            // Prepare additional info without slab keys
            $additionalInfo = $item;
            unset($additionalInfo['slab_5_9'], $additionalInfo['slab_10_17'], $additionalInfo['slab_18_above']);

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
                    'base_discount_price' => $lifting,
                    'description' => "<ul><li><strong>Capacity:</strong> {$capacity}</li><li><strong>Type:</strong> {$type}</li><li><strong>Model:</strong> {$modelName}</li></ul>",
                    'additional_info' => json_encode($additionalInfo),
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
                    'base_discount_price' => $lifting,
                    'type' => 'simple',
                    'description' => "<ul><li><strong>Capacity:</strong> {$capacity}</li><li><strong>Type:</strong> {$type}</li><li><strong>Model:</strong> {$modelName}</li></ul>",
                    'additional_info' => json_encode($additionalInfo),
                    'is_active' => true,
                ]);
            }

            // Dynamic Attributes
            $attributesToSeed = [
                'capacity' => 'Capacity',
                'type' => 'Ref Type',
            ];

            $attrValues = [
                'capacity' => $capacity,
                'type' => $type
            ];

            foreach ($attributesToSeed as $key => $attrName) {
                $value = $attrValues[$key] ?? '';
                if (empty($value) || $value === 'N/A') continue;

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

        $hisense_promotions = [
            // --- Page 1 Data (Series 238NA, 277NA, 270NA) ---
            [
                'model' => 'RTDG238NASR/BD3',
                'description' => 'Hisense 238L TMF RTDG238NA Starry Red',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG238NADP/BD3',
                'description' => 'Hisense 238L TMF RTDG238NA Dreamy Purple',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG238NAAB/BD3',
                'description' => 'Hisense 238L TMF RTDG238NA Avatar Black',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 800,
                'slab_10_17' => 1100,
                'slab_18_above' => 1500
            ],
            [
                'model' => 'RTDG238NAMB/BD3',
                'description' => 'Hisense 238L TMF RTDG238NA Magnetic Blue',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG277NAB2/BD3',
                'description' => 'Hisense 277L TMF RTDG277NA Premium Black',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG277NARG/BD3',
                'description' => 'Hisense 277L TMF RTDG277RG Rose Gold',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 700,
                'slab_10_17' => 1100,
                'slab_18_above' => 1500
            ],
            [
                'model' => 'RTDG277NAGS/BD3',
                'description' => 'Hisense 277L TMF RTDG277NA GoldnSunlight',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 700,
                'slab_10_17' => 1100,
                'slab_18_above' => 1500
            ],
            [
                'model' => 'RTDG277NAFB/BD3',
                'description' => 'Hisense 277L TMF RTDG277NA Fusion Blue',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RBDG270NARG/BD3',
                'description' => 'Hisense 270L BMF RBDG270NA Rose Gold',
                'mrp' => 47900,
                'lifting' => 37362,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RBDG270NAFB/BD3',
                'description' => 'Hisense 270L BMF RBDG270NA Fusion Blue',
                'mrp' => 47900,
                'lifting' => 37362,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RBDG270NAAR/BD3',
                'description' => 'Hisense 270L BMF RBDG270NA Avatar Red',
                'mrp' => 47900,
                'lifting' => 37362,
                'slab_5_9' => 800,
                'slab_10_17' => 1200,
                'slab_18_above' => 1600
            ],
            [
                'model' => 'RT1G238NAB/BD3',
                'description' => 'Hisense 238L TMF Inv RT1G238NA PremBlack',
                'mrp' => 44900,
                'lifting' => 35022,
                'slab_5_9' => 800,
                'slab_10_17' => 1200,
                'slab_18_above' => 1600
            ],
            [
                'model' => 'RT1G238NAMB/BD3',
                'description' => 'Hisense 238L TMF Inv RT1G238NA Mir Black',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RT1G238NACR/BD3',
                'description' => 'Hisense 238L TMF Inv RT1G238NA Cherry Red',
                'mrp' => 44900,
                'lifting' => 35022,
                'slab_5_9' => 800,
                'slab_10_17' => 1100,
                'slab_18_above' => 1400
            ],
            [
                'model' => 'RT1G238NARB/BD3',
                'description' => 'Hisense 238L TMF Inv RT1G238NA Royal Blu',
                'mrp' => 44900,
                'lifting' => 35022,
                'slab_5_9' => 800,
                'slab_10_17' => 1100,
                'slab_18_above' => 1400
            ],
            [
                'model' => 'RT1G277NAB/BD3',
                'description' => 'Hisense 277L TMF Inv RT1G277NA PremBlack',
                'mrp' => 48900,
                'lifting' => 38142,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RT1G277NAMB/BD3',
                'description' => 'Hisense 277L TMF Inv RT1G277NA Mir Black',
                'mrp' => 50900,
                'lifting' => 39702,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RT1G277NABM/BD3',
                'description' => 'Hisense 277L TMF Inv RT1G277NA Met Blue',
                'mrp' => 48900,
                'lifting' => 38142,
                'slab_5_9' => 500,
                'slab_10_17' => 900,
                'slab_18_above' => 1300
            ],
            [
                'model' => 'RT1G277NARB/BD3',
                'description' => 'Hisense 277L TMF Inv RT1G277NA Royal Blu',
                'mrp' => 48900,
                'lifting' => 38142,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RB1G270NAB/BD3',
                'description' => 'Hisense 270L BMF Inv RB1G270NA PremBlack',
                'mrp' => 49900,
                'lifting' => 38922,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G270NADR/BD3',
                'description' => 'Hisense 270L BMF Inv RB1G270NA Dark Red',
                'mrp' => 49900,
                'lifting' => 38922,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G270NARB/BD3',
                'description' => 'Hisense 270L BMF Inv RB1G270NA Royal Blu',
                'mrp' => 49900,
                'lifting' => 38922,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G270NMB/BD3',
                'description' => 'Hisense 270L BMF RB1G270NM PremBlackDisp',
                'mrp' => 51900,
                'lifting' => 40482,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G270NMMB/BD3',
                'description' => 'Hisense 270L BMF RB1G270NM MirBlack Disp',
                'mrp' => 53900,
                'lifting' => 42042,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],

            // --- Page 2 Data (Series 236, 276, 266) ---
            [
                'model' => 'RB1G270NMBM/BD3',
                'description' => 'Hisense 270L BMF RB1G270NM Met Blue Disp',
                'mrp' => 51900,
                'lifting' => 40482,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G270NMRB/BD3',
                'description' => 'Hisense 270L BMF RB1G270NM RoyalBlueDisp',
                'mrp' => 51900,
                'lifting' => 40482,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RT1G236NAB/BD3',
                'description' => 'Hisense 238L TMF Refrigerator RT1G236',
                'mrp' => 44900,
                'lifting' => 35022,
                'slab_5_9' => 800,
                'slab_10_17' => 1200,
                'slab_18_above' => 1600
            ],
            [
                'model' => 'RT1G236NACR/BD3',
                'description' => 'Hisense 238L TMF Inv RT1G236 Cherry Red',
                'mrp' => 44900,
                'lifting' => 35022,
                'slab_5_9' => 800,
                'slab_10_17' => 1100,
                'slab_18_above' => 1400
            ],
            [
                'model' => 'RT1G236NARB/BD3',
                'description' => 'Hisense 238L TMF Inv RT1G236 Royal Blue',
                'mrp' => 44900,
                'lifting' => 35022,
                'slab_5_9' => 800,
                'slab_10_17' => 1100,
                'slab_18_above' => 1400
            ],
            [
                'model' => 'RT1G236NAMB/BD3',
                'description' => 'Hisense 238L TMF Inv RT1G236 Miror Black',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG236NASR/BD3',
                'description' => 'Hisense 238L TMF Ref RTDG236 Starry Red',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG236NADP/BD3',
                'description' => 'Hisense 238L TMF Ref RTDG236 Drmy Purple',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG236NAAB/BD3',
                'description' => 'Hisense 238L TMF Ref RTDG236 Avatr Black',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 800,
                'slab_10_17' => 1100,
                'slab_18_above' => 1500
            ],
            [
                'model' => 'RTDG236NAMB/BD3',
                'description' => 'Hisense 238L TMF RTDG236 Magnetic Blue',
                'mrp' => 42900,
                'lifting' => 33462,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RT1G276NAB/BD3',
                'description' => 'Hisense 277L TMF Refrigerator RT1G276',
                'mrp' => 48900,
                'lifting' => 38142,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RT1G276NABM/BD3',
                'description' => 'Hisense 277L TMF Inv RT1G276 Metro Blue',
                'mrp' => 48900,
                'lifting' => 38142,
                'slab_5_9' => 500,
                'slab_10_17' => 900,
                'slab_18_above' => 1300
            ],
            [
                'model' => 'RT1G276NARB/BD3',
                'description' => 'Hisense 277L TMF Inv RT1G276 Royal Blue',
                'mrp' => 48900,
                'lifting' => 38142,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RT1G276NAMB/BD3',
                'description' => 'Hisense 277L TMF Inv RT1G276 Miror Black',
                'mrp' => 50900,
                'lifting' => 39702,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG276NAB2/BD3',
                'description' => 'Hisense 277L TMF Ref RTDG276 Pre Black',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RTDG276NARG/BD3',
                'description' => 'Hisense 277L TMF Ref RTDG276 Rose Gold',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 700,
                'slab_10_17' => 1100,
                'slab_18_above' => 1500
            ],
            [
                'model' => 'RTDG276NAGS/BD3',
                'description' => 'Hisense 277L TMF RTDG276 Golden Sunlight',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 700,
                'slab_10_17' => 1100,
                'slab_18_above' => 1500
            ],
            [
                'model' => 'RTDG276NAFB/BD3',
                'description' => 'Hisense 277L TMF Ref RTDG276 Fusion Blue',
                'mrp' => 46900,
                'lifting' => 36582,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RB1G266NAB/BD3',
                'description' => 'Hisense 270 L BMF Refrigerator RB1G266',
                'mrp' => 49900,
                'lifting' => 38922,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G266NADR/BD3',
                'description' => 'Hisense 270L BMF Inv RB1G266 Dark Red',
                'mrp' => 49900,
                'lifting' => 38922,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G266NARB/BD3',
                'description' => 'Hisense 270L BMF Inv RB1G266 Royal Blue',
                'mrp' => 49900,
                'lifting' => 38922,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RBDG266NARG/BD3',
                'description' => 'Hisense 270L BMF Ref RBDG266 Rose Gold',
                'mrp' => 47900,
                'lifting' => 37362,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RBDG266NAFB/BD3',
                'description' => 'Hisense 270L BMF Ref RBDG266 Fusion Blue',
                'mrp' => 47900,
                'lifting' => 37362,
                'slab_5_9' => 400,
                'slab_10_17' => 800,
                'slab_18_above' => 1200
            ],
            [
                'model' => 'RBDG266NAAR/BD3',
                'description' => 'Hisense 270L BMF Ref RBDG266 Avatar Red',
                'mrp' => 47900,
                'lifting' => 37362,
                'slab_5_9' => 800,
                'slab_10_17' => 1200,
                'slab_18_above' => 1600
            ],
            [
                'model' => 'RB1G266NMB/BD3',
                'description' => 'Hisense 270 L BMF Refrigerator RB1G266 with Disp',
                'mrp' => 51900,
                'lifting' => 40482,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G266NMBM/BD3',
                'description' => 'Hisense 270L BMF RB1G266 Met Blue Disp',
                'mrp' => 51900,
                'lifting' => 40482,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G266NMRB/BD3',
                'description' => 'Hisense 270L BMF RB1G266 RoyalBlue Disp',
                'mrp' => 51900,
                'lifting' => 40482,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
            [
                'model' => 'RB1G266NMMB/BD3',
                'description' => 'Hisense 270L BMF RB1G266 Mir Black Disp',
                'mrp' => 53900,
                'lifting' => 42042,
                'slab_5_9' => 0,
                'slab_10_17' => 0,
                'slab_18_above' => 0
            ],
        ];

        return $hisense_promotions;
    }
}

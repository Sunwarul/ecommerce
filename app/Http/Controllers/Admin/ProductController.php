<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Requests\Admin\StoreProductRequest as AdminStoreProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\Tag;
use App\Models\Tax;
use App\Models\Warehouse;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'products',
            modelClass: Product::class,
            storeRequestClass: ProductStoreRequest::class,
            updateRequestClass: ProductUpdateRequest::class,
            componentPath: 'Admin/Products/Index',
            searchColumns: ['name', 'description'],
            exportClass: ProductExport::class,
            withRelations: ['category:id,name', 'brand:id,name', 'tags:id,name'],
            addProps: $this->addProps(),
        ));
    }

    protected function addProps(): array
    {
        return [
            // CATEGORIES WITH CHILDREN
            'categories' => Category::select('id', 'name', 'parent_id')
                ->with('children:id,name,parent_id')
                ->whereNull('parent_id')
                ->get(),

            // BRANDS
            'brands' => Brand::select('id', 'name')->get(),

            // TAGS
            'tags' => Tag::select('id', 'name')->get(),

            // TAXES
            'taxes' => Tax::select('id', 'name', 'rate_value', 'rate_type')->get(),

            // WAREHOUSES
            'warehouses' => Warehouse::select('id', 'name')->get(),

            // ATTRIBUTES
            'attributes' => ProductAttribute::select('id', 'name', 'display_name', 'type')
                ->with('values:id,attribute_id,value,display_value,color_code')
                ->get(),
        ];
    }


    public function store(\App\Http\Requests\Admin\StoreProductRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
// dd($data);
            // Clean product inputs
            $productData = Arr::except($data, ['tag_ids', 'variations', 'warehouse_id']);

            $productData['created_by'] = auth()->id();
            $productData['slug'] = $productData['slug'] ?? Str::slug($productData['name']);
            $productData['is_active'] = $request->boolean('is_active');

            // 🔧 FIX: make sure *_id fields are SCALARS, not arrays
            foreach (['category_id', 'tax_id', 'brand_id'] as $key) {
                if (isset($productData[$key]) && is_array($productData[$key])) {
                    // take first value if it's an array like [3]
                    $productData[$key] = $productData[$key][0] ?? null;
                }
            }

            // These are fine as arrays – your seeder uses same style and casts handle them
            $productData['images'] = $productData['images'] ?? [];
            $productData['dimensions'] = $productData['dimensions'] ?? [];
            $productData['materials'] = $productData['materials'] ?? [];

            // --------------------------------------------
            // CREATE PRODUCT (same shape as seeder)
            // --------------------------------------------
            $product = Product::create([
                'category_id' => $productData['category_id'],
                'tax_id' => $productData['tax_id'] ?? null,
                'brand_id' => $productData['brand_id'] ?? null,
                'created_by' => $productData['created_by'],

                'name' => $productData['name'],
                'slug' => $productData['slug'],
                'thumbnail' => $productData['thumbnail'] ?? null,
                'images' => $productData['images'],

                'sku' => $productData['sku'] ?? null,
                'barcode' => $productData['barcode'] ?? null,
                'code' => $productData['code'] ?? null,

                'base_price' => $productData['base_price'],
                'base_discount_price' => $productData['base_discount_price'] ?? null,

                'stock_quantity' => $productData['stock_quantity'] ?? 0,
                'stock_status' => $productData['stock_status'],
                'type' => $productData['type'],

                'weight' => $productData['weight'] ?? null,
                'dimensions' => $productData['dimensions'],
                'materials' => $productData['materials'],

                'description' => $productData['description'] ?? null,
                'additional_info' => $productData['additional_info'] ?? null,
                'is_active' => $productData['is_active'],

                'meta_title' => $productData['meta_title'] ?? null,
                'meta_description' => $productData['meta_description'] ?? null,
                'meta_keywords' => $productData['meta_keywords'] ?? null,
            ]);

            // SIMPLE PRODUCT STOCK (uses warehouse)
            if ($product->type === 'simple') {
                ProductStock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $data['warehouse_id'] ?? null,
                    'quantity' => $data['stock_quantity'] ?? 0,
                    'alert_quantity' => 10,
                ]);
            }

            // TAG RELATION
            if (!empty($data['tag_ids'] ?? null)) {
                $tagIds = is_array($data['tag_ids']) ? $data['tag_ids'] : [$data['tag_ids']];
                $product->tags()->sync($tagIds);
            }

            // VARIABLE PRODUCT → variations
            if ($product->type === 'variable' && !empty($data['variations'] ?? null)) {
                foreach ($data['variations'] as $variationInput) {
                    $variation = ProductVariation::create([
                        'product_id' => $product->id,
                        'sku' => $variationInput['sku'],
                        'price' => $variationInput['price'],
                        'discount_price' => $variationInput['discount_price'] ?? null,
                        'stock_quantity' => $variationInput['stock_quantity'],
                        'stock_status' => $variationInput['stock_status'],
                        'image' => $variationInput['image'] ?? null,
                        'is_active' => true,
                    ]);

                    $attributeValueIds = $variationInput['attribute_value_ids'] ?? [];
                    if (!is_array($attributeValueIds)) {
                        $attributeValueIds = [$attributeValueIds];
                    }

                    $attachData = [];
                    foreach ($attributeValueIds as $valueId) {
                        $value = ProductAttributeValue::findOrFail($valueId);
                        $attachData[$value->id] = [
                            'attribute_id' => $value->attribute_id,
                            'product_id' => $product->id,
                        ];
                    }

                    if (!empty($attachData)) {
                        $variation->attributeValues()->attach($attachData);
                    }
                }
            }

        });
    }



    public function indexDisabledForNow(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = Product::query();
        $query->with(['category:id,name', 'brand:id,name', 'tags:id,name']);
        $query->with(['category:id,name', 'brand:id,name', 'tags:id,name']);

        $searchColumns = ['name', 'slug'];

        $query->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, 'like', "%{$search}%")
                        ->orWhere();
                }
            });
        });

        if ($request->has('trashed')) {
            $query->when($request->trashed, fn($query) => $query->onlyTrashed());
            $query->when($request->trashed, fn($query) => $query->onlyTrashed());
        }

        $query = $this->modifyQuery($query);
        $items = $query->latest()->paginate($perPage);

        return Inertia::render($this->componentPath, [
            'items' => $items,
            'filters' => ['search' => $search],
            'config' => $this->makeConfig(),
            ...$this->addProps(),
        ]);
    }

    // public function storeOld(ProductStoreRequest $request)
    // // public function store(StoreProductRequest $request)
    // {
    //     $data = $request->validated();
    //     return DB::transaction(function () use ($data, $request) {
    //         // 1) Create product
    //         $productData = [
    //             'name' => $data['name'],
    //             'slug' => $data['slug'] ?? Str::slug($data['name']),
    //             'category_id' => $data['category_id'] ?? null,
    //             'brand_id' => $data['brand_id'] ?? null,
    //             'description' => $data['description'] ?? null,
    //             'is_active' => $data['is_active'] ?? true,
    //             'has_variants' => $data['has_variants'] ?? false,
    //             'base_unit_id' => $data['base_unit_id'] ?? null,
    //             'tax_class_id' => $data['tax_class_id'] ?? null,
    //             'meta_json' => $data['meta_json'] ?? [],
    //         ];
    //         /** @var \App\Models\Product $product */

    //         $product = Product::create($productData);
    //         // 2) Attach attributes (Color, Size, etc.)
    //         if (!empty($data['attribute_ids'])) {
    //             $product->attributes()->sync($data['attribute_ids']);
    //         }
    //         // 3) Handle images (if you’re uploading on the same request)
    //         if ($request->hasFile('images')) {
    //             foreach ($request->file('images') as $index => $file) {
    //                 $path = $file->store('products', 'public');
    //                 $image = ProductImage::create([
    //                     'product_id' => $product->id,
    //                     'path' => $path,
    //                     'is_primary' => $index === 0, // first image as primary

    //                     'sort' => $index,
    //                 ]);
    //                 if ($index === 0) {
    //                     // optional: set this as default image for SKUs later
    //                 }
    //             }
    //         }
    //         // 4) If SKUs are provided, create them + inventory
    //         if (!empty($data['skus'])) {
    //             foreach ($data['skus'] as $skuData) {
    //                 $sku = Sku::create([
    //                     'product_id' => $product->id,
    //                     'code' => $skuData['code'],

    //                     'barcode' => $skuData['barcode'] ?? null,
    //                     'price' => $skuData['price'],
    //                     'compare_at_price' => $skuData['compare_at_price'] ?? null,
    //                     'cost_price' => $skuData['cost_price'] ?? null,
    //                     'currency' => $skuData['currency'] ?? 'USD',
    //                     'is_active' => $skuData['is_active'] ?? true,
    //                 ]);
    //                 // attach variant values (attribute_value_ids)
    //                 if (!empty($skuData['attribute_value_ids'])) {

    //                     $pivotData = [];

    //                     foreach ($skuData['attribute_value_ids'] as $valueId) {

    //                         /** @var AttributeValue $val */
    //                         $val = AttributeValue::find($valueId);

    //                         if (!$val) {
    //                             continue;
    //                         }

    //                         $pivotData[$val->id] = [

    //                             'attribute_id' => $val->attribute_id,
    //                         ];
    //                     }

    //                     $sku->attributeValues()->sync($pivotData);

    //                 }
    //                 // create inventory record if provided
    //                 if (!empty($skuData['inventory'])) {
    //                     $inv = $skuData['inventory'];
    //                     InventoryItem::create([
    //                         'sku_id' => $sku->id,

    //                         'warehouse_id' => $inv['warehouse_id'],
    //                         'qty_on_hand' => $inv['qty_on_hand'],
    //                         'qty_reserved' => 0,

    //                         'low_stock_threshold' => $inv['low_stock_threshold'] ?? 0,

    //                     ]);
    //                 }
    //             }
    //         }
    //         return redirect()
    //             ->route('admin.products.edit', $product)
    //             ->with('success', 'Product created successfully.');
    //     });


    // }
}

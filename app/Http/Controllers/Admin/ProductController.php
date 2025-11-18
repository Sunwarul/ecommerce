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
use App\Models\ProductAttributeValue;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\Tag;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;
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
            'categories' => Category::select('id', 'name')->get(),
            'brands' => Brand::select('id', 'name')->get(),
            'tags' => Tag::select('id', 'name')->get(),
        ];
    }

    public function indexDisabledForNow(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = Product::query();
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

    public function store(AdminStoreProductRequest $request)
    {
        $data = $request->validated();

        $data["created_by"] = auth()->id();
        $data["slug"] = $data["slug"] ?? Str::slug($data["name"]);
        $data["is_active"] = $request->boolean("is_active");

        // ----------------------------------------------
        // CREATE PRODUCT
        // ----------------------------------------------
        $product = Product::create($data);

        // TAG SYNC
        if (!empty($data["tag_ids"])) {
            $product->tags()->sync($data["tag_ids"]);
        }

        // ----------------------------------------------
        // SIMPLE PRODUCT → USE product_stocks TABLE
        // ----------------------------------------------
        if ($data["type"] === "simple") {
            ProductStock::create([
                "product_id" => $product->id,
                "quantity" => $data["stock_quantity"] ?? 0,
                "alert_quantity" => 10
            ]);
        }

        // ----------------------------------------------
        // VARIABLE PRODUCT → CREATE VARIATIONS
        // ----------------------------------------------
        if ($data["type"] === "variable" && isset($data["variations"])) {

            foreach ($data["variations"] as $variationInput) {

                $variation = ProductVariation::create([
                    "product_id" => $product->id,
                    "sku" => $variationInput["sku"],
                    "price" => $variationInput["price"],
                    "discount_price" => $variationInput["discount_price"] ?? null,
                    "stock_quantity" => $variationInput["stock_quantity"],
                    "stock_status" => $variationInput["stock_status"],
                    "image" => $variationInput["image"] ?? null,
                    "is_active" => true,
                ]);

                // attach attribute_value_ids → pivot table
                foreach ($variationInput["attribute_value_ids"] as $valueId) {

                    $value = ProductAttributeValue::find($valueId);

                    $variation->attributeValues()->attach($valueId, [
                        "attribute_id" => $value->attribute_id,
                        "product_id" => $product->id
                    ]);
                }
            }
        }

        return response()->json([
            "message" => "Product created successfully",
            "product" => $product->load("variations.attributeValues")
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

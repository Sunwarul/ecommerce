<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Requests\Admin\StoreProductRequest as AdminStoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
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
use Illuminate\Support\Facades\Storage;
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

    public function index(Request $request)
    {
        $products = Product::query()
            ->with([
                'category:id,name',
                'brand:id,name',
            ])
            ->withSum('stocks as total_stock', 'quantity') // ✅ total stock
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
        ]);
    }


    public function create()
    {
        return Inertia::render('Admin/Products/FormPage', [
            'categories' => Category::with('children')->get(),
            'brands' => Brand::all(),
            'taxes' => Tax::all(),
            'tags' => Tag::all(),
            'attributes' => ProductAttribute::select('id', 'name', 'display_name', 'type')
                ->with('values:id,attribute_id,value,display_value,color_code')
                ->get(),
            'warehouses' => Warehouse::all(),
        ]);
    }


    public function edit(Product $product)
    {
        $product->load([
            'category',
            'brand',
            'tax',
            'tags',

            // ✅ simple stock
            'stocks.warehouse',

            // ✅ variation + attributes + variation stock
            'variations.attributeValues.attribute',
            'variations.stocks.warehouse',
        ]);

        return Inertia::render('Admin/Products/FormPage', [
            'product' => $product,
            'categories' => Category::with('children')->get(),
            'brands' => Brand::all(),
            'taxes' => Tax::all(),
            'tags' => Tag::all(),
            'attributes' => ProductAttribute::select('id', 'name', 'display_name', 'type')
                ->with('values:id,attribute_id,value,display_value,color_code')
                ->get(),
            'warehouses' => Warehouse::all(),
        ]);
    }


    public function show(Product $product)
    {
        $product->load([
            'category',
            'brand',
            'tax',
            'tags',
            'stocks.warehouse', // simple stocks (variation_id null) OR for total view
            'variations.attributeValues.attribute',
            'variations.stocks.warehouse',
        ]);

        // dd($product);

        return inertia('Admin/Products/Show', [
            'product' => $product,
        ]);
    }


    public function store(\App\Http\Requests\Admin\StoreProductRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            // Remove relations we handle separately
            $productData = Arr::except($data, ['tag_ids', 'variations', 'stocks']);

            $productData['created_by'] = auth()->id();
            $productData['slug'] = $productData['slug'] ?? Str::slug($productData['name']);
            $productData['is_active'] = $request->boolean('is_active');

            // Ensure scalar *_id fields
            foreach (['category_id', 'tax_id', 'brand_id'] as $key) {
                if (isset($productData[$key]) && is_array($productData[$key])) {
                    $productData[$key] = $productData[$key][0] ?? null;
                }
            }

            // Cast-safe defaults
            $productData['images'] = $productData['images'] ?? [];
            $productData['dimensions'] = $productData['dimensions'] ?? [];
            $productData['materials'] = $productData['materials'] ?? [];

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                $productData['thumbnail'] = $request->file('thumbnail')->store('products');
            }

            // ✅ Create Product (NO stock fields)
            $product = Product::create($productData);

            // ✅ Tags
            if (!empty($data['tag_ids'])) {
                $product->tags()->sync($data['tag_ids']);
            }

            // ✅ SIMPLE PRODUCT → create product_stocks rows (variation_id = NULL)
            if ($product->type === 'simple') {
                foreach ($data['stocks'] as $s) {
                    ProductStock::create([
                        'product_id' => $product->id,
                        'variation_id' => null,
                        'warehouse_id' => $s['warehouse_id'],
                        'quantity' => 0,
                        'alert_quantity' => $s['alert_quantity'] ?? null,
                    ]);
                }
            }

            // ✅ VARIABLE PRODUCT → create variations + pivot + stocks
            if ($product->type === 'variable') {
                foreach ($data['variations'] as $variationInput) {

                    // Create variation (NO stock_quantity/status if you removed them)
                    $variation = ProductVariation::create([
                        'product_id' => $product->id,
                        'sku' => $variationInput['sku'],
                        'price' => $variationInput['price'],
                        'discount_price' => $variationInput['discount_price'] ?? null,
                        'image' => $variationInput['image'] ?? null,
                        'is_active' => true,
                    ]);

                    // Attach attribute values with attribute_id in pivot
                    $attributeValueIds = $variationInput['attribute_value_ids'];

                    $values = ProductAttributeValue::whereIn('id', $attributeValueIds)->get();

                    $attachData = [];
                    foreach ($values as $value) {
                        $attachData[$value->id] = [
                            'attribute_id' => $value->attribute_id,
                        ];
                    }

                    $variation->attributeValues()->attach($attachData);

                    // Create stocks for this variation per warehouse
                    foreach ($variationInput['stocks'] as $s) {
                        ProductStock::create([
                            'product_id' => $product->id,
                            'variation_id' => $variation->id,
                            'warehouse_id' => $s['warehouse_id'],
                            'quantity' => 0,
                            'alert_quantity' => $s['alert_quantity'] ?? null,
                        ]);
                    }
                }
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');
        });
    }



    public function editData(Product $product)
    {
        $product->load([
            'category',
            'brand',
            'tax',
            'tags',
            'variations.attributeValues.attribute',
            'stocks.warehouse',
        ]);

        return response()->json($product);
    }


    public function update(UpdateProductRequest $request, Product $product)
    {
        return DB::transaction(function () use ($request, $product) {
            $data = $request->validated();

            // -----------------------------
            // Product data (remove handled arrays)
            // -----------------------------
            $productData = Arr::except($data, ['tag_ids', 'variations', 'stocks']);

            $productData['slug'] = $productData['slug'] ?? Str::slug($productData['name']);
            $productData['is_active'] = $request->boolean('is_active');

            // ensure scalar *_id
            foreach (['category_id', 'tax_id', 'brand_id'] as $key) {
                if (isset($productData[$key]) && is_array($productData[$key])) {
                    $productData[$key] = $productData[$key][0] ?? null;
                }
            }

            // cast defaults
            $productData['images'] = $productData['images'] ?? [];
            $productData['dimensions'] = $productData['dimensions'] ?? [];
            $productData['materials'] = $productData['materials'] ?? [];

            // Thumbnail replace
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail && Storage::exists($product->thumbnail)) {
                    Storage::delete($product->thumbnail);
                }
                $productData['thumbnail'] = $request->file('thumbnail')->store('products');
            }

            // -----------------------------
            // Update Product (NO STOCK FIELDS)
            // -----------------------------
            $product->update([
                'category_id' => $productData['category_id'],
                'tax_id' => $productData['tax_id'] ?? null,
                'brand_id' => $productData['brand_id'] ?? null,

                'name' => $productData['name'],
                'slug' => $productData['slug'],
                'thumbnail' => $productData['thumbnail'] ?? $product->thumbnail,
                'images' => $productData['images'],

                'sku' => $productData['sku'] ?? null,
                'barcode' => $productData['barcode'] ?? null,
                'code' => $productData['code'] ?? null,

                'base_price' => $productData['base_price'],
                'base_discount_price' => $productData['base_discount_price'] ?? null,

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

            // -----------------------------
            // Tags
            // -----------------------------
            $product->tags()->sync($data['tag_ids'] ?? []);

            // -----------------------------
            // SIMPLE: update stocks (variation_id = NULL) + delete variations & their stocks
            // -----------------------------
            if ($product->type === 'simple') {
                // remove variation records + their stocks
                $product->variations()->each(function ($variation) {
                    $variation->attributeValues()->detach();
                    $variation->delete();
                });

                // remove all stocks then recreate only simple ones
                ProductStock::where('product_id', $product->id)->delete();

                foreach (($data['stocks'] ?? []) as $s) {
                    ProductStock::create([
                        'product_id' => $product->id,
                        'variation_id' => null,
                        'warehouse_id' => $s['warehouse_id'],
                        'quantity' => 0,
                        'alert_quantity' => $s['alert_quantity'] ?? null,
                    ]);
                }

                return redirect()
                    ->route('products.index')
                    ->with('success', 'Product updated successfully.');
            }

            // -----------------------------
            // VARIABLE: update/create/delete variations + upsert stocks per variation
            // -----------------------------
            // If switching from simple -> variable, clean simple stocks first
            ProductStock::where('product_id', $product->id)
                ->whereNull('variation_id')
                ->delete();

            $incoming = $data['variations'] ?? [];

            // Map existing variations by id
            $existingIds = $product->variations()->pluck('id')->toArray();
            $incomingIds = collect($incoming)->pluck('id')->filter()->map(fn($v) => (int) $v)->values()->toArray();

            // Delete removed variations (and their pivot + stocks via cascade if set)
            $toDelete = array_diff($existingIds, $incomingIds);
            if (!empty($toDelete)) {
                $product->variations()
                    ->whereIn('id', $toDelete)
                    ->each(function ($variation) {
                        $variation->attributeValues()->detach();
                        // delete stocks for this variation
                        ProductStock::where('variation_id', $variation->id)->delete();
                        $variation->delete();
                    });
            }

            foreach ($incoming as $variationInput) {
                $variationId = $variationInput['id'] ?? null;

                // Update or create variation
                if ($variationId) {
                    $variation = $product->variations()->where('id', $variationId)->firstOrFail();

                    $variation->update([
                        'sku' => $variationInput['sku'],
                        'price' => $variationInput['price'],
                        'discount_price' => $variationInput['discount_price'] ?? null,
                        'image' => $variationInput['image'] ?? null,
                        'is_active' => true,
                    ]);
                } else {
                    $variation = ProductVariation::create([
                        'product_id' => $product->id,
                        'sku' => $variationInput['sku'],
                        'price' => $variationInput['price'],
                        'discount_price' => $variationInput['discount_price'] ?? null,
                        'image' => $variationInput['image'] ?? null,
                        'is_active' => true,
                    ]);
                }

                // Sync attribute values (pivot with attribute_id)
                $attributeValueIds = $variationInput['attribute_value_ids'] ?? [];
                $values = ProductAttributeValue::whereIn('id', $attributeValueIds)->get();

                $attachData = [];
                foreach ($values as $value) {
                    $attachData[$value->id] = [
                        'attribute_id' => $value->attribute_id,
                    ];
                }
                $variation->attributeValues()->sync($attachData);

                // Upsert variation stocks by warehouse
                $stocks = $variationInput['stocks'] ?? [];

                // Remove old stocks for warehouses not present anymore
                $incomingWarehouseIds = collect($stocks)->pluck('warehouse_id')->map(fn($x) => (int) $x)->values()->toArray();
                ProductStock::where('variation_id', $variation->id)
                    ->whereNotIn('warehouse_id', $incomingWarehouseIds)
                    ->delete();

                foreach ($stocks as $s) {
                    ProductStock::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'variation_id' => $variation->id,
                            'warehouse_id' => $s['warehouse_id'],
                        ],
                        [
                            'quantity' => 0,
                            'alert_quantity' => $s['alert_quantity'] ?? null,
                        ]
                    );
                }
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Product updated successfully.');
        });
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);

        return DB::transaction(function () use ($request) {

            $products = Product::withTrashed()
                ->whereIn('id', $request->ids)
                ->get();

            foreach ($products as $product) {

                // 🧹 Delete thumbnail file
                if ($product->thumbnail && Storage::exists($product->thumbnail)) {
                    Storage::delete($product->thumbnail);
                }

                // 🧹 Variations + pivots + stocks
                $product->variations()->withTrashed()->each(function ($variation) {

                    // pivot table
                    $variation->attributeValues()->detach();

                    // variation stocks
                    ProductStock::where('variation_id', $variation->id)->delete();

                    $variation->forceDelete();
                });

                // 🧹 Simple product stocks
                ProductStock::where('product_id', $product->id)->delete();

                // 🧹 Tags pivot
                $product->tags()->detach();

                // ❌ HARD DELETE PRODUCT
                $product->forceDelete();
            }

            return Inertia::render('Admin/Products/Index')
                ->with('success', 'Selected products permanently deleted.');
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

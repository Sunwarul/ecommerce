<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProductRequest;
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
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        // No HasCrud trait - using explicit methods instead
    }

    protected function addProps(): array
    {
        return [
            'categories' => $this->getCachedCategories(),
            'brands' => $this->getCachedBrands(),
            'tags' => Tag::select('id', 'name')->get(),
            'taxes' => Tax::select('id', 'name', 'rate_value', 'rate_type')->get(),
            'warehouses' => $this->getCachedWarehouses(),
            'attributes' => $this->getCachedAttributes(),
        ];
    }

    protected function getCachedCategories(): mixed
    {
        $version = cache('categories_version', 1);

        return cache()->remember("product_categories_v{$version}", now()->addMinutes(10), function () {
            return Category::select('id', 'name', 'parent_id')
                ->with('children:id,name,parent_id')
                ->whereNull('parent_id')
                ->get();
        });
    }

    protected function getCachedBrands(): mixed
    {
        $version = cache('brands_version', 1);

        return cache()->remember("product_brands_v{$version}", now()->addMinutes(10), function () {
            return Brand::select('id', 'name')->get();
        });
    }

    protected function getCachedWarehouses(): mixed
    {
        $version = cache('warehouses_version', 1);

        return cache()->remember("product_warehouses_v{$version}", now()->addMinutes(10), function () {
            return Warehouse::select('id', 'name')->get();
        });
    }

    protected function getCachedAttributes(): mixed
    {
        $version = cache('attributes_version', 1);

        return cache()->remember("product_attributes_v{$version}", now()->addMinutes(10), function () {
            return ProductAttribute::select('id', 'name', 'display_name', 'type')
                ->with('values:id,attribute_id,value,display_value,color_code')
                ->get();
        });
    }

    public static function clearProductCache(): void
    {
        // Increment version to invalidate cache - next request will fetch fresh data
        cache()->increment('categories_version', 1);
        cache()->increment('brands_version', 1);
        cache()->increment('warehouses_version', 1);
        cache()->increment('attributes_version', 1);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $category_id = $request->input('category_id');
        $brand_id = $request->input('brand_id');
        $status = $request->input('status'); // 'active', 'inactive'
        $trashed = $request->input('trashed'); // 'with', 'only'
        $type = $request->input('type'); // 'simple', 'variable'
        $warehouse_id = $request->input('warehouse_id');
        $stock_min = $request->input('stock_min');
        $stock_max = $request->input('stock_max');
        $price_min = $request->input('price_min');
        $price_max = $request->input('price_max');
        $stock_status = $request->input('stock_status'); // 'in_stock', 'low_stock', 'out_of_stock'
        $sort = $request->input('sort', 'id_desc');

        $query = Product::query()
            ->with(['category:id,name', 'brand:id,name', 'stocks' => function ($q) {
                $q->select('id', 'product_id', 'warehouse_id', 'quantity', 'alert_quantity');
            }])
            ->withSum('stocks as total_stock', 'quantity');

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
        if ($brand_id) {
            $query->where('brand_id', $brand_id);
        }
        if ($status) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($warehouse_id) {
            $query->whereHas('stocks', function ($q) use ($warehouse_id) {
                $q->where('warehouse_id', $warehouse_id);
            });
        }

        // Price range
        if ($price_min !== null && $price_min !== '') {
            $query->where('base_price', '>=', floatval($price_min));
        }
        if ($price_max !== null && $price_max !== '') {
            $query->where('base_price', '<=', floatval($price_max));
        }

        // Stock range (using having since it's an aggregate)
        if ($stock_min !== null && $stock_min !== '') {
            $query->having('total_stock', '>=', intval($stock_min));
        }
        if ($stock_max !== null && $stock_max !== '') {
            $query->having('total_stock', '<=', intval($stock_max));
        }

        // Stock status filter - simplified for performance (using default threshold of 10)
        if ($stock_status === 'out_of_stock') {
            $query->having('total_stock', '<=', 0);
        } elseif ($stock_status === 'low_stock') {
            $query->having('total_stock', '>', 0)->having('total_stock', '<=', 10);
        } elseif ($stock_status === 'in_stock') {
            $query->having('total_stock', '>', 10);
        }

        // Sorting
        $sortParts = explode('_', $sort);
        $sortField = $sortParts[0] ?? 'id';
        $sortDir = $sortParts[1] ?? 'desc';

        $allowedSorts = ['id', 'name', 'base_price', 'total_stock', 'created_at', 'category_id', 'brand_id'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('id');
        }

        // Trashed
        if ($trashed === 'only') {
            $query->onlyTrashed();
        } elseif ($trashed === 'with') {
            $query->withTrashed();
        }

        $products = $query->paginate($perPage)->withQueryString();

        // Get props from addProps method (includes warehouses, categories, brands, etc.)
        $props = $this->addProps();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'category_id' => $category_id,
                'brand_id' => $brand_id,
                'status' => $status,
                'trashed' => $trashed,
                'per_page' => $perPage,
                'type' => $type,
                'warehouse_id' => $warehouse_id,
                'stock_min' => $stock_min,
                'stock_max' => $stock_max,
                'price_min' => $price_min,
                'price_max' => $price_max,
                'stock_status' => $stock_status,
                'sort' => $sort,
            ],
            'categories' => $props['categories'] ?? [],
            'brands' => $props['brands'] ?? [],
            'warehouses' => $props['warehouses'] ?? [],
            'tags' => $props['tags'] ?? [],
            'taxes' => $props['taxes'] ?? [],
            'attributes' => $props['attributes'] ?? [],
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
            $productData['branch_id'] = $request?->branch_id ?? 1;

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
            if (! empty($data['tag_ids'])) {
                $product->tags()->sync($data['tag_ids']);
            }

            // ✅ SIMPLE PRODUCT → create product_stocks rows (variation_id = NULL)
            if ($product->type === 'simple') {
                foreach ($data['stocks'] as $s) {
                    ProductStock::create([
                        'branch_id' => $request?->branch_id ?? 1,
                        'product_id' => $product->id,
                        'variation_id' => null,
                        'warehouse_id' => $s['warehouse_id'],
                        'quantity' => $s['quantity'] ?? 0,
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
                            'branch_id' => $request?->branch_id ?? 1,
                            'product_id' => $product->id,
                            'variation_id' => $variation->id,
                            'warehouse_id' => $s['warehouse_id'],
                            'quantity' => $s['quantity'] ?? 0,
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
            $productData['branch_id'] = $request?->branch_id ?? 1;

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
                'base_discount_price' => array_key_exists('base_discount_price', $productData) ? $productData['base_discount_price'] : $product->base_discount_price,

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
                        'branch_id' => $request?->branch_id ?? 1,
                        'product_id' => $product->id,
                        'variation_id' => null,
                        'warehouse_id' => $s['warehouse_id'],
                        'quantity' => $s['quantity'] ?? 0,
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
            $incomingIds = collect($incoming)->pluck('id')->filter()->map(fn ($v) => (int) $v)->values()->toArray();

            // Delete removed variations (and their pivot + stocks via cascade if set)
            $toDelete = array_diff($existingIds, $incomingIds);
            if (! empty($toDelete)) {
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
                $incomingWarehouseIds = collect($stocks)->pluck('warehouse_id')->map(fn ($x) => (int) $x)->values()->toArray();
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
                            'branch_id' => $request?->branch_id ?? 1,
                            'quantity' => $s['quantity'] ?? 0,
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

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);

        Product::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Selected products moved to trash.');
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);

        Product::onlyTrashed()
            ->whereIn('id', $request->ids)
            ->restore();

        // Also restore variations if needed, but usually cascading soft deletes handles this or we need manual looped restore.
        // Assuming simple restore for now. If using cascading package, it handles it.
        // If standard Laravel, we might need to manually restore relations if they were soft deleted.
        // Let's assume user just wants the product back for now.

        return back()->with('success', 'Selected products restored successfully.');
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return back()->with('success', 'Product restored successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product moved to trash.');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $stock = ProductStock::where('product_id', $product->id)
            ->where('warehouse_id', $request->warehouse_id)
            ->whereNull('variation_id')
            ->first();

        if ($stock) {
            $stock->update(['quantity' => $request->quantity]);
        } else {
            ProductStock::create([
                'product_id' => $product->id,
                'warehouse_id' => $request->warehouse_id,
                'variation_id' => null,
                'branch_id' => 1,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Stock updated successfully']);
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $filename = 'products-'.now()->format('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new ProductExport($search), $filename);
    }
}

<?php

use App\Models\Brand;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));
    $this->actingAs($admin);
});

test('editing a record that already has a photo keeps it', function () {
    $customer = Customer::create(['name' => 'Rahim', 'phone' => '01711111111', 'photo' => 'customers/keep.jpg']);

    // the edit form echoes the stored path back as a string
    $this->put(route('customers.update', $customer->id), [
        'name' => 'Rahim Uddin', 'phone' => '01711111111', 'photo' => 'customers/keep.jpg',
    ])->assertSessionHasNoErrors();

    expect($customer->fresh()->name)->toBe('Rahim Uddin')
        ->and($customer->fresh()->photo)->toBe('customers/keep.jpg');
});

test('brand photos are stored as files, not temp paths', function () {
    $this->post(route('brands.store'), ['name' => 'Haier', 'photo' => UploadedFile::fake()->image('haier.png')])
        ->assertSessionHasNoErrors();

    $photo = Brand::where('name', 'Haier')->value('photo');
    expect($photo)->toStartWith('brands/');
    Storage::disk('public')->assertExists($photo);
});

test('brand names are unique among brands, not categories', function () {
    \App\Models\Category::factory()->create(['name' => 'Walton']);

    $this->post(route('brands.store'), ['name' => 'Walton'])->assertSessionHasNoErrors();
    $brand = Brand::where('name', 'Walton')->first();
    $this->put(route('brands.update', $brand->id), ['name' => 'Walton'])->assertSessionHasNoErrors();
});

test('products that appear on invoices are never permanently deleted', function () {
    $product = Product::create(['name' => 'AC', 'slug' => 'ac', 'sku' => 'AC-1', 'base_price' => 100, 'type' => 'simple', 'is_active' => true]);
    $unsold = Product::create(['name' => 'Fan', 'slug' => 'fan', 'sku' => 'FAN-1', 'base_price' => 50, 'type' => 'simple', 'is_active' => true]);
    \Illuminate\Support\Facades\DB::table('pos_sessions')->insert(['id' => 1, 'user_id' => auth()->id(), 'status' => 'open', 'created_at' => now(), 'updated_at' => now()]);
    $orderId = \Illuminate\Support\Facades\DB::table('pos_orders')->insertGetId(['pos_session_id' => 1, 'user_id' => auth()->id(), 'status' => 'completed', 'created_at' => now(), 'updated_at' => now()]);
    \Illuminate\Support\Facades\DB::table('pos_order_items')->insert(['order_id' => $orderId, 'product_id' => $product->id, 'name' => 'AC', 'quantity' => 1, 'unit_price' => 100, 'line_total' => 100, 'created_at' => now(), 'updated_at' => now()]);

    $this->post(route('products.bulk-force-delete'), ['ids' => [$product->id, $unsold->id]])->assertRedirect();

    expect(Product::withTrashed()->find($product->id))->not->toBeNull()
        ->and(Product::withTrashed()->find($unsold->id))->toBeNull()
        ->and(\Illuminate\Support\Facades\DB::table('pos_order_items')->count())->toBe(1);
});

test('product bulk routes are not captured by the product id route', function () {
    $p = Product::create(['name' => 'AC', 'slug' => 'ac2', 'sku' => 'AC-2', 'base_price' => 100, 'type' => 'simple', 'is_active' => true]);

    $this->delete(route('products.bulk-delete'), ['ids' => [$p->id]])->assertStatus(302);
    $this->assertSoftDeleted($p);
});

test('server messages follow the user language', function () {
    auth()->user()->update(['locale' => 'bn']);

    $this->post(route('brands.store'), ['name' => 'Singer'])
        ->assertSessionHas('success', __('Created successfully', [], 'bn'));
    expect(__('Created successfully', [], 'bn'))->not->toBe('Created successfully');
});

test('frontend route list hides the sign-up route while registration is off', function () {
    expect(config('ziggy.except'))->toContain('register');
});

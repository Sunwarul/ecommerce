<?php

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole(Role::findOrCreate('Super Admin', 'web'));
    $this->actingAs($user);
});

test('index lists categories', function () {
    Category::factory(3)->create();

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Categories/Index', false)->has('items.data', 3));
});

test('store creates a category, optionally with a photo', function () {
    $this->post(route('categories.store'), ['name' => 'Air Conditioners', 'is_active' => false])
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('success');
    $this->assertDatabaseHas('categories', ['name' => 'Air Conditioners', 'is_active' => false]);

    $this->post(route('categories.store'), ['name' => 'Fans', 'photo' => UploadedFile::fake()->image('fan.jpg')]);
    Storage::disk('public')->assertExists(Category::where('name', 'Fans')->value('photo'));
});

test('update changes a category and replaces its photo', function () {
    $category = Category::factory()->create(['photo' => 'categories/old.jpg']);
    Storage::disk('public')->put('categories/old.jpg', 'x');

    $this->put(route('categories.update', $category), ['name' => 'Renamed', 'photo' => UploadedFile::fake()->image('new.jpg')])
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('success');

    $category->refresh();
    expect($category->name)->toBe('Renamed');
    Storage::disk('public')->assertMissing('categories/old.jpg');
    Storage::disk('public')->assertExists($category->photo);
});

test('destroy soft deletes and bulk restore brings it back', function () {
    $category = Category::factory()->create();

    $this->delete(route('categories.destroy', $category))->assertRedirect(route('categories.index'));
    $this->assertSoftDeleted($category);

    $this->post(route('categories.bulk-restore'), ['ids' => [$category->id]]);
    $this->assertNotSoftDeleted($category);
});

test('bulk destroy validates ids', function () {
    $this->post(route('categories.bulk-destroy'), ['ids' => 'not-an-array'])->assertSessionHasErrors('ids');

    $categories = Category::factory(2)->create();
    $this->post(route('categories.bulk-destroy'), ['ids' => $categories->pluck('id')->all()])->assertRedirect();
    $categories->each(fn ($c) => $this->assertSoftDeleted($c));
});

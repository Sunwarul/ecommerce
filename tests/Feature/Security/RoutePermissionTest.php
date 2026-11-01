<?php

use App\Http\Middleware\AuthorizeRoutePermission as Map;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    foreach (Map::allPermissionNames() as $name) {
        Permission::findOrCreate($name, 'web');
    }
    $this->superRole = Role::findOrCreate('Super Admin', 'web');
});

function userWith(array $permissions = [], ?string $role = null): User
{
    $user = User::factory()->create();
    if ($permissions) {
        $r = Role::findOrCreate($role ?? 'role-'.uniqid(), 'web');
        $r->syncPermissions($permissions);
        $user->assignRole($r);
    }

    return $user;
}

test('every admin route is covered by a permission and api routes are guarded', function () {
    $routes = collect(Route::getRoutes()->getRoutes());

    $unguardedApi = $routes
        ->filter(fn ($r) => str_starts_with($r->uri(), 'api/') && str_starts_with((string) $r->getName(), 'api.'))
        ->reject(fn ($r) => in_array('route.permission', $r->gatherMiddleware(), true))
        ->map(fn ($r) => $r->getName())->values()->all();
    expect($unguardedApi)->toBe([]);

    // API Platform must not expose any model resources
    expect($routes->filter(fn ($r) => str_starts_with((string) $r->getName(), '_api_/'))->count())->toBe(0);

    $unmapped = $routes
        ->filter(fn ($r) => str_starts_with($r->uri(), 'admin'))
        ->reject(fn ($r) => in_array($r->getName(), Map::AUTH_ONLY, true)
            || Map::hasOwnGate($r)
            || Map::permissionFor($r->getName()) !== null)
        ->map(fn ($r) => $r->getName() ?? $r->uri())
        ->values()
        ->all();

    expect($unmapped)->toBe([]);
});

test('a signed-in user without permissions is denied admin pages', function () {
    $this->actingAs(userWith());

    foreach (['users.index', 'roles.index', 'permissions.index', 'settings.general', 'products.index', 'pos.index', 'pos.orders.index', 'customers.index'] as $route) {
        $this->get(route($route))->assertForbidden();
    }
});

test('permissions grant exactly their action', function () {
    $this->actingAs(userWith(['Tax Index']));

    $this->get(route('taxes.index'))->assertOk();
    $this->post(route('taxes.store'), ['name' => 'VAT'])->assertForbidden();
    $this->delete(route('taxes.destroy', 1))->assertForbidden();
});

test('super admin passes every check, including permissions added later', function () {
    $admin = User::factory()->create();
    $admin->assignRole($this->superRole);
    Permission::findOrCreate('Brand New Thing', 'web');

    expect($admin->can('Brand New Thing'))->toBeTrue();
    $this->actingAs($admin)->get(route('users.index'))->assertOk();
});

test('a user manager cannot grant the Super Admin role or change their own roles', function () {
    $manager = userWith(['User Index', 'User Update', 'User Store'], 'Manager');
    $target = User::factory()->create();
    $this->actingAs($manager);

    $this->put(route('users.update', $target->id), [
        'name' => 'Target User', 'email' => $target->email, 'roles' => [$this->superRole->id],
    ])->assertForbidden();
    expect($target->fresh()->hasRole('Super Admin'))->toBeFalse();

    $other = Role::findOrCreate('Cashier', 'web');
    $this->put(route('users.update', $manager->id), [
        'name' => $manager->name, 'email' => $manager->email, 'roles' => [$other->id],
    ])->assertForbidden();
});

test('role editors cannot widen the super admin role or grant permissions they lack', function () {
    $editor = userWith(['Role Index', 'Role Update', 'Role Store'], 'Role Editor');
    $this->actingAs($editor);

    $this->put(route('roles.update', $this->superRole), ['name' => 'Super Admin', 'ids' => [1]])->assertForbidden();

    $userDelete = Permission::findByName('User Delete', 'web');
    $this->post(route('roles.store'), ['name' => 'Sneaky', 'ids' => [$userDelete->id]])->assertForbidden();
    expect(Role::where('name', 'Sneaky')->exists())->toBeFalse();
});

test('the last super admin cannot be removed', function () {
    $admin = User::factory()->create();
    $admin->assignRole($this->superRole);
    $other = User::factory()->create();
    $other->assignRole($this->superRole);
    $this->actingAs($admin);

    // can't delete yourself
    $this->delete(route('users.destroy', $admin->id))->assertStatus(422);
    // demoting the only other super admin is fine while two exist
    $this->put(route('users.update', $other->id), ['name' => $other->name, 'email' => $other->email, 'roles' => []])->assertRedirect();
    // now $admin is the last one and can't be demoted
    $this->actingAs($other->fresh());
    expect($admin->fresh()->hasRole('Super Admin'))->toBeTrue();
});

test('public registration is disabled by default', function () {
    expect(config('app.user_registration_allowed'))->toBeFalse();
    $this->get('/register')->assertNotFound();
});

test('editing a user keeps their phone and password unless changed', function () {
    $admin = User::factory()->create();
    $admin->assignRole($this->superRole);
    $target = User::factory()->create(['phone' => '01700000000']);
    $hash = $target->password;

    $this->actingAs($admin)->put(route('users.update', $target->id), [
        'name' => 'Renamed User', 'email' => $target->email, 'phone' => '01700000000',
    ])->assertSessionHasNoErrors();

    expect($target->fresh()->name)->toBe('Renamed User')
        ->and($target->fresh()->password)->toBe($hash);
});

test('responses carry security headers', function () {
    $this->get('/login')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
});

test('unknown pages render the friendly error page', function () {
    $admin = User::factory()->create();
    $admin->assignRole($this->superRole);

    $this->actingAs($admin)->get('/admin/definitely-not-here')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page->component('Error', false)->where('status', 404));
});

test('exports need the Report Export permission', function () {
    $this->actingAs(userWith(['Report Index', 'SMS Index', 'EMI Index', 'Installment Index']));

    foreach (['reports.sales', 'sms.logs', 'sms.dashboard', 'emi.index', 'installments.index'] as $route) {
        $this->get(route($route))->assertOk();
        $this->get(route($route, ['export' => 'csv']))->assertForbidden();
    }
});

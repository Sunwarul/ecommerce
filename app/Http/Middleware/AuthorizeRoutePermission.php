<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authorizes every named admin route against a Spatie permission.
 *
 * CRUD resources map "{resource}.{action}" to "{Prefix} {Ability}" (e.g. taxes.store => "Tax Store").
 * Anything not mapped and not explicitly listed in AUTH_ONLY is denied, so a new route can never
 * be reachable by every logged-in user by accident (tests/Feature/Security/RoutePermissionTest.php
 * fails when a route is left unmapped).
 */
class AuthorizeRoutePermission
{
    /** CRUD resource (route name prefix) => permission prefix */
    public const RESOURCES = [
        'taxes' => 'Tax',
        'categories' => 'Category',
        'sub-categories' => 'Sub Category',
        'tags' => 'Tag',
        'brands' => 'Brand',
        'payment-methods' => 'Payment Methods',
        'todos' => 'Todo',
        'tasks' => 'Task',
        'users' => 'User',
        'roles' => 'Role',
        'permissions' => 'Permission',
        'branches' => 'Branch',
        'warehouses' => 'Warehouse',
        'suppliers' => 'Supplier',
        'base-units' => 'Base Unit',
        'units' => 'Unit',
        'expense-categories' => 'Expense Categories',
        'expenses' => 'Expense',
        'currencies' => 'Country',
        'customers' => 'Customer',
        'warranty-guarantees' => 'Warranty',
        'product-attributes' => 'Product Attribute',
        'product-attribute-values' => 'Product Attribute',
        'products' => 'Product',
    ];

    /** CRUD action => ability */
    public const ACTIONS = [
        'index' => 'Index',
        'show' => 'Index',
        'create' => 'Store',
        'store' => 'Store',
        'edit' => 'Edit',
        'edit-data' => 'Edit',
        'update' => 'Update',
        'destroy' => 'Delete',
        'restore' => 'Bulk Restore',
        'bulk-destroy' => 'Bulk Delete',
        'bulk-delete' => 'Bulk Delete',
        'bulk-force-delete' => 'Bulk Delete',
        'bulk-restore' => 'Bulk Restore',
        'export' => 'Export',
    ];

    /** Non-CRUD admin routes => permission */
    public const ROUTES = [
        'products.update-stock' => 'Product Movement',
        'admin.stock.move.form' => 'Product Movement',
        'admin.stock.move' => 'Product Movement',
        'pos.stock.adjust' => 'Product Movement',

        'pos.index' => 'POS Index',
        'pos.session.current' => 'POS Index',
        'pos.session.open' => 'POS Index',
        'pos.session.close' => 'POS Index',
        'pos.orders.store' => 'POS Index',
        'pos.orders.edit' => 'POS Index',
        'pos.orders.update' => 'POS Index',
        'pos.customers.search' => 'POS Index',

        'pos.orders.index' => 'Order Index',
        'pos.orders.invoice' => 'Order Invoice',
        'pos.orders.complete' => 'Order Complete',
        'pos.orders.payments.store' => 'Order Payment',
        'pos.orders.void' => 'Order Void',
        'pos.orders.destroy' => 'Order Delete',
        'pos.orders.restore' => 'Order Delete',
        'pos.orders.force-delete' => 'Order Delete',
        'pos.orders.bulk-action' => 'Order Delete',
        'pos.orders.transfer' => 'Order Transfer',
        'pos.orders.update-date' => 'Order Edit Date',

        'settings.general' => 'Setting Index',
        'settings.update' => 'Setting Update',
    ];

    /** Admin routes any signed-in user may call (their own controllers enforce what they need). */
    public const AUTH_ONLY = [
        'branch.switch',          // users switch between branches they work in
        'installments.preview',   // pure calculation used by POS
        'emi.quote',              // pure calculation used by POS
    ];

    /** Every permission name the map can require (used by the seeder / migration). */
    public static function allPermissionNames(): array
    {
        $names = array_values(self::ROUTES);
        foreach (array_unique(self::RESOURCES) as $prefix) {
            foreach (array_unique(self::ACTIONS) as $ability) {
                $names[] = "{$prefix} {$ability}";
            }
        }

        return array_values(array_unique($names));
    }

    public static function permissionFor(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }
        // API routes share the web permission names: api.taxes.index => taxes.index
        if (str_starts_with($routeName, 'api.')) {
            $routeName = substr($routeName, 4);
        }
        if (isset(self::ROUTES[$routeName])) {
            return self::ROUTES[$routeName];
        }

        $dot = strrpos($routeName, '.');
        if ($dot === false) {
            return null;
        }
        $resource = substr($routeName, 0, $dot);
        $action = substr($routeName, $dot + 1);

        if (isset(self::RESOURCES[$resource], self::ACTIONS[$action])) {
            return self::RESOURCES[$resource].' '.self::ACTIONS[$action];
        }

        return null;
    }

    /** Routes guarded elsewhere (e.g. `can:` middleware on the route itself). */
    public static function hasOwnGate($route): bool
    {
        foreach ($route?->gatherMiddleware() ?? [] as $m) {
            if (is_string($m) && str_starts_with($m, 'can:')) {
                return true;
            }
        }

        return false;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $name = $route?->getName();

        if (in_array($name, self::AUTH_ONLY, true) || self::hasOwnGate($route)) {
            return $next($request);
        }

        $permission = self::permissionFor($name);
        abort_unless($permission && $request->user()?->can($permission), 403);

        return $next($request);
    }
}

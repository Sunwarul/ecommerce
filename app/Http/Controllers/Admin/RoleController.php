<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class RoleController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'roles',
            modelClass: Role::class,
            storeRequestClass: RoleStoreRequest::class,
            updateRequestClass: RoleUpdateRequest::class,
            componentPath: 'Admin/Roles/Index',
            searchColumns: ['name'],
            withRelations: ['permissions:id,name']
        ));
    }

    protected function addProps(): array
    {
        return [
            'permissions' => Permission::select('id', 'name')->get(),
        ];
    }

    public function store(RoleStoreRequest $request)
    {
        $data = $request->validated();
        $this->ensureGrantable($data['ids']);
        $role = Role::create(['name' => $data['name']]);
        if (isset($role)) {
            $permissions = Permission::whereKey($data['ids'])->get();
            $role->syncPermissions($permissions);
        }
        $this->clearAuthCache();
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        // The Super Admin role is managed by Gate::before; it can't be renamed or narrowed,
        // and users can't widen the role they themselves hold.
        abort_if($role->name === 'Super Admin', 403, __('The Super Admin role cannot be edited.'));
        abort_if(! $request->user()->hasRole('Super Admin') && $request->user()->hasRole($role->name), 403, __('You cannot edit a role you belong to.'));

        $data = $request->validated();
        $this->ensureGrantable($data['ids']);
        $role->update(['name' => $data['name']]);
        if (isset($role)) {
            $permissions = Permission::whereKey($data['ids'])->get();
            $role->syncPermissions($permissions);
        }
        $this->clearAuthCache();
    }

    public function destroy(Role $role)
    {
        if (isset($role)) {
            $isNotSuperAdmin = (Role::count() > 1) && $role->name !== 'Super Admin';
            if ($isNotSuperAdmin) {
                $role->delete();
                $this->clearAuthCache();

                return back();
            }
        }

        return \redirect()->back()->with('error', __('The Super Admin role cannot be deleted.'));
    }

    /** Non-Super-Admins can only hand out permissions they hold themselves. */
    protected function ensureGrantable(array $permissionIds): void
    {
        $actor = request()->user();
        if ($actor->hasRole('Super Admin')) {
            return;
        }
        $own = $actor->getAllPermissions()->pluck('id');
        abort_if(collect($permissionIds)->map(fn ($id) => (int) $id)->diff($own)->isNotEmpty(), 403,
            __('You can only grant permissions you have yourself.'));
    }

    protected function clearAuthCache(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        cache()->forever('auth_version', time());
    }
}

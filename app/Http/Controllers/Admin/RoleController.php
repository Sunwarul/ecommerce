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
        $role = Role::create(['name' => $data['name']]);
        if (isset($role)) {
            $permissions = Permission::whereKey($data['ids'])->get();
            $role->syncPermissions($permissions);
        }
        $this->clearAuthCache();
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        $data = $request->validated();
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
            $superAdminId = 1;
            $isNotSuperAdmin = (Role::count() > 1) && $role->id != $superAdminId;
            if ($isNotSuperAdmin) {
                $role->delete();
                $this->clearAuthCache();

                return back();
            }
        }

        return \redirect()->back()->withErrors('Super Admin Can\'t be Deleted');
    }

    protected function clearAuthCache(): void
    {
        cache()->increment('auth_version', 1);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Permission::count() == 0) {
            foreach ($this->getPermissions() as $permissionName) {
                Permission::create(['name' => $permissionName]);
            }
        }

        if (Role::count() == 0) {
            $role = Role::create(['name' => 'Super Admin']);
            $role = Role::create(['name' => 'Branch Manager']);
            $role = Role::create(['name' => 'Sales']);
            $role = Role::create(['name' => 'Inventory']);
            $role = Role::create(['name' => 'Accounts']);
            $role = Role::create(['name' => 'Service']);
            $permissions = Permission::all();
            $role->syncPermissions($permissions);
        }
    }

    private function getPermissions(): array
    {
        return [
            'user_index',
            'user_store',
            'user_update',
            'user_edit',
            'user_delete',
            
            'role_index',
            'role_store',
            'role_update',
            'role_edit',
            'role_delete',
            
            'permission_index',
            'permission_store',
            'permission_update',
            'permission_edit',
            'permission_delete',
            
            'category_index',
            'category_store',
            'category_update',
            'category_edit',
            'category_delete',
            'category_search',
            'category_export',
            'category_bulk_delete',
            'category_bulk_restore',
            
            'sub-category_index',
            'sub-category_store',
            'sub-category_update',
            'sub-category_edit',
            'sub-category_delete',
            'sub-category_search',
            'sub-category_export',
            'sub-category_bulk_delete',
            'sub-category_bulk_restore',
            
            'brand_index',
            'brand_store',
            'brand_update',
            'brand_edit',
            'brand_delete',
            'brand_search',
            'brand_export',
            'brand_bulk_delete',
            'brand_bulk_restore',
            
            'tag_index',
            'tag_store',
            'tag_update',
            'tag_edit',
            'tag_delete',
            'tag_search',
            'tag_export',
            'tag_bulk_delete',
            'tag_bulk_restore',
            
            'product_index',
            'product_store',
            'product_update',
            'product_edit',
            'product_delete',
            'product_search',
            'product_export',
            'product_bulk_delete',
            'product_bulk_restore',
            
            'payment_method_index',
            'payment_method_store',
            'payment_method_update',
            'payment_method_edit',
            'payment_method_delete',
            'payment_method_search',
            'payment_method_export',
            'payment_method_bulk_delete',
            'payment_method_bulk_restore',
        ];
    }
}

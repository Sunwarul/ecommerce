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
        // foregin key checks disabled
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        Permission::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if (Permission::count() == 0) {
            foreach ($this->getPermissions() as $permissionName) {
                Permission::create(['name' => $permissionName]);
            }
        }

        if (Role::count() == 0) {
            $role = Role::create(['name' => 'Super Admin']);
            $permissions = Permission::all();
            $role->syncPermissions($permissions);
        }
    }

    private function getPermissions(): array
    {
        return [
            // category
            'Category Index',
            'Category Store',
            'Category Update',
            'Category Edit',
            'Category Delete',
            'Category Search',
            'Category Export',
            'Category Bulk Delete',
            'Category Bulk Restore',

            // BRAND
            'Brand Index',
            'Brand Store',
            'Brand Update',
            'Brand Edit',
            'Brand Delete',
            'Brand Search',
            'Brand Export',
            'Brand Bulk Delete',
            'Brand Bulk Restore',

            // Tag
            'Tag Index',
            'Tag Store',
            'Tag Update',
            'Tag Edit',
            'Tag Delete',
            'Tag Search',
            'Tag Export',
            'Tag Bulk Delete',
            'Tag Bulk Restore',
            // tAX
            'Tax Index',
            'Tax Store',
            'Tax Update',
            'Tax Edit',
            'Tax Delete',
            'Tax Search',
            'Tax Export',
            'Tax Bulk Delete',
            'Tax Bulk Restore',
            // Payment Methods
            'Payment Methods Index',
            'Payment Methods Store',
            'Payment Methods Update',
            'Payment Methods Edit',
            'Payment Methods Delete',
            'Payment Methods Search',
            'Payment Methods Export',
            'Payment Methods Bulk Delete',
            'Payment Methods Bulk Restore',
            // Product
            'Product Index',
            'Product Store',
            'Product Update',
            'Product Edit',
            'Product Delete',
            'Product Movement',
            'Product Search',
            'Product Export',
            'Product Bulk Delete',
            'Product Bulk Restore',
            // POS
            'POS Index',
            
            // Order
            'Order Index',
            'Order View',
            'Order Invoice',
            'Order Complete',
            'Order Void',
            // Role
            'Role Index',
            'Role Store',
            'Role Update',
            'Role Edit',
            'Role Delete',
            'Role Search',
            'Role Export',
            'Role Bulk Delete',
            'Role Bulk Restore',
            // Permission
            'Permission Index',
            'Permission Store',
            // User
            'User Index',
            'User Store',
            'User Update',
            'User Edit',
            'User Delete',
            'User Search',
            'User Export',
            'User Bulk Delete',
            'User Bulk Restore',
            // Branch
            'Branch Index',
            'Branch Store',
            'Branch Update',
            'Branch Edit',
            'Branch Delete',
            'Branch Search',
            'Branch Export',
            'Branch Bulk Delete',
            'Branch Bulk Restore',
            // Warehouse
            'Warehouse Index',
            'Warehouse Store',
            'Warehouse Update',
            'Warehouse Edit',
            'Warehouse Delete',
            'Warehouse Search',
            'Warehouse Export',
            'Warehouse Bulk Delete',
            'Warehouse Bulk Restore',
            // Base Unit
            'Base Unit Index',
            'Base Unit Store',
            'Base Unit Update',
            'Base Unit Edit',
            'Base Unit Delete',
            'Base Unit Search',
            'Base Unit Export',
            'Base Unit Bulk Delete',
            'Base Unit Bulk Restore',
            // Unit
            'Unit Index',
            'Unit Store',
            'Unit Update',
            'Unit Edit',
            'Unit Delete',
            'Unit Search',
            'Unit Export',
            'Unit Bulk Delete',
            'Unit Bulk Restore',
            // Supplier
            'Supplier Index',
            'Supplier Store',
            'Supplier Update',
            'Supplier Edit',
            'Supplier Delete',
            'Supplier Search',
            'Supplier Export',
            'Supplier Bulk Delete',
            'Supplier Bulk Restore',
            // Customer
            'Customer Index',
            'Customer Store',
            'Customer Update',
            'Customer Edit',
            'Customer Delete',
            'Customer Search',
            'Customer Export',
            'Customer Bulk Delete',
            'Customer Bulk Restore',
            // Expense Categories
            'Expense Categories Index',
            'Expense Categories Store',
            'Expense Categories Update',
            'Expense Categories Edit',
            'Expense Categories Delete',
            'Expense Categories Search',
            'Expense Categories Export',
            'Expense Categories Bulk Delete',
            'Expense Categories Bulk Restore',
            // Expense
            'Expense Index',
            'Expense Store',
            'Expense Update',
            'Expense Edit',
            'Expense Delete',
            'Expense Search',
            'Expense Export',
            'Expense Bulk Delete',
            'Expense Bulk Restore',
            // Country
            'Country Index',
            'Country Store',
            'Country Update',
            'Country Edit',
            'Country Delete',
            'Country Search',
            'Country Export',
            'Country Bulk Delete',
            'Country Bulk Restore',
            // Setting
            'Setting Index',
        ];
        
    }
}

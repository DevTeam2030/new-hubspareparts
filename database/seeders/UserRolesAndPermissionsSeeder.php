<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        $rolePermissions = [
            'Account Owner' => [
                'Dashboard', 'Edite profile', 'Change password', 'My orders', 'Refund System',
                'Request Refund', 'Shipping Address', 'Support Ticket', 'Logout', 'Add to Cart',
                'Add/Delete Products to Wishlist', 'View Cart', 'Update Cart', 'Checkout',
                'Engineering Confirm', 'Procurement Confirm', 'View/Creat /Edite / Remove Collection',
                'Delet from Cart', 'Add/Remove Members', 'Perioterization Wishlist Collections'
                , 'View Collection','Export to Cart'
            ],

            'Billing' => [
                'Dashboard', 'My orders', 'Refund System', 'Support Ticket',
                'Logout', 'View Cart', 'Update Cart', 'Checkout', 'View Collection','Export to Cart'
            ],

            'Procurement' => [
                'Dashboard', 'My orders', 'Refund System', 'Support Ticket',
                'Logout', 'View Cart', 'Procurement Confirm', 'View Collection','Export to Cart'
            ],

            'Engineer Manager' => [
                'Dashboard', 'My orders', 'Refund System', 'Request Refund', 'Support Ticket',
                'Logout', 'Add/Delete Products to Wishlist', 'View Cart',
                'View/Creat /Edite / Remove Collection', 'Perioterization Wishlist Collections',
                'Engineering Confirm' , 'View Collection','Export to Cart'
            ],

            'Engineer' => [
                'Dashboard', 'My orders', 'Refund System', 'Support Ticket', 'Logout', 'View Cart',
                'View/Creat /Edite / Remove Collection', 'Add/Delete Products to Wishlist' ,
                'View Collection','Export to Cart'
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => $guard]
            );

            foreach ($permissions as $permLabel) {
                // Convert to lowercase slug with dot prefix
                $permissionName = 'web-' . Str::slug($permLabel, '-');

                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => $guard],
                    ['menu_name' => $permLabel]
                );

                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}

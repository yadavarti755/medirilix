<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Permission Groups
        $permissionGroups = [
            'Dashboard' => [
                'view admin dashboard',
            ],
            'User' => [
                'view user',
                'add user',
                'edit user',
                'delete user',
                'reset password'
            ],
            'Role' => [
                'view role',
                'add role',
                'edit role',
                'delete role'
            ],
            'Menu' => [
                'view menu',
                'add menu',
                'edit menu',
                'delete menu'
            ],
            'Site Setting' => [
                'edit site setting',
                'menu setup'
            ],
            'Page' => [
                'view page',
                'add page',
                'edit page',
                'delete page',
                'publish page',
                'approve page',
            ],
            'Slider' => [
                'view slider',
                'add slider',
                'edit slider',
                'delete slider',
                'publish slider',
            ],
            'Announcements' => [
                'view announcement',
                'add announcement',
                'edit announcement',
                'delete announcement',
                'publish announcement',
                'approve announcement',
            ],
            'Our Partners' => [
                'view our partner',
                'add our partner',
                'edit our partner',
                'delete our partner',
                'publish our partner',
                'approve our partner',
            ],
            'Contact Details' => [
                'view contact detail',
                'add contact detail',
                'edit contact detail',
                'delete contact detail',
                'publish contact detail',
                'approve contact detail',
            ],
            'Social Media' => [
                'view social media',
                'add social media',
                'edit social media',
                'delete social media',
            ],
            'Media' => [
                'view media',
                'add media',
                'edit media',
                'delete media',
                'approve media',
                'publish media',
            ],
            'Audit Log' => [
                'view audit log',
            ],
            'Authentication Log' => [
                'view authentication log',
            ],
            'Feedback' => [
                'view feedback',
                'delete feedback',
            ],
            'Email Log' => [
                'view email log',
            ],
            'Sms Log' => [
                'view sms log',
            ],
            'Payment Gateway' => [
                'view payment gateway',
                'add payment gateway',
                'edit payment gateway',
                'delete payment gateway',
            ],
            'Coupon' => [
                'view coupon',
                'add coupon',
                'edit coupon',
                'delete coupon',
            ],
            'Return Reason' => [
                'view return reason',
                'add return reason',
                'edit return reason',
                'delete return reason',
            ],
            'Return Request' => [
                'view return request',
                'edit return request',
            ],
            'Category' => [
                'view category',
                'add category',
                'edit category',
                'delete category',
            ],
            'Size' => [
                'view size',
                'add size',
                'edit size',
                'delete size',
            ],
            'Material' => [
                'view material',
                'add material',
                'edit material',
                'delete material',
            ],
            'Brand' => [
                'view brand',
                'add brand',
                'edit brand',
                'delete brand',
            ],
            'Offer' => [
                'view offer',
                'add offer',
                'edit offer',
                'delete offer',
            ],
            'Product Type' => [
                'view product type',
                'add product type',
                'edit product type',
                'delete product type',
            ],
            'Product' => [
                'view product',
                'add product',
                'edit product',
                'delete product',
                'publish product',
            ],
            'Unit Type' => [
                'view unit type',
                'add unit type',
                'edit unit type',
                'delete unit type',
            ],
            'Intended Use' => [
                'view intended use',
                'add intended use',
                'edit intended use',
                'delete intended use',
            ],
            'Country' => [
                'view country',
                'add country',
                'edit country',
                'delete country',
            ],
            'State' => [
                'view state',
                'add state',
                'edit state',
                'delete state',
            ],
            'Order' => [
                'view order',
                'edit order',
            ],
            'Return Policy' => [
                'view return policy',
                'add return policy',
                'edit return policy',
                'delete return policy',
            ],
            'Refund' => [
                'view refund',
                'edit refund',
            ],
            'Currency' => [
                'view currency',
                'add currency',
                'edit currency',
                'delete currency',
                'currency setup',
            ],
            'Cancel Reason' => [
                'view cancel reason',
                'add cancel reason',
                'edit cancel reason',
                'delete cancel reason',
            ],
            'Order Cancellation Request' => [
                'view order cancellation request',
                'edit order cancellation request',
            ],
            'Contact Query' => [
                'view contact query',
                'delete contact query',
            ],
            'Newsletter' => [
                'view newsletter',
                'delete newsletter',
            ],
        ];

        // Store permissions with groups
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'group' => $group
                ]);
            }
        }

        // Define Roles
        $roles = ['SUPERADMIN', 'ADMIN', 'USER'];

        // Assign Permissions to Roles
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if ($roleName === 'SUPERADMIN') {
                // Assign all permissions to ADMIN
                $role->syncPermissions(Permission::all());
            } else {
                // Keep non-admin roles empty initially
                // $role->syncPermissions([]);
            }
        }
    }
}

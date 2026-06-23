<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'product.view', 'product.create', 'product.edit', 'product.delete',
            'category.manage', 'brand.manage',
            'order.view', 'order.update', 'order.delete', 'order.status.update',
            'customer.view', 'customer.manage',
            'coupon.manage', 'inventory.manage', 'report.view',
            'banner.manage', 'page.manage', 'setting.manage',
            'role.manage', 'permission.manage',
            'newsletter.manage', 'review.manage',
            'ticket.view', 'ticket.reply',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'Super Admin' => [],
            'Admin'       => array_diff($permissions, ['role.manage', 'permission.manage']),
            'Manager'     => [
                'dashboard.view', 'product.view', 'product.create', 'product.edit',
                'category.manage', 'brand.manage', 'order.view', 'order.update',
                'order.status.update', 'customer.view', 'inventory.manage', 'report.view',
            ],
            'Sales Executive' => [
                'dashboard.view', 'order.view', 'order.update', 'order.status.update', 'customer.view',
            ],
            'Inventory Manager' => [
                'dashboard.view', 'product.view', 'inventory.manage',
            ],
            'Order Manager' => [
                'dashboard.view', 'order.view', 'order.update', 'order.status.update',
            ],
            'Customer Support' => [
                'dashboard.view', 'order.view', 'ticket.view', 'ticket.reply', 'customer.view',
            ],
            'Customer' => [],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            if (!empty($rolePermissions)) {
                $role->syncPermissions($rolePermissions);
            }
        }
    }
}

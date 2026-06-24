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
            'analytics.view', 'analytics.sales.view', 'analytics.profit_loss.view',
            'analytics.stock.view', 'analytics.customer.view', 'analytics.report.export',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.status_update',
            'customers.view', 'customers.edit', 'customers.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.assign',
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
            'Admin'       => array_diff($permissions, [
                'role.manage', 'permission.manage',
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'permissions.view', 'permissions.assign',
            ]),
            'Manager'     => [
                'dashboard.view', 'analytics.view', 'analytics.sales.view', 'analytics.stock.view', 'analytics.customer.view',
                'product.view', 'products.view', 'product.create', 'product.edit',
                'category.manage', 'brand.manage', 'order.view', 'order.update',
                'order.status.update', 'orders.view', 'orders.edit', 'orders.status_update',
                'customer.view', 'customers.view', 'inventory.manage', 'report.view',
            ],
            'Sales Manager' => [
                'dashboard.view', 'analytics.view', 'analytics.sales.view',
                'order.view', 'orders.view', 'order.update', 'orders.edit', 'order.status.update', 'orders.status_update',
            ],
            'Sales Executive' => [
                'dashboard.view', 'order.view', 'order.update', 'order.status.update', 'customer.view',
            ],
            'Inventory Manager' => [
                'dashboard.view', 'analytics.view', 'analytics.stock.view',
                'product.view', 'products.view', 'product.edit', 'products.edit', 'inventory.manage',
            ],
            'Order Manager' => [
                'dashboard.view', 'order.view', 'order.update', 'order.status.update',
            ],
            'Customer Support' => [
                'dashboard.view', 'order.view', 'ticket.view', 'ticket.reply', 'customer.view',
            ],
            'Accountant' => [
                'dashboard.view', 'analytics.view', 'analytics.sales.view',
                'analytics.profit_loss.view', 'analytics.report.export',
                'report.view',
            ],
            'Staff' => [
                'dashboard.view',
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

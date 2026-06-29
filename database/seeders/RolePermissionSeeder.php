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

        // Dead permissions to remove (orders.* — never used in code, replaced by order.*)
        $deadPermissions = [
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.status_update',
            'customers.view', 'customers.edit', 'customers.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.assign',
            'products.view', 'products.create', 'products.edit', 'products.delete',
        ];
        \Spatie\Permission\Models\Permission::whereIn('name', $deadPermissions)->delete();

        $permissions = [
            'dashboard.view',
            'analytics.view', 'analytics.sales.view', 'analytics.profit_loss.view',
            'analytics.stock.view', 'analytics.customer.view', 'analytics.report.export',
            'product.view', 'product.create', 'product.edit', 'product.delete',
            'category.manage', 'brand.manage',
            'order.view', 'order.update', 'order.delete', 'order.status.update',
            'order.payment.update',
            'order.chat',
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
                'product.view', 'product.create', 'product.edit',
                'category.manage', 'brand.manage',
                'order.view', 'order.update', 'order.status.update', 'order.payment.update', 'order.chat',
                'customer.view', 'inventory.manage', 'report.view',
            ],
            'Sales Manager' => [
                'dashboard.view', 'analytics.view', 'analytics.sales.view',
                'order.view', 'order.update', 'order.status.update', 'order.payment.update', 'order.chat',
            ],
            'Sales Executive' => [
                'dashboard.view', 'order.view', 'order.update', 'order.status.update', 'order.chat', 'customer.view',
            ],
            'Inventory Manager' => [
                'dashboard.view', 'analytics.view', 'analytics.stock.view',
                'product.view', 'product.edit', 'inventory.manage',
            ],
            'Order Manager' => [
                'dashboard.view', 'order.view', 'order.update', 'order.status.update', 'order.payment.update', 'order.chat',
            ],
            'Customer Support' => [
                'dashboard.view', 'order.view', 'order.chat', 'ticket.view', 'ticket.reply', 'customer.view',
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

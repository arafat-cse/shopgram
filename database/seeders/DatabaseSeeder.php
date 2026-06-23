<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            SettingSeeder::class,
            ShippingZoneSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}

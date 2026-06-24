<?php
namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Samsung',    'slug' => 'samsung',    'status' => 'active'],
            ['name' => 'Apple',      'slug' => 'apple',      'status' => 'active'],
            ['name' => 'Sony',       'slug' => 'sony',       'status' => 'active'],
            ['name' => 'LG',         'slug' => 'lg',         'status' => 'active'],
            ['name' => 'Xiaomi',     'slug' => 'xiaomi',     'status' => 'active'],
            ['name' => 'Walton',     'slug' => 'walton',     'status' => 'active'],
            ['name' => 'Singer',     'slug' => 'singer',     'status' => 'active'],
            ['name' => 'Philips',    'slug' => 'philips',    'status' => 'active'],
            ['name' => 'Generic',    'slug' => 'generic',    'status' => 'active'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['slug' => $brand['slug']], $brand);
        }
    }
}

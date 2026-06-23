<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Oil & Ghee',        'children' => ['Mustard Oil', 'Gawa Ghee', 'Olive Oil', 'Coconut Oil']],
            ['name' => 'Organic',           'children' => ['Organic Honey', 'Organic Tea', 'Organic Powder', 'Certified Food']],
            ['name' => 'Honey',             'children' => ['Sundarban Honey', 'Black Seed Honey', 'Lychee Flower Honey', 'Honeycomb']],
            ['name' => 'Dates',             'children' => ['Safawi Kalmi', 'Medjool', 'Sukkari', 'Ajwa', 'Mabroom']],
            ['name' => 'Spices',            'children' => ['Whole Spices', 'Basic Spices', 'Mixed Spices', 'Masala']],
            ['name' => 'Nuts & Seeds',      'children' => ['Nuts', 'Seeds', 'Cashew Nuts', 'Honey Nuts']],
            ['name' => 'Beverage',          'children' => ['Tea', 'Coffee', 'Juice', 'Health Drinks']],
            ['name' => 'Rice',              'children' => ['Aromatic Rice', 'Regular Rice', 'Rice Flour']],
            ['name' => 'Flours & Lentils',  'children' => ['Flours', 'Lentils', 'Atta', 'Dal']],
            ['name' => 'Functional Food',   'children' => ['Super Food', 'Supplements', 'Healthy Snacks']],
            ['name' => 'Pickle',            'children' => ['Mango Pickle', 'Mixed Pickle', 'Chili Pickle']],
            ['name' => 'Combos',            'children' => ['Honey Combos', 'Ghee Combos', 'Masala Combos']],
            ['name' => 'Electronics', 'children' => ['Phones', 'Laptops', 'Tablets', 'Accessories']],
            ['name' => 'Fashion',     'children' => ['Men', 'Women', 'Kids', 'Shoes']],
            ['name' => 'Home & Garden','children' => ['Furniture', 'Kitchen', 'Decor']],
            ['name' => 'Sports',      'children' => ['Fitness', 'Outdoor', 'Team Sports']],
            ['name' => 'Books',       'children' => ['Fiction', 'Non-Fiction', 'Educational']],
            ['name' => 'Beauty',      'children' => ['Skincare', 'Makeup', 'Hair Care']],
        ];

        foreach ($categories as $cat) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'status' => 'active']
            );

            foreach ($cat['children'] as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($cat['name'] . '-' . $child)],
                    ['name' => $child, 'parent_id' => $parent->id, 'status' => 'active']
                );
            }
        }
    }
}

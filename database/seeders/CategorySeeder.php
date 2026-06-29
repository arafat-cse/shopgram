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
            ['name' => 'Oil & Ghee',        'img_seed' => 'photo-1474979266404-7eaacbcd87c5', 'children' => ['Mustard Oil', 'Gawa Ghee', 'Olive Oil', 'Coconut Oil']],
            ['name' => 'Organic',           'img_seed' => 'photo-1540420773420-3366772f4999', 'children' => ['Organic Honey', 'Organic Tea', 'Organic Powder', 'Certified Food']],
            ['name' => 'Honey',             'img_seed' => 'photo-1587049352846-4a222e784d38', 'children' => ['Sundarban Honey', 'Black Seed Honey', 'Lychee Flower Honey', 'Honeycomb']],
            ['name' => 'Dates',             'img_seed' => 'photo-1596547609652-9cf5d8d76921', 'children' => ['Safawi Kalmi', 'Medjool', 'Sukkari', 'Ajwa', 'Mabroom']],
            ['name' => 'Spices',            'img_seed' => 'photo-1596790011460-155490a8a6ec', 'children' => ['Whole Spices', 'Basic Spices', 'Mixed Spices', 'Masala']],
            ['name' => 'Nuts & Seeds',      'img_seed' => 'photo-1597528380839-a9a3b680c057', 'children' => ['Nuts', 'Seeds', 'Cashew Nuts', 'Honey Nuts']],
            ['name' => 'Beverage',          'img_seed' => 'photo-1513558161293-cdaf765ed2fd', 'children' => ['Tea', 'Coffee', 'Juice', 'Health Drinks']],
            ['name' => 'Rice',              'img_seed' => 'photo-1586201375761-83865001e31c', 'children' => ['Aromatic Rice', 'Regular Rice', 'Rice Flour']],
            ['name' => 'Flours & Lentils',  'img_seed' => 'photo-1542990253-0d0f5be5f0ed', 'children' => ['Flours', 'Lentils', 'Atta', 'Dal']],
            ['name' => 'Functional Food',   'img_seed' => 'photo-1622484211148-716598e0db01', 'children' => ['Super Food', 'Supplements', 'Healthy Snacks']],
            ['name' => 'Pickle',            'img_seed' => 'photo-1601004890684-d8cbf643f5f2', 'children' => ['Mango Pickle', 'Mixed Pickle', 'Chili Pickle']],
            ['name' => 'Combos',            'img_seed' => 'photo-1542838132-92c53300491e', 'children' => ['Honey Combos', 'Ghee Combos', 'Masala Combos']],
            ['name' => 'Electronics',       'img_seed' => 'photo-1498049794561-7780e7231661', 'children' => ['Phones', 'Laptops', 'Tablets', 'Accessories']],
            ['name' => 'Fashion',           'img_seed' => 'photo-1483985988355-763728e1935b', 'children' => ['Men', 'Women', 'Kids', 'Shoes']],
            ['name' => 'Home & Garden',     'img_seed' => 'photo-1616486338812-3dadae4b4ace', 'children' => ['Furniture', 'Kitchen', 'Decor']],
            ['name' => 'Sports',            'img_seed' => 'photo-1461896836934-ffe607ba8211', 'children' => ['Fitness', 'Outdoor', 'Team Sports']],
            ['name' => 'Books',             'img_seed' => 'photo-1495446815901-a7297e633e8d', 'children' => ['Fiction', 'Non-Fiction', 'Educational']],
            ['name' => 'Beauty',            'img_seed' => 'photo-1522335789203-aabd1fc54bc9', 'children' => ['Skincare', 'Makeup', 'Hair Care']],
        ];

        foreach ($categories as $cat) {
            $imgSeed = $cat['img_seed'] ?? null;
            $slug = Str::slug($cat['name']);
            
            $existingCategory = Category::where('slug', $slug)->first();
            
            $imagePath = null;
            if ($imgSeed) {
                if (!$existingCategory || !$existingCategory->image || !str_contains($existingCategory->image, $imgSeed)) {
                    $imagePath = $this->downloadImage($imgSeed);
                } else {
                    $imagePath = $existingCategory->image;
                }
            }

            $parent = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $cat['name'],
                    'image' => $imagePath ?: ($existingCategory->image ?? null),
                    'status' => 'active'
                ]
            );

            foreach ($cat['children'] as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($cat['name'] . '-' . $child)],
                    ['name' => $child, 'parent_id' => $parent->id, 'status' => 'active']
                );
            }
        }
    }

    private function downloadImage(string $seed): ?string
    {
        try {
            if (str_starts_with($seed, 'photo-')) {
                $url = "https://images.unsplash.com/{$seed}?w=300&h=300&fit=crop&q=80";
            } else {
                $url = "https://picsum.photos/seed/{$seed}/300/300";
            }
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
            if ($response->successful()) {
                $filename = "categories/{$seed}-" . uniqid() . '.jpg';
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception) {
            // Silently fail
        }
        return null;
    }
}

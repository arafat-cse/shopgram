<?php
namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Samsung',    'slug' => 'samsung',    'status' => 'active', 'logo_seed' => 'photo-1610945265064-0e34e5519bbf'],
            ['name' => 'Apple',      'slug' => 'apple',      'status' => 'active', 'logo_seed' => 'photo-1563206767-5b18f218e8de'],
            ['name' => 'Sony',       'slug' => 'sony',       'status' => 'active', 'logo_seed' => 'photo-1526512340740-9217d0159da9'],
            ['name' => 'LG',         'slug' => 'lg',         'status' => 'active', 'logo_seed' => 'photo-1546868871-7041f2a55e12'],
            ['name' => 'Xiaomi',     'slug' => 'xiaomi',     'status' => 'active', 'logo_seed' => 'photo-1598327105666-5b89351aff97'],
            ['name' => 'Walton',     'slug' => 'walton',     'status' => 'active', 'logo_seed' => 'photo-1605787020600-b9ebd5df1d07'],
            ['name' => 'Singer',     'slug' => 'singer',     'status' => 'active', 'logo_seed' => 'photo-1523275335684-37898b6baf30'],
            ['name' => 'Philips',    'slug' => 'philips',    'status' => 'active', 'logo_seed' => 'photo-1558089687-f282ffcbd1d5'],
            ['name' => 'Generic',    'slug' => 'generic',    'status' => 'active', 'logo_seed' => 'photo-1618005182384-a83a8bd57fbe'],
        ];

        foreach ($brands as $brandData) {
            $existingBrand = Brand::where('slug', $brandData['slug'])->first();
            $logoSeed = $brandData['logo_seed'];

            $logoPath = null;
            if ($logoSeed) {
                if (!$existingBrand || !$existingBrand->logo || !file_exists(public_path($existingBrand->logo)) || !str_contains($existingBrand->logo, $logoSeed)) {
                    $logoPath = $this->downloadImage($logoSeed);
                } else {
                    $logoPath = $existingBrand->logo;
                }
            }

            Brand::updateOrCreate(
                ['slug' => $brandData['slug']],
                [
                    'name' => $brandData['name'],
                    'logo' => $logoPath ?: ($existingBrand->logo ?? null),
                    'status' => $brandData['status'],
                ]
            );
        }
    }

    private function downloadImage(string $seed): ?string
    {
        try {
            if (str_starts_with($seed, 'photo-')) {
                $url = "https://images.unsplash.com/{$seed}?w=200&h=200&fit=crop&q=80";
            } else {
                $url = "https://picsum.photos/seed/{$seed}/200/200";
            }
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $dir = public_path('images/brands');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $filename = "{$seed}-" . uniqid() . '.jpg';
                file_put_contents("{$dir}/{$filename}", $response->body());
                return "images/brands/{$filename}";
            }
        } catch (\Exception) {
            // Silently fail
        }
        return null;
    }
}

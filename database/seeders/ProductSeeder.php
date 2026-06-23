<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create brands first
        $brands = $this->seedBrands();

        $products = [
            // ── Electronics / Phones (cat 2)
            [
                'name'           => 'Samsung Galaxy A55 5G',
                'category_id'    => 2,
                'brand_id'       => $brands['Samsung'],
                'regular_price'  => 47000,
                'sale_price'     => 44500,
                'purchase_price' => 40000,
                'stock_quantity' => 35,
                'short_description' => '6.6" Super AMOLED, 50MP Camera, 5000mAh Battery, 8GB RAM 256GB Storage',
                'description'    => '<p>Samsung Galaxy A55 5G features a stunning 6.6-inch Super AMOLED display with 120Hz refresh rate. Powered by Exynos 1480 processor with 8GB RAM. Triple camera setup with 50MP main sensor. 5000mAh battery with 25W fast charging. Available in Awesome Iceblue and Awesome Navy.</p>',
                'sku'            => 'SAM-A55-5G',
                'is_featured'    => true,
                'is_new_arrival' => true,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'samsung-phone',
                'variants'       => [
                    ['color' => 'Awesome Iceblue', 'extra_price' => 0, 'stock' => 18],
                    ['color' => 'Awesome Navy',    'extra_price' => 0, 'stock' => 17],
                ],
            ],
            [
                'name'           => 'iPhone 15 128GB',
                'category_id'    => 2,
                'brand_id'       => $brands['Apple'],
                'regular_price'  => 120000,
                'sale_price'     => 115000,
                'purchase_price' => 105000,
                'stock_quantity' => 20,
                'short_description' => '6.1" OLED, A16 Bionic, 48MP Main Camera, Dynamic Island, USB-C',
                'description'    => '<p>iPhone 15 brings Dynamic Island and a 48MP main camera to the standard lineup. Powered by the A16 Bionic chip. USB-C connector for universal charging. Available in multiple stunning colors.</p>',
                'sku'            => 'APL-IP15-128',
                'is_featured'    => true,
                'is_new_arrival' => true,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'iphone-apple',
                'variants'       => [
                    ['color' => 'Black',    'extra_price' => 0,    'stock' => 5],
                    ['color' => 'Blue',     'extra_price' => 0,    'stock' => 5],
                    ['color' => 'Pink',     'extra_price' => 0,    'stock' => 5],
                    ['color' => 'Yellow',   'extra_price' => 0,    'stock' => 5],
                ],
            ],
            [
                'name'           => 'Xiaomi Redmi Note 13 Pro',
                'category_id'    => 2,
                'brand_id'       => $brands['Xiaomi'],
                'regular_price'  => 28000,
                'sale_price'     => 25500,
                'purchase_price' => 22000,
                'stock_quantity' => 50,
                'short_description' => '6.67" AMOLED 120Hz, 200MP Camera, 5100mAh Battery, 67W Fast Charge',
                'description'    => '<p>Redmi Note 13 Pro features an incredible 200MP camera system, 120Hz AMOLED display, and a massive 5100mAh battery with 67W HyperCharge technology. Perfect for photography enthusiasts on a budget.</p>',
                'sku'            => 'XMI-RN13P',
                'is_featured'    => false,
                'is_new_arrival' => true,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'xiaomi-redmi',
                'variants'       => [],
            ],

            // ── Electronics / Laptops (cat 3)
            [
                'name'           => 'HP Pavilion 15 Core i5',
                'category_id'    => 3,
                'brand_id'       => $brands['HP'],
                'regular_price'  => 72000,
                'sale_price'     => 68000,
                'purchase_price' => 60000,
                'stock_quantity' => 15,
                'short_description' => 'Intel Core i5-1235U, 8GB DDR4, 512GB SSD, 15.6" FHD IPS, Windows 11',
                'description'    => '<p>HP Pavilion 15 is the perfect everyday laptop. Intel 12th Gen Core i5 processor handles multitasking with ease. 15.6-inch Full HD IPS display with anti-glare coating. 512GB NVMe SSD for fast boot times. Backlit keyboard and HP Fast Charge.</p>',
                'sku'            => 'HP-PAV15-I5',
                'is_featured'    => true,
                'is_new_arrival' => false,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'hp-laptop',
                'variants'       => [
                    ['custom_option' => '8GB RAM / 512GB SSD',  'extra_price' => 0,     'stock' => 10],
                    ['custom_option' => '16GB RAM / 512GB SSD', 'extra_price' => 8000,  'stock' => 5],
                ],
            ],
            [
                'name'           => 'Dell Inspiron 14 AMD',
                'category_id'    => 3,
                'brand_id'       => $brands['Dell'],
                'regular_price'  => 75000,
                'sale_price'     => null,
                'purchase_price' => 64000,
                'stock_quantity' => 10,
                'short_description' => 'AMD Ryzen 5 7530U, 16GB RAM, 512GB SSD, 14" FHD, Backlit KB',
                'description'    => '<p>Dell Inspiron 14 with AMD Ryzen 5 7530U offers exceptional performance per watt. 16GB DDR4 RAM ensures smooth multitasking. 14-inch FHD display with narrow bezels. Up to 10 hours battery life. Ideal for students and professionals.</p>',
                'sku'            => 'DELL-INS14-R5',
                'is_featured'    => false,
                'is_new_arrival' => false,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'dell-laptop',
                'variants'       => [],
            ],

            // ── Electronics / Accessories (cat 5)
            [
                'name'           => 'TWS Wireless Earbuds Pro',
                'category_id'    => 5,
                'brand_id'       => null,
                'regular_price'  => 1800,
                'sale_price'     => 1350,
                'purchase_price' => 900,
                'stock_quantity' => 100,
                'short_description' => 'Active Noise Cancellation, 30hr Battery, IPX5 Waterproof, Bluetooth 5.3',
                'description'    => '<p>Premium TWS earbuds with Active Noise Cancellation. 30-hour total battery life with charging case. IPX5 water resistance for workouts. Bluetooth 5.3 for stable connectivity. Touch controls and voice assistant support.</p>',
                'sku'            => 'TWS-EARBUD-PRO',
                'is_featured'    => false,
                'is_new_arrival' => true,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'earbuds-wireless',
                'variants'       => [
                    ['color' => 'Black', 'extra_price' => 0,   'stock' => 50],
                    ['color' => 'White', 'extra_price' => 0,   'stock' => 50],
                ],
            ],

            // ── Fashion / Men (cat 7)
            [
                'name'           => 'Premium Cotton Round Neck T-Shirt',
                'category_id'    => 7,
                'brand_id'       => null,
                'regular_price'  => 550,
                'sale_price'     => 420,
                'purchase_price' => 220,
                'stock_quantity' => 200,
                'short_description' => '100% Combed Cotton, Anti-shrink, Soft & Breathable, Available in 8 Colors',
                'description'    => '<p>Premium quality 100% combed cotton t-shirt. Anti-shrink treatment ensures it maintains shape after multiple washes. Ultra-soft and breathable fabric perfect for Bangladesh weather. Available in 8 vibrant colors. Regular fit design.</p>',
                'sku'            => 'TSHIRT-RN-MEN',
                'is_featured'    => false,
                'is_new_arrival' => false,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'cotton-tshirt-men',
                'variants'       => [
                    ['size' => 'S',  'color' => 'Black',  'extra_price' => 0, 'stock' => 25],
                    ['size' => 'M',  'color' => 'Black',  'extra_price' => 0, 'stock' => 30],
                    ['size' => 'L',  'color' => 'Black',  'extra_price' => 0, 'stock' => 30],
                    ['size' => 'XL', 'color' => 'Black',  'extra_price' => 0, 'stock' => 20],
                    ['size' => 'S',  'color' => 'White',  'extra_price' => 0, 'stock' => 20],
                    ['size' => 'M',  'color' => 'White',  'extra_price' => 0, 'stock' => 30],
                    ['size' => 'L',  'color' => 'White',  'extra_price' => 0, 'stock' => 25],
                    ['size' => 'XL', 'color' => 'White',  'extra_price' => 0, 'stock' => 20],
                ],
            ],
            [
                'name'           => 'Slim Fit Denim Jeans',
                'category_id'    => 7,
                'brand_id'       => null,
                'regular_price'  => 1500,
                'sale_price'     => 1200,
                'purchase_price' => 700,
                'stock_quantity' => 80,
                'short_description' => '98% Cotton 2% Spandex, Slim Fit, 5 Pocket Design, Stretchable',
                'description'    => '<p>Premium slim fit denim jeans with 2% spandex for comfort and stretch. 5-pocket classic design. Mid-rise waist. Machine washable. Available in waist sizes 28 to 36.</p>',
                'sku'            => 'JEANS-SLIM-MEN',
                'is_featured'    => false,
                'is_new_arrival' => false,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'denim-jeans-men',
                'variants'       => [
                    ['size' => '28', 'color' => 'Dark Blue', 'extra_price' => 0, 'stock' => 10],
                    ['size' => '30', 'color' => 'Dark Blue', 'extra_price' => 0, 'stock' => 20],
                    ['size' => '32', 'color' => 'Dark Blue', 'extra_price' => 0, 'stock' => 20],
                    ['size' => '34', 'color' => 'Dark Blue', 'extra_price' => 0, 'stock' => 15],
                    ['size' => '36', 'color' => 'Dark Blue', 'extra_price' => 0, 'stock' => 15],
                ],
            ],

            // ── Fashion / Women (cat 8)
            [
                'name'           => 'Floral Print Cotton Kurti',
                'category_id'    => 8,
                'brand_id'       => null,
                'regular_price'  => 950,
                'sale_price'     => 750,
                'purchase_price' => 380,
                'stock_quantity' => 120,
                'short_description' => 'Pure Cotton, Hand Block Print, A-Line Cut, Breathable Fabric',
                'description'    => '<p>Beautiful hand block printed floral kurti in pure cotton fabric. A-line cut with 3/4 sleeves. Comfortable for daily wear in summer. Machine washable. Available in multiple colors and sizes.</p>',
                'sku'            => 'KURTI-FLORAL-W',
                'is_featured'    => true,
                'is_new_arrival' => true,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'floral-kurti-women',
                'variants'       => [
                    ['size' => 'S',  'color' => 'Red',    'extra_price' => 0, 'stock' => 15],
                    ['size' => 'M',  'color' => 'Red',    'extra_price' => 0, 'stock' => 20],
                    ['size' => 'L',  'color' => 'Red',    'extra_price' => 0, 'stock' => 20],
                    ['size' => 'XL', 'color' => 'Red',    'extra_price' => 0, 'stock' => 15],
                    ['size' => 'M',  'color' => 'Green',  'extra_price' => 0, 'stock' => 25],
                    ['size' => 'L',  'color' => 'Green',  'extra_price' => 0, 'stock' => 25],
                ],
            ],

            // ── Fashion / Shoes (cat 10)
            [
                'name'           => 'Running Sport Shoes',
                'category_id'    => 10,
                'brand_id'       => null,
                'regular_price'  => 3200,
                'sale_price'     => 2600,
                'purchase_price' => 1500,
                'stock_quantity' => 60,
                'short_description' => 'Lightweight EVA Sole, Mesh Upper, Anti-slip, Shock Absorption',
                'description'    => '<p>High-performance running shoes with lightweight EVA midsole for superior cushioning. Breathable mesh upper keeps feet cool during intense workouts. Anti-slip rubber outsole for stability on all surfaces. Reflective details for night running safety.</p>',
                'sku'            => 'SHOES-RUN-SPORT',
                'is_featured'    => true,
                'is_new_arrival' => false,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'running-shoes',
                'variants'       => [
                    ['size' => '40', 'color' => 'Black/White', 'extra_price' => 0, 'stock' => 10],
                    ['size' => '41', 'color' => 'Black/White', 'extra_price' => 0, 'stock' => 15],
                    ['size' => '42', 'color' => 'Black/White', 'extra_price' => 0, 'stock' => 15],
                    ['size' => '43', 'color' => 'Black/White', 'extra_price' => 0, 'stock' => 10],
                    ['size' => '44', 'color' => 'Black/White', 'extra_price' => 0, 'stock' => 10],
                ],
            ],

            // ── Home & Garden / Kitchen (cat 13)
            [
                'name'           => 'Non-Stick Granite Frying Pan 26cm',
                'category_id'    => 13,
                'brand_id'       => null,
                'regular_price'  => 1500,
                'sale_price'     => 1150,
                'purchase_price' => 650,
                'stock_quantity' => 45,
                'short_description' => 'German Granite Coating, Heat Resistant Handle, Induction Compatible, PFOA Free',
                'description'    => '<p>Premium non-stick frying pan with 5-layer German granite coating. PFOA and PFOS free. Compatible with all cooktops including induction. Heat-resistant bakelite handle stays cool. Dishwasher safe. 26cm diameter perfect for everyday cooking.</p>',
                'sku'            => 'PAN-GRANITE-26',
                'is_featured'    => false,
                'is_new_arrival' => false,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'frying-pan-kitchen',
                'variants'       => [],
            ],
            [
                'name'           => 'Electric Rice Cooker 1.8L',
                'category_id'    => 13,
                'brand_id'       => null,
                'regular_price'  => 2800,
                'sale_price'     => 2400,
                'purchase_price' => 1600,
                'stock_quantity' => 30,
                'short_description' => '1.8L Capacity, Auto Keep Warm, Non-Stick Inner Pot, 600W',
                'description'    => '<p>Automatic electric rice cooker with 1.8L capacity — perfect for 4-6 people. Non-stick inner pot for easy cleaning. Auto switch to keep-warm mode after cooking. Cool-touch outer body. Comes with measuring cup and serving spatula. 600W power.</p>',
                'sku'            => 'RC-ELEC-18L',
                'is_featured'    => false,
                'is_new_arrival' => true,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'rice-cooker',
                'variants'       => [],
            ],

            // ── Sports / Fitness (cat 16)
            [
                'name'           => 'Yoga & Exercise Mat 6mm',
                'category_id'    => 16,
                'brand_id'       => null,
                'regular_price'  => 900,
                'sale_price'     => 750,
                'purchase_price' => 380,
                'stock_quantity' => 70,
                'short_description' => '6mm Thick TPE Foam, Non-slip, Eco Friendly, 183×61cm with Carry Strap',
                'description'    => '<p>Premium 6mm thick TPE yoga mat made from eco-friendly material. Double-sided non-slip texture for stability. Moisture-resistant and easy to clean. 183×61cm standard yoga mat size. Lightweight with carry strap included. Ideal for yoga, pilates, and exercise.</p>',
                'sku'            => 'YOGA-MAT-6MM',
                'is_featured'    => false,
                'is_new_arrival' => false,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'yoga-mat-exercise',
                'variants'       => [
                    ['color' => 'Purple', 'extra_price' => 0,  'stock' => 25],
                    ['color' => 'Blue',   'extra_price' => 0,  'stock' => 25],
                    ['color' => 'Green',  'extra_price' => 0,  'stock' => 20],
                ],
            ],

            // ── Beauty / Skincare (cat 24)
            [
                'name'           => 'Vitamin C Brightening Face Serum 30ml',
                'category_id'    => 24,
                'brand_id'       => null,
                'regular_price'  => 750,
                'sale_price'     => 600,
                'purchase_price' => 280,
                'stock_quantity' => 90,
                'short_description' => '20% Vitamin C, Hyaluronic Acid, Anti-aging, Brightening, All Skin Types',
                'description'    => '<p>Advanced brightening serum with 20% stabilized Vitamin C and Hyaluronic Acid. Reduces dark spots, evens skin tone, and boosts collagen production. Lightweight formula absorbs quickly. Suitable for all skin types. Use morning and evening for best results. Dermatologist tested.</p>',
                'sku'            => 'SERUM-VITC-30ML',
                'is_featured'    => true,
                'is_new_arrival' => true,
                'is_best_selling' => false,
                'status'         => 'active',
                'img_seed'       => 'vitamin-c-serum',
                'variants'       => [],
            ],

            // ── Books / Educational (cat 22)
            [
                'name'           => 'IELTS Complete Preparation Book 2024',
                'category_id'    => 22,
                'brand_id'       => null,
                'regular_price'  => 700,
                'sale_price'     => 580,
                'purchase_price' => 300,
                'stock_quantity' => 40,
                'short_description' => 'Reading, Writing, Listening & Speaking, 5 Full Practice Tests, Latest Edition',
                'description'    => '<p>Comprehensive IELTS preparation guide with complete coverage of all four modules. Includes 5 full-length practice tests with answer keys and audio scripts. Expert tips and strategies from certified IELTS trainers. Updated for the latest IELTS format. Band 7+ guaranteed with consistent practice.</p>',
                'sku'            => 'BOOK-IELTS-2024',
                'is_featured'    => false,
                'is_new_arrival' => false,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'ielts-book',
                'variants'       => [],
            ],
            [
                'name'           => 'Himu Somogro — Humayun Ahmed',
                'category_id'    => 20,
                'brand_id'       => null,
                'regular_price'  => 850,
                'sale_price'     => null,
                'purchase_price' => 420,
                'stock_quantity' => 25,
                'short_description' => 'হিমু সমগ্র — হুমায়ূন আহমেদ রচিত হিমু সিরিজের সকল বই একত্রে',
                'description'    => '<p>হুমায়ূন আহমেদের অমর সৃষ্টি হিমু সমগ্র। হিমু সিরিজের সকল উপন্যাস একটি সংকলনে। মিসির আলির পর হিমু বাংলা সাহিত্যের সবচেয়ে জনপ্রিয় চরিত্রগুলোর একটি। হলুদ পাঞ্জাবি পরিহিত এই অদ্ভুত মানুষটির জগতে ডুব দিন।</p>',
                'sku'            => 'BOOK-HIMU-SOMOGRO',
                'is_featured'    => false,
                'is_new_arrival' => false,
                'is_best_selling' => true,
                'status'         => 'active',
                'img_seed'       => 'bangla-book',
                'variants'       => [],
            ],
        ];

        foreach ($products as $data) {
            $variants = $data['variants'];
            $imgSeed  = $data['img_seed'];
            unset($data['variants'], $data['img_seed']);

            $data['slug']              = Str::slug($data['name']);
            $data['low_stock_threshold'] = 5;

            // Download & store thumbnail
            $thumbnail = $this->downloadImage($imgSeed);
            if ($thumbnail) {
                $data['thumbnail'] = $thumbnail;
            }

            $product = Product::create($data);

            // Create variants
            foreach ($variants as $v) {
                $stock = $v['stock'];
                unset($v['stock']);
                $product->variants()->create([
                    'size'          => $v['size']          ?? null,
                    'color'         => $v['color']         ?? null,
                    'custom_option' => $v['custom_option'] ?? null,
                    'price'         => $product->regular_price + ($v['extra_price'] ?? 0) ?: null,
                    'stock_quantity'=> $stock,
                    'sku'           => $product->sku . '-' . Str::slug(implode('-', array_filter([$v['size'] ?? null, $v['color'] ?? null, $v['custom_option'] ?? null]))),
                ]);
            }

            $this->command->getOutput()->writeln("  <info>✓</info> {$product->name}");
        }
    }

    private function downloadImage(string $seed): ?string
    {
        try {
            $url      = "https://picsum.photos/seed/{$seed}/600/600";
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $filename = "products/{$seed}-" . uniqid() . '.jpg';
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception) {
            // Silently fail — product will show no-image.png
        }
        return null;
    }

    private function seedBrands(): array
    {
        $brandData = [
            ['name' => 'Samsung', 'status' => 'active'],
            ['name' => 'Apple',   'status' => 'active'],
            ['name' => 'Xiaomi',  'status' => 'active'],
            ['name' => 'HP',      'status' => 'active'],
            ['name' => 'Dell',    'status' => 'active'],
        ];

        $map = [];
        foreach ($brandData as $b) {
            $brand        = Brand::firstOrCreate(['name' => $b['name']], ['slug' => Str::slug($b['name']), 'status' => $b['status']]);
            $map[$b['name']] = $brand->id;
        }
        return $map;
    }
}

<?php
namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            // Hero slides — same content as fallback slides in home view
            [
                'title'       => 'Up to 50% OFF',
                'subtitle'    => 'Flash Sale — Today Only',
                'button_text' => 'Shop Now',
                'button_url'  => '/products',
                'image'       => null,
                'type'        => 'hero',
                'sort_order'  => 1,
                'status'      => 'active',
            ],
            [
                'title'       => 'New Arrivals',
                'subtitle'    => 'Just Dropped — Fresh Picks',
                'button_text' => 'Explore Now',
                'button_url'  => '/products',
                'image'       => null,
                'type'        => 'hero',
                'sort_order'  => 2,
                'status'      => 'active',
            ],
            [
                'title'       => 'Grab Big Deals',
                'subtitle'    => 'Exclusive Deal — Best Value',
                'button_text' => 'See Offers',
                'button_url'  => '/products',
                'image'       => null,
                'type'        => 'hero',
                'sort_order'  => 3,
                'status'      => 'active',
            ],
            // Promo card (right side)
            [
                'title'       => 'Exclusive Offers',
                'subtitle'    => "Don't Miss Out",
                'button_text' => 'View All Deals',
                'button_url'  => '/products',
                'image'       => null,
                'type'        => 'promo',
                'sort_order'  => 1,
                'status'      => 'active',
            ],
        ];

        foreach ($banners as $data) {
            Banner::firstOrCreate(
                ['title' => $data['title'], 'type' => $data['type']],
                $data
            );
        }
    }
}

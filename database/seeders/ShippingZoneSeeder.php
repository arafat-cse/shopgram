<?php
namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Inside Dhaka City', 'charge' => 60,  'free_above' => 2000, 'status' => 'active'],
            ['name' => 'Outside Dhaka',     'charge' => 120, 'free_above' => 3000, 'status' => 'active'],
            ['name' => 'Chittagong',        'charge' => 130, 'free_above' => 3000, 'status' => 'active'],
            ['name' => 'Nationwide',        'charge' => 150, 'free_above' => 5000, 'status' => 'active'],
        ];

        foreach ($zones as $zone) {
            ShippingZone::firstOrCreate(['name' => $zone['name']], $zone);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class PromotedProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::whereIn('id', [1, 2, 3])->update(['is_promoted' => true]);

        $this->command->info('3 products marked as promoted.');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add plain index on user_id so the FK backing index exists before we drop the unique
        \DB::statement('ALTER TABLE reviews ADD INDEX reviews_user_id_index (user_id)');
        \DB::statement('ALTER TABLE reviews DROP INDEX reviews_user_id_product_id_unique');
        \DB::statement('ALTER TABLE reviews ADD UNIQUE reviews_user_product_order_unique (user_id, product_id, order_id)');
    }

    public function down(): void
    {
        \DB::statement('DROP INDEX reviews_user_product_order_unique ON reviews');
        \DB::statement('ALTER TABLE reviews ADD UNIQUE reviews_user_id_product_id_unique (user_id, product_id)');
        \DB::statement('DROP INDEX reviews_user_id_index ON reviews');
    }
};

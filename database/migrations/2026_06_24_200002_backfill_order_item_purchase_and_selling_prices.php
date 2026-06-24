<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            UPDATE order_items
            LEFT JOIN products ON products.id = order_items.product_id
            SET
                order_items.selling_price = CASE
                    WHEN order_items.selling_price = 0 THEN order_items.unit_price
                    ELSE order_items.selling_price
                END,
                order_items.purchase_price = CASE
                    WHEN order_items.purchase_price = 0 THEN COALESCE(products.purchase_price, 0)
                    ELSE order_items.purchase_price
                END
        ");
    }

    public function down(): void
    {
        //
    }
};

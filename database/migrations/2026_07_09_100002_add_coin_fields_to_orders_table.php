<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('coins_earned')->default(0)->after('total');
            $table->timestamp('coins_awarded_at')->nullable()->after('coins_earned');
            $table->timestamp('coins_reversed_at')->nullable()->after('coins_awarded_at');
            $table->unsignedInteger('coins_used')->default(0)->after('coins_reversed_at');
            $table->boolean('is_coin_redemption')->default(false)->after('coins_used');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'coins_earned', 'coins_awarded_at', 'coins_reversed_at',
                'coins_used', 'is_coin_redemption',
            ]);
        });
    }
};

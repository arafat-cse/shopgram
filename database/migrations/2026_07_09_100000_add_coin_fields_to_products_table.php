<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('coin_reward')->default(0)->after('is_promoted');
            $table->boolean('is_coin_redeemable')->default(false)->after('coin_reward');
            $table->unsignedInteger('coin_price')->nullable()->after('is_coin_redeemable');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['coin_reward', 'is_coin_redeemable', 'coin_price']);
        });
    }
};

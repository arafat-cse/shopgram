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
        // email → nullable (phone becomes primary login identifier)
        \DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
        // phone → not nullable + unique
        \DB::statement('UPDATE users SET phone = CONCAT("temp_", id) WHERE phone IS NULL');
        \DB::statement('ALTER TABLE users MODIFY phone VARCHAR(20) NOT NULL');
        \DB::statement('ALTER TABLE users ADD UNIQUE KEY users_phone_unique (phone)');
    }

    public function down(): void
    {
        \DB::statement('ALTER TABLE users DROP INDEX users_phone_unique');
        \DB::statement('ALTER TABLE users MODIFY phone VARCHAR(255) NULL');
        \DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
    }
};

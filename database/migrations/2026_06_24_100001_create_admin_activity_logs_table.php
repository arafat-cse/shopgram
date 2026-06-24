<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action', 50);          // created, updated, deleted, status_changed, blocked, etc.
            $table->string('model_type', 100)->nullable(); // Product, Order, User, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description');          // Human-readable: "Created product iPhone 15"
            $table->json('meta')->nullable();        // Extra data: old_status, new_status, etc.
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};

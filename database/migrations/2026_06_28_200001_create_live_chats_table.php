<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('live_chats', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 36)->unique();
            $table->string('guest_name', 255);
            $table->string('guest_phone', 30);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['waiting', 'active', 'closed'])->default('waiting');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('live_chats'); }
};

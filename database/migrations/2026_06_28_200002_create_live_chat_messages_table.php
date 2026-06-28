<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('live_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('live_chats')->cascadeOnDelete();
            $table->enum('sender_type', ['guest', 'staff']);
            $table->string('sender_name', 255);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('attachment', 500)->nullable();
            $table->enum('attachment_type', ['image', 'file'])->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->unsignedInteger('attachment_size')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('live_chat_messages'); }
};

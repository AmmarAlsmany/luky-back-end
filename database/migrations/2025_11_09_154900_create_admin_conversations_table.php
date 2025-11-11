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
        Schema::create('admin_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // client or provider
            $table->enum('user_type', ['client', 'provider']);
            $table->foreignId('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('admin_unread_count')->default(0);
            $table->unsignedInteger('user_unread_count')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['admin_id', 'last_message_at']);
            $table->index(['user_id', 'user_type']);
            $table->unique(['admin_id', 'user_id', 'user_type']);
        });

        Schema::create('admin_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('admin_conversations')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->enum('sender_type', ['admin', 'client', 'provider']);
            $table->enum('message_type', ['text', 'image', 'file'])->default('text');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['conversation_id', 'created_at']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_messages');
        Schema::dropIfExists('admin_conversations');
    }
};

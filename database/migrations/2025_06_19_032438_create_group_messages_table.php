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
        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->text('content')->nullable(); // Text message content
            $table->string('file_path')->nullable(); // File upload path
            $table->string('file_name')->nullable(); // Original file name
            $table->string('file_type')->nullable(); // File type (image, document, etc.)
            $table->string('file_size')->nullable(); // File size
            $table->enum('message_type', ['text', 'file', 'photo', 'video'])->default('text');
            $table->foreignId('group_chat_id')->constrained('group_chats')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['group_chat_id', 'created_at']);
            $table->index('sender_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_messages');
    }
};

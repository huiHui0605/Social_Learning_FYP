<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'shared_post' to the message_type ENUM
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('text', 'file', 'photo', 'video', 'shared_post') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'shared_post' from the message_type ENUM
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('text', 'file', 'photo', 'video') DEFAULT 'text'");
    }
};

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
        // Comment out courses table creation since it already exists
        /*
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->string('semester')->nullable();
            $table->string('academic_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        */

        // Create course enrollments table
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();
            
            // Prevent duplicate enrollments
            $table->unique(['course_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
        // Schema::dropIfExists('courses');
    }
};

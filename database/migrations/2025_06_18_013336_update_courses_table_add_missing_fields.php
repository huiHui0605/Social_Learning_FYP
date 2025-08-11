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
        // Add missing fields to courses table if they don't exist
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'course_code')) {
                $table->string('course_code')->unique();
            }
            if (!Schema::hasColumn('courses', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('courses', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('courses', 'lecturer_id')) {
                $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('courses', 'semester')) {
                $table->string('semester')->nullable();
            }
            if (!Schema::hasColumn('courses', 'academic_year')) {
                $table->string('academic_year')->nullable();
            }
            if (!Schema::hasColumn('courses', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        // Create course enrollments table if it doesn't exist
        if (!Schema::hasTable('course_enrollments')) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
        
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'course_code',
                'title', 
                'description',
                'lecturer_id',
                'semester',
                'academic_year',
                'is_active'
            ]);
        });
    }
};

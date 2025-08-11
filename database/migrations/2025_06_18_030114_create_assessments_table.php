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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->date('due_date');
            $table->integer('total_marks');
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();
        });

        // Create assessment submissions table
        Schema::create('assessment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('submission_content');
            $table->string('attachment_path')->nullable();
            $table->integer('marks_obtained')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['submitted', 'graded', 'late'])->default('submitted');
            $table->timestamp('submitted_at');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_submissions');
        Schema::dropIfExists('assessments');
    }
};

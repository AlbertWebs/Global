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
        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('academic_year');
            $table->string('term'); // e.g., 'Term 1', 'Term 2', 'Term 3'
            $table->string('exam_type'); // e.g., 'Midterm', 'Final', 'Assignment', 'Quiz'
            $table->decimal('score', 5, 2); // Score out of 100
            $table->string('grade')->nullable(); // e.g., 'A', 'B', 'C', 'D', 'F'
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'published', 'archived'])->default('pending');
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};

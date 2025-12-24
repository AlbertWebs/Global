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
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('academic_year', 50); // Limit length for composite index compatibility
            $table->string('term', 50); // Limit length for composite index compatibility
            $table->date('registration_date')->default(now());
            $table->enum('status', ['registered', 'completed', 'dropped'])->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure a student can't register for the same course in the same term/year twice
            $table->unique(['student_id', 'course_id', 'academic_year', 'term'], 'unique_student_course_registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};

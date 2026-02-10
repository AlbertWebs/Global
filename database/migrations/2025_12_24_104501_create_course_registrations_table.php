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
            $table->date('registration_date')->default(now());
            $table->decimal('agreed_amount', 10, 2)->default(0); // New field for agreed price
            $table->enum('status', ['registered', 'completed', 'dropped'])->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure a student can't register for the same course twice
            $table->unique(['student_id', 'course_id'], 'unique_student_course_registration');
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

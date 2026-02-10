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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->timestamps();

            $table->unique(['teacher_id', 'student_id', 'course_id', 'date'], 'unique_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

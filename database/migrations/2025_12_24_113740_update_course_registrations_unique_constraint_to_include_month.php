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
        Schema::table('course_registrations', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique('unique_student_course_registration');
            
            // Create new unique constraint that includes month and year
            // This allows the same course to be registered in different months
            // Note: Using limited string lengths to avoid MySQL key length issues
            $table->unique(['student_id', 'course_id', 'academic_year', 'month', 'year'], 'unique_student_course_month_registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropUnique('unique_student_course_month_registration');
            
            // Restore the old constraint
            $table->unique(['student_id', 'course_id', 'academic_year'], 'unique_student_course_registration');
        });
    }
};

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
            // Drop the unique constraint that includes term
            $table->dropUnique('unique_student_course_registration');
            
            // Remove term column
            $table->dropColumn('term');
            
            // Add month and year columns for monthly tracking
            $table->string('month', 50)->nullable()->after('academic_year'); // e.g., "2024-12" or "December 2024"
            $table->integer('year')->nullable()->after('month');
            
            // Create new unique constraint without term
            $table->unique(['student_id', 'course_id', 'academic_year'], 'unique_student_course_registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropUnique('unique_student_course_registration');
            $table->dropColumn(['month', 'year']);
            $table->string('term', 50)->after('academic_year');
            $table->unique(['student_id', 'course_id', 'academic_year', 'term'], 'unique_student_course_registration');
        });
    }
};

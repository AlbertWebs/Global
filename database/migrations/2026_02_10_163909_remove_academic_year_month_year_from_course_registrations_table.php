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
            if (Schema::hasColumn('course_registrations', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
            if (Schema::hasColumn('course_registrations', 'month')) {
                $table->dropColumn('month');
            }
            if (Schema::hasColumn('course_registrations', 'year')) {
                $table->dropColumn('year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->string('academic_year')->nullable();
            $table->string('month')->nullable();
            $table->integer('year')->nullable();
        });
    }
};

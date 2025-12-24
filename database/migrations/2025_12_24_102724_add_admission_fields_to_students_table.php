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
        Schema::table('students', function (Blueprint $table) {
            $table->string('admission_number')->unique()->nullable()->after('student_number');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('level_of_education')->nullable()->after('gender');
            $table->string('nationality')->nullable()->after('level_of_education');
            $table->string('id_passport_number')->nullable()->after('nationality');
            $table->string('next_of_kin_name')->nullable()->after('id_passport_number');
            $table->string('next_of_kin_mobile')->nullable()->after('next_of_kin_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'admission_number',
                'middle_name',
                'gender',
                'level_of_education',
                'nationality',
                'id_passport_number',
                'next_of_kin_name',
                'next_of_kin_mobile',
            ]);
        });
    }
};

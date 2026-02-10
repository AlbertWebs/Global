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
            if (!Schema::hasColumn('course_registrations', 'agreed_amount')) {
                $table->decimal('agreed_amount', 10, 2)->default(0)->after('registration_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('course_registrations', 'agreed_amount')) {
                $table->dropColumn('agreed_amount');
            }
        });
    }
};

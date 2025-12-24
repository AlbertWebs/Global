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
        Schema::table('payments', function (Blueprint $table) {
            // Remove term column
            $table->dropColumn('term');
            
            // Add month and year for monthly billing
            $table->string('month')->nullable()->after('academic_year'); // e.g., "2024-12" or "December 2024"
            $table->integer('year')->nullable()->after('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['month', 'year']);
            $table->string('term')->nullable()->after('academic_year');
        });
    }
};

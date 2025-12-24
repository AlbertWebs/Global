<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('academic_year')->nullable()->after('course_id'); // e.g., "2024/2025"
            $table->string('term')->nullable()->after('academic_year'); // e.g., "Term 1", "Term 2", "Term 3"
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['academic_year', 'term']);
        });
    }
};

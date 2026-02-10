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
        // No changes to payments table for month or year as these fields are being removed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes needed as these fields are being removed
    }
};

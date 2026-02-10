<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No changes to payments table for academic_year or term as these fields are being removed
    }

    public function down(): void
    {
        // No changes needed as these fields are being removed
    }
};

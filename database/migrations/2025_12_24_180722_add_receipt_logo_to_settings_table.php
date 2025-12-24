<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add receipt_logo setting if it doesn't exist
        $exists = DB::table('settings')->where('key', 'receipt_logo')->exists();
        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'receipt_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'school',
                'description' => 'School logo image (for receipts)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'receipt_logo')->delete();
    }
};

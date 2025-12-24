<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, email, phone, etc.
            $table->string('group')->default('general'); // general, school, contact, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'school_name',
                'value' => 'Global College',
                'type' => 'text',
                'group' => 'school',
                'description' => 'Name of the school',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'school_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'school',
                'description' => 'School logo image',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'school_email',
                'value' => 'info@globalcollege.edu',
                'type' => 'email',
                'group' => 'contact',
                'description' => 'School email address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'school_phone',
                'value' => '+254 700 000 000',
                'type' => 'phone',
                'group' => 'contact',
                'description' => 'School phone number',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'school_address',
                'value' => 'P.O. Box 12345, Nairobi, Kenya',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'School physical address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'school_website',
                'value' => null,
                'type' => 'url',
                'group' => 'contact',
                'description' => 'School website URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency',
                'value' => 'KES',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Default currency',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'KES',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Currency symbol or code',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

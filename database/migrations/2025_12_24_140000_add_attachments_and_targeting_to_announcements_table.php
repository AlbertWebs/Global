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
        Schema::table('announcements', function (Blueprint $table) {
            // Add file attachment support
            $table->string('attachment_path')->nullable()->after('message');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->string('attachment_type')->nullable()->after('attachment_name'); // pdf, docx, image, etc.
            
            // Add course/group targeting
            $table->json('target_courses')->nullable()->after('target_audience'); // Array of course IDs
            $table->json('target_student_groups')->nullable()->after('target_courses'); // Array of student IDs or group identifiers
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_name', 'attachment_type', 'target_courses', 'target_student_groups']);
        });
    }
};


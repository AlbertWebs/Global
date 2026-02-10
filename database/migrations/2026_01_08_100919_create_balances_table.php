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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->decimal('base_price', 10, 2);
            $table->decimal('agreed_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('outstanding_balance', 10, 2);
            $table->enum('status', ['pending', 'partially_paid', 'cleared'])->default('pending');
            $table->date('last_payment_date')->nullable();
            $table->timestamps();

            // Add a unique constraint for student and course
            $table->unique(['student_id', 'course_id'], 'unique_balance_per_course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};

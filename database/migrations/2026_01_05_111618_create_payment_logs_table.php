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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('cascade');
            $table->string('description'); // e.g., "Course fee for AI", "Wallet Top-up"
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('agreed_amount', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('balance_before', 10, 2); // Outstanding balance for the course BEFORE this payment
            $table->decimal('balance_after', 10, 2); // Outstanding balance for the course AFTER this payment
            $table->decimal('wallet_balance_after', 10, 2)->default(0); // Student's wallet balance after transaction
            $table->timestamp('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};

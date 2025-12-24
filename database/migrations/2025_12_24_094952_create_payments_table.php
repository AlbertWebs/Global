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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2); // What cashier enters
            $table->decimal('base_price', 10, 2); // Stored for admin reports
            $table->decimal('discount_amount', 10, 2)->default(0); // Computed: base_price - amount_paid
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');
            $table->string('payment_method')->default('cash'); // cash, bank_transfer, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

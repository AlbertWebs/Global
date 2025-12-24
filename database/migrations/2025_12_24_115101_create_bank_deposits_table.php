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
        Schema::create('bank_deposits', function (Blueprint $table) {
            $table->id();
            $table->enum('source_account', ['cash_on_hand', 'mpesa_wallet']); // Where money is coming from
            $table->decimal('amount', 10, 2);
            $table->date('deposit_date');
            $table->string('reference_number')->nullable(); // Bank deposit slip number, transaction ID, etc.
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('deposit_date');
            $table->index('source_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_deposits');
    }
};

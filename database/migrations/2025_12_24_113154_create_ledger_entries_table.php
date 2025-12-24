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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['inflow', 'outflow']); // inflow = income, outflow = expense
            $table->enum('payment_source', ['mpesa', 'cash', 'bank_transfer']); // Where money came from (for inflows)
            $table->enum('holding_account', ['cash_on_hand', 'mpesa_wallet', 'bank_account']); // Where money is held
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->nullable(); // Receipt number, transaction ID, etc.
            $table->string('description'); // Description of the transaction
            $table->string('entity_type')->nullable(); // 'App\Models\Payment' or 'App\Models\Expense'
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of the payment or expense
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade'); // Who recorded this
            $table->timestamp('transaction_date'); // When the transaction occurred
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['entity_type', 'entity_id']);
            $table->index('holding_account');
            $table->index('transaction_date');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};

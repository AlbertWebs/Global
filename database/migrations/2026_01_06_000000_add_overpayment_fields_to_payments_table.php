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
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('overpayment_amount', 10, 2)->default(0)->after('agreed_amount');
            $table->decimal('wallet_amount_used', 10, 2)->default(0)->after('overpayment_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['overpayment_amount', 'wallet_amount_used']);
        });
    }
};

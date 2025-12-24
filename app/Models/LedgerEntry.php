<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LedgerEntry extends Model
{
    protected $fillable = [
        'type',
        'payment_source',
        'holding_account',
        'amount',
        'reference_number',
        'description',
        'entity_type',
        'entity_id',
        'recorded_by',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'datetime',
        ];
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get payment source label
     */
    public function getPaymentSourceLabelAttribute(): string
    {
        return match($this->payment_source) {
            'mpesa' => 'M-Pesa',
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            default => ucfirst($this->payment_source),
        };
    }

    /**
     * Get holding account label
     */
    public function getHoldingAccountLabelAttribute(): string
    {
        return match($this->holding_account) {
            'cash_on_hand' => 'Cash on Hand',
            'mpesa_wallet' => 'M-Pesa Wallet',
            'bank_account' => 'Bank Account',
            default => ucfirst(str_replace('_', ' ', $this->holding_account)),
        };
    }

    /**
     * Map payment source to holding account
     */
    public static function mapSourceToAccount(string $paymentSource): string
    {
        return match($paymentSource) {
            'cash' => 'cash_on_hand',
            'mpesa' => 'mpesa_wallet',
            'bank_transfer' => 'bank_account',
            default => 'cash_on_hand',
        };
    }

    /**
     * Create ledger entry for payment (inflow)
     */
    public static function createFromPayment(Payment $payment): self
    {
        $holdingAccount = self::mapSourceToAccount($payment->payment_method);
        
        // Load relationships if not already loaded
        if (!$payment->relationLoaded('student')) {
            $payment->load('student');
        }
        if (!$payment->relationLoaded('course')) {
            $payment->load('course');
        }
        if (!$payment->relationLoaded('receipt')) {
            $payment->load('receipt');
        }
        
        return self::create([
            'type' => 'inflow',
            'payment_source' => $payment->payment_method,
            'holding_account' => $holdingAccount,
            'amount' => $payment->amount_paid,
            'reference_number' => $payment->receipt?->receipt_number,
            'description' => "Payment from {$payment->student->full_name} for {$payment->course->name}",
            'entity_type' => Payment::class,
            'entity_id' => $payment->id,
            'recorded_by' => $payment->cashier_id,
            'transaction_date' => $payment->created_at,
        ]);
    }

    /**
     * Create ledger entry for expense (outflow)
     */
    public static function createFromExpense(Expense $expense): self
    {
        $holdingAccount = self::mapSourceToAccount($expense->payment_method);
        
        return self::create([
            'type' => 'outflow',
            'payment_source' => $expense->payment_method,
            'holding_account' => $holdingAccount,
            'amount' => $expense->amount,
            'reference_number' => null,
            'description' => $expense->title . ($expense->description ? ': ' . $expense->description : ''),
            'entity_type' => Expense::class,
            'entity_id' => $expense->id,
            'recorded_by' => $expense->recorded_by,
            'transaction_date' => $expense->expense_date,
        ]);
    }

    /**
     * Calculate current balance for a holding account
     */
    public static function getBalance(string $holdingAccount): float
    {
        $inflows = self::where('holding_account', $holdingAccount)
            ->where('type', 'inflow')
            ->sum('amount');
        
        $outflows = self::where('holding_account', $holdingAccount)
            ->where('type', 'outflow')
            ->sum('amount');
        
        return $inflows - $outflows;
    }

    /**
     * Get all account balances
     */
    public static function getAllBalances(): array
    {
        return [
            'cash_on_hand' => self::getBalance('cash_on_hand'),
            'mpesa_wallet' => self::getBalance('mpesa_wallet'),
            'bank_account' => self::getBalance('bank_account'),
        ];
    }
}

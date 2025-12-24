<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDeposit extends Model
{
    protected $fillable = [
        'source_account',
        'amount',
        'deposit_date',
        'reference_number',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getSourceAccountLabelAttribute(): string
    {
        return match($this->source_account) {
            'cash_on_hand' => 'Cash on Hand',
            'mpesa_wallet' => 'M-Pesa Wallet',
            default => ucfirst(str_replace('_', ' ', $this->source_account)),
        };
    }
}

<?php

namespace App\Models;

use App\Models\Balance; // Import the Balance model
use App\Models\Course; // Import the Course model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'academic_year',
        'month',
        'year',
        'amount_paid',
        'agreed_amount',
        'overpayment_amount',
        'wallet_amount_used',
        'base_price',
        'discount_amount',
        'cashier_id',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'agreed_amount' => 'decimal:2',
            'overpayment_amount' => 'decimal:2',
            'wallet_amount_used' => 'decimal:2',
            'base_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'student_id', 'student_id')
            ->whereColumn('balances.course_id', 'payments.course_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            // Initialize discount_amount to 0 if not explicitly set
            if (is_null($payment->discount_amount)) {
                $payment->discount_amount = 0;
            }

            // NOTE: Balance updates are now handled by BillingController
            // to avoid double-counting and to properly handle wallet amounts,
            // overpayments, and outstanding balance clearing logic.
            // This boot method no longer updates the Balance model.
        });

        static::updating(function ($payment) {
            // Recompute discount_amount to 0 if not explicitly set and related fields change
            if ($payment->isDirty(['agreed_amount', 'amount_paid']) && is_null($payment->discount_amount)) {
                $payment->discount_amount = 0;
            }

            // NOTE: Balance updates on payment updates should be handled manually
            // by the controller that updates the payment, to ensure proper calculation.
            // This boot method no longer automatically updates the Balance model.
        });

        static::deleting(function ($payment) {
            // NOTE: Balance updates on payment deletion should be handled manually
            // by the controller that deletes the payment, to ensure proper calculation
            // of wallet amounts, overpayments, etc.
            // This boot method no longer automatically reverts balance updates.
        });
    }
}

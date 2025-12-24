<?php

namespace App\Models;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            // Calculate balance if agreed_amount is set
            if ($payment->agreed_amount && $payment->amount_paid) {
                $payment->discount_amount = max(0, $payment->agreed_amount - $payment->amount_paid);
            } elseif ($payment->base_price && $payment->amount_paid) {
                // Fallback to old discount calculation for backward compatibility
                $payment->discount_amount = max(0, $payment->base_price - $payment->amount_paid);
            }
        });

        static::updating(function ($payment) {
            // Recompute balance if agreed_amount or amount_paid changes
            if ($payment->isDirty(['agreed_amount', 'amount_paid'])) {
                if ($payment->agreed_amount && $payment->amount_paid) {
                    $payment->discount_amount = max(0, $payment->agreed_amount - $payment->amount_paid);
                }
            }
        });
    }

    /**
     * Get the balance (outstanding amount)
     */
    public function getBalanceAttribute(): float
    {
        if ($this->agreed_amount) {
            return max(0, $this->agreed_amount - $this->amount_paid);
        }
        return 0;
    }
}

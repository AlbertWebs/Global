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
            // Initialize discount_amount to 0 if not explicitly set
            if (is_null($payment->discount_amount)) {
                $payment->discount_amount = 0;
            }
        });

        static::updating(function ($payment) {
            // Recompute discount_amount to 0 if not explicitly set and related fields change
            if ($payment->isDirty(['agreed_amount', 'amount_paid']) && is_null($payment->discount_amount)) {
                $payment->discount_amount = 0;
            }
        });
    }
}

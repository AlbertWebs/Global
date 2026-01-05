<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'payment_id',
        'description',
        'base_price',
        'agreed_amount',
        'amount_paid',
        'balance_before',
        'balance_after',
        'wallet_balance_after',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'agreed_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'wallet_balance_after' => 'decimal:2',
            'payment_date' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

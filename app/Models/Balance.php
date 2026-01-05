<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'base_price',
        'agreed_amount',
        'total_paid',
        'discount_amount',
        'outstanding_balance',
        'status',
        'last_payment_date',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'agreed_amount' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
            'last_payment_date' => 'datetime',
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
}

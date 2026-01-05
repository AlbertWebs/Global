<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'academic_year',
        'month',
        'year',
        'agreed_amount',
        'amount_paid',
        'discount_amount',
        'balance',
    ];
}

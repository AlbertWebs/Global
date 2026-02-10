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

            // Fetch the course details to get base_price and agreed_amount
            $course = Course::find($payment->course_id);

            // Update Balance record
            $balance = Balance::firstOrNew(
                ['student_id' => $payment->student_id, 'course_id' => $payment->course_id]
            );

            // Set defaults if new balance record is created or existing record is missing values
            if (!$balance->exists) {

        
                $balance->base_price = $course->base_price ?? 0;
                $balance->agreed_amount = $payment->agreed_amount ?? $course->base_price ?? 0;
                $balance->discount_amount = $payment->discount_amount;
                $balance->total_paid = 0;
                $balance->outstanding_balance = ($payment->agreed_amount ?? $course->base_price ?? 0) - ($payment->discount_amount ?? 0);
                $balance->status = 'pending';
                $balance->last_payment_date = null;
            } else {
                // Ensure discount_amount is set for existing records if null
                if (is_null($balance->discount_amount)) {
                    $balance->discount_amount = 0;
                }
                // Ensure base_price is set for existing records if null
                if (is_null($balance->base_price)) {
                    $balance->base_price = $course->base_price ?? 0;
                }
                // Ensure agreed_amount is set for existing records if null
                if (is_null($balance->agreed_amount)) {
                    $balance->agreed_amount = $payment->agreed_amount ?? $course->base_price ?? 0;
                }
            }
            
            $balance->total_paid += $payment->amount_paid;
            $balance->outstanding_balance = $balance->agreed_amount - $balance->total_paid;
            $balance->status = ($balance->outstanding_balance <= 0) ? 'cleared' : 'partially_paid';
            $balance->last_payment_date = now();
            $balance->save();
        });

        static::updating(function ($payment) {
            // Recompute discount_amount to 0 if not explicitly set and related fields change
            if ($payment->isDirty(['agreed_amount', 'amount_paid']) && is_null($payment->discount_amount)) {
                $payment->discount_amount = 0;
            }

            // Only update balance if amount_paid has changed
            if ($payment->isDirty('amount_paid')) {
                $originalAmountPaid = $payment->getOriginal('amount_paid');
                $newAmountPaid = $payment->amount_paid;
                $difference = $newAmountPaid - $originalAmountPaid;

                $balance = Balance::firstOrNew(
                    ['student_id' => $payment->student_id, 'course_id' => $payment->course_id]
                );

                // Ensure discount_amount is set for existing records if null
                if (is_null($balance->discount_amount)) {
                    $balance->discount_amount = 0;
                }
                // Ensure base_price is set for existing records if null
                if (is_null($balance->base_price)) {
                    $course = Course::find($payment->course_id);
                    $balance->base_price = $course->base_price ?? 0;
                }
                // Ensure agreed_amount is set for existing records if null
                if (is_null($balance->agreed_amount)) {
                    $balance->agreed_amount = $payment->agreed_amount ?? $balance->base_price ?? 0;
                }

                $balance->total_paid += $difference;
                $balance->outstanding_balance = $balance->agreed_amount - $balance->total_paid;
                $balance->status = ($balance->outstanding_balance <= 0) ? 'cleared' : 'partially_paid';
                $balance->last_payment_date = now();
                $balance->save();
            }
        });

        static::deleting(function ($payment) {
            // When a payment is deleted, revert the balance update
            $balance = Balance::where('student_id', $payment->student_id)
                              ->where('course_id', $payment->course_id)
                              ->first();
            if ($balance) {
                $balance->total_paid -= $payment->amount_paid;
                $balance->outstanding_balance = $balance->agreed_amount - $balance->total_paid;
                $balance->save();
            }
        });
    }
}

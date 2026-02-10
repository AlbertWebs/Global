<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherIncomeReceipt extends Model
{
    protected $fillable = [
        'other_income_id',
        'receipt_number',
        'receipt_date',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
        ];
    }

    public function otherIncome(): BelongsTo
    {
        return $this->belongsTo(OtherIncome::class);
    }

    /**
     * Generate a serialized receipt number for other income
     * Format: OI-001, OI-002, etc.
     */
    public static function generateReceiptNumber(): string
    {
        $allReceiptNumbers = self::whereNotNull('receipt_number')
            ->pluck('receipt_number')
            ->map(function ($number) {
                if (preg_match('/OI-(\d+)/', $number, $matches)) {
                    return (int)$matches[1];
                }
                return null;
            })
            ->filter()
            ->map(function ($number) {
                return (int)$number;
            });

        if ($allReceiptNumbers->isEmpty()) {
            $nextNumber = 1;
        } else {
            $maxNumber = $allReceiptNumbers->max();
            $nextNumber = $maxNumber + 1;
        }

        $receiptNumber = 'OI-' . sprintf('%03d', $nextNumber);

        while (self::where('receipt_number', $receiptNumber)->exists()) {
            $nextNumber++;
            $receiptNumber = 'OI-' . sprintf('%03d', $nextNumber);
        }

        return $receiptNumber;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    protected $fillable = [
        'payment_id',
        'receipt_number',
        'receipt_date',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Generate a serialized receipt number as numeric only (001, 002, etc.)
     * Ensures auto-increment and no duplicates
     */
    public static function generateReceiptNumber(): string
    {
        // Get all receipt numbers and find the highest numeric one
        $allReceiptNumbers = self::whereNotNull('receipt_number')
            ->pluck('receipt_number')
            ->map(function ($number) {
                // Extract numeric part from GTC-XXX format or use the number itself if numeric
                if (preg_match('/GTC-(\d+)/', $number, $matches)) {
                    return (int)$matches[1];
                } elseif (is_numeric($number) && ctype_digit($number)) {
                    return (int)$number;
                }
                return null;
            })
            ->filter()
            ->map(function ($number) {
                return (int)$number;
            });

        // If no numeric receipt numbers exist, start from 1
        if ($allReceiptNumbers->isEmpty()) {
            $nextNumber = 1;
        } else {
            // Get the maximum numeric receipt number and increment
            $maxNumber = $allReceiptNumbers->max();
            $nextNumber = $maxNumber + 1;
        }

        // Format: 001, 002, etc. (3 digits minimum)
        $receiptNumber = sprintf('%03d', $nextNumber);

        // Ensure uniqueness (handle race conditions)
        while (self::where('receipt_number', $receiptNumber)->exists()) {
            $nextNumber++;
            $receiptNumber = sprintf('%03d', $nextNumber);
        }

        return $receiptNumber;
    }
}

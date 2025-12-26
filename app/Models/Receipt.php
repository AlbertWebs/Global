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
     * Generate a serialized receipt number in format GTC-001, GTC-002, etc.
     * Ensures auto-increment and no duplicates
     */
    public static function generateReceiptNumber(): string
    {
        // Get the last receipt number
        $lastReceipt = self::where('receipt_number', 'like', 'GTC-%')
            ->orderByRaw('CAST(SUBSTRING(receipt_number, 5) AS UNSIGNED) DESC')
            ->first();

        if ($lastReceipt && preg_match('/GTC-(\d+)/', $lastReceipt->receipt_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        // Format: GTC-001, GTC-002, etc. (3 digits minimum)
        $receiptNumber = sprintf('GTC-%03d', $nextNumber);

        // Ensure uniqueness (handle race conditions)
        while (self::where('receipt_number', $receiptNumber)->exists()) {
            $nextNumber++;
            $receiptNumber = sprintf('GTC-%03d', $nextNumber);
        }

        return $receiptNumber;
    }
}

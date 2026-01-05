<?php

namespace App\Exports;

use App\Models\PaymentLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentLogsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $studentId;

    public function __construct(int $studentId)
    {
        $this->studentId = $studentId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PaymentLog::with(['student', 'course', 'payment'])
            ->where('student_id', $this->studentId)
            ->latest()
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Payment Date',
            'Student Name',
            'Student Admission No',
            'Course',
            'Description',
            'Base Price',
            'Agreed Amount',
            'Amount Paid',
            'Balance Before',
            'Balance After',
            'Wallet Balance After',
        ];
    }

    /**
     * @param mixed $paymentLog
     * @return array
     */
    public function map($paymentLog): array
    {
        return [
            $paymentLog->id,
            $paymentLog->payment_date->format('Y-m-d H:i:s'),
            $paymentLog->student->full_name,
            $paymentLog->student->admission_number,
            $paymentLog->course ? $paymentLog->course->name : 'N/A',
            $paymentLog->description,
            number_format($paymentLog->base_price, 2),
            number_format($paymentLog->agreed_amount, 2),
            number_format($paymentLog->amount_paid, 2),
            number_format($paymentLog->balance_before, 2),
            number_format($paymentLog->balance_after, 2),
            number_format($paymentLog->wallet_balance_after, 2),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}


<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'Student Name',
            'Student Number',
            'Course',
            'Course Code',
            'Payment Method',
            'Amount Paid (KES)',
            'Agreed Amount (KES)',
            'Balance (KES)',
            'Receipt Number',
            'Processed By'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->created_at->format('Y-m-d'),
            $payment->created_at->format('H:i:s'),
            $payment->student->full_name,
            $payment->student->student_number,
            $payment->course->name,
            $payment->course->code,
            ucfirst(str_replace('_', ' ', $payment->payment_method)),
            number_format($payment->amount_paid, 2),
            number_format($payment->agreed_amount ?? 0, 2),
            number_format($payment->balance ?? 0, 2),
            $payment->receipt?->receipt_number ?? 'N/A',
            $payment->cashier->name ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Payments';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}


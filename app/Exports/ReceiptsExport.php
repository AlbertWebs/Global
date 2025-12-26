<?php

namespace App\Exports;

use App\Models\Receipt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReceiptsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $receipts;

    public function __construct($receipts)
    {
        $this->receipts = $receipts;
    }

    public function collection()
    {
        return $this->receipts;
    }

    public function headings(): array
    {
        return [
            'Receipt Number',
            'Receipt Date',
            'Student Number',
            'Student Name',
            'Course Name',
            'Amount Paid (KES)',
            'Agreed Amount (KES)',
            'Payment Method',
            'Processed By',
            'Payment Date',
        ];
    }

    public function map($receipt): array
    {
        return [
            $receipt->receipt_number ?? 'N/A',
            $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : 'N/A',
            $receipt->payment->student->student_number ?? 'N/A',
            $receipt->payment->student->full_name ?? 'N/A',
            $receipt->payment->course->name ?? 'N/A',
            number_format($receipt->payment->amount_paid, 2),
            $receipt->payment->agreed_amount ? number_format($receipt->payment->agreed_amount, 2) : 'N/A',
            ucfirst(str_replace('_', ' ', $receipt->payment->payment_method ?? 'N/A')),
            $receipt->payment->cashier->name ?? 'N/A',
            $receipt->payment->created_at ? $receipt->payment->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}


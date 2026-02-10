<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Balance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CashPaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $payments;
    protected $balances;

    public function __construct($payments)
    {
        $this->payments = $payments;
        
        // Pre-load all balances to avoid N+1 queries
        $studentIds = $payments->pluck('student_id')->unique();
        $courseIds = $payments->pluck('course_id')->unique();
        
        $this->balances = Balance::whereIn('student_id', $studentIds)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy(function ($balance) {
                return $balance->student_id . '-' . $balance->course_id;
            });
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Admission Number',
            'Course',
            'Amount Paid (KES)',
            'Receipt Number',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->created_at->format('M d, Y'),
            $payment->student->full_name,
            $payment->student->admission_number ?? 'N/A',
            $payment->course->name,
            number_format($payment->amount_paid, 2),
            $payment->receipt->receipt_number ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Cash Payments';
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

<?php

namespace App\Exports;

use App\Models\PaymentLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
            'Admission Number',
            'Name',
            'Date',
            'Amount Paid (KES)',
            'Balance After (KES)',
            'Course',
        ];
    }

    /**
     * @param mixed $paymentLog
     * @return array
     */
    public function map($paymentLog): array
    {
        return [
            $paymentLog->student ? ($paymentLog->student->admission_number ?? 'N/A') : 'N/A',
            $paymentLog->student ? $paymentLog->student->full_name : 'Student Deleted',
            $paymentLog->payment_date->format('Y-m-d'),
            number_format($paymentLog->amount_paid, 2),
            number_format($paymentLog->balance_after, 2),
            $paymentLog->course ? $paymentLog->course->name : 'N/A',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
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


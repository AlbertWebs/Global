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
            'Date',
            'Student',
            'Course',
            'Description',
            'Amount Paid (KES)',
        ];
    }

    /**
     * @param mixed $paymentLog
     * @return array
     */
    public function map($paymentLog): array
    {
        return [
            $paymentLog->payment_date->format('Y-m-d'),
            $paymentLog->student ? $paymentLog->student->full_name : 'Student Deleted',
            $paymentLog->course ? $paymentLog->course->name : 'N/A',
            $paymentLog->description,
            number_format($paymentLog->amount_paid, 2),
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


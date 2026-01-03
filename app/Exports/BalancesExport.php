<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BalancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students->filter(function ($student) {
            $totalAgreed = $student->payments->sum('agreed_amount');
            $totalPaid = $student->payments->sum('amount_paid');
            return ($totalAgreed - $totalPaid) > 0;
        });
    }

    public function headings(): array
    {
        return [
            'Admission Number',
            'Full Name',
            'Amount Paid (KES)',
            'Outstanding Balance (KES)',
        ];
    }

    public function map($student): array
    {
        $totalAgreed = $student->payments->sum('agreed_amount');
        $totalPaid = $student->payments->sum('amount_paid');
        $balance = $totalAgreed - $totalPaid;

        return [
            $student->admission_number ?? 'N/A',
            $student->full_name,
            number_format($totalPaid, 2),
            number_format($balance, 2),
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


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
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Student Number',
            'Admission Number',
            'Full Name',
            'Email',
            'Phone',
            'Total Agreed Amount (KES)',
            'Total Paid (KES)',
            'Outstanding Balance (KES)',
            'Number of Payments',
            'Status',
        ];
    }

    public function map($student): array
    {
        $totalAgreed = $student->payments->sum('agreed_amount');
        $totalPaid = $student->payments->sum('amount_paid');
        $balance = max(0, $totalAgreed - $totalPaid);

        return [
            $student->student_number ?? 'N/A',
            $student->admission_number ?? 'N/A',
            $student->full_name,
            $student->email ?? 'N/A',
            $student->phone ?? 'N/A',
            number_format($totalAgreed, 2),
            number_format($totalPaid, 2),
            number_format($balance, 2),
            $student->payments->count(),
            ucfirst($student->status ?? 'N/A'),
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


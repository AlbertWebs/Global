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

class StudentsRegisteredExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'Admission Number',
            'Student Number',
            'Full Name',
            'Email',
            'Phone',
            'Gender',
            'Date of Birth',
            'Level of Education',
            'Nationality',
            'ID/Passport Number',
            'Guardian Name',
            'Guardian Mobile',
            'Address',
            'Status',
            'Registration Date',
        ];
    }

    public function map($student): array
    {
        return [
            $student->admission_number ?? 'N/A',
            $student->student_number ?? 'N/A',
            $student->full_name,
            $student->email ?? 'N/A',
            $student->phone ?? 'N/A',
            ucfirst($student->gender ?? 'N/A'),
            $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : 'N/A',
            $student->level_of_education ?? 'N/A',
            $student->nationality ?? 'N/A',
            $student->id_passport_number ?? 'N/A',
            $student->next_of_kin_name ?? 'N/A',
            $student->next_of_kin_mobile ?? 'N/A',
            $student->address ?? 'N/A',
            ucfirst($student->status ?? 'N/A'),
            $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : 'N/A',
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


<?php

namespace App\Exports;

use App\Models\CourseRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CourseRegistrationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $registrations;

    public function __construct($registrations)
    {
        $this->registrations = $registrations;
    }

    public function collection()
    {
        return $this->registrations;
    }

    public function headings(): array
    {
        return [
            'Registration Date',
            'Student Number',
            'Student Name',
            'Course Name',
            'Course Code',
            'Academic Year',
            'Month',
            'Year',
            'Status',
            'Notes',
        ];
    }

    public function map($registration): array
    {
        return [
            $registration->registration_date ? $registration->registration_date->format('Y-m-d') : 'N/A',
            $registration->student->student_number ?? 'N/A',
            $registration->student->full_name ?? 'N/A',
            $registration->course->name ?? 'N/A',
            $registration->course->code ?? 'N/A',
            $registration->academic_year ?? 'N/A',
            $registration->month ?? 'N/A',
            $registration->year ?? 'N/A',
            ucfirst($registration->status ?? 'N/A'),
            $registration->notes ?? 'N/A',
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


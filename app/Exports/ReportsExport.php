<?php

namespace App\Exports;

use App\Models\BankDeposit;
use App\Models\Expense;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $payments;
    protected $expenses;
    protected $bankDeposits;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($payments, $expenses, $bankDeposits, $dateFrom, $dateTo)
    {
        $this->payments = $payments;
        $this->expenses = $expenses;
        $this->bankDeposits = $bankDeposits;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        return collect([
            // We'll use mapping to create multiple sheets
        ]);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Student Number',
            'Course',
            'Payment Method',
            'Amount Paid (KES)',
            'Agreed Amount (KES)',
            'Balance (KES)',
            'Receipt Number',
            'Processed By'
        ];
    }

    public function map($row): array
    {
        // This will be handled by separate sheet classes
        return [];
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
                'font' => ['color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Financial Report';
    }
}

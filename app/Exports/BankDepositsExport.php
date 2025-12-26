<?php

namespace App\Exports;

use App\Models\BankDeposit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BankDepositsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $deposits;

    public function __construct($deposits)
    {
        $this->deposits = $deposits;
    }

    public function collection()
    {
        return $this->deposits;
    }

    public function headings(): array
    {
        return [
            'Deposit Date',
            'Source Account',
            'Amount (KES)',
            'Reference Number',
            'Recorded By',
            'Notes',
        ];
    }

    public function map($deposit): array
    {
        return [
            $deposit->deposit_date ? $deposit->deposit_date->format('Y-m-d') : 'N/A',
            $deposit->source_account_label ?? ucfirst(str_replace('_', ' ', $deposit->source_account ?? 'N/A')),
            number_format($deposit->amount, 2),
            $deposit->reference_number ?? 'N/A',
            $deposit->recorder->name ?? 'N/A',
            $deposit->notes ?? 'N/A',
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


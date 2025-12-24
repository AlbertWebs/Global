<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FinancialSummarySheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $summary;
    protected $paymentMethodBreakdown;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($summary, $paymentMethodBreakdown, $dateFrom, $dateTo)
    {
        $this->summary = $summary;
        $this->paymentMethodBreakdown = $paymentMethodBreakdown;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return [
            ['FINANCIAL REPORT SUMMARY'],
            [''],
            ['Report Period:', \Carbon\Carbon::parse($this->dateFrom)->format('F d, Y') . ' - ' . \Carbon\Carbon::parse($this->dateTo)->format('F d, Y')],
            ['Generated:', now()->format('F d, Y \a\t h:i A')],
            [''],
            ['FINANCIAL SUMMARY'],
            [''],
            ['Item', 'Amount (KES)'],
        ];
    }

    public function array(): array
    {
        $totalIncome = $this->summary['total_amount_paid'];
        $totalExpenses = $this->summary['total_expenses'];
        $netIncome = $this->summary['net_income'];

        return [
            ['Total Payments', $this->summary['total_payments']],
            ['Total Income (Payments)', number_format($totalIncome, 2)],
            ['Total Base Price', number_format($this->summary['total_base_price'], 2)],
            ['Total Discounts Given', number_format($this->summary['total_discounts'], 2)],
            [''],
            ['Total Expenses', number_format($totalExpenses, 2)],
            [''],
            ['Net Income', number_format($netIncome, 2)],
            [''],
            ['PAYMENT METHOD BREAKDOWN'],
            [''],
            ['Method', 'Amount (KES)', 'Percentage'],
            ['M-Pesa', number_format($this->paymentMethodBreakdown['mpesa'], 2), 
                $totalIncome > 0 ? number_format(($this->paymentMethodBreakdown['mpesa'] / $totalIncome) * 100, 2) . '%' : '0.00%'],
            ['Cash', number_format($this->paymentMethodBreakdown['cash'], 2),
                $totalIncome > 0 ? number_format(($this->paymentMethodBreakdown['cash'] / $totalIncome) * 100, 2) . '%' : '0.00%'],
            ['Bank Transfer', number_format($this->paymentMethodBreakdown['bank_transfer'], 2),
                $totalIncome > 0 ? number_format(($this->paymentMethodBreakdown['bank_transfer'] / $totalIncome) * 100, 2) . '%' : '0.00%'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            6 => [
                'font' => ['bold' => true, 'size' => 14],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7E6E6']
                ],
            ],
            8 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            10 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2']
                ],
            ],
            12 => [
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


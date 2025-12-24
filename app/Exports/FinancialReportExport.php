<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FinancialReportExport implements WithMultipleSheets
{
    protected $payments;
    protected $expenses;
    protected $summary;
    protected $paymentMethodBreakdown;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($payments, $expenses, $summary, $paymentMethodBreakdown, $dateFrom, $dateTo)
    {
        $this->payments = $payments;
        $this->expenses = $expenses;
        $this->summary = $summary;
        $this->paymentMethodBreakdown = $paymentMethodBreakdown;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Summary sheet
        $sheets[] = new FinancialSummarySheet($this->summary, $this->paymentMethodBreakdown, $this->dateFrom, $this->dateTo);

        // Payments sheet
        $sheets[] = new PaymentsExport($this->payments);

        // Expenses sheet
        $sheets[] = new ExpensesExport($this->expenses);

        return $sheets;
    }
}


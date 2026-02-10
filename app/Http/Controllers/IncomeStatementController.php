<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Payment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class IncomeStatementController extends Controller
{
    /**
     * Get PDF instance from service container
     */
    protected function getPdfInstance()
    {
        return app('dompdf.wrapper');
    }

    public function index()
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can view income statements');
        }

        return view('income-statement.index');
    }

    public function generate(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can generate income statements');
        }

        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $format = $request->get('format', 'pdf');

        // Calculate Income
        $paymentQuery = Payment::query();
        $otherIncomeQuery = OtherIncome::query();

        if ($dateFrom) {
            $paymentQuery->whereDate('created_at', '>=', $dateFrom);
            $otherIncomeQuery->whereDate('income_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $paymentQuery->whereDate('created_at', '<=', $dateTo);
            $otherIncomeQuery->whereDate('income_date', '<=', $dateTo);
        }

        $totalPayments = $paymentQuery->sum('amount_paid');
        $totalOtherIncome = $otherIncomeQuery->sum('amount');
        $totalIncome = $totalPayments + $totalOtherIncome;

        // Calculate Expenses
        $expenseQuery = Expense::query();
        if ($dateFrom) {
            $expenseQuery->whereDate('expense_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $expenseQuery->whereDate('expense_date', '<=', $dateTo);
        }
        $totalExpenses = $expenseQuery->sum('amount');

        // Calculate Net Profit
        $netProfit = $totalIncome - $totalExpenses;

        // Get detailed data for PDF
        $payments = $paymentQuery->with(['student', 'course'])->get();
        $otherIncomes = $otherIncomeQuery->with('recorder')->get();
        $expenses = $expenseQuery->with('recorder')->get();

        if ($format === 'pdf') {
            $pdf = $this->getPdfInstance();
            $pdf->loadView('income-statement.pdf', compact(
                'totalIncome',
                'totalPayments',
                'totalOtherIncome',
                'totalExpenses',
                'netProfit',
                'payments',
                'otherIncomes',
                'expenses',
                'dateFrom',
                'dateTo'
            ));
            $dateRange = $dateFrom && $dateTo ? '_' . $dateFrom . '_to_' . $dateTo : '';
            $fileName = 'income_statement' . $dateRange . '_' . now()->format('Y-m-d') . '.pdf';
            return $pdf->download($fileName);
        }

        // Excel format (can be added later if needed)
        return back()->with('error', 'Excel format not yet implemented. Please use PDF format.');
    }
}

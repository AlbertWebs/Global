<?php

namespace App\Http\Controllers;

use App\Exports\ExpensesExport;
use App\Exports\PaymentsExport;
use App\Models\BankDeposit;
use App\Models\CourseRegistration;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can view reports');
        }

        $dateFrom = $request->get('date_from', now()->startOfDay()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfDay()->toDateString());
        $period = $request->get('period', 'custom');
        $academicYear = $request->get('academic_year');
        $term = $request->get('term');

        // Set default periods
        if ($period === 'today') {
            $dateFrom = now()->startOfDay()->toDateString();
            $dateTo = now()->endOfDay()->toDateString();
        } elseif ($period === 'week') {
            $dateFrom = now()->startOfWeek()->toDateString();
            $dateTo = now()->endOfWeek()->toDateString();
        } elseif ($period === 'month') {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->endOfMonth()->toDateString();
        }

        $query = Payment::with(['student', 'course', 'cashier', 'receipt'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        // Filter by academic year if provided
        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }

        // Filter by term if provided
        if ($term) {
            $query->where('term', $term);
        }

        $payments = $query->latest()->get();

        // Get expenses for the same date range
        // expense_date is a date field, so we use whereDate for proper comparison
        $expensesQuery = Expense::with('recorder');
        
        // Apply date filter - use whereDate to handle date-only fields correctly
        $expensesQuery->whereDate('expense_date', '>=', $dateFrom)
                      ->whereDate('expense_date', '<=', $dateTo);
        
        $expenses = $expensesQuery->latest('expense_date')->get();

        // Get available academic years and terms for filter
        $academicYears = Payment::whereNotNull('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        $terms = ['Term 1', 'Term 2', 'Term 3', 'Term 4'];

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount_paid' => $payments->sum('amount_paid'),
            'total_base_price' => $payments->sum('base_price'),
            'total_discounts' => $payments->sum('discount_amount'),
            'total_expenses' => $expenses->sum('amount'),
            'net_income' => $payments->sum('amount_paid') - $expenses->sum('amount'),
        ];

        // Payment method breakdown
        $paymentMethodBreakdown = [
            'mpesa' => $payments->where('payment_method', 'mpesa')->sum('amount_paid'),
            'cash' => $payments->where('payment_method', 'cash')->sum('amount_paid'),
            'bank_transfer' => $payments->where('payment_method', 'bank_transfer')->sum('amount_paid'),
        ];

        // Student registrations statistics
        $studentStats = [
            'today' => Student::whereDate('created_at', today())->count(),
            'week' => Student::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => Student::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'year' => Student::whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count(),
        ];

        // Today's payments
        $todayPayments = Payment::with(['student', 'course', 'receipt'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        // Today's expenses
        $todayExpenses = Expense::with('recorder')
            ->whereDate('expense_date', today())
            ->latest('expense_date')
            ->get();

        // Today's course registrations
        $todayCourseRegistrations = CourseRegistration::with(['student', 'course'])
            ->whereDate('registration_date', today())
            ->latest('registration_date')
            ->get();

        // Bank deposits
        $bankDeposits = BankDeposit::with('recorder')
            ->whereBetween('deposit_date', [$dateFrom, $dateTo])
            ->latest('deposit_date')
            ->get();

        return view('reports.index', compact(
            'payments', 
            'expenses', 
            'summary', 
            'paymentMethodBreakdown', 
            'dateFrom', 
            'dateTo', 
            'period', 
            'academicYear', 
            'term', 
            'academicYears', 
            'terms',
            'studentStats',
            'todayPayments',
            'todayExpenses',
            'todayCourseRegistrations',
            'bankDeposits'
        ));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can export reports');
        }

        $dateFrom = $request->get('date_from', now()->startOfDay()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfDay()->toDateString());
        $period = $request->get('period', 'custom');

        // Set default periods
        if ($period === 'today') {
            $dateFrom = now()->startOfDay()->toDateString();
            $dateTo = now()->endOfDay()->toDateString();
        } elseif ($period === 'week') {
            $dateFrom = now()->startOfWeek()->toDateString();
            $dateTo = now()->endOfWeek()->toDateString();
        } elseif ($period === 'month') {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->endOfMonth()->toDateString();
        }

        $payments = Payment::with(['student', 'course', 'cashier', 'receipt'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest()
            ->get();

        $expenses = Expense::with('recorder')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->latest('expense_date')
            ->get();

        $fileName = 'financial_report_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(new PaymentsExport($payments), $fileName);
    }

    public function exportExpenses(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can export reports');
        }

        $dateFrom = $request->get('date_from', now()->startOfDay()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfDay()->toDateString());

        $expenses = Expense::with('recorder')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->latest('expense_date')
            ->get();

        $fileName = 'expenses_report_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(new ExpensesExport($expenses), $fileName);
    }
}

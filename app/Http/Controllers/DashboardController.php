<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats = [
                'total_students' => Student::count(),
                'total_courses' => Course::count(),
                'today_payments' => Payment::whereDate('created_at', today())->sum('amount_paid'),
                'month_payments' => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount_paid'),
                'total_discounts' => Payment::sum('discount_amount'),
            ];
        } else {
            $stats = [
                'total_students' => Student::count(),
                'total_courses' => Course::count(),
                'today_payments' => Payment::where('cashier_id', $user->id)
                    ->whereDate('created_at', today())
                    ->sum('amount_paid'),
            ];
        }

        // Get chart data for the last 12 months
        $chartData = $this->getChartData($user);

        return view('dashboard', compact('stats', 'chartData'));
    }

    protected function getChartData($user)
    {
        $months = [];
        $enrollments = [];
        $payments = [];
        $expenses = [];

        // Generate last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');
            
            $months[] = $monthLabel;

            // Student enrollments (using created_at)
            $enrollmentQuery = Student::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            if (!$user->isSuperAdmin()) {
                // For non-super admins, you might want to filter by creator if that field exists
                // For now, we'll show all enrollments
            }
            
            $enrollments[] = $enrollmentQuery->count();

            // Payments
            $paymentQuery = Payment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            
            if (!$user->isSuperAdmin()) {
                $paymentQuery->where('cashier_id', $user->id);
            }
            
            $payments[] = (float) $paymentQuery->sum('amount_paid');

            // Expenses
            $expenseQuery = Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month);
            
            if (!$user->isSuperAdmin()) {
                $expenseQuery->where('recorded_by', $user->id);
            }
            
            $expenses[] = (float) $expenseQuery->sum('amount');
        }

        return [
            'months' => $months,
            'enrollments' => $enrollments,
            'payments' => $payments,
            'expenses' => $expenses,
        ];
    }
}

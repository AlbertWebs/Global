<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access mobile dashboard');
        }

        // Get current academic year and term (you can make this configurable)
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $currentTerm = $this->getCurrentTerm();

        // Today's payments
        $todayPayments = Payment::whereDate('created_at', today())
            ->sum('amount_paid');

        // This week's payments
        $weekPayments = Payment::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->sum('amount_paid');

        // This month's payments
        $monthPayments = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount_paid');

        // Recent transactions (last 10)
        $recentTransactions = Payment::with(['student', 'course', 'receipt'])
            ->latest()
            ->limit(10)
            ->get();

        // System Health Checks
        $healthIssues = $this->getSystemHealthIssues($currentAcademicYear, $currentTerm);

        // Term-based summaries
        $termSummaries = $this->getTermSummaries();

        return view('mobile.dashboard', compact(
            'todayPayments',
            'weekPayments',
            'monthPayments',
            'recentTransactions',
            'healthIssues',
            'termSummaries',
            'currentAcademicYear',
            'currentTerm'
        ));
    }

    private function getCurrentAcademicYear(): string
    {
        $year = now()->year;
        $month = now()->month;
        
        // Academic year typically runs from September to August
        if ($month >= 9) {
            return $year . '/' . ($year + 1);
        } else {
            return ($year - 1) . '/' . $year;
        }
    }

    private function getCurrentTerm(): string
    {
        $month = now()->month;
        
        // Term 1: Sep-Nov, Term 2: Dec-Feb, Term 3: Mar-May, Term 4: Jun-Aug
        if ($month >= 9 && $month <= 11) {
            return 'Term 1';
        } elseif ($month == 12 || $month <= 2) {
            return 'Term 2';
        } elseif ($month >= 3 && $month <= 5) {
            return 'Term 3';
        } else {
            return 'Term 4';
        }
    }

    private function getSystemHealthIssues(string $academicYear, string $term): array
    {
        $issues = [];

        // Check for courses with frequent discounts (>30% average)
        $coursesWithHighDiscounts = Course::with('payments')
            ->get()
            ->filter(function ($course) {
                $payments = $course->payments;
                if ($payments->isEmpty()) return false;
                
                $avgDiscountPercent = $payments->avg(function ($payment) use ($course) {
                    if ($course->base_price == 0) return 0;
                    return ($payment->discount_amount / $course->base_price) * 100;
                });
                
                return $avgDiscountPercent > 30;
            })
            ->take(5);

        if ($coursesWithHighDiscounts->isNotEmpty()) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Courses with High Discount Rates',
                'message' => $coursesWithHighDiscounts->count() . ' course(s) have average discounts exceeding 30%',
                'count' => $coursesWithHighDiscounts->count(),
            ];
        }

        // Check for students without payments in current month (monthly billing system)
        $currentMonth = now()->format('F Y'); // e.g., "December 2024"
        $currentYear = now()->year;
        
        $studentsWithoutMonthlyPayments = Student::whereDoesntHave('payments', function ($query) use ($academicYear, $currentMonth, $currentYear) {
            $query->where('academic_year', $academicYear)
                  ->where('month', $currentMonth)
                  ->where('year', $currentYear);
        })
        ->where('status', 'active')
        ->count();

        if ($studentsWithoutMonthlyPayments > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Students Without Monthly Payments',
                'message' => $studentsWithoutMonthlyPayments . ' active student(s) have no payments for ' . $currentMonth . ' ' . $academicYear,
                'count' => $studentsWithoutMonthlyPayments,
            ];
        }

        // Check for payments without month/academic year (data inconsistency)
        $paymentsWithoutMonth = Payment::whereNull('academic_year')
            ->orWhereNull('month')
            ->orWhereNull('year')
            ->count();

        if ($paymentsWithoutMonth > 0) {
            $issues[] = [
                'type' => 'error',
                'title' => 'Data Inconsistency Detected',
                'message' => $paymentsWithoutMonth . ' payment(s) missing month, year, or academic year information',
                'count' => $paymentsWithoutMonth,
            ];
        }

        // Check for courses without academic year/term
        $coursesWithoutTerm = Course::whereNull('academic_year')
            ->orWhereNull('term')
            ->where('status', 'active')
            ->count();

        if ($coursesWithoutTerm > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Courses Missing Term Information',
                'message' => $coursesWithoutTerm . ' active course(s) missing academic year or term',
                'count' => $coursesWithoutTerm,
            ];
        }

        return $issues;
    }

    private function getTermSummaries(): array
    {
        $summaries = [];
        
        // Get unique academic years and months from payments (monthly billing system)
        $monthlyData = Payment::select('academic_year', 'month', 'year')
            ->whereNotNull('academic_year')
            ->whereNotNull('month')
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        foreach ($monthlyData as $data) {
            $payments = Payment::where('academic_year', $data->academic_year)
                ->where('month', $data->month)
                ->where('year', $data->year)
                ->get();

            $summaries[] = [
                'academic_year' => $data->academic_year,
                'period' => $data->month . ' ' . $data->year, // e.g., "December 2024"
                'month' => $data->month,
                'year' => $data->year,
                'total_payments' => $payments->count(),
                'total_amount' => $payments->sum('amount_paid'),
                'total_discounts' => $payments->sum('discount_amount'),
            ];
        }

        return $summaries;
    }
}

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

        // Check for students without payments in current term
        $studentsWithoutTermPayments = Student::whereDoesntHave('payments', function ($query) use ($academicYear, $term) {
            $query->where('academic_year', $academicYear)
                  ->where('term', $term);
        })
        ->where('status', 'active')
        ->count();

        if ($studentsWithoutTermPayments > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Students Without Term Payments',
                'message' => $studentsWithoutTermPayments . ' active student(s) have no payments for ' . $term . ' ' . $academicYear,
                'count' => $studentsWithoutTermPayments,
            ];
        }

        // Check for payments without term/academic year (data inconsistency)
        $paymentsWithoutTerm = Payment::whereNull('academic_year')
            ->orWhereNull('term')
            ->count();

        if ($paymentsWithoutTerm > 0) {
            $issues[] = [
                'type' => 'error',
                'title' => 'Data Inconsistency Detected',
                'message' => $paymentsWithoutTerm . ' payment(s) missing term or academic year information',
                'count' => $paymentsWithoutTerm,
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
        
        // Get unique academic years and terms from payments
        $terms = Payment::select('academic_year', 'term')
            ->whereNotNull('academic_year')
            ->whereNotNull('term')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->get();

        foreach ($terms as $termData) {
            $payments = Payment::where('academic_year', $termData->academic_year)
                ->where('term', $termData->term)
                ->get();

            $summaries[] = [
                'academic_year' => $termData->academic_year,
                'term' => $termData->term,
                'total_payments' => $payments->count(),
                'total_amount' => $payments->sum('amount_paid'),
                'total_discounts' => $payments->sum('discount_amount'),
            ];
        }

        return $summaries;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::where('status', 'active')->orderBy('first_name')->get();
        $courses = Course::where('status', 'active')->orderBy('name')->get();
        
        // Get current academic year and month for monthly billing
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $currentMonth = now()->format('F Y'); // e.g., "December 2024"
        $currentYear = now()->year;
        
        // Pre-select student if provided in query string
        $selectedStudentId = $request->get('student_id');
        $selectedStudent = null;
        if ($selectedStudentId) {
            $selectedStudent = Student::find($selectedStudentId);
        }
        
        return view('billing.index', compact('students', 'courses', 'currentAcademicYear', 'currentMonth', 'currentYear', 'selectedStudentId', 'selectedStudent'));
    }

    public function getStudentBalance($studentId, $courseId)
    {
        $balance = \App\Models\Balance::where('student_id', $studentId)
                                      ->where('course_id', $courseId)
                                      ->first();

        return response()->json([
            'outstanding_balance' => $balance ? $balance->outstanding_balance : 0,
            'total_paid' => $balance ? $balance->total_paid : 0,
            'agreed_amount' => $balance ? $balance->agreed_amount : 0,
        ]);
    }

    public function getStudentOverallBalance($studentId)
    {
        $balances = \App\Models\Balance::where('student_id', $studentId)
                                       ->where('outstanding_balance', '>', 0)
                                       ->with('course') // Eager load course relationship
                                       ->get();

        $totalOutstandingBalance = $balances->sum('outstanding_balance');

        $formattedBalances = $balances->map(function ($balance) {
            return [
                'course_id' => $balance->course_id,
                'course_name' => $balance->course->name,
                'agreed_amount' => $balance->agreed_amount,
                'total_paid' => $balance->total_paid,
                'discount_amount' => $balance->discount_amount,
                'outstanding_balance' => $balance->outstanding_balance,
            ];
        });

        return response()->json([
            'total_outstanding_balance' => $totalOutstandingBalance,
            'course_balances' => $formattedBalances,
        ]);
    }

    private function getCurrentAcademicYear(): string
    {
        $year = now()->year;
        $month = now()->month;
        
        if ($month >= 9) {
            return $year . '/' . ($year + 1);
        } else {
            return ($year - 1) . '/' . $year;
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'academic_year' => ['required', 'string'],
            'month' => ['required', 'string'],
            'year' => ['required', 'integer'],
            'agreed_amount' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $balance = $validated['agreed_amount'] - $validated['amount_paid'];

        \App\Models\Billing::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'academic_year' => $validated['academic_year'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'agreed_amount' => $validated['agreed_amount'],
            'amount_paid' => $validated['amount_paid'],
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'balance' => $balance,
        ]);

        return back()->with('success', 'Payment processed successfully!');
    }

    public function getCourseInfo($courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = auth()->user();

        // Only Super Admin can see base price
        if ($user->isSuperAdmin()) {
            return response()->json([
                'id' => $course->id,
                'name' => $course->name,
                'code' => $course->code,
                'base_price' => $course->base_price,
            ]);
        }

        // Cashier sees no price information
        return response()->json([
            'id' => $course->id,
            'name' => $course->name,
            'code' => $course->code,
        ]);
    }

    public function getStudentCourses($studentId, Request $request)
    {
        $student = Student::findOrFail($studentId);
        
        // Get selected month from request (if provided)
        $selectedMonth = $request->get('month');
        
        // Get current academic year
        $currentAcademicYear = $this->getCurrentAcademicYear();
        
        // Get all active courses (not just registered ones)
        // This allows students to pay for courses in different months
        $allCourses = Course::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // If a specific month is selected, prioritize courses registered for that month
        // But still show all courses to allow payment for any course in any month
        $courses = $allCourses->map(function ($course) use ($studentId, $currentAcademicYear, $selectedMonth) {
            // Check if student is registered for this course in the selected month
            $isRegistered = false;
            if ($selectedMonth) {
                $monthParts = explode(' ', $selectedMonth);
                $monthName = $monthParts[0];
                $year = isset($monthParts[1]) ? (int)$monthParts[1] : now()->year;
                
                $isRegistered = \App\Models\CourseRegistration::where('student_id', $studentId)
                    ->where('course_id', $course->id)
                    ->where('academic_year', $currentAcademicYear)
                    ->where('month', $selectedMonth)
                    ->where('year', $year)
                    ->where('status', 'registered')
                    ->exists();
            }
            
            return [
                'id' => $course->id,
                'name' => $course->name,
                'code' => $course->code,
                'registered' => $isRegistered,
            ];
        });
        
        return response()->json($courses);
    }
}

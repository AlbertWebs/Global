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
        
        return view('billing.index', compact('students', 'courses', 'currentAcademicYear', 'currentMonth', 'currentYear', 'selectedStudentId'));
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
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        
        $payment = Payment::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'academic_year' => $validated['academic_year'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'agreed_amount' => $validated['agreed_amount'],
            'amount_paid' => $validated['amount_paid'],
            'base_price' => $course->base_price, // Keep for backward compatibility/reports
            'cashier_id' => auth()->id(),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        // Generate receipt
        $receipt = Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'RCP-' . strtoupper(Str::random(8)),
            'receipt_date' => now(),
        ]);

        // Refresh payment to load receipt relationship
        $payment->refresh();

        // Automatically register student for the course if not already registered
        // Check if registration already exists for this student, course, academic year, month, and year
        $existingRegistration = CourseRegistration::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->where('academic_year', $validated['academic_year'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if (!$existingRegistration) {
            CourseRegistration::create([
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
                'academic_year' => $validated['academic_year'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'registration_date' => now(),
                'status' => 'registered',
                'notes' => 'Auto-registered upon payment',
            ]);
        }

        // Create ledger entry for money trace
        LedgerEntry::createFromPayment($payment);

        // Send payment confirmation SMS (queued for async processing)
        try {
            $smsService = app(\App\Services\SmsService::class);
            $smsService->sendPaymentSMS(
                $payment->student,
                $payment->amount_paid,
                $payment->course->name,
                $receipt->receipt_number
            );
        } catch (\Exception $e) {
            // Log error but don't fail the payment
            \Log::error("Failed to send payment SMS", [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Log the activity
        ActivityLog::log(
            'payment.created',
            "Processed payment of KES " . number_format($payment->amount_paid, 2) . " from {$payment->student->full_name} for {$payment->course->name} (Receipt: {$receipt->receipt_number})",
            $payment
        );

        return redirect()->route('receipts.show', $receipt->id)
            ->with('success', 'Payment processed successfully!');
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

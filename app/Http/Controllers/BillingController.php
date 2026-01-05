<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\PaymentLog;
use App\Models\Wallet;
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

        $course = Course::findOrFail($validated['course_id']);

        $totalDiscount = $validated['discount_amount'] ?? 0;
        
        $payment = Payment::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'academic_year' => $validated['academic_year'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'agreed_amount' => $validated['agreed_amount'],
            'amount_paid' => $validated['amount_paid'],
            'base_price' => $course->base_price,
            'discount_amount' => $totalDiscount,
            'cashier_id' => auth()->id(),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        // Generate receipt with serialized receipt number
        $receipt = Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => Receipt::generateReceiptNumber(),
            'receipt_date' => now(),
        ]);

        // Refresh payment to load receipt relationship
        $payment->refresh();

        // Update or create Balance record
        // Update or create Balance record for the current course
        $currentCourseBalance = \App\Models\Balance::firstOrNew(
            ['student_id' => $validated['student_id'], 'course_id' => $validated['course_id']]
        );

        if (!$currentCourseBalance->exists) {
            $currentCourseBalance->base_price = $course->base_price;
            $currentCourseBalance->agreed_amount = $validated['agreed_amount'];
            $currentCourseBalance->discount_amount = $totalDiscount;
            $currentCourseBalance->total_paid = 0;
        }

        // Apply payment to the current course balance first
        $amountToApplyToCurrentCourse = min($currentCourseBalance->agreed_amount - $currentCourseBalance->total_paid, $validated['amount_paid']);
        $currentCourseBalance->total_paid += $amountToApplyToCurrentCourse;
        $remainingPayment = $validated['amount_paid'] - $amountToApplyToCurrentCourse;

        $currentCourseBalance->outstanding_balance = max(0, $currentCourseBalance->agreed_amount - $currentCourseBalance->total_paid);
        $currentCourseBalance->status = ($currentCourseBalance->outstanding_balance <= 0) ? 'cleared' : 'partially_paid';
        $currentCourseBalance->last_payment_date = now();
        $currentCourseBalance->save();

        // Log payment for the current course
        PaymentLog::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'payment_id' => $payment->id,
            'description' => 'Payment for ' . $course->name,
            'base_price' => $currentCourseBalance->base_price,
            'agreed_amount' => $currentCourseBalance->agreed_amount,
            'amount_paid' => $amountToApplyToCurrentCourse,
            'balance_before' => $currentCourseBalance->getOriginal('outstanding_balance') + $amountToApplyToCurrentCourse, // Calculate balance before
            'balance_after' => $currentCourseBalance->outstanding_balance,
            'payment_date' => now(),
        ]);

        // Distribute any remaining payment to other outstanding balances
        if ($remainingPayment > 0) {
            $otherOutstandingBalances = \App\Models\Balance::where('student_id', $validated['student_id'])
                ->where('course_id', '!=', $validated['course_id'])
                ->where('outstanding_balance', '>', 0)
                ->orderBy('last_payment_date', 'asc') // Prioritize older balances
                ->get();

            foreach ($otherOutstandingBalances as $otherBalance) {
                if ($remainingPayment <= 0) break;

                $amountToApply = min($otherBalance->outstanding_balance, $remainingPayment);
                $otherBalance->total_paid += $amountToApply;
                $otherBalance->outstanding_balance -= $amountToApply;
                $otherBalance->status = ($otherBalance->outstanding_balance <= 0) ? 'cleared' : 'partially_paid';
                $otherBalance->last_payment_date = now();
                $otherBalance->save();
                
                $remainingPayment -= $amountToApply;

                // Log payment for other outstanding balances
                PaymentLog::create([
                    'student_id' => $validated['student_id'],
                    'course_id' => $otherBalance->course_id,
                    'payment_id' => $payment->id,
                    'description' => 'Payment for ' . $otherBalance->course->name . ' (clearing previous balance)',
                    'base_price' => $otherBalance->base_price,
                    'agreed_amount' => $otherBalance->agreed_amount,
                    'amount_paid' => $amountToApply,
                    'balance_before' => $otherBalance->getOriginal('outstanding_balance') + $amountToApply, // Calculate balance before
                    'balance_after' => $otherBalance->outstanding_balance,
                    'payment_date' => now(),
                ]);
            }
        }

        // Handle any remaining payment as a wallet top-up
        if ($remainingPayment > 0) {
            $wallet = Wallet::firstOrNew(['student_id' => $validated['student_id']]);
            $wallet->balance += $remainingPayment;
            $wallet->save();

            // Log wallet top-up
            PaymentLog::create([
                'student_id' => $validated['student_id'],
                'payment_id' => $payment->id,
                'description' => 'Wallet Top-up',
                'amount_paid' => $remainingPayment,
                'balance_before' => $wallet->getOriginal('balance') ?? 0, // Assuming it's 0 if new
                'balance_after' => $wallet->balance,
                'wallet_balance_after' => $wallet->balance,
                'payment_date' => now(),
            ]);

            // Send SMS notification for wallet top-up
            try {
                $smsService = app(\App\Services\SmsService::class);
                $smsService->sendWalletTopUpSMS(
                    $payment->student,
                    $remainingPayment,
                    $wallet->balance
                );
            } catch (\Exception $e) {
                \Log::error("Failed to send wallet top-up SMS", [
                    'student_id' => $payment->student_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

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

        // Send payment confirmation SMS
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

        // Send payment confirmation email
        try {
            $emailService = app(\App\Services\EmailService::class);
            $emailService->sendPaymentEmail(
                $payment->student,
                $payment->amount_paid,
                $payment->course->name,
                $receipt->receipt_number
            );
        } catch (\Exception $e) {
            // Log error but don't fail the payment
            \Log::error("Failed to send payment email", [
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

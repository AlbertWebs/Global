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
        $courses = collect(); // Initialize as an empty collection
        if ($selectedStudentId) {
            $selectedStudent = Student::find($selectedStudentId);
            $studentWallet = Wallet::where('student_id', $selectedStudentId)->first();
            $walletBalance = $studentWallet ? $studentWallet->balance : 0;
            
            // Fetch only courses registered by the selected student
            $courses = CourseRegistration::where('student_id', $selectedStudentId)
                                         ->with('course')
                                         ->get()
                                         ->map(fn($reg) => $reg->course)
                                         ->filter(); // Remove nulls if any course was deleted
        } else {
            $walletBalance = 0;
        }
        
        return view('billing.index', compact('students', 'courses', 'currentAcademicYear', 'currentMonth', 'currentYear', 'selectedStudentId', 'selectedStudent', 'walletBalance'));
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

    private function getStudentOverallOutstandingBalance($studentId): float
    {
        return \App\Models\Balance::where('student_id', $studentId)
            ->sum('outstanding_balance');
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
            'academic_year' => ['nullable', 'string'],
            'month' => ['nullable', 'string'],
            'year' => ['nullable', 'string'],
            'agreed_amount' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $studentId = $validated['student_id'];
        $courseId = $validated['course_id'];
        $agreedAmount = (float) $validated['agreed_amount'];
        $cashPaymentAmount = (float) $validated['amount_paid'];
        $totalDiscount = (float) ($validated['discount_amount'] ?? 0);

        // Get or create wallet
        $wallet = Wallet::firstOrNew(['student_id' => $studentId]);
        $initialWalletBalance = (float) ($wallet->balance ?? 0);
        $amountFromWallet = 0;

        // Check if there's an existing balance for this course
        $currentCourseBalance = \App\Models\Balance::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->first();
        
        // Get course information
        $course = Course::findOrFail($courseId);
        
        // Determine how much is needed for this course
        // If there's an existing balance with outstanding amount, we need to clear that first
        // Then we need to pay the agreed amount
        $outstandingBalance = 0;
        if ($currentCourseBalance) {
            $outstandingBalance = (float) $currentCourseBalance->outstanding_balance;
        }
        
        // Calculate how much wallet to use
        // First, use cash to clear outstanding balance (if any), then apply to agreed amount
        // Wallet should only cover the shortfall for the agreed amount after cash is applied
        $cashAfterClearingOutstanding = $cashPaymentAmount;
        if ($outstandingBalance > 0) {
            // Use cash to clear outstanding balance first
            $cashAfterClearingOutstanding = max(0, $cashPaymentAmount - $outstandingBalance);
        }
        
        // Calculate shortfall for agreed amount after cash (and after clearing outstanding)
        $shortfallForAgreedAmount = max(0, $agreedAmount - $cashAfterClearingOutstanding);

        // Use wallet funds to cover ONLY the shortfall for the agreed amount (if available)
        if ($initialWalletBalance > 0 && $shortfallForAgreedAmount > 0) {
            $amountFromWallet = min($initialWalletBalance, $shortfallForAgreedAmount);
            
            if ($amountFromWallet > 0) {
                $wallet->balance = $initialWalletBalance - $amountFromWallet;
                $wallet->save();
            }
        }

        // Calculate total payment (cash + wallet)
        $totalPayment = $cashPaymentAmount + $amountFromWallet;

        // Calculate how much to apply to this course
        // First, clear any outstanding balance, then apply to agreed amount
        $amountToApplyToCourse = 0;
        $overpaymentAmount = 0;
        
        if ($outstandingBalance > 0) {
            // First, clear the outstanding balance
            $amountToClearOutstanding = min($totalPayment, $outstandingBalance);
            $remainingAfterClearing = $totalPayment - $amountToClearOutstanding;
            
            // Then apply remaining to the agreed amount (if any)
            $amountToApplyToAgreed = min($remainingAfterClearing, $agreedAmount);
            $amountToApplyToCourse = $amountToClearOutstanding + $amountToApplyToAgreed;
            
            // Any remaining is overpayment
            $overpaymentAmount = max(0, $remainingAfterClearing - $agreedAmount);
        } else {
            // No outstanding balance, just apply to agreed amount
            $amountToApplyToCourse = min($totalPayment, $agreedAmount);
            $overpaymentAmount = max(0, $totalPayment - $agreedAmount);
        }

        // Add overpayment to wallet if any
        if ($overpaymentAmount > 0) {
            $wallet->balance = (float) $wallet->balance + $overpaymentAmount;
            $wallet->save();
        }

        // Create payment record
        $payment = Payment::create([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'academic_year' => $validated['academic_year'] ?? $this->getCurrentAcademicYear(),
            'month' => $validated['month'] ?? null,
            'year' => $validated['year'] ?? now()->year,
            'agreed_amount' => $agreedAmount,
            'amount_paid' => $cashPaymentAmount,
            'wallet_amount_used' => $amountFromWallet,
            'overpayment_amount' => $overpaymentAmount,
            'base_price' => $course->base_price,
            'discount_amount' => $totalDiscount,
            'cashier_id' => auth()->id(),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        // Generate receipt
        $receipt = Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => Receipt::generateReceiptNumber(),
            'receipt_date' => now(),
        ]);

        // Update or create Balance record for this course
        if (!$currentCourseBalance) {
            $currentCourseBalance = new \App\Models\Balance();
            $currentCourseBalance->student_id = $studentId;
            $currentCourseBalance->course_id = $courseId;
            $currentCourseBalance->base_price = $course->base_price;
            $currentCourseBalance->agreed_amount = $agreedAmount;
            $currentCourseBalance->discount_amount = $totalDiscount;
            $currentCourseBalance->total_paid = 0;
            $currentCourseBalance->outstanding_balance = $agreedAmount;
        } else {
            // Update agreed amount if it's different (user may have changed it)
            $currentCourseBalance->agreed_amount = $agreedAmount;
            $currentCourseBalance->base_price = $course->base_price;
            $currentCourseBalance->discount_amount = $totalDiscount;
        }

        // Apply payment to this course
        // The amountToApplyToCourse already accounts for clearing outstanding balance first
        $currentCourseBalance->total_paid = (float) $currentCourseBalance->total_paid + $amountToApplyToCourse;
        $currentCourseBalance->outstanding_balance = max(0, $currentCourseBalance->agreed_amount - $currentCourseBalance->total_paid);
        $currentCourseBalance->status = ($currentCourseBalance->outstanding_balance <= 0) ? 'cleared' : 'partially_paid';
        $currentCourseBalance->last_payment_date = now();
        $currentCourseBalance->save();

        // Log payment for this course
        PaymentLog::create([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'payment_id' => $payment->id,
            'description' => 'Payment for ' . $course->name,
            'base_price' => $currentCourseBalance->base_price,
            'agreed_amount' => $currentCourseBalance->agreed_amount,
            'amount_paid' => $amountToApplyToCourse,
            'balance_before' => (float) ($currentCourseBalance->getOriginal('outstanding_balance') ?? $currentCourseBalance->agreed_amount),
            'balance_after' => $currentCourseBalance->outstanding_balance,
            'wallet_balance_after' => (float) $wallet->balance,
            'payment_date' => now(),
        ]);

        // Log wallet usage if applicable
        if ($amountFromWallet > 0) {
            PaymentLog::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'payment_id' => $payment->id,
                'description' => 'Wallet Funds Applied',
                'amount_paid' => $amountFromWallet,
                'balance_before' => $initialWalletBalance,
                'balance_after' => $initialWalletBalance - $amountFromWallet,
                'wallet_balance_after' => (float) $wallet->balance,
                'payment_date' => now(),
            ]);
        }

        // Log wallet top-up if there was overpayment
        if ($overpaymentAmount > 0) {
            PaymentLog::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'payment_id' => $payment->id,
                'description' => 'Wallet Credit from Overpayment',
                'amount_paid' => $overpaymentAmount,
                'balance_before' => (float) $wallet->balance - $overpaymentAmount,
                'balance_after' => (float) $wallet->balance,
                'wallet_balance_after' => (float) $wallet->balance,
                'payment_date' => now(),
            ]);

            // Send SMS notification for wallet top-up
            try {
                $smsService = app(\App\Services\SmsService::class);
                $smsService->sendWalletTopUpSMS(
                    $payment->student,
                    $overpaymentAmount,
                    $wallet->balance
                );
            } catch (\Exception $e) {
                \Log::error("Failed to send wallet top-up SMS", [
                    'student_id' => $studentId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Automatically register student for the course if not already registered
        // Check if registration already exists for this student, course, academic year, month, and year
        $existingRegistration = CourseRegistration::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
          
            ->first();

        if (!$existingRegistration) {
            CourseRegistration::create([
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
              
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

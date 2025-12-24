<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        $students = $query->latest()->paginate(20)->withQueryString();
        $searchTerm = $request->get('search', '');
        $statusFilter = $request->get('status', '');
        
        return view('students.index', compact('students', 'searchTerm', 'statusFilter'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'date_of_birth' => ['required', 'date'],
                'phone' => ['required', 'string', 'max:20'],
                'gender' => ['required', 'in:male,female,other'],
                'email' => ['nullable', 'email', 'max:255'],
                'level_of_education' => ['required', 'string', 'max:255'],
                'nationality' => ['required', 'string', 'max:255'],
                'id_passport_number' => ['required', 'string', 'max:255'],
                'next_of_kin_name' => ['required', 'string', 'max:255'],
                'next_of_kin_mobile' => ['required', 'string', 'max:20'],
                'address' => ['nullable', 'string'],
                'status' => ['required', 'in:active,inactive,graduated'],
            ]);

            // Auto-generate admission number
            $validated['admission_number'] = $this->generateAdmissionNumber();
            $validated['student_number'] = 'STU-' . strtoupper(Str::random(8));
            // Set default password as student_number (will be hashed automatically by model cast)
            $validated['password'] = $validated['student_number'];

            $student = Student::create($validated);

            // Send enrollment SMS
            try {
                $smsService = app(\App\Services\SmsService::class);
                $smsService->sendEnrollmentSMS($student);
            } catch (\Exception $e) {
                // Log error but don't fail the enrollment
                \Log::error("Failed to send enrollment SMS", [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Log the activity
            ActivityLog::log(
                'student.created',
                "Admitted new student: {$student->full_name} (Admission #: {$student->admission_number})",
                $student
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student admitted successfully!',
                    'student' => $student,
                    'redirect' => route('students.show', $student->id)
                ]);
            }

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check the form.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    public function show(Student $student)
    {
        $student->load('payments.course', 'payments.receipt', 'payments.cashier', 'courseRegistrations.course');
        $courses = \App\Models\Course::where('status', 'active')->orderBy('name')->get();
        
        // Get current academic year and month for monthly billing
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $currentMonth = now()->format('F Y'); // e.g., "December 2024"
        $currentYear = now()->year;
        $currentMonthName = now()->format('F'); // e.g., "December"
        
        // Generate months list for dropdown
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        // Calculate statistics
        $totalPayments = $student->payments->count();
        $totalPaid = $student->payments->sum('amount_paid');
        $totalAgreed = $student->payments->sum('agreed_amount');
        $totalBalance = max(0, $totalAgreed - $totalPaid);
        $registeredCourses = $student->courseRegistrations->count();
        
        // Payment method breakdown
        $paymentMethods = $student->payments->groupBy('payment_method')->map(function($payments) {
            return [
                'count' => $payments->count(),
                'total' => $payments->sum('amount_paid')
            ];
        });
        
        // Outstanding balances per course
        $courseBalances = $student->payments->groupBy('course_id')->map(function($payments, $courseId) {
            $course = $payments->first()->course;
            $agreed = $payments->sum('agreed_amount');
            $paid = $payments->sum('amount_paid');
            $balance = max(0, $agreed - $paid);
            
            return [
                'course' => $course,
                'agreed' => $agreed,
                'paid' => $paid,
                'balance' => $balance,
                'payments_count' => $payments->count()
            ];
        })->filter(function($item) {
            return $item['balance'] > 0;
        });
        
        // Recent payments (last 5)
        $recentPayments = $student->payments()->with('course', 'receipt', 'cashier')->latest()->take(5)->get();
        
        // Monthly payment trend (last 6 months) - SQLite compatible
        $monthlyTrend = $student->payments()
            ->selectRaw("strftime('%Y-%m', created_at) as month, SUM(amount_paid) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('students.show', compact(
            'student', 
            'courses', 
            'currentAcademicYear', 
            'currentMonth', 
            'currentYear', 
            'currentMonthName', 
            'months',
            'totalPayments',
            'totalPaid',
            'totalAgreed',
            'totalBalance',
            'registeredCourses',
            'paymentMethods',
            'courseBalances',
            'recentPayments',
            'monthlyTrend'
        ));
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

    private function getCurrentTerm(): string
    {
        $month = now()->month;
        
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

    private function generateAdmissionNumber(): string
    {
        $year = now()->year;
        
        // Get the last admission number for this year
        $lastAdmission = Student::where('admission_number', 'like', "ADM-{$year}-%")
            ->orderBy('admission_number', 'desc')
            ->first();
        
        if ($lastAdmission && preg_match('/ADM-' . $year . '-(\d+)/', $lastAdmission->admission_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format: ADM-YYYY-XXXXX (5 digits)
        $admissionNumber = sprintf('ADM-%d-%05d', $year, $nextNumber);
        
        // Ensure uniqueness (in case of race conditions)
        while (Student::where('admission_number', $admissionNumber)->exists()) {
            $nextNumber++;
            $admissionNumber = sprintf('ADM-%d-%05d', $year, $nextNumber);
        }
        
        return $admissionNumber;
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['nullable', 'email', 'max:255'],
            'level_of_education' => ['required', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
            'id_passport_number' => ['required', 'string', 'max:255'],
            'next_of_kin_name' => ['required', 'string', 'max:255'],
            'next_of_kin_mobile' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,graduated'],
        ]);

        $student->update($validated);

        // Log the activity
        ActivityLog::log(
            'student.updated',
            "Updated student information: {$student->full_name} (Admission #: {$student->admission_number})",
            $student
        );

        return redirect()->route('students.show', $student->id)
            ->with('success', 'Student information updated successfully!');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully!');
    }

    /**
     * Send welcome SMS to a student
     */
    public function sendWelcomeSMS(Request $request, Student $student)
    {
        try {
            // Ensure we return JSON even if there's an error
            if (!$request->wantsJson() && !$request->ajax()) {
                $request->headers->set('Accept', 'application/json');
            }

            $smsService = app(SmsService::class);
            $success = $smsService->sendEnrollmentSMS($student);

            if ($success) {
                // Log the activity
                ActivityLog::log(
                    'student.welcome_sms_sent',
                    "Welcome SMS sent to {$student->full_name} (Admission #: {$student->admission_number})",
                    $student
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Welcome SMS sent successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send welcome SMS. Please check the student\'s phone number and SMS configuration.'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send welcome SMS", [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the welcome SMS: ' . $e->getMessage()
            ], 500);
        }
    }
}

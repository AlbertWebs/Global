<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Balance;
use App\Models\BankDeposit;
use App\Models\Billing;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Expense;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\Teacher;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPurgeController extends Controller
{
    public function index()
    {
        $this->requirePermission('data_purge.manage');

        // Get counts for each table
        $counts = [
            'students' => Student::count(),
            'payments' => Payment::count(),
            'receipts' => Receipt::count(),
            'expenses' => Expense::count(),
            'course_registrations' => CourseRegistration::count(),
            'bank_deposits' => BankDeposit::count(),
            'ledger_entries' => LedgerEntry::count(),
            'activity_logs' => ActivityLog::count(),
            'balances' => Balance::count(),
            'wallets' => Wallet::count(),
            'courses' => Course::count(),
            'teachers' => Teacher::count(),
            'payment_logs' => PaymentLog::count(),
            'billings' => Billing::count(),
            'attendances' => Attendance::count(),
            'announcements' => Announcement::count(),
            'student_results' => StudentResult::count(),
        ];

        return view('data-purge.index', compact('counts'));
    }

    public function purge(Request $request)
    {
        // Only Super Admin can purge data
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can purge data');
        }

        $validated = $request->validate([
            'purge_all' => ['nullable', 'boolean'],
            'purge_students' => ['nullable', 'boolean'],
            'purge_payments' => ['nullable', 'boolean'],
            'purge_receipts' => ['nullable', 'boolean'],
            'purge_expenses' => ['nullable', 'boolean'],
            'purge_course_registrations' => ['nullable', 'boolean'],
            'purge_bank_deposits' => ['nullable', 'boolean'],
            'purge_ledger_entries' => ['nullable', 'boolean'],
            'purge_activity_logs' => ['nullable', 'boolean'],
            'purge_balances' => ['nullable', 'boolean'],
            'purge_wallets' => ['nullable', 'boolean'],
            'purge_courses' => ['nullable', 'boolean'],
            'purge_teachers' => ['nullable', 'boolean'],
            'purge_payment_logs' => ['nullable', 'boolean'],
            'purge_billings' => ['nullable', 'boolean'],
            'purge_attendances' => ['nullable', 'boolean'],
            'purge_announcements' => ['nullable', 'boolean'],
            'purge_student_results' => ['nullable', 'boolean'],
            'confirm_text' => ['required', 'string'],
        ]);

        // Verify confirmation text
        if (strtoupper($validated['confirm_text']) !== 'DELETE ALL DATA') {
            return redirect()->route('data-purge.index')
                ->with('error', 'Confirmation text does not match. Data purge cancelled.');
        }

        DB::beginTransaction();
        try {
            $purged = [];

            if ($validated['purge_all'] ?? false) {
                // Purge everything except users
                LedgerEntry::truncate();
                Receipt::truncate();
                Payment::truncate();
                PaymentLog::truncate();
                BankDeposit::truncate();
                Expense::truncate();
                CourseRegistration::truncate();
                ActivityLog::truncate();
                Balance::truncate();
                Wallet::truncate();
                Billing::truncate();
                Attendance::truncate();
                Announcement::truncate();
                StudentResult::truncate();
                Student::truncate();
                Course::truncate();
                Teacher::truncate();
                
                $purged[] = 'All data (except users)';
            } else {
                // Selective purging
                if ($validated['purge_ledger_entries'] ?? false) {
                    LedgerEntry::truncate();
                    $purged[] = 'Ledger Entries';
                }

                if ($validated['purge_receipts'] ?? false) {
                    Receipt::truncate();
                    $purged[] = 'Receipts';
                }

                if ($validated['purge_payments'] ?? false) {
                    Payment::truncate();
                    $purged[] = 'Payments';
                }

                if ($validated['purge_bank_deposits'] ?? false) {
                    BankDeposit::truncate();
                    $purged[] = 'Bank Deposits';
                }

                if ($validated['purge_expenses'] ?? false) {
                    Expense::truncate();
                    $purged[] = 'Expenses';
                }

                if ($validated['purge_course_registrations'] ?? false) {
                    CourseRegistration::truncate();
                    $purged[] = 'Course Registrations';
                }

                if ($validated['purge_students'] ?? false) {
                    // Delete students (this will cascade to related records)
                    Student::truncate();
                    $purged[] = 'Students';
                }

                if ($validated['purge_activity_logs'] ?? false) {
                    ActivityLog::truncate();
                    $purged[] = 'Activity Logs';
                }

                if ($validated['purge_balances'] ?? false) {
                    Balance::truncate();
                    $purged[] = 'Balances';
                }

                if ($validated['purge_wallets'] ?? false) {
                    Wallet::truncate();
                    $purged[] = 'Wallet Balances';
                }

                if ($validated['purge_courses'] ?? false) {
                    Course::truncate();
                    $purged[] = 'Courses';
                }

                if ($validated['purge_teachers'] ?? false) {
                    Teacher::truncate();
                    $purged[] = 'Teachers';
                }

                if ($validated['purge_payment_logs'] ?? false) {
                    PaymentLog::truncate();
                    $purged[] = 'Payment Logs';
                }

                if ($validated['purge_billings'] ?? false) {
                    Billing::truncate();
                    $purged[] = 'Billings';
                }

                if ($validated['purge_attendances'] ?? false) {
                    Attendance::truncate();
                    $purged[] = 'Attendances';
                }

                if ($validated['purge_announcements'] ?? false) {
                    Announcement::truncate();
                    $purged[] = 'Announcements';
                }

                if ($validated['purge_student_results'] ?? false) {
                    StudentResult::truncate();
                    $purged[] = 'Student Results';
                }
            }

            DB::commit();

            // Log the purge activity (if activity logs weren't purged)
            if (!($validated['purge_all'] ?? false) && !($validated['purge_activity_logs'] ?? false)) {
                ActivityLog::log(
                    'data.purged',
                    'Data purge completed: ' . implode(', ', $purged),
                    null
                );
            }

            return redirect()->route('data-purge.index')
                ->with('success', 'Data purge completed successfully. Purged: ' . implode(', ', $purged));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('data-purge.index')
                ->with('error', 'Error purging data: ' . $e->getMessage());
        }
    }
}

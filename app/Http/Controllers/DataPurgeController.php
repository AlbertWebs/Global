<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Balance;
use App\Models\BankDeposit;
use App\Models\CourseRegistration;
use App\Models\Expense;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPurgeController extends Controller
{
    public function index()
    {
        // Only Super Admin can access data purge
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access Data Purge');
        }

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
                BankDeposit::truncate();
                Expense::truncate();
                CourseRegistration::truncate();
                ActivityLog::truncate();
                Balance::truncate();
                Student::truncate();
                
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

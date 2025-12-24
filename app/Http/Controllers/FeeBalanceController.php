<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeeBalanceController extends Controller
{
    public function index()
    {
        // Only Super Admin can view fee balances
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can view fee balances');
        }

        // Get all students with their payments
        $students = Student::with(['payments' => function($query) {
            $query->latest();
        }])->where('status', 'active')->get();

        // Calculate balances for each student
        $studentsWithBalances = $students->map(function($student) {
            $totalAgreed = $student->payments->sum('agreed_amount');
            $totalPaid = $student->payments->sum('amount_paid');
            $balance = $totalAgreed - $totalPaid;
            
            return [
                'student' => $student,
                'total_agreed' => $totalAgreed,
                'total_paid' => $totalPaid,
                'balance' => $balance,
                'has_balance' => $balance > 0,
            ];
        })->filter(function($item) {
            return $item['has_balance'];
        })->sortByDesc('balance');

        return view('fee-balances.index', compact('studentsWithBalances'));
    }

    public function sendReminders(Request $request)
    {
        // Only Super Admin can send reminders
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can send reminders');
        }

        $studentIds = $request->input('student_ids', []);
        $method = $request->input('method', 'both'); // 'email', 'sms', 'both'

        if (empty($studentIds)) {
            return redirect()->route('fee-balances.index')
                ->with('error', 'Please select at least one student.');
        }

        $students = Student::whereIn('id', $studentIds)->get();
        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            $totalAgreed = $student->payments->sum('agreed_amount');
            $totalPaid = $student->payments->sum('amount_paid');
            $balance = $totalAgreed - $totalPaid;

            if ($balance <= 0) {
                continue;
            }

            $message = "Dear {$student->full_name}, you have an outstanding balance of KES " . number_format($balance, 2) . ". Please make payment at your earliest convenience. Thank you.";

            try {
                if ($method === 'email' || $method === 'both') {
                    if ($student->email) {
                        Mail::raw($message, function($mail) use ($student) {
                            $mail->to($student->email)
                                 ->subject('Fee Balance Reminder - Global College');
                        });
                        $sent++;
                    }
                }

                if ($method === 'sms' || $method === 'both') {
                    if ($student->phone) {
                        // TODO: Integrate with SMS provider (e.g., Twilio, Africa's Talking)
                        // For now, we'll just log it
                        \Log::info("SMS to {$student->phone}: {$message}");
                        $sent++;
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send reminder to {$student->full_name}: " . $e->getMessage());
                $failed++;
            }
        }

        $message = "Reminders sent successfully to {$sent} student(s).";
        if ($failed > 0) {
            $message .= " {$failed} failed.";
        }

        return redirect()->route('fee-balances.index')
            ->with('success', $message);
    }
}

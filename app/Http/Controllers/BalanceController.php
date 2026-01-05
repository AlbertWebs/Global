<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('super_admin'); // Only Super Admin can manage balances
    }

    public function index(Request $request)
    {
        $query = Balance::with(['student', 'course']);

        if ($request->has('student_id') && $request->input('student_id') != '') {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->has('course_id') && $request->input('course_id') != '') {
            $query->where('course_id', $request->input('course_id'));
        }

        if ($request->has('status') && $request->input('status') != '') {
            $query->where('status', $request->input('status'));
        }

        $balances = $query->orderBy('last_payment_date', 'desc')->paginate(10);
        $students = Student::orderBy('first_name')->get();
        $courses = Course::orderBy('name')->get();

        return view('balances.index', compact('balances', 'students', 'courses'));
    }

    public function show(Balance $balance)
    {
        return view('balances.show', compact('balance'));
    }

    public function edit(Balance $balance)
    {
        return view('balances.edit', compact('balance'));
    }

    public function update(Request $request, Balance $balance)
    {
        $validated = $request->validate([
            'adjustment_amount' => ['required', 'numeric'],
            'adjustment_reason' => ['required', 'string', 'max:255'],
        ]);

        // Log the adjustment before applying
        // You would typically use a dedicated AuditLog model here
        // For simplicity, we'll log to ActivityLog for now
        \App\Models\ActivityLog::log(
            'balance.adjusted',
            'Manual balance adjustment for student ' . $balance->student->full_name .
            ' course ' . $balance->course->name . ': ' . $validated['adjustment_amount'] .
            ' (Reason: ' . $validated['adjustment_reason'] . '). Previous balance: ' .
            $balance->outstanding_balance,
            $balance
        );

        $balance->outstanding_balance += $validated['adjustment_amount'];
        $balance->total_paid -= $validated['adjustment_amount']; // Adjust total paid inverse to adjustment

        $balance->status = ($balance->outstanding_balance <= 0) ? 'cleared' : 'partially_paid';
        $balance->last_payment_date = now();
        $balance->save();

        return redirect()->route('balances.index')->with('success', 'Balance adjusted successfully.');
    }
}

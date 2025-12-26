<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        // Check if user has permission to view this receipt
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $receipt->payment->cashier_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('receipts.show', compact('receipt'));
    }

    public function print($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $receipt->payment->cashier_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('receipts.print', compact('receipt'));
    }

    public function thermal($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $receipt->payment->cashier_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('receipts.thermal', compact('receipt'));
    }

    public function printBw($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $receipt->payment->cashier_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('receipts.print-bw', compact('receipt'));
    }

    public function index()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $receipts = Receipt::with(['payment.student', 'payment.course'])
                ->whereHas('payment', function($query) {
                    $query->whereHas('student')->whereHas('course');
                })
                ->latest()
                ->paginate(20);
        } else {
            $receipts = Receipt::whereHas('payment', function($query) use ($user) {
                $query->where('cashier_id', $user->id)
                    ->whereHas('student')
                    ->whereHas('course');
            })
            ->with(['payment.student', 'payment.course'])
            ->latest()
            ->paginate(20);
        }

        return view('receipts.index', compact('receipts'));
    }

    /**
     * Show receipt for student portal (student-accessible)
     */
    public function studentShow($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        // Check if student is logged in and owns this receipt
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to view your receipt.');
        }

        // Verify the receipt belongs to the logged-in student
        if ($receipt->payment->student_id != $studentId) {
            abort(403, 'You do not have permission to view this receipt.');
        }

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Print receipt for student portal (student-accessible)
     */
    public function studentPrint($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to view your receipt.');
        }

        if ($receipt->payment->student_id != $studentId) {
            abort(403, 'You do not have permission to view this receipt.');
        }

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt (B&W) for student portal (student-accessible)
     */
    public function studentPrintBw($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to view your receipt.');
        }

        if ($receipt->payment->student_id != $studentId) {
            abort(403, 'You do not have permission to view this receipt.');
        }

        return view('receipts.print-bw', compact('receipt'));
    }

    /**
     * Print receipt (thermal) for student portal (student-accessible)
     */
    public function studentThermal($id)
    {
        $receipt = Receipt::with(['payment.student', 'payment.course', 'payment.cashier'])->findOrFail($id);
        
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to view your receipt.');
        }

        if ($receipt->payment->student_id != $studentId) {
            abort(403, 'You do not have permission to view this receipt.');
        }

        return view('receipts.thermal', compact('receipt'));
    }
}

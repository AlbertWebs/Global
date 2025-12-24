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
                ->latest()
                ->paginate(20);
        } else {
            $receipts = Receipt::whereHas('payment', function($query) use ($user) {
                $query->where('cashier_id', $user->id);
            })
            ->with(['payment.student', 'payment.course'])
            ->latest()
            ->paginate(20);
        }

        return view('receipts.index', compact('receipts'));
    }
}

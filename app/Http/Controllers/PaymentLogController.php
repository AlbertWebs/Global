<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('super_admin');
    }

    public function index()
    {
        $paymentLogs = \App\Models\PaymentLog::with(['student', 'course', 'payment'])
            ->latest()
            ->paginate(20);

        return view('payment-logs.index', compact('paymentLogs'));
    }

    public function show($id)
    {
        $paymentLog = \App\Models\PaymentLog::with(['student', 'course', 'payment'])
            ->findOrFail($id);

        return view('payment-logs.show', compact('paymentLog'));
    }
}

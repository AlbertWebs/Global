<?php

namespace App\Http\Controllers;

use App\Exports\PaymentLogsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

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

    public function exportExcel(Request $request)
    {
        $studentId = $request->query('student_id');
        $student = \App\Models\Student::find($studentId);
        $fileName = 'payment_logs_' . ($student ? Str::slug($student->full_name) : 'all') . '_' . now()->format('Ymd_His') . '.xlsx';
        
        return Excel::download(new PaymentLogsExport($studentId), $fileName);
    }
}

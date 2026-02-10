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

    public function index(Request $request)
    {
        $query = \App\Models\PaymentLog::with(['student', 'course', 'payment']);
        
        // Filter by student_id if provided
        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }
        
        $paymentLogs = $query->latest()->paginate(20);

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
        
        if (!$studentId) {
            return back()->with('error', 'Student ID is required for export.');
        }
        
        $student = \App\Models\Student::find($studentId);
        
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }
        
        $fileName = 'payment_records_' . Str::slug($student->full_name) . '_' . now()->format('Ymd_His') . '.xlsx';
        
        return Excel::download(new PaymentLogsExport($studentId), $fileName);
    }
}

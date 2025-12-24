<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function index()
    {
        // Get the authenticated student (assuming student_id is stored in session or auth)
        // For now, we'll get the first student as a placeholder
        // In production, you'd get this from authentication
        $student = Student::first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student not found');
        }

        $student->load('payments.course', 'courseRegistrations.course');
        
        // Calculate statistics
        $totalPayments = $student->payments->count();
        $totalPaid = $student->payments->sum('amount_paid');
        $totalAgreed = $student->payments->sum('agreed_amount');
        $currentBalance = max(0, $totalAgreed - $totalPaid);
        $registeredCourses = $student->courseRegistrations->count();
        
        // Latest results (placeholder - you'd have actual results data)
        $latestResults = [];
        
        return view('student-portal.index', compact(
            'student',
            'totalPayments',
            'totalPaid',
            'currentBalance',
            'registeredCourses',
            'latestResults'
        ));
    }

    public function financialInfo()
    {
        $student = Student::first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student not found');
        }

        $student->load('payments.course', 'payments.receipt');
        
        return view('student-portal.financial-info', compact('student'));
    }

    public function courses()
    {
        $student = Student::first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student not found');
        }

        $student->load('courseRegistrations.course');
        
        return view('student-portal.courses', compact('student'));
    }

    public function results()
    {
        $student = Student::first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student not found');
        }

        // Placeholder for results data
        $results = [];
        
        return view('student-portal.results', compact('student', 'results'));
    }

    public function settings()
    {
        $student = Student::first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student not found');
        }

        return view('student-portal.settings', compact('student'));
    }
}


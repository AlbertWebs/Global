<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can manage students.'
            ]);
        }

        $query = Student::query();
        
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $students = $query->with('courseRegistrations.course', 'payments')->latest()->paginate(20)->withQueryString();
        $searchTerm = $request->get('search', '');
        $statusFilter = $request->get('status', '');

        return view('admin.students.index', compact('students', 'searchTerm', 'statusFilter'));
    }

    public function show(Student $student)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can view student details.'
            ]);
        }

        $student->load('payments.course', 'courseRegistrations.course', 'results.course');
        return view('admin.students.show', compact('student'));
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can manage teachers.'
            ]);
        }

        $query = Teacher::query();
        
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $teachers = $query->latest()->paginate(20)->withQueryString();
        $searchTerm = $request->get('search', '');
        $statusFilter = $request->get('status', '');

        return view('admin.teachers.index', compact('teachers', 'searchTerm', 'statusFilter'));
    }

    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can create teachers.'
            ]);
        }

        $courses = Course::where('status', 'active')->get();
        return view('admin.teachers.create', compact('courses'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can create teachers.'
            ]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        // Auto-generate employee number
        $validated['employee_number'] = $this->generateEmployeeNumber();
        $validated['password'] = Hash::make('password123'); // Default password

        $teacher = Teacher::create($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully! Default password: password123');
    }

    public function show(Teacher $teacher)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can view teacher details.'
            ]);
        }

        $teacher->load('courses');
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can edit teachers.'
            ]);
        }

        $courses = Course::where('status', 'active')->get();
        return view('admin.teachers.edit', compact('teacher', 'courses'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can update teachers.'
            ]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        $teacher->update($validated);

        return redirect()->route('admin.teachers.show', $teacher->id)
            ->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can delete teachers.'
            ]);
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }

    private function generateEmployeeNumber(): string
    {
        $year = now()->year;
        $lastEmployee = Teacher::where('employee_number', 'like', "EMP-{$year}-%")
            ->orderBy('employee_number', 'desc')
            ->first();
        
        if ($lastEmployee && preg_match('/EMP-' . $year . '-(\d+)/', $lastEmployee->employee_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }
        
        return sprintf('EMP-%d-%05d', $year, $nextNumber);
    }
}


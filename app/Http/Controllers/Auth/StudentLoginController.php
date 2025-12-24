<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'student_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('student_number', $request->student_number)
            ->where('status', 'active')
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'student_number' => 'Invalid student number or account is inactive.',
            ]);
        }

        // Check password (using student_number as default password for now)
        // In production, you'd have a password field in students table
        if ($request->password !== $student->student_number) {
            throw ValidationException::withMessages([
                'password' => 'Invalid credentials.',
            ]);
        }

        // Store student in session
        session(['student_id' => $student->id]);
        session(['student_logged_in' => true]);

        return redirect()->route('student-portal.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['student_id', 'student_logged_in']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('student.login');
    }
}


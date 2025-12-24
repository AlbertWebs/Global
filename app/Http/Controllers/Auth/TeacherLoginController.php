<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TeacherLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.teacher-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'employee_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $teacher = Teacher::where('employee_number', $request->employee_number)
            ->where('status', 'active')
            ->first();

        if (!$teacher) {
            throw ValidationException::withMessages([
                'employee_number' => 'Invalid employee number or account is inactive.',
            ]);
        }

        // Check password - try hashed password first, then fallback to employee_number as default
        $passwordValid = false;
        
        // Check if password matches (hashed)
        if (Hash::check($request->password, $teacher->password)) {
            $passwordValid = true;
        }
        // Fallback: check if password is the employee_number (for default password)
        elseif ($request->password === $teacher->employee_number) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            throw ValidationException::withMessages([
                'password' => 'Invalid credentials.',
            ]);
        }

        // Store teacher in session
        session(['teacher_id' => $teacher->id]);
        session(['teacher_logged_in' => true]);

        return redirect()->route('teacher-portal.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['teacher_id', 'teacher_logged_in']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('teacher.login');
    }
}

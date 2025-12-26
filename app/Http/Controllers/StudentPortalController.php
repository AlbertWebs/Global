<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentResult;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class StudentPortalController extends Controller
{
    public function index()
    {
        // Get the authenticated student from session
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to access the student portal.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            session()->forget(['student_id', 'student_logged_in']);
            return redirect()->route('student.login')->with('error', 'Student not found. Please login again.');
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
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to access the student portal.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Student not found.');
        }

        $student->load('payments.course', 'payments.receipt');
        
        return view('student-portal.financial-info', compact('student'));
    }

    public function courses()
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to access the student portal.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Student not found.');
        }

        $student->load('courseRegistrations.course');
        
        return view('student-portal.courses', compact('student'));
    }

    public function announcements()
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to access the student portal.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Student not found.');
        }

        // Get student's course IDs
        $studentCourseIds = $student->courseRegistrations()->pluck('course_id')->toArray();
        
        // Load all active announcements for students
        $allAnnouncements = Announcement::where('status', 'active')
            ->where(function($query) {
                $query->where('target_audience', 'all')
                      ->orWhere('target_audience', 'students');
            })
            ->with('postedBy')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Filter announcements based on targeting
        $announcements = $allAnnouncements->filter(function($announcement) use ($student, $studentCourseIds) {
            // If no specific targeting, include it
            if (empty($announcement->target_courses) && empty($announcement->target_student_groups)) {
                return true;
            }
            
            // Check if student is specifically targeted
            if (!empty($announcement->target_student_groups) && in_array($student->id, $announcement->target_student_groups)) {
                return true;
            }
            
            // Check if any of student's courses are targeted
            if (!empty($announcement->target_courses) && !empty($studentCourseIds)) {
                foreach ($studentCourseIds as $courseId) {
                    if (in_array($courseId, $announcement->target_courses)) {
                        return true;
                    }
                }
            }
            
            return false;
        });
        
        return view('student-portal.announcements', compact('student', 'announcements'));
    }

    public function settings()
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to access the student portal.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Student not found.');
        }

        return view('student-portal.settings', compact('student'));
    }

    public function changePassword(Request $request)
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to access the student portal.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Student not found.');
        }

        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($student) {
                    // If password is null, check against student_number (backward compatibility)
                    if ($student->password) {
                        if (!Hash::check($value, $student->password)) {
                            $fail('The current password is incorrect.');
                        }
                    } else {
                        // Fallback: if no password set, use student_number as default
                        if ($value !== $student->student_number) {
                            $fail('The current password is incorrect.');
                        }
                    }
                },
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $student->update([
            'password' => $request->password,
        ]);

        return redirect()->route('student-portal.settings')
            ->with('success', 'Password changed successfully!');
    }

    public function uploadPhoto(Request $request)
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Please login to access the student portal.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Student not found.');
        }

        $request->validate([
            'photo' => ['required', 'image', 'max:2048', 'mimes:jpeg,jpg,png'], // 2MB max
        ]);

        // Delete old photo if exists
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        // Store new photo
        $photoPath = $request->file('photo')->store('student-photos', 'public');

        $student->update([
            'photo' => $photoPath,
        ]);

        return redirect()->route('student-portal.settings')
            ->with('success', 'Photo uploaded successfully!');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\Announcement;
use Illuminate\Http\Request;

class TeacherPortalController extends Controller
{
    public function index()
    {
        // Get the authenticated teacher (placeholder - in production, get from auth)
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'teacher@school.edu',
            'photo' => null,
        ];
        
        // Get courses taught by this teacher
        $courses = Course::where('status', 'active')->take(5)->get();
        
        // Get students in these courses
        $totalStudents = Student::where('status', 'active')->count();
        
        // Get recent announcements
        $recentAnnouncements = Announcement::latest()->take(5)->get();
        
        // Get pending results to post
        $pendingResults = StudentResult::where('status', 'pending')->count();
        
        return view('teacher-portal.index', compact('teacher', 'courses', 'totalStudents', 'recentAnnouncements', 'pendingResults'));
    }

    public function personalInfo()
    {
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'teacher@school.edu',
            'phone' => '+254 700 000 000',
            'address' => 'Nairobi, Kenya',
            'photo' => null,
        ];
        
        return view('teacher-portal.personal-info', compact('teacher'));
    }

    public function courses()
    {
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
        ];
        
        $courses = Course::where('status', 'active')->get();
        
        return view('teacher-portal.courses', compact('teacher', 'courses'));
    }

    public function studentProgress()
    {
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
        ];
        
        $students = Student::where('status', 'active')->with('courseRegistrations.course')->get();
        
        return view('teacher-portal.student-progress', compact('teacher', 'students'));
    }

    public function postResults()
    {
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
        ];
        
        $courses = Course::where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        $results = StudentResult::with('student', 'course')->latest()->paginate(20);
        
        return view('teacher-portal.post-results', compact('teacher', 'courses', 'students', 'results'));
    }

    public function storeResult(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'term' => 'required|string',
            'exam_type' => 'required|string',
            'score' => 'required|numeric|min:0|max:100',
            'grade' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        StudentResult::create($validated);

        return redirect()->route('teacher-portal.post-results')
            ->with('success', 'Result posted successfully!');
    }

    public function communicate()
    {
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
        ];
        
        $announcements = Announcement::latest()->paginate(20);
        $students = Student::where('status', 'active')->get();
        
        return view('teacher-portal.communicate', compact('teacher', 'announcements', 'students'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,students,parents',
            'priority' => 'required|in:low,medium,high',
        ]);

        Announcement::create([
            ...$validated,
            'posted_by' => auth()->id() ?? 1,
            'status' => 'active',
        ]);

        return redirect()->route('teacher-portal.communicate')
            ->with('success', 'Announcement posted successfully!');
    }

    public function attendance()
    {
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
        ];
        
        $courses = Course::where('status', 'active')->get();
        
        return view('teacher-portal.attendance', compact('teacher', 'courses'));
    }

    public function settings()
    {
        $teacher = (object)[
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'teacher@school.edu',
        ];
        
        return view('teacher-portal.settings', compact('teacher'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\Announcement;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class TeacherPortalController extends Controller
{
    public function index()
    {
        // Get the authenticated teacher from session
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            session()->forget(['teacher_id', 'teacher_logged_in']);
            return redirect()->route('teacher.login')->with('error', 'Teacher not found. Please login again.');
        }
        
        // Get courses taught by this teacher
        $courses = $teacher->courses()->where('status', 'active')->take(5)->get();
        
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
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }
        
        return view('teacher-portal.personal-info', compact('teacher'));
    }

    public function courses()
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }
        
        $courses = $teacher->courses()->where('status', 'active')->get();
        
        return view('teacher-portal.courses', compact('teacher', 'courses'));
    }

    public function studentProgress()
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }
        
        $students = Student::where('status', 'active')->with('courseRegistrations.course')->get();
        
        return view('teacher-portal.student-progress', compact('teacher', 'students'));
    }

    public function postResults()
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }
        
        $courses = $teacher->courses()->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        $results = StudentResult::with('student', 'course')->latest()->paginate(20);
        
        return view('teacher-portal.post-results', compact('teacher', 'courses', 'students', 'results'));
    }

    public function storeResult(Request $request)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'term' => 'required|string',
            'exam_type' => 'required|string',
            'score' => 'required|numeric|min:0|max:100',
            'grade' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'nullable|in:pending,published,archived',
        ]);

        // Calculate grade if not provided
        if (empty($validated['grade'])) {
            $result = new StudentResult();
            $result->score = $validated['score'];
            $validated['grade'] = $result->calculateGrade();
        }

        StudentResult::create([
            ...$validated,
            'status' => $validated['status'] ?? 'published', // Default to published
            'posted_by' => auth()->id() ?? null,
        ]);

        return redirect()->route('teacher-portal.post-results')
            ->with('success', 'Result posted successfully!');
    }

    public function editResult($id)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $result = StudentResult::with('student', 'course')->findOrFail($id);
        $teacher = Teacher::find($teacherId);
        $courses = $teacher->courses()->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        
        return view('teacher-portal.edit-result', compact('result', 'teacher', 'courses', 'students'));
    }

    public function updateResult(Request $request, $id)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $result = StudentResult::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'term' => 'required|string',
            'exam_type' => 'required|string',
            'score' => 'required|numeric|min:0|max:100',
            'grade' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'nullable|in:pending,published,archived',
        ]);

        // Calculate grade if not provided
        if (empty($validated['grade'])) {
            $tempResult = new StudentResult();
            $tempResult->score = $validated['score'];
            $validated['grade'] = $tempResult->calculateGrade();
        }

        $result->update([
            ...$validated,
            'status' => $validated['status'] ?? $result->status,
        ]);

        return redirect()->route('teacher-portal.post-results')
            ->with('success', 'Result updated successfully!');
    }

    public function communicate()
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }
        
        $announcements = Announcement::latest()->paginate(20);
        $students = Student::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->get();
        
        return view('teacher-portal.communicate', compact('teacher', 'announcements', 'students', 'courses'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,students,parents',
            'priority' => 'required|in:low,medium,high',
            'target_courses' => 'nullable|array',
            'target_courses.*' => 'exists:courses,id',
            'target_student_groups' => 'nullable|array',
            'target_student_groups.*' => 'exists:students,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('announcement-attachments', 'public');
            
            // Determine file type
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, ['pdf'])) {
                $attachmentType = 'pdf';
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $attachmentType = 'docx';
            } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $attachmentType = 'image';
            } else {
                $attachmentType = 'file';
            }
        }

        Announcement::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'priority' => $validated['priority'],
            'target_courses' => $validated['target_courses'] ?? null,
            'target_student_groups' => $validated['target_student_groups'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'posted_by' => auth()->id() ?? 1,
            'status' => 'active',
            'published_at' => now(),
        ]);

        return redirect()->route('teacher-portal.communicate')
            ->with('success', 'Announcement posted successfully!');
    }

    public function editAnnouncement($id)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $announcement = Announcement::findOrFail($id);
        $teacher = Teacher::find($teacherId);
        $students = Student::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->get();
        
        return view('teacher-portal.edit-announcement', compact('announcement', 'teacher', 'students', 'courses'));
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,students,parents',
            'priority' => 'required|in:low,medium,high',
            'target_courses' => 'nullable|array',
            'target_courses.*' => 'exists:courses,id',
            'target_student_groups' => 'nullable|array',
            'target_student_groups.*' => 'exists:students,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $attachmentPath = $announcement->attachment_path;
        $attachmentName = $announcement->attachment_name;
        $attachmentType = $announcement->attachment_type;

        // Handle file upload if new file is provided
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($announcement->attachment_path && Storage::disk('public')->exists($announcement->attachment_path)) {
                Storage::disk('public')->delete($announcement->attachment_path);
            }

            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('announcement-attachments', 'public');
            
            // Determine file type
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, ['pdf'])) {
                $attachmentType = 'pdf';
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $attachmentType = 'docx';
            } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $attachmentType = 'image';
            } else {
                $attachmentType = 'file';
            }
        }

        $announcement->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'priority' => $validated['priority'],
            'target_courses' => $validated['target_courses'] ?? null,
            'target_student_groups' => $validated['target_student_groups'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        return redirect()->route('teacher-portal.communicate')
            ->with('success', 'Announcement updated successfully!');
    }

    public function deleteAnnouncement($id)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $announcement = Announcement::findOrFail($id);

        // Delete attachment if exists
        if ($announcement->attachment_path && Storage::disk('public')->exists($announcement->attachment_path)) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }

        $announcement->delete();

        return redirect()->route('teacher-portal.communicate')
            ->with('success', 'Announcement deleted successfully!');
    }

    public function attendance()
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }
        
        $courses = $teacher->courses()->where('status', 'active')->get();
        
        // Get attendance records for the teacher's courses
        $attendances = collect();
        if ($courses->isNotEmpty()) {
            $attendances = Attendance::whereIn('course_id', $courses->pluck('id'))
                ->with('student', 'course')
                ->latest('attendance_date')
                ->paginate(20);
        } else {
            // Create empty paginator if no courses
            $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1
            );
        }
        
        return view('teacher-portal.attendance', compact('teacher', 'courses', 'attendances'));
    }

    public function markAttendance(Request $request)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'attendance_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string',
        ]);

        // Verify teacher teaches this course
        if (!$teacher->courses()->where('courses.id', $validated['course_id'])->exists()) {
            return redirect()->route('teacher-portal.attendance')
                ->with('error', 'You do not teach this course.');
        }

        $marked = 0;
        foreach ($validated['attendances'] as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'course_id' => $validated['course_id'],
                    'attendance_date' => $validated['attendance_date'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'status' => $attendanceData['status'],
                    'notes' => $attendanceData['notes'] ?? null,
                ]
            );
            $marked++;
        }

        return redirect()->route('teacher-portal.attendance')
            ->with('success', "Attendance marked successfully for {$marked} student(s).");
    }

    public function getCourseStudents($courseId)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        // Verify teacher teaches this course
        if (!$teacher->courses()->where('courses.id', $courseId)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $course = Course::findOrFail($courseId);
        $students = $course->registeredStudents()
            ->where('status', 'active')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'student_number' => $student->student_number,
                ];
            });

        return response()->json(['students' => $students]);
    }

    public function settings()
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }
        
        return view('teacher-portal.settings', compact('teacher'));
    }

    public function changePassword(Request $request)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }

        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($teacher) {
                    if (!Hash::check($value, $teacher->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $teacher->update([
            'password' => $request->password,
        ]);

        return redirect()->route('teacher-portal.settings')
            ->with('success', 'Password changed successfully!');
    }

    public function uploadPhoto(Request $request)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }

        $request->validate([
            'photo' => ['required', 'image', 'max:2048', 'mimes:jpeg,jpg,png'], // 2MB max
        ]);

        // Delete old photo if exists
        if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
            Storage::disk('public')->delete($teacher->photo);
        }

        // Store new photo
        $photoPath = $request->file('photo')->store('teacher-photos', 'public');

        $teacher->update([
            'photo' => $photoPath,
        ]);

        return redirect()->route('teacher-portal.personal-info')
            ->with('success', 'Photo uploaded successfully!');
    }

    public function updatePersonalInfo(Request $request)
    {
        $teacherId = session('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('teacher.login')->with('error', 'Teacher not found.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $teacher->update($validated);

        return redirect()->route('teacher-portal.personal-info')
            ->with('success', 'Personal information updated successfully!');
    }
}

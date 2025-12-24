<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Http\Request;

class CourseRegistrationController extends Controller
{
    public function index()
    {
        // Group registrations by course
        $registrations = CourseRegistration::with(['student', 'course'])
            ->latest('registration_date')
            ->get();
        
        // Group by course
        $coursesGrouped = $registrations->groupBy('course_id')->map(function ($courseRegistrations) {
            $course = $courseRegistrations->first()->course;
            return [
                'course' => $course,
                'registrations' => $courseRegistrations,
                'student_count' => $courseRegistrations->unique('student_id')->count(),
            ];
        })->sortBy(function ($group) {
            return $group['course']->name;
        });
        
        return view('course-registrations.index', compact('coursesGrouped'));
    }

    public function create(Request $request)
    {
        $students = Student::where('status', 'active')->orderBy('first_name')->get();
        $courses = Course::where('status', 'active')->orderBy('name')->get();
        
        // Get current academic year
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $currentMonth = now()->format('F Y'); // e.g., "December 2024"
        $currentYear = now()->year;
        
        // Pre-select student if provided in query string
        $selectedStudentId = $request->get('student_id');
        
        return view('course-registrations.create', compact('students', 'courses', 'currentAcademicYear', 'currentMonth', 'currentYear', 'selectedStudentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['exists:courses,id'],
            'academic_year' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $registrations = [];
        $errors = [];
        $currentMonth = now()->format('F Y'); // e.g., "December 2024"
        $currentYear = now()->year;

        foreach ($validated['course_ids'] as $courseId) {
            // Check if student is already registered for this course in this month/year
            // This allows the same course to be registered in different months
            $existing = CourseRegistration::where('student_id', $validated['student_id'])
                ->where('course_id', $courseId)
                ->where('academic_year', $validated['academic_year'])
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->first();

            if ($existing) {
                $course = Course::find($courseId);
                $errors[] = "Student is already registered for {$course->name} in {$currentMonth} {$currentYear}";
                continue;
            }

            $registrations[] = CourseRegistration::create([
                'student_id' => $validated['student_id'],
                'course_id' => $courseId,
                'academic_year' => $validated['academic_year'],
                'month' => $currentMonth,
                'year' => $currentYear,
                'registration_date' => now(), // Always use current date
                'status' => 'registered',
                'notes' => $validated['notes'],
            ]);
        }

        if (!empty($errors) && empty($registrations)) {
            return back()->withErrors(['course_ids' => implode(', ', $errors)])->withInput();
        }

        $message = count($registrations) . ' course(s) registered successfully.';
        if (!empty($errors)) {
            $message .= ' Some courses were skipped: ' . implode(', ', $errors);
        }

        return redirect()->route('course-registrations.index')
            ->with('success', $message);
    }

    public function destroy(CourseRegistration $courseRegistration)
    {
        $courseRegistration->delete();
        return redirect()->route('course-registrations.index')
            ->with('success', 'Course registration removed successfully!');
    }

    private function getCurrentAcademicYear(): string
    {
        $year = now()->year;
        $month = now()->month;
        
        if ($month >= 9) {
            return $year . '/' . ($year + 1);
        } else {
            return ($year - 1) . '/' . $year;
        }
    }
}

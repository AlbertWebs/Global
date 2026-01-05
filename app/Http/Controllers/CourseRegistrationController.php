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
        $currentMonth = now()->format('F Y');
        $currentYear = now()->year;
        $currentAcademicYear = $this->getCurrentAcademicYear();

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['exists:courses,id',
                function ($attribute, $value, $fail) use ($request, $currentAcademicYear, $currentMonth, $currentYear) {
                    $studentId = $request->input('student_id');
                    if (!$studentId) {
                        $fail('A student must be selected before registering courses.');
                        return;
                    }

                    $existing = CourseRegistration::where('student_id', $studentId)
                        ->where('course_id', $value)
                        ->where('academic_year', $currentAcademicYear)
                        ->where('month', $currentMonth)
                        ->where('year', $currentYear)
                        ->first();

                    if ($existing) {
                        $course = Course::find($value);
                        $fail("Student is already registered for {$course->name} in {$currentMonth} {$currentYear}.");
                    }
                },
            ],
            'academic_year' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $registrations = [];
        foreach ($validated['course_ids'] as $courseId) {
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

        $message = count($registrations) . ' course(s) registered successfully.';

        return redirect()->route('course-registrations.index')
            ->with('success', $message);
    }

    public function destroy(CourseRegistration $courseRegistration)
    {
        $courseRegistration->delete();
        return redirect()->route('course-registrations.index')
            ->with('success', 'Course registration removed successfully!');
    }

    /**
     * Get registered courses for a specific student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegisteredCoursesForStudent(Request $request, $studentId)
    {
        $currentMonth = now()->format('F Y');
        $currentYear = now()->year;
        $currentAcademicYear = $this->getCurrentAcademicYear();

        $registeredCourseIds = CourseRegistration::where('student_id', $studentId)
            ->where('academic_year', $currentAcademicYear)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->pluck('course_id')
            ->toArray();

        return response()->json(['registered_course_ids' => $registeredCourseIds]);
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

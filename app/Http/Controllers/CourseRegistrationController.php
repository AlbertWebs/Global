<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksPermissions;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\Balance; // Import the Balance model
use Illuminate\Http\Request;

class CourseRegistrationController extends Controller
{
    use ChecksPermissions;

    public function index()
    {
        $this->requirePermission('course_registrations.view');
    {
        // Group registrations by course
        $registrations = CourseRegistration::with(['student', 'course'])
            ->latest('registration_date')
            ->get();
        
        // Group by course
        $coursesGrouped = $registrations->groupBy('course_id')->map(function ($courseRegistrations) {
            $course = $courseRegistrations->first()->course;
            
            // Skip if course is null (e.g., if the associated course was deleted)
            if (!$course) {
                return null;
            }

            return [
                'course' => $course,
                'registrations' => $courseRegistrations,
                'student_count' => $courseRegistrations->unique('student_id')->count(),
            ];
        })->filter()->sortBy(function ($group) {
            return $group['course']->name;
        });
        
        return view('course-registrations.index', compact('coursesGrouped'));
    }

    public function create(Request $request)
    {
        $this->requirePermission('course_registrations.create');
        $students = Student::where('status', 'active')->orderBy('first_name')->get();
        $courses = Course::where('status', 'active')->orderBy('name')->get();
        
        // Pre-select student if provided in query string
        $selectedStudentId = $request->get('student_id');
        
        return view('course-registrations.create', compact('students', 'courses', 'selectedStudentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['exists:courses,id',
                function ($attribute, $value, $fail) use ($request) {
                    $studentId = $request->input('student_id');
                    if (!$studentId) {
                        $fail('A student must be selected before registering courses.');
                        return;
                    }

                    $existing = CourseRegistration::where('student_id', $studentId)
                        ->where('course_id', $value)
                        ->first();

                    if ($existing) {
                        $course = Course::find($value);
                        $fail("Student is already registered for {$course->name}.");
                    }
                },
            ],
            'agreed_amounts' => ['required', 'array', 'min:1'],
            'agreed_amounts.*' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $registrations = [];
        // agreed_amounts comes as an associative array keyed by course_id
        $agreedAmounts = $validated['agreed_amounts'];

        foreach ($validated['course_ids'] as $courseId) {
            // Get the agreed amount for this course from the associative array
            $agreedAmount = $agreedAmounts[$courseId] ?? 0;
            
            if ($agreedAmount <= 0) {
                continue; // Skip if no agreed amount provided
            }
            
            $course = Course::findOrFail($courseId); // Retrieve the course to get its base_price
            
            $registrations[] = CourseRegistration::create([
                'student_id' => $validated['student_id'],
                'course_id' => $courseId,
                'registration_date' => now(), // Always use current date
                'agreed_amount' => $agreedAmount,
                'status' => 'registered',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create or update balance for the registered course
            Balance::updateOrCreate(
                ['student_id' => $validated['student_id'], 'course_id' => $courseId],
                [
                    'agreed_amount' => $agreedAmount,
                    'base_price' => $course->base_price,
                    'outstanding_balance' => $agreedAmount, // Initially, outstanding balance is the agreed amount
                    'total_paid' => 0,
                ]
            );
        }

        if (empty($registrations)) {
            return back()->withErrors(['course_ids' => 'Please select at least one course with a valid agreed amount.'])->withInput();
        }

        $message = count($registrations) . ' course(s) registered successfully.';

        return redirect()->route('course-registrations.index')
            ->with('success', $message);
    }

    public function destroy(CourseRegistration $courseRegistration)
    {
        $this->requirePermission('course_registrations.delete');
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

    public function getRegisteredCoursesForStudent($studentId)
    {
        $registeredCourses = CourseRegistration::where('student_id', $studentId)
            ->with('course') // Eager load the course relationship
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->course->id,
                    'name' => $registration->course->name,
                    'code' => $registration->course->code,
                ];
            });

        // Return both the full course list and just the IDs for compatibility
        return response()->json([
            'courses' => $registeredCourses,
            'registered_course_ids' => $registeredCourses->pluck('id')->toArray(),
        ]);
    }
}

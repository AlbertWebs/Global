<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksPermissions;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ChecksPermissions;

    public function index()
    {
        $this->requirePermission('courses.view');
        $courses = Course::latest()->paginate(20);
        $user = auth()->user();
        
        return view('courses.index', compact('courses', 'user'));
    }

    public function create()
    {
        $this->requirePermission('courses.create');
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $this->requirePermission('courses.create');
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $this->requirePermission('courses.view');
        $course->load('payments.student', 'payments.receipt');
        $user = auth()->user();
        return view('courses.show', compact('course', 'user'));
    }

    public function edit(Course $course)
    {
        $this->requirePermission('courses.edit');
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->requirePermission('courses.edit');
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code,' . $course->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $this->requirePermission('courses.delete');
        $course->delete();
        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully!');
    }
}

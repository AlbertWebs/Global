@extends('layouts.app')

@section('title', 'Course Registration')
@section('page-title', 'Course Registration')

@section('content')
<div class="max-w-4xl mx-auto" x-data="registrationForm()">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Register Student for Courses</h2>
            <p class="text-sm text-gray-600 mt-1">Create a new course registration</p>
        </div>
        <a href="{{ route('course-registrations.index') }}" class="px-4 py-2 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            View All Registrations
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">

        <form method="POST" action="{{ route('course-registrations.store') }}" @submit.prevent="validateForm">
            @csrf

            <!-- Student Selection -->
            <div class="mb-6">
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Select Student *</label>
                <select 
                    id="student_id" 
                    name="student_id" 
                    x-model="studentId"
                    @change="loadStudentInfo"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Choose a student...</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ isset($selectedStudentId) && $selectedStudentId == $student->id ? 'selected' : '' }}>
                            {{ $student->full_name }} ({{ $student->admission_number ?? $student->student_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Year (Hidden - Auto-set to current) -->
            <input 
                type="hidden" 
                id="academic_year" 
                name="academic_year" 
                value="{{ $currentAcademicYear }}"
            >

            <!-- Registration Info -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-gray-700">
                    <strong>Registration Date:</strong> {{ now()->format('F d, Y') }} (automatically set)
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    Students will be billed monthly for registered courses.
                </p>
            </div>

            <!-- Course Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Courses *</label>
                <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto">
                    @if($courses->count() > 0)
                        @foreach($courses as $course)
                        <label class="flex items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="course_ids[]" 
                                value="{{ $course->id }}"
                                x-bind:disabled="!studentId || isCourseRegistered({{ $course->id }})"
                                x-on:change="updateSelectedCourseCount()"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $course->name }}</p>
                                <p class="text-xs text-gray-500">{{ $course->code }}</p>
                            </div>
                            @if(auth()->user()->isSuperAdmin())
                            <span class="text-sm font-semibold text-gray-700">KES {{ number_format($course->base_price, 2) }}</span>
                            @endif
                        </label>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">No active courses available.</p>
                    @endif
                </div>
    <p class="mt-2 text-sm text-gray-500" x-show="!studentId">Please select a student first to enable course selection.</p>
                <p class="mt-2 text-sm text-red-600" x-show="formErrors.course_selection" x-text="formErrors.course_selection"></p>
                @error('course_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Additional notes about this registration..."
                ></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('course-registrations.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    x-bind:disabled="!studentId || loadingCourses"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Register Courses
                </button>
            </div>
        </form>
    </div>
</div>

<script>
        function registrationForm() {
            return {
                studentId: '{{ $selectedStudentId ?? '' }}',
                loadingCourses: false,
                registeredCourses: [],
                formErrors: {},

                // New reactive property for selected course count
                selectedCourseCount: 0,

                init() {
                    console.log('Alpine.js component initialized.');
                    this.loadStudentInfo(); // Always load student info on init to set initial state of courses
                },

                async loadStudentInfo() {
                    console.log('loadStudentInfo called. Student ID:', this.studentId);
                    this.registeredCourses = []; // Reset previously registered courses
                    this.formErrors = {}; // Reset form errors

                    // Reset all course checkboxes and enable them by default
                    document.querySelectorAll('input[name="course_ids[]"]').forEach(checkbox => {
                        checkbox.checked = false;
                        checkbox.disabled = false;
                        checkbox.closest('label').classList.remove('opacity-50', 'cursor-not-allowed', 'line-through');
                        checkbox.closest('label').querySelector('.registration-status')?.remove();
                    });
                    this.updateSelectedCourseCount(); // Update count after resetting checkboxes

                    if (!this.studentId) {
                        console.log('No student ID selected. Courses remain disabled.');
                        return;
                    }

                    this.loadingCourses = true;
                    console.log('Fetching registered courses for student:', this.studentId);
                    try {
                        const response = await fetch(`/api/students/${this.studentId}/registered-courses`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const data = await response.json();
                        this.registeredCourses = data.registered_course_ids;
                        console.log('Registered courses received:', this.registeredCourses);
                        this.updateCourseCheckboxes();
                    } catch (error) {
                        console.error('Error fetching registered courses:', error);
                        // Optionally, display an error message to the user
                    } finally {
                        this.loadingCourses = false;
                        console.log('Finished loading courses. loadingCourses:', this.loadingCourses);
                    }
                },

                updateCourseCheckboxes() {
                    console.log('Updating course checkboxes. Registered courses:', this.registeredCourses);
                    document.querySelectorAll('input[name="course_ids[]"]').forEach(checkbox => {
                        const courseId = parseInt(checkbox.value);
                        const label = checkbox.closest('label');

                        if (this.registeredCourses.includes(courseId)) {
                            checkbox.checked = false; // Ensure it's unchecked if already registered
                            checkbox.disabled = true; // Disable if already registered
                            label.classList.add('opacity-50', 'cursor-not-allowed', 'line-through');
                            // Add a status indicator if not already present
                            if (!label.querySelector('.registration-status')) {
                                const statusSpan = document.createElement('span');
                                statusSpan.classList.add('ml-auto', 'text-xs', 'text-red-500', 'registration-status');
                                statusSpan.textContent = 'Already Registered';
                                label.appendChild(statusSpan);
                            }
                        } else {
                            checkbox.disabled = false; // Enable if not registered
                            label.classList.remove('opacity-50', 'cursor-not-allowed', 'line-through');
                            label.querySelector('.registration-status')?.remove();
                        }
                    });
                    this.updateSelectedCourseCount(); // Update count after changing checkboxes
                },

                updateSelectedCourseCount() {
                    this.selectedCourseCount = Array.from(
                        document.querySelectorAll('input[name="course_ids[]"]:checked:not([disabled])')
                    ).length;
                    console.log('Selected course count updated:', this.selectedCourseCount);
                },

                isCourseRegistered(courseId) {
                    return this.registeredCourses.includes(courseId);
                },

                validateForm(event) {
                    console.log('validateForm called.');
                    this.formErrors = {};

                    if (!this.studentId) {
                        this.formErrors.student_id = 'Please select a student.';
                        console.log('Validation Error: No student selected.');
                    }

                    if (this.selectedCourseCount === 0) {
                        this.formErrors.course_selection = 'Please select at least one course.';
                        console.log('Validation Error: No course selected.');
                    }
                    console.log('Current formErrors:', this.formErrors);

                    if (Object.keys(this.formErrors).length > 0) {
                        console.log('Preventing form submission due0 to errors.');
                        event.preventDefault();
                    } else {
                        console.log('Validation successful. Explicitly submitting form.');
                        event.target.submit(); // Explicitly submit the form
                    }
                },

                get hasSelectedCourses() {
                    return this.selectedCourseCount > 0;
                }
            }
        }
</script>
@endsection


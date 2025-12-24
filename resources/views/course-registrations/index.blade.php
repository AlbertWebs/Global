@extends('layouts.app')

@section('title', 'Course Registrations')
@section('page-title', 'Course Registrations')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Course Registrations</h2>
        <p class="text-sm text-gray-600 mt-1">View students grouped by course</p>
    </div>
    <a href="{{ route('course-registrations.create') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Register Courses
    </a>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
    <p class="text-green-800 font-semibold">{{ session('success') }}</p>
</div>
@endif

<div x-data="{ expandedCourses: [] }">
    @if($coursesGrouped->count() > 0)
    <div class="space-y-4">
        @foreach($coursesGrouped as $courseId => $group)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
            <!-- Course Header (Clickable) -->
            <button 
                @click="expandedCourses.includes({{ $courseId }}) ? expandedCourses = expandedCourses.filter(id => id !== {{ $courseId }}) : expandedCourses.push({{ $courseId }})"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors"
            >
                <div class="flex items-center flex-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="text-left flex-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $group['course']->name }}</h3>
                        <div class="flex items-center gap-4 mt-1">
                            <p class="text-sm text-gray-600">{{ $group['course']->code }}</p>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $group['student_count'] }} {{ $group['student_count'] === 1 ? 'student' : 'students' }}
                            </span>
                            @if(auth()->user()->isSuperAdmin())
                            <span class="text-sm text-gray-600">
                                Base Price: <span class="font-semibold">KES {{ number_format($group['course']->base_price, 2) }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="ml-4 flex items-center">
                    <svg 
                        class="w-6 h-6 text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-180': expandedCourses.includes({{ $courseId }}) }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>

            <!-- Students List (Expandable) -->
            <div 
                x-show="expandedCourses.includes({{ $courseId }})"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="border-t border-gray-200"
                style="display: none;"
            >
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                            Registered Students ({{ $group['registrations']->count() }})
                        </h4>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admission #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Academic Year</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($group['registrations'] as $registration)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                                <span class="text-xs font-semibold text-gray-600">
                                                    {{ strtoupper(substr($registration->student->first_name, 0, 1) . substr($registration->student->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $registration->student->full_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $registration->student->email ?? 'No email' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ $registration->student->admission_number ?? $registration->student->student_number }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $registration->academic_year }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $registration->registration_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($registration->status === 'registered')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Registered</span>
                                        @elseif($registration->status === 'completed')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Completed</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Dropped</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">
                                            <a 
                                                href="{{ route('students.show', $registration->student->id) }}" 
                                                class="text-blue-600 hover:text-blue-900 transition-colors" 
                                                title="View Student"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <form 
                                                action="{{ route('course-registrations.destroy', $registration->id) }}" 
                                                method="POST" 
                                                class="inline" 
                                                onsubmit="return confirm('Are you sure you want to remove this registration?')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition-colors" 
                                                    title="Remove Registration"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <p class="text-gray-500 text-lg mb-2">No course registrations found.</p>
        <p class="text-gray-400 text-sm mb-6">Start by registering students for courses.</p>
        <a href="{{ route('course-registrations.create') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Register First Course
        </a>
    </div>
    @endif
</div>
@endsection

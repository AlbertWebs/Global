@extends('teacher-portal.layout')

@section('title', 'My Courses')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Courses</h1>
        <p class="text-gray-600">Manage and view all courses you teach</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $course->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $course->code }}</p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
            </div>
            @if($course->description)
            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($course->description, 100) }}</p>
            @endif
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('teacher-portal.student-progress') }}?course={{ $course->id }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">View Students →</a>
                <span class="text-sm text-gray-500">{{ $course->registrations->count() }} students</span>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-500">No courses assigned yet</p>
        </div>
        @endforelse
    </div>
</div>
@endsection


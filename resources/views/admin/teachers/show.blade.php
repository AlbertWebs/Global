@extends('layouts.app')

@section('title', $teacher->full_name)
@section('page-title', 'Teacher Details')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Teacher Portal Login Credentials -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Teacher Portal Login Credentials
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Login URL:</p>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono text-gray-800 flex-1">{{ url('/teacher/login') }}</code>
                            <button 
                                onclick="copyToClipboard('{{ url('/teacher/login') }}')"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                title="Copy URL"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Employee Number (Username):</p>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono text-gray-800 flex-1">{{ $teacher->employee_number }}</code>
                            <button 
                                onclick="copyToClipboard('{{ $teacher->employee_number }}')"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                title="Copy Employee Number"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Password:</p>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono text-gray-800 flex-1">{{ $teacher->employee_number }}</code>
                            <button 
                                onclick="copyToClipboard('{{ $teacher->employee_number }}')"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                title="Copy Password"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Quick Login:</p>
                        <a 
                            href="{{ route('teacher.login') }}" 
                            target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Open Teacher Portal
                        </a>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-blue-800">
                        <strong>Note:</strong> The default password is the employee number. Teachers should change their password after first login for security.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Information -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $teacher->full_name }}</h2>
            <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Edit Teacher
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Employee Number</p>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->employee_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Email</p>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Phone</p>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Status</p>
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : ($teacher->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst(str_replace('_', ' ', $teacher->status)) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Gender</p>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($teacher->gender) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Date of Birth</p>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->date_of_birth->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Hire Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->hire_date->format('M d, Y') }}</p>
            </div>
            @if($teacher->qualification)
            <div>
                <p class="text-sm text-gray-600 mb-1">Qualification</p>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->qualification }}</p>
            </div>
            @endif
            @if($teacher->specialization)
            <div>
                <p class="text-sm text-gray-600 mb-1">Specialization</p>
                <p class="text-lg font-semibold text-gray-900">{{ $teacher->specialization }}</p>
            </div>
            @endif
        </div>

        @if($teacher->courses->count() > 0)
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Courses</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teacher->courses as $course)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="font-semibold text-gray-900">{{ $course->name }}</p>
                    <p class="text-sm text-gray-600">{{ $course->code }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.teachers.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Back to Teachers
        </a>
        <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Edit Teacher
        </a>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
        toast.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Copied to clipboard!</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy:', err);
        alert('Failed to copy to clipboard. Please copy manually.');
    });
}
</script>
@endsection


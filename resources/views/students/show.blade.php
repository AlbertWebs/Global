@extends('layouts.app')

@section('title', $student->full_name)
@section('page-title', 'Student Details')

@section('content')
<div x-data="studentDetailPage()" class="space-y-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    <div class="w-24 h-24 rounded-2xl overflow-hidden shadow-xl border-4 border-indigo-300 border-opacity-60 flex items-center justify-center bg-gradient-to-br from-indigo-700 to-purple-800">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold text-white drop-shadow-lg">
                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Student Info -->
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $student->full_name }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-white">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="font-medium">{{ $student->admission_number }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                </svg>
                                <span>{{ $student->student_number }}</span>
                            </div>
                            <span class="px-3 py-1 bg-gradient-to-r from-emerald-500 to-green-600 rounded-full text-sm font-semibold text-white border-2 border-emerald-300 border-opacity-60 shadow-md">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('course-registrations.create') }}?student_id={{ $student->id }}" class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg text-sm font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Register Course
                    </a>
                    <a href="{{ route('billing.index') }}?student_id={{ $student->id }}" class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md hover:shadow-lg text-sm font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Process Payment
                    </a>
                    <a href="{{ route('students.edit', $student->id) }}" class="px-3 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-lg hover:from-amber-600 hover:to-orange-700 transition-all shadow-md hover:shadow-lg text-sm font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <button 
                        onclick="sendWelcomeMessage({{ $student->id }})"
                        class="px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg text-sm font-medium flex items-center gap-1.5"
                        id="welcome-btn-{{ $student->id }}"
                    >
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span id="welcome-text-{{ $student->id }}">Send Welcome SMS</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Portal Login Credentials -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Student Portal Login Credentials
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Login URL:</p>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono text-gray-800 flex-1">{{ url('/student/login') }}</code>
                            <button 
                                onclick="copyToClipboard('{{ url('/student/login') }}')"
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
                        <p class="text-sm text-gray-600 mb-1">Student Number (Username):</p>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono text-gray-800 flex-1">{{ $student->student_number }}</code>
                            <button 
                                onclick="copyToClipboard('{{ $student->student_number }}')"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                title="Copy Student Number"
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
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono text-gray-800 flex-1">{{ $student->student_number }}</code>
                            <button 
                                onclick="copyToClipboard('{{ $student->student_number }}')"
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
                            href="{{ route('student.login') }}" 
                            target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Open Student Portal
                        </a>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-blue-800">
                        <strong>Note:</strong> The default password is the student number. Students should change their password after first login for security.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-700 bg-opacity-80 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold">{{ $totalPayments }}</span>
            </div>
            <p class="text-blue-100 text-sm font-medium">Total Payments</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-700 bg-opacity-80 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold">KES {{ number_format($totalPaid, 0) }}</span>
            </div>
            <p class="text-green-100 text-sm font-medium">Total Paid</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-700 bg-opacity-80 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold">KES {{ number_format($totalBalance, 0) }}</span>
            </div>
            <p class="text-orange-100 text-sm font-medium">Outstanding Balance</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-700 bg-opacity-80 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold">{{ $registeredCourses }}</span>
            </div>
            <p class="text-purple-100 text-sm font-medium">Registered Courses</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Overview
                </button>
                <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Payments
                </button>
                <button @click="activeTab = 'courses'" :class="activeTab === 'courses' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Courses
                </button>
                <button @click="activeTab = 'quick-payment'" :class="activeTab === 'quick-payment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Quick Payment
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">First Name</p>
                                <p class="font-semibold text-gray-900">{{ $student->first_name }}</p>
                            </div>
                            @if($student->middle_name)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Middle Name</p>
                                <p class="font-semibold text-gray-900">{{ $student->middle_name }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Last Name</p>
                                <p class="font-semibold text-gray-900">{{ $student->last_name }}</p>
                            </div>
                            @if($student->date_of_birth)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Date of Birth</p>
                                <p class="font-semibold text-gray-900">{{ $student->date_of_birth->format('M d, Y') }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Gender</p>
                                <p class="font-semibold text-gray-900">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Nationality</p>
                                <p class="font-semibold text-gray-900">{{ $student->nationality ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Level of Education</p>
                                <p class="font-semibold text-gray-900">{{ $student->level_of_education ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">ID/Passport</p>
                                <p class="font-semibold text-gray-900">{{ $student->id_passport_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Contact Information
                        </h3>
                        <div class="space-y-4">
                            @if($student->email)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $student->email }}" class="text-blue-600 hover:underline font-medium">{{ $student->email }}</a>
                            </div>
                            @endif
                            @if($student->phone)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:{{ $student->phone }}" class="text-blue-600 hover:underline font-medium">{{ $student->phone }}</a>
                            </div>
                            @endif
                            @if($student->address)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-gray-900 font-medium">{{ $student->address }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Guardian -->
                        @if($student->next_of_kin_name)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Guardian</h4>
                            <div class="space-y-2">
                                <p class="text-gray-900 font-medium">{{ $student->next_of_kin_name }}</p>
                                @if($student->next_of_kin_mobile)
                                <a href="tel:{{ $student->next_of_kin_mobile }}" class="text-blue-600 hover:underline text-sm">{{ $student->next_of_kin_mobile }}</a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Method Breakdown -->
                @if($paymentMethods->count() > 0)
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Methods</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($paymentMethods as $method => $data)
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                                <span class="text-xs text-gray-500">{{ $data['count'] }} payment(s)</span>
                            </div>
                            <p class="text-xl font-bold text-indigo-600">KES {{ number_format($data['total'], 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Outstanding Balances -->
                @if($courseBalances->count() > 0)
                <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-red-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Outstanding Balances by Course
                    </h3>
                    <div class="space-y-3">
                        @foreach($courseBalances as $balance)
                        <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-red-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $balance['course']->name }}</p>
                                    <p class="text-sm text-gray-500">Paid: KES {{ number_format($balance['paid'], 2) }} / KES {{ number_format($balance['agreed'], 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold text-red-600">KES {{ number_format($balance['balance'], 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $balance['payments_count'] }} payment(s)</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Payments Tab -->
            <div x-show="activeTab === 'payments'" class="space-y-6">
                @if($student->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount Paid</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($student->payments->sortByDesc('created_at') as $payment)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $payment->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $payment->course->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->course->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $payment->month }} {{ $payment->year }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-gray-900">KES {{ number_format($payment->amount_paid, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($payment->balance > 0)
                                    <span class="text-sm font-semibold text-orange-600">KES {{ number_format($payment->balance, 2) }}</span>
                                    @else
                                    <span class="text-sm font-semibold text-green-600">Paid</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($payment->receipt)
                                    <a href="{{ route('receipts.show', $payment->receipt->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                        View
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-xs">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 mb-4">No payment history found for this student.</p>
                    <button @click="activeTab = 'quick-payment'" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                        Process First Payment
                    </button>
                </div>
                @endif
            </div>

            <!-- Courses Tab -->
            <div x-show="activeTab === 'courses'">
                @if($student->courseRegistrations->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($student->courseRegistrations as $registration)
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-5 border border-purple-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $registration->course->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $registration->course->code }}</p>
                            </div>
                            @if($registration->status === 'registered')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @elseif($registration->status === 'completed')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Completed</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Dropped</span>
                            @endif
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Academic Year:</span>
                                <span class="font-semibold text-gray-900">{{ $registration->academic_year }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Registered:</span>
                                <span class="font-semibold text-gray-900">{{ $registration->registration_date->format('M d, Y') }}</span>
                            </div>
                            @if($registration->notes)
                            <div class="pt-2 border-t border-purple-200">
                                <p class="text-xs text-gray-600">{{ $registration->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-500 mb-4">No courses registered for this student.</p>
                    <a href="{{ route('course-registrations.create') }}?student_id={{ $student->id }}" class="inline-block px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">
                        Register Courses
                    </a>
                </div>
                @endif
            </div>

            <!-- Quick Payment Tab -->
            <div x-show="activeTab === 'quick-payment'" x-data="quickPaymentForm()">
                <form method="POST" action="{{ route('billing.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                            <select 
                                id="course_id" 
                                name="course_id" 
                                x-model="courseId"
                                @change="loadCourseInfo"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Select course...</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Billing Month *</label>
                            <select 
                                id="month" 
                                name="month" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                @foreach($months as $month)
                                    <option value="{{ $month }} {{ $currentYear }}" {{ $month === $currentMonthName ? 'selected' : '' }}>
                                        {{ $month }} {{ $currentYear }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="academic_year" value="{{ $currentAcademicYear }}">
                        <input type="hidden" name="year" value="{{ $currentYear }}">

                        <div>
                            <label for="agreed_amount" class="block text-sm font-medium text-gray-700 mb-2">Agreed Amount (KES) *</label>
                            <input 
                                type="number" 
                                id="agreed_amount" 
                                name="agreed_amount" 
                                x-model="agreedAmount"
                                @input="calculateBalance"
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                                placeholder="0.00"
                            >
                        </div>

                        <div>
                            <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-2">Amount Paid (KES) *</label>
                            <input 
                                type="number" 
                                id="amount_paid" 
                                name="amount_paid" 
                                x-model="amountPaid"
                                @input="calculateBalance"
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                                placeholder="0.00"
                            >
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                            <select 
                                id="payment_method" 
                                name="payment_method" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="cash">Cash</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                            <input 
                                type="text" 
                                id="notes" 
                                name="notes" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Additional notes..."
                            >
                        </div>
                    </div>

                    <div x-show="agreedAmount && amountPaid" class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Agreed Amount:</span>
                                <span class="text-xl font-bold text-gray-900" x-text="'KES ' + (agreedAmount ? parseFloat(agreedAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Amount Paid:</span>
                                <span class="text-xl font-bold text-green-600" x-text="'KES ' + (amountPaid ? parseFloat(amountPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00')"></span>
                            </div>
                            <div x-show="balance > 0" class="flex justify-between items-center pt-3 border-t-2 border-blue-200">
                                <span class="text-gray-700 font-medium">Outstanding Balance:</span>
                                <span class="text-xl font-bold text-orange-600" x-text="'KES ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                            </div>
                            <div x-show="balance === 0 && agreedAmount && amountPaid" class="flex justify-between items-center pt-3 border-t-2 border-green-200">
                                <span class="text-gray-700 font-medium">Status:</span>
                                <span class="text-xl font-bold text-green-600">✓ Fully Paid</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <button 
                            type="button" 
                            @click="activeTab = 'overview'"
                            class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-bold hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl"
                        >
                            Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function studentDetailPage() {
    return {
        activeTab: 'overview'
    }
}

function quickPaymentForm() {
    return {
        courseId: '',
        agreedAmount: '',
        amountPaid: '',
        balance: 0,
        courseInfo: null,
        
        async loadCourseInfo() {
            if (!this.courseId) {
                this.courseInfo = null;
                return;
            }
            
            try {
                const response = await fetch(`/billing/course/${this.courseId}`);
                const data = await response.json();
                this.courseInfo = data;
                
                @if(auth()->user()->isSuperAdmin())
                if (data.base_price && !this.agreedAmount) {
                    this.agreedAmount = parseFloat(data.base_price);
                    this.calculateBalance();
                }
                @endif
            } catch (error) {
                console.error('Error loading course info:', error);
            }
        },
        
        calculateBalance() {
            const agreed = parseFloat(this.agreedAmount) || 0;
            const paid = parseFloat(this.amountPaid) || 0;
            this.balance = Math.max(0, agreed - paid);
        }
    }
}

async function sendWelcomeMessage(studentId) {
    const btn = document.getElementById(`welcome-btn-${studentId}`);
    const text = document.getElementById(`welcome-text-${studentId}`);
    
    if (!btn || !text) return;
    
    // Disable button and show loading state
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    text.textContent = 'Sending...';
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }
        
        // Create a form and submit it via fetch
        const formData = new FormData();
        formData.append('_token', csrfToken);
        
        const url = `{{ url('/students') }}/${studentId}/send-welcome-sms`;
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            credentials: 'same-origin'
        });
        
        // Check if response is ok
        if (!response.ok) {
            // Try to get error message from response
            let errorMessage = 'Failed to send SMS';
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    errorMessage = 'Server returned an error. Status: ' + response.status;
                }
            } else {
                const text = await response.text();
                if (text.includes('<!DOCTYPE')) {
                    errorMessage = 'Server returned an error page (Status: ' + response.status + '). Please check the console.';
                    console.error('HTML Error Response:', text.substring(0, 500));
                } else {
                    errorMessage = text || errorMessage;
                }
            }
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        
        if (data.success) {
            text.textContent = 'Sent! ✓';
            btn.classList.remove('bg-green-500', 'bg-opacity-90');
            btn.classList.add('bg-green-600');
            
            // Show success message
            alert('Welcome SMS sent successfully!');
            
            // Reset after 3 seconds
            setTimeout(() => {
                text.textContent = 'Send Welcome SMS';
                btn.classList.remove('bg-green-600', 'opacity-50', 'cursor-not-allowed');
                btn.classList.add('bg-green-500', 'bg-opacity-90');
                btn.disabled = false;
            }, 3000);
        } else {
            throw new Error(data.message || 'Failed to send SMS');
        }
    } catch (error) {
        console.error('Error sending welcome SMS:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message
        });
        
        text.textContent = 'Failed';
        btn.classList.remove('bg-green-500', 'bg-opacity-90');
        btn.classList.add('bg-red-500', 'bg-opacity-90');
        
        const errorMsg = error.message || 'Network error. Please check your connection and try again.';
        alert('Failed to send welcome SMS: ' + errorMsg);
        
        // Reset after 3 seconds
        setTimeout(() => {
            text.textContent = 'Send Welcome SMS';
            btn.classList.remove('bg-red-500', 'bg-opacity-90', 'opacity-50', 'cursor-not-allowed');
            btn.classList.add('bg-green-500', 'bg-opacity-90');
            btn.disabled = false;
        }, 3000);
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show a temporary success message
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

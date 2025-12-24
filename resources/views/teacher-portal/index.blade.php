<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Portal - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="teacherPortal()">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <a href="{{ route('teacher-portal.index') }}" class="flex items-center space-x-3 flex-shrink-0">
                        @if(\App\Models\Setting::get('school_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('school_logo')) }}" alt="School Logo" class="h-10 w-auto object-contain">
                        @else
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ strtoupper(substr(\App\Models\Setting::get('school_name', 'GC'), 0, 2)) }}</span>
                            </div>
                        @endif
                        <h1 class="text-xl font-bold text-blue-600 hidden sm:block">Teacher Portal</h1>
                    </a>
                </div>

                <!-- Right Side: Profile Photo and Settings -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->name }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ $teacher->name }}</p>
                                <p class="text-xs text-gray-500">Teacher</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('teacher-portal.personal-info') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="{{ route('teacher-portal.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                <hr class="my-1">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex h-[calc(100vh-4rem)]">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 shadow-xl transform transition-transform duration-300 ease-in-out"
        >
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-blue-700">
                    <h2 class="text-white font-bold text-lg">Teacher Portal</h2>
                    <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a href="{{ route('teacher-portal.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.index') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.index') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('teacher-portal.personal-info') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.personal-info') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.personal-info') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Info
                    </a>
                    <a href="{{ route('teacher-portal.courses') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.courses') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.courses') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        My Courses
                    </a>
                    <a href="{{ route('teacher-portal.post-results') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.post-results') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.post-results') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Post Results
                        @if($pendingResults > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingResults }}</span>
                        @endif
                    </a>
                    <a href="{{ route('teacher-portal.communicate') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.communicate') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.communicate') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Communicate
                    </a>
                    <a href="{{ route('teacher-portal.student-progress') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.student-progress') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.student-progress') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Student Progress
                    </a>
                    <a href="{{ route('teacher-portal.attendance') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.attendance') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.attendance') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Attendance
                    </a>
                    <a href="{{ route('teacher-portal.settings') }}" class="flex items-center px-4 py-3 rounded-lg transition-all group {{ request()->routeIs('teacher-portal.settings') ? 'bg-white bg-opacity-20 shadow-lg text-gray-900 font-semibold' : 'text-white hover:bg-white hover:bg-opacity-20 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher-portal.settings') ? 'text-gray-900' : 'text-white group-hover:text-gray-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Welcome Message -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, {{ $teacher->name }}!</h1>
                    <p class="text-gray-600">Here's what's happening in your classes today.</p>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-800 bg-opacity-50 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold">{{ $courses->count() }}</span>
                        </div>
                        <p class="text-blue-100 text-sm font-medium">Active Courses</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-800 bg-opacity-50 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold">{{ $totalStudents }}</span>
                        </div>
                        <p class="text-green-100 text-sm font-medium">Total Students</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-800 bg-opacity-50 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold">{{ $pendingResults }}</span>
                        </div>
                        <p class="text-purple-100 text-sm font-medium">Pending Results</p>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-800 bg-opacity-50 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold">{{ $recentAnnouncements->count() }}</span>
                        </div>
                        <p class="text-orange-100 text-sm font-medium">Recent Announcements</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Recent Announcements -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Recent Announcements</h2>
                            <a href="{{ route('teacher-portal.communicate') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($recentAnnouncements as $announcement)
                            <div class="border-l-4 border-blue-500 pl-4 py-2 hover:bg-gray-50 rounded-r-lg transition-colors">
                                <h3 class="font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($announcement->message, 80) }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $announcement->created_at->diffForHumans() }}</p>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">No announcements yet</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- My Courses -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">My Courses</h2>
                            <a href="{{ route('teacher-portal.courses') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                        </div>
                        <div class="space-y-3">
                            @forelse($courses as $course)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $course->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $course->code }}</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">No courses assigned</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a href="{{ route('teacher-portal.post-results') }}" class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white hover:from-purple-600 hover:to-purple-700 transition-all transform hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold">Post Results</h3>
                        </div>
                        <p class="text-purple-100">Upload and manage student results</p>
                    </a>

                    <a href="{{ route('teacher-portal.communicate') }}" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold">Communicate</h3>
                        </div>
                        <p class="text-blue-100">Send announcements to students</p>
                    </a>

                    <a href="{{ route('teacher-portal.student-progress') }}" class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white hover:from-green-600 hover:to-green-700 transition-all transform hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold">Student Progress</h3>
                        </div>
                        <p class="text-green-100">Track student performance</p>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Toggle -->
    <button @click="sidebarOpen = true" class="lg:hidden fixed bottom-4 right-4 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function teacherPortal() {
            return {
                sidebarOpen: false,
                darkMode: false,
                init() {
                    // Check for saved dark mode preference
                    if (localStorage.getItem('darkMode') === 'true') {
                        this.darkMode = true;
                        document.documentElement.classList.add('dark');
                    }
                },
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('darkMode', 'true');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('darkMode', 'false');
                    }
                }
            }
        }
    </script>
</body>
</html>


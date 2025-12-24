<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="teacherPortal()">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
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
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-2 sm:space-x-3 focus:outline-none">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs sm:text-sm shadow-lg overflow-hidden">
                                @if($teacher->photo ?? null)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->name ?? 'Teacher' }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    {{ strtoupper(substr($teacher->name ?? 'T', 0, 1)) }}
                                @endif
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ $teacher->name ?? 'Teacher' }}</p>
                                <p class="text-xs text-gray-500">Teacher</p>
                            </div>
                            <svg class="hidden sm:block w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
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

    <div class="flex h-[calc(100vh-4rem)] pb-16 lg:pb-0">
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 shadow-xl transform transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between h-16 px-6 border-b border-blue-700">
                    <h2 class="text-white font-bold text-lg">Teacher Portal</h2>
                    <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
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
        <main class="flex-1 overflow-y-auto pb-20 lg:pb-0">
            @yield('content')
        </main>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
        <div class="flex items-center justify-around h-16">
            <a href="{{ route('teacher-portal.index') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('teacher-portal.index') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs font-medium">Dashboard</span>
            </a>
            <a href="{{ route('teacher-portal.personal-info') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('teacher-portal.personal-info') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-xs font-medium">Profile</span>
            </a>
            <a href="{{ route('teacher-portal.courses') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('teacher-portal.courses') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-xs font-medium">Courses</span>
            </a>
            <a href="{{ route('teacher-portal.post-results') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('teacher-portal.post-results') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-xs font-medium">Results</span>
            </a>
            <a href="{{ route('teacher-portal.settings') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('teacher-portal.settings') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-xs font-medium">Settings</span>
            </a>
        </div>
    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    <script>
        function teacherPortal() {
            return {
                sidebarOpen: false,
                darkMode: false,
                init() {
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


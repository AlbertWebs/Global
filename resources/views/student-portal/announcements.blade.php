<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Portal - Announcements</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="studentPortal()">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <a href="{{ route('student-portal.index') }}" class="flex items-center space-x-3 flex-shrink-0">
                        @if(\App\Models\Setting::get('school_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('school_logo')) }}" alt="School Logo" class="h-10 w-auto object-contain">
                        @else
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ strtoupper(substr(\App\Models\Setting::get('school_name', 'GC'), 0, 2)) }}</span>
                            </div>
                        @endif
                        <h1 class="text-xl font-bold text-blue-600 hidden sm:block">Student Portal</h1>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('student-portal.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student-portal.index') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('student-portal.financial-info') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student-portal.financial-info') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Financial Info
                    </a>
                    <a href="{{ route('student-portal.courses') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student-portal.courses') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Courses
                    </a>
                    <a href="{{ route('student-portal.announcements') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student-portal.announcements') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Announcements
                    </a>
                    <a href="{{ route('student-portal.results') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student-portal.results') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Results
                    </a>
                    <a href="{{ route('student-portal.settings') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student-portal.settings') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Settings
                    </a>
                </div>

                <!-- Right Side: Profile -->
                <div class="flex items-center space-x-4">
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ $student->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $student->student_number }}</p>
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
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('student-portal.settings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Settings
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('student-portal.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-5 h-5 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Announcements</h2>
            <p class="text-gray-600 mt-2">Stay updated with the latest news and announcements from your school.</p>
        </div>

        @if($announcements->count() > 0)
            <div class="space-y-6">
                @foreach($announcements as $announcement)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 {{ $announcement->priority === 'high' ? 'border-red-500' : ($announcement->priority === 'medium' ? 'border-yellow-500' : 'border-green-500') }}">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $announcement->title }}</h3>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $announcement->priority === 'high' ? 'bg-red-100 text-red-800' : ($announcement->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($announcement->priority) }} Priority
                                        </span>
                                    </div>
                                    <p class="text-gray-700 whitespace-pre-wrap mb-4">{{ $announcement->message }}</p>
                                    
                                    @if($announcement->hasAttachment())
                                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                @if($announcement->attachment_type === 'pdf')
                                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                @elseif($announcement->attachment_type === 'docx')
                                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $announcement->attachment_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ strtoupper($announcement->attachment_type) }} File</p>
                                                </div>
                                            </div>
                                            <a href="{{ $announcement->getAttachmentUrl() }}" download="{{ $announcement->attachment_name }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium flex items-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                                <span>Download</span>
                                            </a>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        @if($announcement->postedBy)
                                            <span>Posted by: {{ $announcement->postedBy->name }}</span>
                                        @endif
                                        <span>{{ $announcement->created_at->format('M d, Y') }}</span>
                                        <span>{{ $announcement->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Announcements</h3>
                <p class="text-gray-500">There are no announcements at this time. Check back later for updates.</p>
            </div>
        @endif
    </div>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
        <div class="flex items-center justify-around h-16">
            <a href="{{ route('student-portal.index') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('student-portal.index') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs font-medium">Dashboard</span>
            </a>
            <a href="{{ route('student-portal.financial-info') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('student-portal.financial-info') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-medium">Financial</span>
            </a>
            <a href="{{ route('student-portal.courses') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('student-portal.courses') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-xs font-medium">Courses</span>
            </a>
            <a href="{{ route('student-portal.announcements') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('student-portal.announcements') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="text-xs font-medium">Announcements</span>
            </a>
            <a href="{{ route('student-portal.settings') }}" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('student-portal.settings') ? 'text-blue-600' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-xs font-medium">Settings</span>
            </a>
        </div>
    </nav>

    <script>
        function studentPortal() {
            return {
                // Add any Alpine.js functionality here if needed
            }
        }
    </script>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Portal - Results</title>
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
            <h2 class="text-3xl font-bold text-gray-900">Academic Results</h2>
            <p class="text-gray-600 mt-2">View your published academic results and grades.</p>
        </div>

        @php
            $groupedResults = $results->groupBy(function($result) {
                return $result->academic_year . ' - ' . $result->term;
            });
        @endphp

        @if($results->count() > 0)
            @foreach($groupedResults as $period => $periodResults)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $period }}</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $periodResults->count() }} {{ Str::plural('result', $periodResults->count()) }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Posted</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($periodResults as $result)
                                    @php
                                        $grade = $result->grade ?? $result->calculateGrade();
                                        $gradeColor = match($grade) {
                                            'A' => 'bg-green-100 text-green-800',
                                            'B' => 'bg-blue-100 text-blue-800',
                                            'C' => 'bg-yellow-100 text-yellow-800',
                                            'D' => 'bg-orange-100 text-orange-800',
                                            'F' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $scoreColor = $result->score >= 70 ? 'text-green-600' : ($result->score >= 60 ? 'text-yellow-600' : 'text-red-600');
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $result->course->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $result->course->code ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $result->exam_type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-semibold {{ $scoreColor }}">
                                                {{ number_format($result->score, 2) }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $gradeColor }}">
                                                {{ $grade }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $result->remarks ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $result->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <!-- Summary Card -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-xl font-semibold mb-4">Overall Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Total Results</p>
                        <p class="text-3xl font-bold mt-1">{{ $results->count() }}</p>
                    </div>
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Average Score</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($results->avg('score'), 2) }}%</p>
                    </div>
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Courses</p>
                        <p class="text-3xl font-bold mt-1">{{ $results->pluck('course_id')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Results Available</h3>
                <p class="text-gray-500">Your results will appear here once they are published by your teachers.</p>
            </div>
        @endif
    </div>

    <script>
        function studentPortal() {
            return {
                // Add any Alpine.js functionality here if needed
            }
        }
    </script>
</body>
</html>


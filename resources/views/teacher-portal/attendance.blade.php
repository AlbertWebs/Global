@extends('teacher-portal.layout')

@section('title', 'Attendance')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="attendance()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Attendance Management</h1>
        <p class="text-gray-600">Track and manage student attendance</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
        <p class="text-red-800 font-semibold">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Mark Attendance Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Mark Attendance</h2>
        <form method="POST" action="{{ route('teacher-portal.mark-attendance') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Select Course *</label>
                    <select id="course_id" name="course_id" x-model="selectedCourse" @change="loadStudents" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Course...</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="attendance_date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                    <input type="date" id="attendance_date" name="attendance_date" x-model="selectedDate" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Students List -->
            <div x-show="selectedCourse && students.length > 0" x-transition class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Students</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <template x-for="(student, index) in students" :key="student.id">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900" x-text="student.full_name"></div>
                                <div class="text-sm text-gray-500" x-text="student.student_number"></div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <select 
                                    :name="'attendances[' + index + '][status]'"
                                    x-model="student.status"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                </select>
                                <input 
                                    type="hidden" 
                                    :name="'attendances[' + index + '][student_id]'"
                                    :value="student.id"
                                >
                                <input 
                                    type="text" 
                                    :name="'attendances[' + index + '][notes]'"
                                    x-model="student.notes"
                                    placeholder="Notes (optional)"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm w-48"
                                >
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg font-bold hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl">
                        Mark Attendance
                    </button>
                </div>
            </div>
            <div x-show="selectedCourse && students.length === 0" class="text-center py-8 text-gray-500">
                <p>No students registered for this course.</p>
            </div>
        </form>
    </div>

    <!-- Attendance Records -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Attendance Records</h2>
            <div class="flex items-center space-x-4">
                <input type="text" x-model="searchQuery" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <select x-model="filterCourse" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($attendances->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $attendance->attendance_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $attendance->student->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $attendance->student->student_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $attendance->course->name }}</div>
                            <div class="text-sm text-gray-500">{{ $attendance->course->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ 
                                $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                ($attendance->status === 'absent' ? 'bg-red-100 text-red-800' : 
                                ($attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                'bg-blue-100 text-blue-800')) 
                            }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $attendance->notes ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $attendances->links() }}
        </div>
        @else
        <div class="text-center py-12 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <p>No attendance records found. Mark attendance for a course to get started.</p>
        </div>
        @endif
    </div>
</div>

<script>
function attendance() {
    return {
        selectedCourse: '',
        selectedDate: new Date().toISOString().split('T')[0],
        students: [],
        searchQuery: '',
        filterCourse: '',
        
        async loadStudents() {
            if (!this.selectedCourse) {
                this.students = [];
                return;
            }
            
            try {
                const response = await fetch(`/teacher-portal/courses/${this.selectedCourse}/students`);
                const data = await response.json();
                this.students = data.students.map(student => ({
                    ...student,
                    status: 'present',
                    notes: ''
                }));
            } catch (error) {
                console.error('Error loading students:', error);
                this.students = [];
            }
        }
    }
}
</script>
@endsection


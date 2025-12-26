@extends('teacher-portal.layout')

@section('title', 'Edit Announcement')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="editAnnouncement()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Announcement</h1>
        <p class="text-gray-600">Update your announcement details</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="POST" action="{{ route('teacher-portal.update-announcement', $announcement->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" id="title" name="title" required x-model="title" value="{{ old('title', $announcement->title) }}" placeholder="Enter announcement title..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                <textarea id="message" name="message" required x-model="message" rows="6" placeholder="Enter your message..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('message', $announcement->message) }}</textarea>
                <p class="mt-2 text-sm text-gray-500">Characters: <span x-text="message.length"></span></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Target Audience *</label>
                    <select id="target_audience" name="target_audience" required x-model="target_audience" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ old('target_audience', $announcement->target_audience) == 'all' ? 'selected' : '' }}>All (Students & Parents)</option>
                        <option value="students" {{ old('target_audience', $announcement->target_audience) == 'students' ? 'selected' : '' }}>Students Only</option>
                        <option value="parents" {{ old('target_audience', $announcement->target_audience) == 'parents' ? 'selected' : '' }}>Parents Only</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                    <select id="priority" name="priority" required x-model="priority" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="low" {{ old('priority', $announcement->priority) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $announcement->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $announcement->priority) == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
            </div>

            <!-- File Attachment -->
            <div>
                <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Attachment (Optional)</label>
                @if($announcement->hasAttachment())
                <div class="mb-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if($announcement->attachment_type === 'pdf')
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            @elseif($announcement->attachment_type === 'docx')
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $announcement->attachment_name }}</p>
                                <p class="text-xs text-gray-500">Current attachment</p>
                            </div>
                        </div>
                        <a href="{{ $announcement->getAttachmentUrl() }}" target="_blank" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">View</a>
                    </div>
                </div>
                @endif
                <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-500">Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB). Leave empty to keep current attachment.</p>
            </div>

            <!-- Course Targeting -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Specific Courses (Optional)</label>
                <div class="relative" x-data="{ courseSearch: '', showCourseDropdown: false }">
                    <input 
                        type="text" 
                        x-model="courseSearch" 
                        @focus="showCourseDropdown = true"
                        @click.away="showCourseDropdown = false"
                        placeholder="Search courses by code or name..."
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <div x-show="showCourseDropdown" x-cloak class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <div class="p-2">
                            @foreach($courses as $course)
                                <label 
                                    class="flex items-center px-3 py-2 hover:bg-blue-50 cursor-pointer rounded"
                                    x-show="!courseSearch || ('{{ strtolower($course->code . ' ' . $course->name) }}').includes(courseSearch.toLowerCase())"
                                >
                                    <input 
                                        type="checkbox" 
                                        name="target_courses[]" 
                                        value="{{ $course->id }}"
                                        x-model="target_courses"
                                        class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        {{ in_array($course->id, old('target_courses', $announcement->target_courses ?? [])) ? 'checked' : '' }}
                                    >
                                    <span>
                                        <span class="font-medium text-gray-900">{{ $course->code }}</span> - <span class="text-gray-700">{{ $course->name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex flex-wrap gap-2" x-show="target_courses && target_courses.length > 0">
                    <template x-for="courseId in target_courses" :key="courseId">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                            <span x-text="getCourseName(courseId)"></span>
                            <button type="button" @click="target_courses = target_courses.filter(id => id != courseId)" class="ml-2 text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    </template>
                </div>
                <p class="mt-1 text-xs text-gray-500">Search and select courses. Leave empty to target all courses.</p>
            </div>

            <!-- Student Group Targeting -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Specific Students (Optional)</label>
                <div class="relative" x-data="{ studentSearch: '', showStudentDropdown: false }">
                    <input 
                        type="text" 
                        x-model="studentSearch" 
                        @focus="showStudentDropdown = true"
                        @click.away="showStudentDropdown = false"
                        placeholder="Search students by number or name..."
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <div x-show="showStudentDropdown" x-cloak class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <div class="p-2">
                            @foreach($students as $student)
                                <label 
                                    class="flex items-center px-3 py-2 hover:bg-blue-50 cursor-pointer rounded"
                                    x-show="!studentSearch || ('{{ strtolower($student->student_number . ' ' . $student->full_name) }}').includes(studentSearch.toLowerCase())"
                                >
                                    <input 
                                        type="checkbox" 
                                        name="target_student_groups[]" 
                                        value="{{ $student->id }}"
                                        x-model="target_student_groups"
                                        class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        {{ in_array($student->id, old('target_student_groups', $announcement->target_student_groups ?? [])) ? 'checked' : '' }}
                                    >
                                    <span>
                                        <span class="font-medium text-gray-900">{{ $student->student_number }}</span> - <span class="text-gray-700">{{ $student->full_name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex flex-wrap gap-2" x-show="target_student_groups && target_student_groups.length > 0">
                    <template x-for="studentId in target_student_groups" :key="studentId">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                            <span x-text="getStudentName(studentId)"></span>
                            <button type="button" @click="target_student_groups = target_student_groups.filter(id => id != studentId)" class="ml-2 text-purple-600 hover:text-purple-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    </template>
                </div>
                <p class="mt-1 text-xs text-gray-500">Search and select students. Leave empty to target all students.</p>
            </div>

            <!-- Preview -->
            <div x-show="title && message" x-transition class="p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Preview:</h3>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h4 class="font-bold text-lg text-gray-900 mb-2" x-text="title"></h4>
                    <p class="text-gray-700 whitespace-pre-wrap" x-text="message"></p>
                    <div class="mt-4 flex items-center space-x-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                            'bg-red-100 text-red-800': priority === 'high',
                            'bg-yellow-100 text-yellow-800': priority === 'medium',
                            'bg-green-100 text-green-800': priority === 'low'
                        }" x-text="priority.charAt(0).toUpperCase() + priority.slice(1)"></span>
                        <span class="text-xs text-gray-500" x-text="'To: ' + (target_audience === 'all' ? 'All' : target_audience.charAt(0).toUpperCase() + target_audience.slice(1))"></span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('teacher-portal.communicate') }}" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                    Update Announcement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editAnnouncement() {
    return {
        title: '{{ $announcement->title }}',
        message: '{{ $announcement->message }}',
        target_audience: '{{ $announcement->target_audience }}',
        priority: '{{ $announcement->priority }}',
        target_courses: @json(old('target_courses', $announcement->target_courses ?? [])),
        target_student_groups: @json(old('target_student_groups', $announcement->target_student_groups ?? [])),
        
        // Course data for lookup
        courses: {
            @foreach($courses as $course)
            {{ $course->id }}: { code: '{{ $course->code }}', name: '{{ $course->name }}' },
            @endforeach
        },
        
        // Student data for lookup
        students: {
            @foreach($students as $student)
            {{ $student->id }}: { number: '{{ $student->student_number }}', name: '{{ $student->full_name }}' },
            @endforeach
        },
        
        getCourseName(courseId) {
            const course = this.courses[courseId];
            return course ? `${course.code} - ${course.name}` : '';
        },
        
        getStudentName(studentId) {
            const student = this.students[studentId];
            return student ? `${student.number} - ${student.name}` : '';
        }
    }
}
</script>
@endsection


@extends('teacher-portal.layout')

@section('title', 'Edit Result')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="editResult()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Student Result</h1>
        <p class="text-gray-600">Update student examination result</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="POST" action="{{ route('teacher-portal.update-result', $result->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Student *</label>
                    <div class="relative" x-data="{ studentSearch: '', showStudentDropdown: false }">
                        <input 
                            type="text" 
                            x-model="studentSearch" 
                            @focus="showStudentDropdown = true"
                            @click.away="showStudentDropdown = false"
                            @input="showStudentDropdown = true"
                            placeholder="Search student by name or number..."
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :value="selectedStudentName"
                            readonly
                        >
                        <input type="hidden" name="student_id" x-model="selectedStudent" required>
                        <div x-show="showStudentDropdown" x-cloak class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div class="p-2">
                                @foreach($students as $student)
                                    <div 
                                        class="px-3 py-2 hover:bg-blue-50 cursor-pointer rounded"
                                        x-show="!studentSearch || ('{{ strtolower($student->full_name . ' ' . $student->student_number) }}').includes(studentSearch.toLowerCase())"
                                        @click="selectedStudent = '{{ $student->id }}'; selectedStudentName = '{{ $student->full_name }} ({{ $student->student_number }})'; studentSearch = ''; showStudentDropdown = false;"
                                    >
                                        <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $student->student_number }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div x-show="selectedStudent" class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                            <span x-text="selectedStudentName"></span>
                            <button type="button" @click="selectedStudent = ''; selectedStudentName = '';" class="ml-2 text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    </div>
                </div>

                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                    <select id="course_id" name="course_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Course...</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $result->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }} ({{ $course->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $result->academic_year) }}" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700 mb-2">Term *</label>
                    <select id="term" name="term" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Term...</option>
                        <option value="Term 1" {{ old('term', $result->term) == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                        <option value="Term 2" {{ old('term', $result->term) == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                        <option value="Term 3" {{ old('term', $result->term) == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                        <option value="Term 4" {{ old('term', $result->term) == 'Term 4' ? 'selected' : '' }}>Term 4</option>
                    </select>
                </div>

                <div>
                    <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">Exam Type *</label>
                    <select id="exam_type" name="exam_type" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Exam Type...</option>
                        <option value="Assignment" {{ old('exam_type', $result->exam_type) == 'Assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="Quiz" {{ old('exam_type', $result->exam_type) == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="Midterm" {{ old('exam_type', $result->exam_type) == 'Midterm' ? 'selected' : '' }}>Midterm</option>
                        <option value="Final" {{ old('exam_type', $result->exam_type) == 'Final' ? 'selected' : '' }}>Final Exam</option>
                        <option value="Project" {{ old('exam_type', $result->exam_type) == 'Project' ? 'selected' : '' }}>Project</option>
                    </select>
                </div>

                <div>
                    <label for="score" class="block text-sm font-medium text-gray-700 mb-2">Score (0-100) *</label>
                    <input type="number" id="score" name="score" x-model="score" @input="calculateGrade" min="0" max="100" step="0.01" value="{{ old('score', $result->score) }}" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold">
                </div>

                <div>
                    <label for="grade" class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                    <input type="text" id="grade" name="grade" x-model="grade" readonly class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 text-lg font-bold">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" {{ old('status', $result->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="published" {{ old('status', $result->status) == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status', $result->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="3" placeholder="Additional comments..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('remarks', $result->remarks) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('teacher-portal.post-results') }}" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                    Update Result
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editResult() {
    return {
        selectedStudent: '{{ $result->student_id }}',
        selectedStudentName: '{{ $result->student->full_name }} ({{ $result->student->student_number }})',
        score: {{ $result->score }},
        grade: '{{ $result->grade ?? $result->calculateGrade() }}',
        calculateGrade() {
            const score = parseFloat(this.score) || 0;
            if (score >= 90) this.grade = 'A';
            else if (score >= 80) this.grade = 'B';
            else if (score >= 70) this.grade = 'C';
            else if (score >= 60) this.grade = 'D';
            else this.grade = 'F';
        }
    }
}
</script>
@endsection


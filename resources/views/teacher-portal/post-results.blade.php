@extends('teacher-portal.layout')

@section('title', 'Post Results')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="postResults()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Post Student Results</h1>
        <p class="text-gray-600">Upload and manage student examination results</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex -mb-px space-x-4">
            <button @click="activeTab = 'post'" :class="activeTab === 'post' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
                Post New Result
            </button>
            <button @click="activeTab = 'view'" :class="activeTab === 'view' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
                View Results
            </button>
        </nav>
    </div>

    <!-- Post New Result Form -->
    <div x-show="activeTab === 'post'" x-transition class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Post New Result</h2>
        <form method="POST" action="{{ route('teacher-portal.store-result') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student *</label>
                    <select id="student_id" name="student_id" required x-model="selectedStudent" @change="loadStudentCourses" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Student...</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->student_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                    <select id="course_id" name="course_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Course...</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ now()->year }}/{{ now()->year + 1 }}" required placeholder="e.g., 2024/2025" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700 mb-2">Term *</label>
                    <select id="term" name="term" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Term...</option>
                        <option value="Term 1">Term 1</option>
                        <option value="Term 2">Term 2</option>
                        <option value="Term 3">Term 3</option>
                        <option value="Term 4">Term 4</option>
                    </select>
                </div>

                <div>
                    <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">Exam Type *</label>
                    <select id="exam_type" name="exam_type" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Exam Type...</option>
                        <option value="Assignment">Assignment</option>
                        <option value="Quiz">Quiz</option>
                        <option value="Midterm">Midterm</option>
                        <option value="Final">Final Exam</option>
                        <option value="Project">Project</option>
                    </select>
                </div>

                <div>
                    <label for="score" class="block text-sm font-medium text-gray-700 mb-2">Score (0-100) *</label>
                    <input type="number" id="score" name="score" x-model="score" @input="calculateGrade" min="0" max="100" step="0.01" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold">
                </div>

                <div>
                    <label for="grade" class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                    <input type="text" id="grade" name="grade" x-model="grade" readonly class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-50 text-lg font-bold">
                </div>

                <div class="md:col-span-2">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="3" placeholder="Additional comments..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" @click="resetForm" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors">
                    Reset
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                    Post Result
                </button>
            </div>
        </form>
    </div>

    <!-- View Results -->
    <div x-show="activeTab === 'view'" x-transition class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">All Results</h2>
            <div class="flex items-center space-x-4">
                <input type="text" x-model="searchQuery" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <select x-model="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($results as $result)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $result->student->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $result->student->student_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $result->course->name }}</div>
                            <div class="text-sm text-gray-500">{{ $result->course->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $result->exam_type }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ number_format($result->score, 2) }}%</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $result->grade === 'A' ? 'bg-green-100 text-green-800' : ($result->grade === 'B' ? 'bg-blue-100 text-blue-800' : ($result->grade === 'C' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">{{ $result->grade ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $result->status === 'published' ? 'bg-green-100 text-green-800' : ($result->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">{{ ucfirst($result->status) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900">Edit</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No results found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function postResults() {
    return {
        activeTab: 'post',
        selectedStudent: '',
        score: '',
        grade: '',
        searchQuery: '',
        filterStatus: '',
        calculateGrade() {
            const score = parseFloat(this.score) || 0;
            if (score >= 90) this.grade = 'A';
            else if (score >= 80) this.grade = 'B';
            else if (score >= 70) this.grade = 'C';
            else if (score >= 60) this.grade = 'D';
            else this.grade = 'F';
        },
        loadStudentCourses() {
            // Load courses for selected student
            console.log('Loading courses for student:', this.selectedStudent);
        },
        resetForm() {
            this.selectedStudent = '';
            this.score = '';
            this.grade = '';
        }
    }
}
</script>
@endsection


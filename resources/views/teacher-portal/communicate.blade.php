@extends('teacher-portal.layout')

@section('title', 'Communicate with Students')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="communicate()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Communicate with Students</h1>
        <p class="text-gray-600">Send announcements and messages to students and parents</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex -mb-px space-x-4">
            <button @click="activeTab = 'create'" :class="activeTab === 'create' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
                Create Announcement
            </button>
            <button @click="activeTab = 'view'" :class="activeTab === 'view' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
                View Announcements
            </button>
        </nav>
    </div>

    <!-- Create Announcement Form -->
    <div x-show="activeTab === 'create'" x-transition class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Create New Announcement</h2>
        <form method="POST" action="{{ route('teacher-portal.store-announcement') }}" class="space-y-6">
            @csrf
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" id="title" name="title" required x-model="title" placeholder="Enter announcement title..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                <textarea id="message" name="message" required x-model="message" rows="6" placeholder="Enter your message..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                <p class="mt-2 text-sm text-gray-500">Characters: <span x-text="message.length"></span></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Target Audience *</label>
                    <select id="target_audience" name="target_audience" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All (Students & Parents)</option>
                        <option value="students">Students Only</option>
                        <option value="parents">Parents Only</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                    <select id="priority" name="priority" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
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
                <button type="button" @click="resetForm" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors">
                    Reset
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                    Post Announcement
                </button>
            </div>
        </form>
    </div>

    <!-- View Announcements -->
    <div x-show="activeTab === 'view'" x-transition class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">All Announcements</h2>
            <div class="flex items-center space-x-4">
                <input type="text" x-model="searchQuery" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <select x-model="filterPriority" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Priorities</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($announcements as $announcement)
            <div class="border-l-4 rounded-lg p-6 hover:shadow-md transition-shadow" :class="{
                'border-red-500 bg-red-50': '{{ $announcement->priority }}' === 'high',
                'border-yellow-500 bg-yellow-50': '{{ $announcement->priority }}' === 'medium',
                'border-green-500 bg-green-50': '{{ $announcement->priority }}' === 'low'
            }">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ $announcement->title }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                                'bg-red-100 text-red-800': '{{ $announcement->priority }}' === 'high',
                                'bg-yellow-100 text-yellow-800': '{{ $announcement->priority }}' === 'medium',
                                'bg-green-100 text-green-800': '{{ $announcement->priority }}' === 'low'
                            }">{{ ucfirst($announcement->priority) }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($announcement->target_audience) }}</span>
                        </div>
                        <p class="text-gray-700 mb-4 whitespace-pre-wrap">{{ $announcement->message }}</p>
                        @if($announcement->status === 'draft')
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                        @elseif($announcement->status === 'archived')
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Archived</span>
                        @endif
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>Posted {{ $announcement->created_at->diffForHumans() }}</span>
                            @if($announcement->postedBy)
                            <span>by {{ $announcement->postedBy->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <button class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-gray-500">No announcements yet. Create your first one!</p>
            </div>
            @endforelse
        </div>

        @if($announcements->hasPages())
        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function communicate() {
    return {
        activeTab: 'create',
        title: '',
        message: '',
        target_audience: 'all',
        priority: 'medium',
        searchQuery: '',
        filterPriority: '',
        resetForm() {
            this.title = '';
            this.message = '';
            this.target_audience = 'all';
            this.priority = 'medium';
        }
    }
}
</script>
@endsection


@extends('layouts.app')

@section('title', 'Courses')
@section('page-title', 'Courses')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-900">All Courses</h2>
    @if(auth()->user()->isSuperAdmin())
    <a href="{{ route('courses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Add Course
    </a>
    @endif
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($courses->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    @if($user->isSuperAdmin())
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base Price</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($courses as $course)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $course->code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $course->name }}</td>
                    @if($user->isSuperAdmin())
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">KES {{ number_format($course->base_price, 2) }}</td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('courses.show', $course->id) }}" class="text-blue-600 hover:text-blue-900 mr-4">View</a>
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('courses.edit', $course->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $courses->links() }}
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <p class="text-gray-500">No courses found.</p>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('courses.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Add your first course</a>
        @endif
    </div>
    @endif
</div>
@endsection


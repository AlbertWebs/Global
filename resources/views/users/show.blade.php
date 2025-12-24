@extends('layouts.app')

@section('title', $user->name)
@section('page-title', 'User Details')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">User Information</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600 mb-1">Name</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Email</p>
                <p class="text-lg text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Role</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $user->role->slug === 'super-admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $user->role->name }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Created At</p>
                <p class="text-lg text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
            </div>
        </div>
        <div class="mt-6 flex space-x-4">
            <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Back</a>
        </div>
    </div>
</div>
@endsection


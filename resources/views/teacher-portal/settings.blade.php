@extends('teacher-portal.layout')

@section('title', 'Settings')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Settings</h1>
        <p class="text-gray-600">Manage your account settings and preferences</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <p class="text-green-800 font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <ul class="list-disc list-inside text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Change Password</h2>
        <form method="POST" action="{{ route('teacher-portal.change-password') }}" class="space-y-4">
            @csrf
            
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password" 
                    required
                    placeholder="Enter current password"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-xs text-gray-500">If you haven't changed your password, use your employee number: {{ $teacher->employee_number }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter new password"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                    placeholder="Confirm new password"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Change Password
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Account Information</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee Number</label>
                <div class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900">
                    {{ $teacher->employee_number }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <div class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900">
                    {{ $teacher->email }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : ($teacher->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst(str_replace('_', ' ', $teacher->status)) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection


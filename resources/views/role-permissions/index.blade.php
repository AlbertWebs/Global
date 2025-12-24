@extends('layouts.app')

@section('title', 'Role Permissions')
@section('page-title', 'Manage Role Permissions')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Role Permissions Management</h2>
                <p class="text-sm text-gray-600 mt-1">Assign permissions to roles. Permissions are grouped by module for easy management.</p>
            </div>
        </div>

        <!-- Roles Tabs -->
        @if($roles->count() > 0)
        <div class="mb-6" x-data="{ activeRole: {{ $roles->first()->id }} }">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px space-x-4">
                    @foreach($roles as $role)
                    <button 
                        @click="activeRole = {{ $role->id }}"
                        :class="activeRole === {{ $role->id }} ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    >
                        {{ $role->name }}
                    </button>
                    @endforeach
                </nav>
            </div>

            <!-- Permission Forms for Each Role -->
            @foreach($roles as $role)
            <div 
                x-show="activeRole === {{ $role->id }}"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="mt-6"
            >
                <form method="POST" action="{{ route('role-permissions.update', $role->id) }}" id="form-role-{{ $role->id }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $role->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $role->description ?? 'No description available' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Currently has {{ $role->permissions->count() }} permission(s)</p>
                    </div>

                    <!-- Permissions by Module -->
                    <div class="space-y-6">
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    {{ ucfirst($module) }} Module
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($modulePermissions as $permission)
                                    <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input 
                                            type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $permission->id }}"
                                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                            @if($permission->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $permission->description }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1 font-mono">{{ $permission->slug }}</p>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                        >
                            Save Permissions for {{ $role->name }}
                        </button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p class="text-gray-500 mb-4">No roles found. Please create roles first.</p>
            <a href="{{ route('users.index') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Go to Users
            </a>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('users.index') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Manage Users
            </a>
            <a href="{{ route('settings.index') }}" class="flex items-center p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-md hover:shadow-lg">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                System Settings
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-md hover:shadow-lg">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection


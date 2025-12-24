<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Login - {{ \App\Models\Setting::get('school_name', 'Global College') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                @if(\App\Models\Setting::get('school_logo'))
                <div class="mb-4 flex justify-center">
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('school_logo')) }}" alt="School Logo" class="h-20 w-20 object-contain">
                </div>
                @endif
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
                <p class="text-gray-600">Student Portal</p>
            </div>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="student_number" class="block text-sm font-medium text-gray-700 mb-2">Student Number</label>
                    <input 
                        type="text" 
                        id="student_number" 
                        name="student_number" 
                        value="{{ old('student_number') }}"
                        required 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Enter your student number"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Enter your password"
                    >
                    <p class="mt-2 text-xs text-gray-500">Default password is your student number</p>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Sign In to Student Portal
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Staff Login →
                </a>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
                <p>Need help? Contact the administration office</p>
            </div>
        </div>
    </div>
</body>
</html>


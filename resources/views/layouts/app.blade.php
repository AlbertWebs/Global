<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GC Billing">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>@yield('title', 'Global College Billing System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
        
        // Auto-capitalization for text inputs
        document.addEventListener('DOMContentLoaded', function() {
            // Function to capitalize text (Title Case)
            function capitalizeText(text) {
                return text.toLowerCase().replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }
            
            // Apply to all text inputs with specific classes or IDs
            const textInputs = document.querySelectorAll('input[type="text"]:not([readonly]):not([disabled]), textarea:not([readonly]):not([disabled])');
            
            textInputs.forEach(input => {
                // Skip email, phone, and ID/passport fields
                const id = input.id || '';
                const name = input.name || '';
                const type = input.type || '';
                
                if (id.includes('email') || name.includes('email') || 
                    id.includes('phone') || name.includes('phone') ||
                    id.includes('mobile') || name.includes('mobile') ||
                    id.includes('id_passport') || name.includes('id_passport') ||
                    id.includes('student_number') || name.includes('student_number') ||
                    id.includes('admission_number') || name.includes('admission_number')) {
                    return; // Skip these fields
                }
                
                // Apply on blur
                input.addEventListener('blur', function() {
                    if (this.value && this.value.trim()) {
                        this.value = capitalizeText(this.value.trim());
                    }
                });
            });
        });
    </script>
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 shadow-2xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-14 px-4 bg-gradient-to-r from-blue-700 to-indigo-700 shadow-lg border-b border-blue-600">
                    <div class="flex items-center space-x-2 min-w-0 flex-1">
                        <div class="w-6 h-6 bg-white rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                            <span class="text-blue-600 font-bold text-xs">GC</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-white text-xs font-bold truncate">{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
                            <p class="text-blue-200 text-xs truncate">Billing System</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-blue-200 transition-colors flex-shrink-0 ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('dashboard') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('dashboard') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Dashboard</span>
                    </a>

                    <a href="{{ route('students.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('students.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('students.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('students.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('students.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Students</span>
                    </a>

                    <a href="{{ route('billing.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('billing.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('billing.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('billing.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('billing.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Record Payment</span>
                    </a>

                    <a href="{{ route('courses.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('courses.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('courses.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('courses.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('courses.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Courses</span>
                    </a>

                    <a href="{{ route('course-registrations.create') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('course-registrations.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('course-registrations.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('course-registrations.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('course-registrations.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Course Registration</span>
                    </a>

                    <a href="{{ route('receipts.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('receipts.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('receipts.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('receipts.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('receipts.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Receipts</span>
                    </a>

                    <a href="{{ route('expenses.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('expenses.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('expenses.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('expenses.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('expenses.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Expenses</span>
                    </a>
                    <a href="{{ route('other-incomes.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('other-incomes.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('other-incomes.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('other-incomes.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('other-incomes.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Other Income</span>
                    </a>
                    <a href="{{ route('bank-deposits.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('bank-deposits.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('bank-deposits.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('bank-deposits.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('bank-deposits.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Bank Deposits</span>
                    </a>

                    @if(auth()->user()->isSuperAdmin())
                    <div class="pt-4 border-t border-blue-700 border-opacity-50">
                        <p class="px-3 py-2 text-xs font-semibold text-blue-300 uppercase tracking-wider">Admin Tools</p>
                    </div>
                    <a href="{{ route('mobile.dashboard') }}" class="hidden lg:flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('mobile.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('mobile.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('mobile.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('mobile.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Mobile Dashboard</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('reports.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('reports.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('reports.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('reports.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Reports</span>
                    </a>
                    <a href="{{ route('income-statement.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('income-statement.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('income-statement.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('income-statement.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('income-statement.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Income Statement</span>
                    </a>
                    <a href="{{ route('data-purge.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('data-purge.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('data-purge.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('data-purge.*') ? 'text-red-900' : 'text-red-200 group-hover:text-red-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('data-purge.*') ? 'text-red-900 font-semibold' : 'text-red-100 font-normal group-hover:text-red-900' }}">Data Purge</span>
                    </a>
                    <a href="{{ route('money-trace.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('money-trace.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('money-trace.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('money-trace.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('money-trace.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Money Trace</span>
                    </a>

                    <a href="{{ route('balances.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('balances.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('balances.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('balances.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 12v-1m-3-6H9m1.45-3.047l-.744.156m1.562 3.856l-.156.744m-3.08-1.562l.744-.156"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('balances.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Balances</span>
                    </a>

                    <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('users.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('users.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('users.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Users & Roles</span>
                    </a>
                    <a href="{{ route('admin.teachers.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('admin.teachers.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('admin.teachers.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.teachers.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('admin.teachers.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Teachers</span>
                    </a>
                    <a href="{{ route('role-permissions.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('role-permissions.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('role-permissions.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('role-permissions.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('role-permissions.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Permissions</span>
                    </a>
                    <a href="{{ route('bulk-sms.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('bulk-sms.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('bulk-sms.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                            <svg class="w-4 h-4 {{ request()->routeIs('bulk-sms.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <span class="{{ request()->routeIs('bulk-sms.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Bulk SMS</span>
                    </a>
                    @endif

                    <div class="pt-4 border-t border-blue-700 border-opacity-50">
                        <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-20 hover:shadow-lg transition-all duration-200 group {{ request()->routeIs('settings.*') ? 'bg-white bg-opacity-20 shadow-lg' : '' }}">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3 {{ request()->routeIs('settings.*') ? 'bg-opacity-30' : '' }} group-hover:bg-opacity-30">
                                <svg class="w-4 h-4 {{ request()->routeIs('settings.*') ? 'text-blue-900' : 'text-blue-200 group-hover:text-blue-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="{{ request()->routeIs('settings.*') ? 'text-gray-900 font-semibold' : 'text-blue-100 font-normal group-hover:text-gray-900' }}">Settings</span>
                        </a>
                    </div>
                </nav>

            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-2">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->role->name ?? 'No Role' }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->role->name ?? 'No Role' }}</p>
                                </div>

                                <!-- Profile -->
                                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>

                                <!-- Settings -->
                                @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Settings
                                </a>
                                @endif

                                <!-- Reports -->
                                @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Reports
                                </a>
                                @endif

                                <!-- Divider -->
                                <div class="border-t border-gray-200 my-1"></div>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-5 h-5 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>


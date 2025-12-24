@extends('layouts.app')

@section('title', 'Data Purge - Danger Zone')
@section('page-title', 'Data Purge - Danger Zone')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Warning Banner -->
    <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-6 rounded-lg">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-bold text-red-900">DANGER ZONE</h3>
                <p class="text-red-800 mt-1">This action cannot be undone. All selected data will be permanently deleted. Users will NOT be deleted.</p>
                <p class="text-red-700 text-sm mt-2 font-semibold">⚠️ Make sure you have a backup before proceeding!</p>
            </div>
        </div>
    </div>

    <!-- Current Data Counts -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Current Data Counts</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-gray-600 font-medium">Students</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['students']) }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <p class="text-sm text-gray-600 font-medium">Payments</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['payments']) }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                <p class="text-sm text-gray-600 font-medium">Expenses</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['expenses']) }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                <p class="text-sm text-gray-600 font-medium">Course Registrations</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['course_registrations']) }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                <p class="text-sm text-gray-600 font-medium">Bank Deposits</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['bank_deposits']) }}</p>
            </div>
            <div class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                <p class="text-sm text-gray-600 font-medium">Receipts</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['receipts']) }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-500">
                <p class="text-sm text-gray-600 font-medium">Ledger Entries</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['ledger_entries']) }}</p>
            </div>
            <div class="bg-pink-50 p-4 rounded-lg border-l-4 border-pink-500">
                <p class="text-sm text-gray-600 font-medium">Activity Logs</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($counts['activity_logs']) }}</p>
            </div>
        </div>
    </div>

    <!-- Purge Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Select Data to Purge</h2>

        <form method="POST" action="{{ route('data-purge.purge') }}" id="purgeForm" x-data="{ showConfirm: false }">
            @csrf

            <!-- Purge All Option -->
            <div class="mb-6 p-4 bg-red-50 border-2 border-red-300 rounded-lg">
                <label class="flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="purge_all" 
                        value="1"
                        class="w-5 h-5 text-red-600 border-red-300 rounded focus:ring-red-500"
                        @change="showConfirm = $event.target.checked"
                    >
                    <div class="ml-3">
                        <p class="text-lg font-bold text-red-900">Purge All Data (Except Users)</p>
                        <p class="text-sm text-red-700 mt-1">This will delete ALL data including students, payments, expenses, course registrations, bank deposits, receipts, ledger entries, and activity logs. Only users will remain.</p>
                    </div>
                </label>
            </div>

            <div class="border-t border-gray-300 pt-6 mt-6">
                <p class="text-sm font-semibold text-gray-700 mb-4">OR Select Individual Tables:</p>

                <div class="space-y-4">
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_students" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Students ({{ number_format($counts['students']) }})</p>
                            <p class="text-sm text-gray-600">This will also delete related payments, receipts, and course registrations</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_payments" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Payments ({{ number_format($counts['payments']) }})</p>
                            <p class="text-sm text-gray-600">This will also delete related receipts</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_receipts" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Receipts ({{ number_format($counts['receipts']) }})</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_expenses" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Expenses ({{ number_format($counts['expenses']) }})</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_course_registrations" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Course Registrations ({{ number_format($counts['course_registrations']) }})</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_bank_deposits" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Bank Deposits ({{ number_format($counts['bank_deposits']) }})</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_ledger_entries" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Ledger Entries ({{ number_format($counts['ledger_entries']) }})</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="purge_activity_logs" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Activity Logs ({{ number_format($counts['activity_logs']) }})</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Confirmation -->
            <div class="mt-8 p-4 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
                <label for="confirm_text" class="block text-sm font-bold text-yellow-900 mb-2">
                    Type "DELETE ALL DATA" to confirm:
                </label>
                <input 
                    type="text" 
                    id="confirm_text" 
                    name="confirm_text" 
                    required
                    class="w-full px-4 py-3 border-2 border-yellow-400 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                    placeholder="DELETE ALL DATA"
                >
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg font-bold hover:from-red-700 hover:to-red-800 transition-all shadow-lg hover:shadow-xl"
                    onclick="return confirm('Are you absolutely sure you want to purge the selected data? This action cannot be undone!');"
                >
                    ⚠️ PURGE SELECTED DATA
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


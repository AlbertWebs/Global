@extends('layouts.app')

@section('title', 'Income Statement')
@section('page-title', 'Income Statement')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Income Statement</h2>
            <p class="text-gray-600">Generate income statement report (Income - Expenses = Net Profit)</p>
        </div>

        <form method="GET" action="{{ route('income-statement.generate') }}" class="space-y-6">
            <!-- Date Range Selection -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border-2 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Select Date Range
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input 
                            type="date" 
                            id="date_from" 
                            name="date_from"
                            required
                            class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input 
                            type="date" 
                            id="date_to" 
                            name="date_to"
                            required
                            class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Select the date range for the income statement report.
                </p>
            </div>

            <!-- Format Selection -->
            <div class="bg-white rounded-lg border-2 border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Format</h3>
                <div class="flex gap-4">
                    <label class="flex items-center cursor-pointer">
                        <input 
                            type="radio" 
                            name="format" 
                            value="pdf" 
                            checked
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-gray-700 font-medium">PDF</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Generate Income Statement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

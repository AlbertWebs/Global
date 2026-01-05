@extends('layouts.app')

@section('title', 'Balance Details')
@section('page-title', 'Balance Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Balance for {{ $balance->student->full_name }}</h1>
            <p class="text-gray-600">Course: <span class="font-semibold">{{ $balance->course->name }} ({{ $balance->course->code }})</span></p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('balances.edit', $balance->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Balance</span>
            </a>
            <a href="{{ route('balances.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to List</span>
            </a>
        </div>
    </div>

    <!-- Balance Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-blue-100 rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <p class="text-sm text-blue-800 font-medium mb-1">Agreed Amount</p>
            <p class="text-3xl font-bold text-blue-900">KES {{ number_format($balance->agreed_amount, 2) }}</p>
        </div>
        <div class="bg-green-100 rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <p class="text-sm text-green-800 font-medium mb-1">Total Paid</p>
            <p class="text-3xl font-bold text-green-900">KES {{ number_format($balance->total_paid, 2) }}</p>
        </div>
        <div class="bg-orange-100 rounded-xl shadow-md p-6 border-l-4 border-orange-500">
            <p class="text-sm text-orange-800 font-medium mb-1">Outstanding Balance</p>
            <p class="text-3xl font-bold {{ $balance->outstanding_balance > 0 ? 'text-orange-900' : 'text-green-900' }}">KES {{ number_format($balance->outstanding_balance, 2) }}</p>
        </div>
    </div>

    <!-- Additional Details -->
    <div class="bg-white rounded-xl shadow-lg p-6 space-y-4">
        <h2 class="text-xl font-bold text-gray-900 border-b pb-3 mb-4">Detailed Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 font-medium">Discount Amount:</p>
                <p class="text-lg font-semibold text-gray-800">KES {{ number_format($balance->discount_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Current Status:</p>
                <p class="text-lg font-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $balance->status)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Last Payment Date:</p>
                <p class="text-lg font-semibold text-gray-800">{{ $balance->last_payment_date ? $balance->last_payment_date->format('M d, Y H:i A') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Student Number:</p>
                <p class="text-lg font-semibold text-gray-800">{{ $balance->student->student_number }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

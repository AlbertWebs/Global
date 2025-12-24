@extends('layouts.app')

@section('title', 'Expense Details')
@section('page-title', 'Expense Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Expense Details</h2>
                        <p class="text-red-100 mt-1">View expense information</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if(auth()->user()->isSuperAdmin() || $expense->recorded_by === auth()->id())
                    <a href="{{ route('expenses.edit', $expense->id) }}" class="px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Expense Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-600">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Expense Title</p>
                    <p class="text-xl font-bold text-gray-900">{{ $expense->title }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-600">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Amount</p>
                    <p class="text-3xl font-bold text-red-600">KES {{ number_format($expense->amount, 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-600">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Payment Method</p>
                    <p class="text-lg font-bold text-gray-900">
                        <span class="px-3 py-1 rounded-full text-sm 
                            {{ $expense->payment_method === 'mpesa' ? 'bg-green-100 text-green-800' : 
                               ($expense->payment_method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ $expense->payment_method_label }}
                        </span>
                    </p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-600">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Expense Date</p>
                    <p class="text-lg font-bold text-gray-900">{{ $expense->expense_date->format('F d, Y') }}</p>
                </div>
            </div>

            @if($expense->description)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border-l-4 border-gray-400">
                <p class="text-xs text-gray-600 mb-2 font-medium uppercase">Description</p>
                <p class="text-gray-900">{{ $expense->description }}</p>
            </div>
            @endif

            @if($expense->notes)
            <div class="mb-6 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                <p class="text-xs text-gray-600 mb-2 font-medium uppercase">Notes</p>
                <p class="text-gray-900">{{ $expense->notes }}</p>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Recorded By</p>
                <p class="text-sm font-semibold text-gray-900">{{ $expense->recorder->name }}</p>
                <p class="text-xs text-gray-500 mt-1">Recorded on {{ $expense->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
            <div class="flex justify-between">
                <a href="{{ route('expenses.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition duration-200">
                    Back to Expenses
                </a>
                @if(auth()->user()->isSuperAdmin())
                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition duration-200">
                        Delete Expense
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


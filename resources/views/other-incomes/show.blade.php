@extends('layouts.app')

@section('title', 'Other Income Details')
@section('page-title', 'Other Income Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Other Income Details</h2>
                        <p class="text-green-100 mt-1">{{ $otherIncome->description }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    @if(auth()->user()->isSuperAdmin() || $otherIncome->recorded_by === auth()->id())
                    <a href="{{ route('other-incomes.edit', $otherIncome->id) }}" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition-colors">
                        Edit
                    </a>
                    @endif
                    @if(!$otherIncome->receipt)
                    <form action="{{ route('other-incomes.generate-receipt', $otherIncome->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition-colors">
                            Generate Receipt
                        </button>
                    </form>
                    @else
                    <a href="{{ route('other-incomes.receipt', $otherIncome->receipt->id) }}" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition-colors">
                        View Receipt
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Description</label>
                    <p class="text-lg font-bold text-gray-900">{{ $otherIncome->description }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Amount</label>
                    <p class="text-2xl font-bold text-green-600">KES {{ number_format($otherIncome->amount, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Payment Method</label>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $otherIncome->payment_method === 'mpesa' ? 'bg-green-100 text-green-800' : 
                           ($otherIncome->payment_method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ $otherIncome->payment_method_label }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Income Date</label>
                    <p class="text-lg text-gray-900">{{ $otherIncome->income_date->format('F d, Y') }}</p>
                </div>
                @if($otherIncome->notes)
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Notes</label>
                    <p class="text-gray-900">{{ $otherIncome->notes }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Recorded By</label>
                    <p class="text-gray-900">{{ $otherIncome->recorder->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Recorded On</label>
                    <p class="text-gray-900">{{ $otherIncome->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                @if($otherIncome->receipt)
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-500 mb-1">Receipt</label>
                    <a href="{{ route('other-incomes.receipt', $otherIncome->receipt->id) }}" class="text-green-600 hover:text-green-800 font-semibold">
                        {{ $otherIncome->receipt->receipt_number }} - View Receipt
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

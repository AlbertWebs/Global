@extends('layouts.app')

@section('title', 'Bank Deposit Details')
@section('page-title', 'Bank Deposit Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Deposit Details</h2>
            <a href="{{ route('bank-deposits.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Back</a>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Source Account</p>
                    <p class="text-xl font-bold text-gray-900">{{ $bankDeposit->source_account_label }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Amount</p>
                    <p class="text-xl font-bold text-gray-900">KES {{ number_format($bankDeposit->amount, 2) }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Deposit Date</p>
                    <p class="text-xl font-bold text-gray-900">{{ $bankDeposit->deposit_date->format('F d, Y') }}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                    <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Reference Number</p>
                    <p class="text-xl font-bold text-gray-900">{{ $bankDeposit->reference_number ?? 'N/A' }}</p>
                </div>
            </div>

            @if($bankDeposit->notes)
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Notes</p>
                <p class="text-gray-900">{{ $bankDeposit->notes }}</p>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-1">Recorded By</p>
                <p class="text-gray-900">{{ $bankDeposit->recorder->name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $bankDeposit->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>

            @if(auth()->user()->isSuperAdmin())
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('bank-deposits.edit', $bankDeposit->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection


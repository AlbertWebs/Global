@extends('layouts.app')

@section('title', 'Bank Deposits')
@section('page-title', 'Bank Deposits')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Bank Deposits</h2>
        <p class="text-sm text-gray-600 mt-1">Record deposits from Cash on Hand or M-Pesa Wallet to Bank Account</p>
    </div>
    <a href="{{ route('bank-deposits.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center shadow-md hover:shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Record Deposit
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('bank-deposits.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="source_account" class="block text-sm font-medium text-gray-700 mb-2">Source Account</label>
            <select id="source_account" name="source_account" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Sources</option>
                <option value="cash_on_hand" {{ $sourceAccountFilter === 'cash_on_hand' ? 'selected' : '' }}>Cash on Hand</option>
                <option value="mpesa_wallet" {{ $sourceAccountFilter === 'mpesa_wallet' ? 'selected' : '' }}>M-Pesa Wallet</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('bank-deposits.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Clear</a>
        </div>
    </form>
</div>

<!-- Summary Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-green-500">
    <p class="text-gray-600 text-sm font-medium">Total Deposits</p>
    <p class="text-3xl font-bold text-gray-900 mt-2">KES {{ number_format($totalDeposits, 2) }}</p>
</div>

<!-- Deposits Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Deposit Records</h3>
    </div>

    @if($deposits->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source Account</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($deposits as $deposit)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $deposit->deposit_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $deposit->source_account === 'cash_on_hand' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $deposit->source_account_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">KES {{ number_format($deposit->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $deposit->reference_number ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $deposit->recorder->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('bank-deposits.show', $deposit->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('bank-deposits.edit', $deposit->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('bank-deposits.destroy', $deposit->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this deposit?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $deposits->links() }}
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <p class="text-gray-500">No bank deposits recorded yet.</p>
        <a href="{{ route('bank-deposits.create') }}" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Record First Deposit</a>
    </div>
    @endif
</div>
@endsection


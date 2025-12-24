@extends('layouts.app')

@section('title', 'Money Trace')
@section('page-title', 'Money Trace')

@section('content')
<div x-data="moneyTrace()">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Money Tracing Dashboard</h2>
        <p class="text-sm text-gray-600 mt-1">Track all income sources and fund locations in real-time</p>
    </div>

    <!-- Current Balances -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Cash on Hand</p>
                    <p class="text-3xl font-bold mt-2">KES {{ number_format($balances['cash_on_hand'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-green-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">M-Pesa Wallet</p>
                    <p class="text-3xl font-bold mt-2">KES {{ number_format($balances['mpesa_wallet'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-blue-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Bank Account</p>
                    <p class="text-3xl font-bold mt-2">KES {{ number_format($balances['bank_account'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-purple-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm font-medium">Total Balance</p>
                    <p class="text-3xl font-bold mt-2">KES {{ number_format($totalBalance, 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-gray-400 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Income by Source -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Today -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase">Today's Income</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">M-Pesa</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($todayIncome['mpesa'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Cash</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($todayIncome['cash'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Bank Transfer</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($todayIncome['bank_transfer'], 2) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-blue-600">KES {{ number_format($todayIncome['total'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- This Week -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase">This Week's Income</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">M-Pesa</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($weekIncome['mpesa'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Cash</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($weekIncome['cash'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Bank Transfer</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($weekIncome['bank_transfer'], 2) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-green-600">KES {{ number_format($weekIncome['total'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase">This Month's Income</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">M-Pesa</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($monthIncome['mpesa'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Cash</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($monthIncome['cash'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Bank Transfer</span>
                    <span class="font-semibold text-gray-900">KES {{ number_format($monthIncome['bank_transfer'], 2) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-purple-600">KES {{ number_format($monthIncome['total'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('money-trace.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Quick Period</label>
                <select id="period" name="period" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="holding_account" class="block text-sm font-medium text-gray-700 mb-2">Holding Account</label>
                <select id="holding_account" name="holding_account" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Accounts</option>
                    <option value="cash_on_hand" {{ $holdingAccount === 'cash_on_hand' ? 'selected' : '' }}>Cash on Hand</option>
                    <option value="mpesa_wallet" {{ $holdingAccount === 'mpesa_wallet' ? 'selected' : '' }}>M-Pesa Wallet</option>
                    <option value="bank_account" {{ $holdingAccount === 'bank_account' ? 'selected' : '' }}>Bank Account</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            </div>
        </form>
    </div>

    <!-- Period Summary -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Period Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <p class="text-sm text-gray-600 font-medium">Total Inflows</p>
                <p class="text-2xl font-bold text-green-600 mt-1">KES {{ number_format($periodTotals['inflows'], 2) }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                <p class="text-sm text-gray-600 font-medium">Total Outflows</p>
                <p class="text-2xl font-bold text-red-600 mt-1">KES {{ number_format($periodTotals['outflows'], 2) }}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-gray-600 font-medium">Net Flow</p>
                <p class="text-2xl font-bold {{ $periodTotals['net'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                    KES {{ number_format($periodTotals['net'], 2) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Ledger Entries Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Transaction Ledger</h3>
        </div>

        @if($entries->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holding Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($entries as $entry)
                    <tr class="hover:bg-gray-50 transition-colors" 
                        x-data="{ expanded: false }"
                        @click="expanded = !expanded"
                        class="cursor-pointer">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $entry->transaction_date->format('M d, Y') }}<br>
                            <span class="text-xs text-gray-500">{{ $entry->transaction_date->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $entry->type === 'inflow' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $entry->type === 'inflow' ? 'Inflow' : 'Outflow' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            {{ $entry->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $entry->payment_source === 'mpesa' ? 'bg-green-100 text-green-800' : 
                                   ($entry->payment_source === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ $entry->payment_source_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $entry->holding_account_label }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $entry->reference_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold {{ $entry->type === 'inflow' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $entry->type === 'inflow' ? '+' : '-' }}KES {{ number_format($entry->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $entry->recorder->name }}
                        </td>
                    </tr>
                    @if($entry->entity)
                    <tr x-show="expanded" x-transition class="bg-gray-50">
                        <td colspan="8" class="px-6 py-4">
                            <div class="flex items-center gap-4 text-sm">
                                <span class="text-gray-600">Linked Entity:</span>
                                @if($entry->entity_type === 'App\Models\Payment')
                                    <a href="{{ route('receipts.show', $entry->entity->receipt->id ?? '#') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Payment Receipt: {{ $entry->entity->receipt->receipt_number ?? 'N/A' }}
                                    </a>
                                @elseif($entry->entity_type === 'App\Models\Expense')
                                    <a href="{{ route('expenses.show', $entry->entity->id) }}" class="text-red-600 hover:text-red-800 font-medium">
                                        Expense: {{ $entry->entity->title }}
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $entries->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-gray-500">No transactions found for the selected period.</p>
        </div>
        @endif
    </div>
</div>

<script>
function moneyTrace() {
    return {
        init() {
            // Initialize any Alpine.js functionality
        }
    }
}
</script>
@endsection


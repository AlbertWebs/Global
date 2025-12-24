@extends('layouts.app')

@section('title', 'Financial Reports')
@section('page-title', 'Financial Reports')

@section('content')
<div x-data="reportPage()" class="print:bg-white">
    <!-- Print Header (Hidden on screen, visible when printing) -->
    <div class="hidden print:block mb-6 border-b-2 border-gray-300 pb-4">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">GLOBAL COLLEGE</h1>
            <p class="text-lg text-gray-700 mt-2">Financial Report</p>
            <p class="text-sm text-gray-600 mt-1">
                Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 print:hidden">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Financial Reports</h2>
            <p class="text-sm text-gray-600 mt-1">Comprehensive financial analysis and reporting</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <!-- Export Full Report -->
            <a 
                href="{{ route('reports.export', request()->query()) }}" 
                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center shadow-md hover:shadow-lg text-sm sm:text-base"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Full Report</span>
                <span class="sm:hidden">Full</span>
            </a>
            <!-- Export Payments -->
            <a 
                href="{{ route('reports.export-payments', request()->query()) }}" 
                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center shadow-md hover:shadow-lg text-sm sm:text-base"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Payments</span>
                <span class="sm:hidden">Payments</span>
            </a>
            <!-- Export Expenses -->
            <a 
                href="{{ route('reports.export-expenses', request()->query()) }}" 
                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center shadow-md hover:shadow-lg text-sm sm:text-base"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Expenses</span>
                <span class="sm:hidden">Expenses</span>
            </a>
            <!-- Print Report -->
            <button 
                @click="window.print()" 
                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center shadow-md hover:shadow-lg text-sm sm:text-base"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span class="hidden sm:inline">Print Report</span>
                <span class="sm:hidden">Print</span>
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 print:hidden">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Report Filters</h2>
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-4">
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Quick Period</label>
                <select 
                    id="period" 
                    name="period" 
                    x-model="period"
                    @change="updateDateRange"
                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input 
                    type="date" 
                    id="date_from" 
                    name="date_from" 
                    x-model="dateFrom"
                    value="{{ $dateFrom }}" 
                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input 
                    type="date" 
                    id="date_to" 
                    name="date_to" 
                    x-model="dateTo"
                    value="{{ $dateTo }}" 
                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            <div class="flex items-end gap-2">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Report Period Info -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg print:bg-white print:border-2 print:border-gray-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-900">Report Period</p>
                <p class="text-lg text-blue-700 mt-1">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}
                </p>
            </div>
            <div class="text-right print:hidden">
                <p class="text-xs text-blue-600">Generated: {{ now()->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Student Registration Statistics -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 print:shadow-none print:border print:border-gray-300">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Registrations</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500 print:bg-white print:border print:border-gray-300">
                <p class="text-sm text-gray-600 font-medium">Today</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $studentStats['today'] }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500 print:bg-white print:border print:border-gray-300">
                <p class="text-sm text-gray-600 font-medium">This Week</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $studentStats['week'] }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500 print:bg-white print:border print:border-gray-300">
                <p class="text-sm text-gray-600 font-medium">This Month</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $studentStats['month'] }}</p>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500 print:bg-white print:border print:border-gray-300">
                <p class="text-sm text-gray-600 font-medium">This Year</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $studentStats['year'] }}</p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 print:grid-cols-4 print:gap-4">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 print:border print:border-gray-300 print:shadow-none">
            <p class="text-gray-600 text-sm font-medium">Total Payments</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $summary['total_payments'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 print:border print:border-gray-300 print:shadow-none">
            <p class="text-gray-600 text-sm font-medium">Total Income</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">KES {{ number_format($summary['total_amount_paid'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500 print:border print:border-gray-300 print:shadow-none">
            <p class="text-gray-600 text-sm font-medium">Total Expenses</p>
            <p class="text-3xl font-bold text-red-600 mt-2">KES {{ number_format($summary['total_expenses'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 {{ $summary['net_income'] >= 0 ? 'border-green-500' : 'border-orange-500' }} print:border print:border-gray-300 print:shadow-none">
            <p class="text-gray-600 text-sm font-medium">Net Income</p>
            <p class="text-3xl font-bold {{ $summary['net_income'] >= 0 ? 'text-green-600' : 'text-orange-600' }} mt-2">
                KES {{ number_format($summary['net_income'], 2) }}
            </p>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 print:shadow-none print:border print:border-gray-300">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method Breakdown</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500 print:bg-white print:border print:border-gray-300">
                <p class="text-sm text-gray-600 font-medium">M-Pesa</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">KES {{ number_format($paymentMethodBreakdown['mpesa'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $summary['total_amount_paid'] > 0 ? number_format(($paymentMethodBreakdown['mpesa'] / $summary['total_amount_paid']) * 100, 1) : 0 }}% of total
                </p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500 print:bg-white print:border print:border-gray-300">
                <p class="text-sm text-gray-600 font-medium">Cash</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">KES {{ number_format($paymentMethodBreakdown['cash'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $summary['total_amount_paid'] > 0 ? number_format(($paymentMethodBreakdown['cash'] / $summary['total_amount_paid']) * 100, 1) : 0 }}% of total
                </p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500 print:bg-white print:border print:border-gray-300">
                <p class="text-sm text-gray-600 font-medium">Bank Transfer</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">KES {{ number_format($paymentMethodBreakdown['bank_transfer'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $summary['total_amount_paid'] > 0 ? number_format(($paymentMethodBreakdown['bank_transfer'] / $summary['total_amount_paid']) * 100, 1) : 0 }}% of total
                </p>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 print:shadow-none print:border print:border-gray-300 print:break-inside-avoid">
        <div class="px-6 py-4 border-b border-gray-200 print:border-b-2 print:border-gray-400">
            <h2 class="text-xl font-semibold text-gray-900">Payment Transactions</h2>
            <p class="text-sm text-gray-600 mt-1">Total: {{ $summary['total_payments'] }} payments</p>
        </div>

        @if($payments->count() > 0)
        <div class="overflow-x-auto print:overflow-visible">
            <table class="min-w-full divide-y divide-gray-200 print:table-fixed print:w-full">
                <thead class="bg-gray-50 print:bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Course</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Payment Method</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Base Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Amount Paid</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Discount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400 print:hidden">Receipt</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 print:hover:bg-white print:border-b print:border-gray-300">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 print:border print:border-gray-300">
                            {{ $payment->created_at->format('M d, Y') }}<br>
                            <span class="text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 print:border print:border-gray-300">
                            <div>{{ $payment->student->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->student->student_number }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 print:border print:border-gray-300">
                            <div>{{ $payment->course->name }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->course->code }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap print:border print:border-gray-300">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $payment->payment_method === 'mpesa' ? 'bg-green-100 text-green-800' : 
                                   ($payment->payment_method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}
                                print:bg-white print:border print:border-gray-400">
                                @if($payment->payment_method === 'mpesa')
                                    M-Pesa
                                @elseif($payment->payment_method === 'bank_transfer')
                                    Bank Transfer
                                @else
                                    Cash
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900 print:border print:border-gray-300">KES {{ number_format($payment->base_price, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900 print:border print:border-gray-300">KES {{ number_format($payment->amount_paid, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-green-600 print:border print:border-gray-300">KES {{ number_format($payment->discount_amount, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium print:hidden">
                            @if($payment->receipt)
                            <a href="{{ route('receipts.show', $payment->receipt->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            @else
                            <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <!-- Summary Row -->
                    <tr class="bg-gray-100 font-bold print:bg-gray-200 print:border-t-2 print:border-gray-400">
                        <td colspan="4" class="px-4 py-3 text-right text-sm text-gray-900 print:border print:border-gray-300">TOTALS:</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900 print:border print:border-gray-300">KES {{ number_format($summary['total_base_price'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900 print:border print:border-gray-300">KES {{ number_format($summary['total_amount_paid'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-green-600 print:border print:border-gray-300">KES {{ number_format($summary['total_discounts'], 2) }}</td>
                        <td class="px-4 py-3 print:hidden"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <p class="text-gray-500">No payments found for the selected period.</p>
        </div>
        @endif
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 print:shadow-none print:border print:border-gray-300 print:break-inside-avoid">
        <div class="px-6 py-4 border-b border-gray-200 print:border-b-2 print:border-gray-400">
            <h2 class="text-xl font-semibold text-gray-900">Expense Transactions</h2>
            <p class="text-sm text-gray-600 mt-1">Total: {{ $expenses->count() }} expenses</p>
        </div>

        @if($expenses->count() > 0)
        <div class="overflow-x-auto print:overflow-visible">
            <table class="min-w-full divide-y divide-gray-200 print:table-fixed print:w-full">
                <thead class="bg-gray-50 print:bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Payment Method</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase print:border print:border-gray-400 print:hidden">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($expenses as $expense)
                    <tr class="hover:bg-gray-50 print:hover:bg-white print:border-b print:border-gray-300">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 print:border print:border-gray-300">{{ $expense->expense_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 print:border print:border-gray-300">{{ $expense->title }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs print:border print:border-gray-300">{{ $expense->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap print:border print:border-gray-300">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $expense->payment_method === 'mpesa' ? 'bg-green-100 text-green-800' : 
                                   ($expense->payment_method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}
                                print:bg-white print:border print:border-gray-400">
                                {{ $expense->payment_method_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-red-600 print:border print:border-gray-300">KES {{ number_format($expense->amount, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 print:hidden">{{ $expense->recorder->name }}</td>
                    </tr>
                    @endforeach
                    <!-- Summary Row -->
                    <tr class="bg-gray-100 font-bold print:bg-gray-200 print:border-t-2 print:border-gray-400">
                        <td colspan="4" class="px-4 py-3 text-right text-sm text-gray-900 print:border print:border-gray-300">TOTAL EXPENSES:</td>
                        <td class="px-4 py-3 text-right text-sm text-red-600 print:border print:border-gray-300">KES {{ number_format($summary['total_expenses'], 2) }}</td>
                        <td class="px-4 py-3 print:hidden"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <p class="text-gray-500">No expenses recorded for this period.</p>
        </div>
        @endif
    </div>

    <!-- Financial Summary Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 print:shadow-none print:border print:border-gray-300 print:break-inside-avoid">
        <div class="px-6 py-4 border-b border-gray-200 print:border-b-2 print:border-gray-400">
            <h2 class="text-xl font-semibold text-gray-900">Financial Summary</h2>
        </div>
        <div class="p-6">
            <table class="w-full print:border print:border-gray-400">
                <tbody>
                    <tr class="print:border-b print:border-gray-300">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-700 print:border print:border-gray-300">Total Income (Payments)</td>
                        <td class="px-4 py-3 text-right text-lg font-bold text-green-600 print:border print:border-gray-300">KES {{ number_format($summary['total_amount_paid'], 2) }}</td>
                    </tr>
                    <tr class="print:border-b print:border-gray-300">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-700 print:border print:border-gray-300">Total Expenses</td>
                        <td class="px-4 py-3 text-right text-lg font-bold text-red-600 print:border print:border-gray-300">KES {{ number_format($summary['total_expenses'], 2) }}</td>
                    </tr>
                    <tr class="bg-gray-50 print:bg-gray-100 print:border-b-2 print:border-gray-400">
                        <td class="px-4 py-3 text-sm font-bold text-gray-900 print:border print:border-gray-300">Net Income</td>
                        <td class="px-4 py-3 text-right text-xl font-bold {{ $summary['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }} print:border print:border-gray-300">
                            KES {{ number_format($summary['net_income'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print Footer -->
    <div class="hidden print:block mt-8 pt-4 border-t-2 border-gray-400 text-center text-xs text-gray-600">
        <p>This is a computer-generated report from Global College Billing System</p>
        <p class="mt-1">Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</div>

<style>
@media print {
    @page {
        margin: 1cm;
        size: A4;
    }
    
    body {
        background: white !important;
    }
    
    .print\:hidden {
        display: none !important;
    }
    
    .print\:block {
        display: block !important;
    }
    
    .print\:bg-white {
        background: white !important;
    }
    
    .print\:border {
        border: 1px solid #d1d5db !important;
    }
    
    .print\:border-2 {
        border-width: 2px !important;
    }
    
    .print\:border-gray-300 {
        border-color: #d1d5db !important;
    }
    
    .print\:border-gray-400 {
        border-color: #9ca3af !important;
    }
    
    .print\:shadow-none {
        box-shadow: none !important;
    }
    
    .print\:break-inside-avoid {
        break-inside: avoid;
    }
    
    .print\:table-fixed {
        table-layout: fixed;
    }
    
    .print\:w-full {
        width: 100% !important;
    }
    
    .print\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
    
    .print\:gap-4 {
        gap: 1rem;
    }
    
    a {
        color: inherit !important;
        text-decoration: none !important;
    }
    
    button {
        display: none !important;
    }
}
</style>

<script>
function reportPage() {
    return {
        period: '{{ $period }}',
        dateFrom: '{{ $dateFrom }}',
        dateTo: '{{ $dateTo }}',
        
        updateDateRange() {
            const today = new Date();
            let start, end;
            
            if (this.period === 'today') {
                start = new Date(today);
                end = new Date(today);
            } else if (this.period === 'week') {
                const day = today.getDay();
                const diff = today.getDate() - day;
                start = new Date(today.setDate(diff));
                end = new Date(today);
                end.setDate(end.getDate() + 6);
            } else if (this.period === 'month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            }
            
            if (start && end) {
                this.dateFrom = start.toISOString().split('T')[0];
                this.dateTo = end.toISOString().split('T')[0];
            }
        }
    }
}
</script>
@endsection

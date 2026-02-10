@extends('layouts.app')

@section('title', 'Payment Logs')
@section('page-title', 'Payment Logs')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Payment Logs</h1>
            @if(request('student_id'))
                @php
                    $student = \App\Models\Student::find(request('student_id'));
                @endphp
                @if($student)
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Filtered by:</span>
                            <a href="{{ route('students.show', $student->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                {{ $student->full_name }}
                            </a>
                            <a href="{{ route('payment-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700" title="Clear filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </div>
                        <a href="{{ route('payment-logs.export-excel', ['student_id' => $student->id]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Payment Records
                        </a>
                    </div>
                @endif
            @endif
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($paymentLogs->count() > 0)
        <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Before</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance After</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet After</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($paymentLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->payment_date->format('M d, Y H:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->student)
                                <a href="{{ route('students.show', $log->student->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $log->student->full_name }}
                                </a>
                            @else
                                <span class="text-gray-500 italic">Student Deleted (ID: {{ $log->student_id }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->course->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                            KES {{ number_format($log->amount_paid, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                            KES {{ number_format($log->balance_before, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                            KES {{ number_format($log->balance_after, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                            KES {{ number_format($log->wallet_balance_after, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('payment-logs.show', $log->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $paymentLogs->links() }}
        </div>
        @else
        <div class="text-center py-10">
            <p class="text-gray-600 text-lg">No payment logs found.</p>
        </div>
        @endif
    </div>
</div>
@endsection


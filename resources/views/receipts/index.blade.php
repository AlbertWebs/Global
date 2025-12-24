@extends('layouts.app')

@section('title', 'Receipts')
@section('page-title', 'Receipts')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">All Receipts</h2>
    </div>

    @if($receipts->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($receipts as $receipt)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-gray-900">{{ $receipt->receipt_number }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $receipt->payment->student->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $receipt->payment->student->student_number }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $receipt->payment->course->name }}</div>
                        <div class="text-sm text-gray-500">{{ $receipt->payment->course->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-gray-900">KES {{ number_format($receipt->payment->amount_paid, 2) }}</span>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-green-600 font-semibold">KES {{ number_format($receipt->payment->discount_amount, 2) }}</span>
                    </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-500">{{ $receipt->receipt_date->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('receipts.show', $receipt->id) }}" class="text-blue-600 hover:text-blue-900 mr-4">View</a>
                        <a href="{{ route('receipts.print', $receipt->id) }}" target="_blank" class="text-green-600 hover:text-green-900">Print</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $receipts->links() }}
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <p class="text-gray-500">No receipts found.</p>
    </div>
    @endif
</div>
@endsection


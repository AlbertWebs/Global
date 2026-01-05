@extends('layouts.app')

@section('title', 'Payment Log Details')
@section('page-title', 'Payment Log Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Payment Log #{{ $paymentLog->id }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-600 font-medium">Payment Date:</p>
                <p class="text-lg font-semibold text-gray-900">{{ $paymentLog->payment_date->format('M d, Y H:i A') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Student:</p>
                <p class="text-lg font-semibold text-blue-600"><a href="{{ route('students.show', $paymentLog->student->id) }}">{{ $paymentLog->student->full_name }}</a></p>
            </div>
            @if($paymentLog->course)
            <div>
                <p class="text-sm text-gray-600 font-medium">Course:</p>
                <p class="text-lg font-semibold text-gray-900">{{ $paymentLog->course->name }} ({{ $paymentLog->course->code }})</p>
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-600 font-medium">Description:</p>
                <p class="text-lg font-semibold text-gray-900">{{ $paymentLog->description }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Original Payment ID:</p>
                <p class="text-lg font-semibold text-gray-900"><a href="{{ route('receipts.show', $paymentLog->payment->receipt->id) }}" class="text-blue-600 hover:text-blue-900">{{ $paymentLog->payment->id }} (Receipt: {{ $paymentLog->payment->receipt->receipt_number }})</a></p>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-1 md:grid-cols-3 gap-6 border border-gray-200">
            <div>
                <p class="text-sm text-gray-600 font-medium">Amount Paid (in this log entry):</p>
                <p class="text-xl font-bold text-green-600">KES {{ number_format($paymentLog->amount_paid, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Balance Before (for this course):</p>
                <p class="text-xl font-bold text-orange-600">KES {{ number_format($paymentLog->balance_before, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Balance After (for this course):</p>
                <p class="text-xl font-bold text-blue-600">KES {{ number_format($paymentLog->balance_after, 2) }}</p>
            </div>
            @if($paymentLog->wallet_balance_after > 0)
            <div class="md:col-span-3 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600 font-medium">Student Wallet Balance After Transaction:</p>
                <p class="text-xl font-bold text-purple-600">KES {{ number_format($paymentLog->wallet_balance_after, 2) }}</p>
            </div>
            @endif
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('payment-logs.index') }}" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                Back to All Logs
            </a>
            @if($paymentLog->payment && $paymentLog->payment->receipt)
            <a href="{{ route('receipts.show', $paymentLog->payment->receipt->id) }}" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                View Original Receipt
            </a>
            @endif
        </div>
    </div>
</div>
@endsection


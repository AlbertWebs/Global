@extends('layouts.app')

@section('title', 'Receipt #' . $receipt->receipt_number)
@section('page-title', 'Receipt Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-2xl p-8 border border-gray-100">
        <!-- Receipt Header with School Branding -->
        <div class="text-center mb-8 pb-6 border-b-4 border-blue-600">
            <div class="mb-4">
                @if(\App\Models\Setting::get('receipt_logo') || \App\Models\Setting::get('school_logo'))
                <div class="mb-4">
                    <img src="{{ asset('storage/' . (\App\Models\Setting::get('receipt_logo') ?? \App\Models\Setting::get('school_logo'))) }}" alt="School Logo" class="h-24 mx-auto object-contain">
                </div>
                @endif
                <div class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg mb-3">
                    <h1 class="text-4xl font-bold">{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
                </div>
            </div>
            <p class="text-lg text-gray-700 font-semibold">OFFICIAL RECEIPT</p>
            <p class="text-sm text-gray-500 mt-1">{{ \App\Models\Setting::get('school_address', 'P.O. Box 12345, Nairobi, Kenya') }} | Tel: {{ \App\Models\Setting::get('school_phone', '+254 700 000 000') }}</p>
        </div>

        <!-- Receipt Details -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-600">
                <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Receipt Number</p>
                <p class="text-xl font-bold text-gray-900">{{ $receipt->receipt_number }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-600 text-right">
                <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Date</p>
                <p class="text-xl font-bold text-gray-900">{{ $receipt->receipt_date->format('F d, Y') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $receipt->receipt_date->format('h:i A') }}</p>
            </div>
        </div>

        <!-- Student Information Section -->
        <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Student Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Full Name</p>
                    <p class="text-lg font-bold text-gray-900">{{ $receipt->payment->student->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Student Number</p>
                    <p class="text-lg font-bold text-gray-900">{{ $receipt->payment->student->student_number }}</p>
                </div>
                @if($receipt->payment->student->email)
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Email</p>
                    <p class="text-sm text-gray-900">{{ $receipt->payment->student->email }}</p>
                </div>
                @endif
                @if($receipt->payment->student->phone)
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Phone</p>
                    <p class="text-sm text-gray-900">{{ $receipt->payment->student->phone }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Details Section -->
        <div class="mb-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Payment Details
            </h3>
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden shadow-md">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase">Description</th>
                            <th class="px-6 py-4 text-right text-sm font-bold uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200 bg-white">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 text-lg">{{ $receipt->payment->course->name }}</p>
                                <p class="text-sm text-gray-600 mt-1">Course Code: {{ $receipt->payment->course->code }}</p>
                                @if($receipt->payment->academic_year && $receipt->payment->term)
                                <p class="text-xs text-gray-500 mt-1">{{ $receipt->payment->term }} - {{ $receipt->payment->academic_year }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-bold text-gray-900 text-lg">KES {{ number_format($receipt->payment->amount_paid, 2) }}</p>
                            </td>
                        </tr>
                        @if($receipt->payment->agreed_amount)
                        <tr class="bg-blue-50 border-b border-gray-200">
                            <td class="px-6 py-3">
                                <p class="text-sm text-gray-700 font-medium">Amount</p>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <p class="text-sm text-gray-700 font-semibold">KES {{ number_format($receipt->payment->agreed_amount, 2) }}</p>
                            </td>
                        </tr>
                        @php
                            $balance = max(0, $receipt->payment->agreed_amount - $receipt->payment->amount_paid);
                        @endphp
                        @if($balance > 0)
                        <tr class="bg-orange-50 border-b border-gray-200">
                            <td class="px-6 py-3">
                                <p class="text-sm text-gray-700 font-medium">Outstanding Balance</p>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <p class="text-sm font-bold text-orange-700">KES {{ number_format($balance, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                        @endif
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 border-t-4 border-gray-400">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 text-xl">Total Amount Paid</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-3xl font-bold text-gray-900">KES {{ number_format($receipt->payment->amount_paid, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Method & Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-600">
                <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Payment Method</p>
                <p class="text-lg font-bold text-gray-900">
                    @if($receipt->payment->payment_method === 'mpesa')
                        M-Pesa
                    @elseif($receipt->payment->payment_method === 'bank_transfer')
                        Bank Transfer
                    @else
                        Cash
                    @endif
                </p>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-600">
                <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Served By</p>
                <p class="text-lg font-bold text-gray-900">{{ $receipt->payment->cashier->name }}</p>
            </div>
        </div>

        @if($receipt->payment->notes)
        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400 shadow-sm">
            <p class="text-xs text-gray-600 mb-1 font-medium uppercase">Notes</p>
            <p class="text-gray-900 font-medium">{{ $receipt->payment->notes }}</p>
        </div>
        @endif

        <!-- Footer Message -->
        <div class="mt-8 pt-6 border-t-2 border-gray-300 text-center bg-gray-50 p-4 rounded-lg">
            <p class="text-sm font-semibold text-gray-700 mb-1">Thank you for your payment!</p>
            <p class="text-xs text-gray-500">This is an official computer-generated receipt from {{ \App\Models\Setting::get('school_name', 'Global College') }}</p>
            <p class="text-xs text-gray-400 mt-2">Keep this receipt for your records</p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('receipts.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                Back to Receipts
            </a>
            <a href="{{ route('receipts.print', $receipt->id) }}" target="_blank" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                Print (Color)
            </a>
            <a href="{{ route('receipts.print-bw', $receipt->id) }}" target="_blank" class="px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-800 text-white rounded-lg font-bold hover:from-gray-800 hover:to-gray-900 transition-all shadow-lg hover:shadow-xl">
                Print (B&W)
            </a>
            <a href="{{ route('receipts.thermal', $receipt->id) }}" target="_blank" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg font-bold hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl">
                Print (Thermal)
            </a>
        </div>
    </div>
</div>
@endsection

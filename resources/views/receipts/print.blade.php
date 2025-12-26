<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $receipt->receipt_number }} - Global College</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { 
                margin: 0; 
                padding: 10mm;
            }
            .no-print { display: none; }
            .print-break { page-break-after: always; }
            @page {
                size: A5;
                margin: 10mm;
            }
            img {
                max-width: 100%;
                height: auto;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        @media screen {
            body {
                max-width: 148mm; /* A5 width */
                margin: 0 auto;
            }
        }
    </style>
</head>
<body class="bg-white p-8">
    <div class="max-w-3xl mx-auto">
        <!-- Receipt Header with School Branding -->
        <div class="text-center mb-8 pb-6 border-b-4 border-blue-600">
            <div class="mb-4">
                @if(\App\Models\Setting::get('receipt_logo') || \App\Models\Setting::get('school_logo'))
                <div class="mb-4">
                    <img src="{{ url('storage/' . (\App\Models\Setting::get('receipt_logo') ?? \App\Models\Setting::get('school_logo'))) }}" alt="School Logo" class="h-28 mx-auto object-contain" style="max-height: 112px; width: auto;">
                </div>
                @endif
                <div class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg mb-3">
                    <h1 class="text-5xl font-bold">{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
                </div>
            </div>
            <p class="text-xl text-gray-700 font-bold uppercase tracking-wide">Official Receipt</p>
            <p class="text-sm text-gray-600 mt-2">{{ \App\Models\Setting::get('school_address', 'P.O. Box 12345, Nairobi, Kenya') }}</p>
            <p class="text-sm text-gray-600">Tel: {{ \App\Models\Setting::get('school_phone', '+254 700 000 000') }}@if(\App\Models\Setting::get('school_email')) | Email: {{ \App\Models\Setting::get('school_email') }}@endif</p>
        </div>

        <!-- Receipt Details -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-600">
                <p class="text-xs text-gray-600 mb-1 font-bold uppercase">Receipt Number</p>
                <p class="text-2xl font-bold text-gray-900">{{ $receipt->receipt_number }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-600 text-right">
                <p class="text-xs text-gray-600 mb-1 font-bold uppercase">Date</p>
                <p class="text-2xl font-bold text-gray-900">{{ $receipt->receipt_date->format('F d, Y') }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ $receipt->receipt_date->format('h:i A') }}</p>
            </div>
        </div>

        <!-- Student Information Section -->
        <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200">
            <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide border-b border-blue-300 pb-2">
                Student Information
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Full Name</p>
                    <p class="text-xl font-bold text-gray-900">{{ $receipt->payment->student->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Student Number</p>
                    <p class="text-xl font-bold text-gray-900">{{ $receipt->payment->student->student_number }}</p>
                </div>
                @if($receipt->payment->student->email)
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Email Address</p>
                    <p class="text-sm text-gray-900">{{ $receipt->payment->student->email }}</p>
                </div>
                @endif
                @if($receipt->payment->student->phone)
                <div>
                    <p class="text-xs text-gray-600 mb-1 font-medium">Phone Number</p>
                    <p class="text-sm text-gray-900">{{ $receipt->payment->student->phone }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Details Section -->
        <div class="mb-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide border-b-2 border-gray-300 pb-2">
                Payment Details
            </h3>
            <div class="border-2 border-gray-800 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase">Description</th>
                            <th class="px-6 py-4 text-right text-sm font-bold uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b-2 border-gray-300 bg-white">
                            <td class="px-6 py-5">
                                <p class="font-bold text-gray-900 text-xl">{{ $receipt->payment->course->name }}</p>
                                <p class="text-sm text-gray-600 mt-1">Course Code: {{ $receipt->payment->course->code }}</p>
                                @if($receipt->payment->academic_year && $receipt->payment->term)
                                <p class="text-xs text-gray-500 mt-1 font-medium">{{ $receipt->payment->term }} - {{ $receipt->payment->academic_year }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <p class="font-bold text-gray-900 text-xl">KES {{ number_format($receipt->payment->amount_paid, 2) }}</p>
                            </td>
                        </tr>
                        @if($receipt->payment->agreed_amount)
                        <tr class="bg-blue-50 border-b border-gray-300">
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">Amount</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm text-gray-700 font-semibold">KES {{ number_format($receipt->payment->agreed_amount, 2) }}</p>
                            </td>
                        </tr>
                        @php
                            $balance = max(0, $receipt->payment->agreed_amount - $receipt->payment->amount_paid);
                        @endphp
                        @if($balance > 0)
                        <tr class="bg-orange-50 border-b border-gray-300">
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">Outstanding Balance</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-bold text-orange-700">KES {{ number_format($balance, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                        @endif
                        <tr class="bg-gradient-to-r from-gray-200 to-gray-300 border-t-4 border-gray-800">
                            <td class="px-6 py-5">
                                <p class="font-bold text-gray-900 text-2xl">Total Amount Paid</p>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <p class="text-4xl font-bold text-gray-900">KES {{ number_format($receipt->payment->amount_paid, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Method & Additional Info -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-600">
                <p class="text-xs text-gray-600 mb-1 font-bold uppercase">Payment Method</p>
                <p class="text-xl font-bold text-gray-900">
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
                <p class="text-xs text-gray-600 mb-1 font-bold uppercase">Served By</p>
                <p class="text-xl font-bold text-gray-900">{{ $receipt->payment->cashier->name }}</p>
            </div>
        </div>

        @if($receipt->payment->notes)
        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
            <p class="text-xs text-gray-600 mb-1 font-bold uppercase">Additional Notes</p>
            <p class="text-gray-900 font-medium">{{ $receipt->payment->notes }}</p>
        </div>
        @endif

        <!-- Footer Message -->
        <div class="mt-12 pt-6 border-t-4 border-gray-800 text-center bg-gray-100 p-6 rounded-lg">
            <p class="text-base font-bold text-gray-800 mb-2">Thank you for your payment!</p>
            <p class="text-sm text-gray-600 mb-1">This is an official computer-generated receipt from {{ \App\Models\Setting::get('school_name', 'Global College') }}</p>
            <p class="text-xs text-gray-500 mt-2">Please keep this receipt for your records</p>
            <p class="text-xs text-gray-400 mt-4">For inquiries, contact: {{ \App\Models\Setting::get('school_email', 'info@globalcollege.edu') }} | {{ \App\Models\Setting::get('school_phone', '+254 700 000 000') }}</p>
        </div>

        <!-- Print Buttons -->
        <div class="no-print mt-8 text-center space-x-4">
            <button onclick="window.print()" class="px-8 py-4 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-colors text-lg shadow-lg">
                Print (Normal)
            </button>
            <a href="{{ route('receipts.thermal', $receipt->id) }}" target="_blank" class="inline-block px-8 py-4 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition-colors text-lg shadow-lg">
                Print (Thermal)
            </a>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto-print can be enabled by uncommenting below
            // window.print();
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $receipt->receipt_number }} - Global College</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none; }
            .print-break { page-break-after: always; }
            * {
                color: #000 !important;
                background: #fff !important;
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
            color: #000;
            background: #fff;
        }
        
        /* Black and White Styles */
        .bw-header {
            border-bottom: 2px solid #000;
        }
        
        .bw-box {
            border: 1px solid #000;
            background: #fff;
        }
        
        .bw-bg-light {
            background: #f5f5f5 !important;
        }
        
        .bw-text-bold {
            font-weight: bold;
            color: #000;
        }
        
        .bw-border {
            border: 1px solid #000;
        }
        
        .bw-divider {
            border-top: 1px solid #000;
        }
    </style>
</head>
<body class="bg-white p-8">
    <div class="max-w-3xl mx-auto">
        <!-- Receipt Header with School Branding -->
        <div class="text-center mb-8 pb-6 bw-header">
            <div class="mb-4">
                @if(\App\Models\Setting::get('receipt_logo') || \App\Models\Setting::get('school_logo'))
                <div class="mb-4">
                    <img src="{{ url('storage/' . (\App\Models\Setting::get('receipt_logo') ?? \App\Models\Setting::get('school_logo'))) }}" alt="School Logo" class="h-28 mx-auto object-contain" style="max-height: 112px; width: auto; filter: grayscale(100%);">
                </div>
                @endif
                <h1 class="text-5xl font-bold" style="color: #000;">{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
            </div>
            <p class="text-xl font-bold uppercase tracking-wide" style="color: #000;">Official Receipt</p>
            <p class="text-sm mt-2" style="color: #000;">{{ \App\Models\Setting::get('school_address', 'P.O. Box 12345, Nairobi, Kenya') }}</p>
            <p class="text-sm" style="color: #000;">Tel: {{ \App\Models\Setting::get('school_phone', '+254 700 000 000') }}@if(\App\Models\Setting::get('school_email')) | Email: {{ \App\Models\Setting::get('school_email') }}@endif</p>
        </div>

        <!-- Receipt Details -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="p-4 rounded-lg bw-box">
                <p class="text-xs mb-1 font-bold uppercase" style="color: #000;">Receipt Number</p>
                <p class="text-2xl font-bold" style="color: #000;">{{ $receipt->receipt_number }}</p>
            </div>
            <div class="p-4 rounded-lg bw-box text-right">
                <p class="text-xs mb-1 font-bold uppercase" style="color: #000;">Date</p>
                <p class="text-2xl font-bold" style="color: #000;">{{ $receipt->receipt_date->format('F d, Y') }}</p>
                <p class="text-sm mt-1" style="color: #000;">{{ $receipt->receipt_date->format('h:i A') }}</p>
            </div>
        </div>

        <!-- Student Information Section -->
        <div class="mb-6 p-5 bw-bg-light rounded-lg bw-border">
            <h3 class="text-sm font-bold mb-4 uppercase tracking-wide bw-divider pb-2" style="color: #000;">
                Student Information
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs mb-1 font-medium" style="color: #666;">Full Name</p>
                    <p class="text-xl font-bold" style="color: #000;">{{ $receipt->payment->student->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs mb-1 font-medium" style="color: #666;">Student Number</p>
                    <p class="text-xl font-bold" style="color: #000;">{{ $receipt->payment->student->student_number }}</p>
                </div>
                @if($receipt->payment->student->email)
                <div>
                    <p class="text-xs mb-1 font-medium" style="color: #666;">Email Address</p>
                    <p class="text-sm" style="color: #000;">{{ $receipt->payment->student->email }}</p>
                </div>
                @endif
                @if($receipt->payment->student->phone)
                <div>
                    <p class="text-xs mb-1 font-medium" style="color: #666;">Phone Number</p>
                    <p class="text-sm" style="color: #000;">{{ $receipt->payment->student->phone }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Details Section -->
        <div class="mb-6">
            <h3 class="text-sm font-bold mb-4 uppercase tracking-wide bw-divider pb-2" style="color: #000;">
                Payment Details
            </h3>
            <div class="bw-border rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bw-bg-light">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase" style="color: #000; border-bottom: 2px solid #000;">Description</th>
                            <th class="px-6 py-4 text-right text-sm font-bold uppercase" style="color: #000; border-bottom: 2px solid #000;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bw-divider">
                            <td class="px-6 py-5">
                                <p class="font-bold text-xl" style="color: #000;">{{ $receipt->payment->course->name }}</p>
                                <p class="text-sm mt-1" style="color: #666;">Course Code: {{ $receipt->payment->course->code }}</p>
                                @if($receipt->payment->academic_year && $receipt->payment->month)
                                <p class="text-xs mt-1 font-medium" style="color: #666;">{{ $receipt->payment->month }} - {{ $receipt->payment->academic_year }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <p class="font-bold text-xl" style="color: #000;">KES {{ number_format($receipt->payment->amount_paid, 2) }}</p>
                            </td>
                        </tr>
                        @if($receipt->payment->agreed_amount)
                        <tr class="bw-bg-light bw-divider">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium" style="color: #000;">Amount</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-semibold" style="color: #000;">KES {{ number_format($receipt->payment->agreed_amount, 2) }}</p>
                            </td>
                        </tr>
                        @php
                            $balance = max(0, $receipt->payment->agreed_amount - $receipt->payment->amount_paid);
                        @endphp
                        @if($balance > 0)
                        <tr class="bw-bg-light bw-divider">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium" style="color: #000;">Outstanding Balance</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-bold" style="color: #000;">KES {{ number_format($balance, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                        @endif
                        <tr class="bw-divider" style="border-top: 3px solid #000;">
                            <td class="px-6 py-5">
                                <p class="font-bold text-2xl" style="color: #000;">Total Amount Paid</p>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <p class="text-4xl font-bold" style="color: #000;">KES {{ number_format($receipt->payment->amount_paid, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Method & Additional Info -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="p-4 rounded-lg bw-box">
                <p class="text-xs mb-1 font-bold uppercase" style="color: #666;">Payment Method</p>
                <p class="text-xl font-bold" style="color: #000;">
                    @if($receipt->payment->payment_method === 'mpesa')
                        M-Pesa
                    @elseif($receipt->payment->payment_method === 'bank_transfer')
                        Bank Transfer
                    @else
                        Cash
                    @endif
                </p>
            </div>
            <div class="p-4 rounded-lg bw-box">
                <p class="text-xs mb-1 font-bold uppercase" style="color: #666;">Served By</p>
                <p class="text-xl font-bold" style="color: #000;">{{ $receipt->payment->cashier->name }}</p>
            </div>
        </div>

        @if($receipt->payment->notes)
        <div class="mb-6 p-4 bw-bg-light rounded-lg bw-border">
            <p class="text-xs mb-1 font-bold uppercase" style="color: #666;">Additional Notes</p>
            <p class="font-medium" style="color: #000;">{{ $receipt->payment->notes }}</p>
        </div>
        @endif

        <!-- Footer Message -->
        <div class="mt-12 pt-6 bw-divider text-center bw-bg-light p-6 rounded-lg">
            <p class="text-base font-bold mb-2" style="color: #000;">Thank you for your payment!</p>
            <p class="text-sm mb-1" style="color: #000;">This is an official computer-generated receipt from {{ \App\Models\Setting::get('school_name', 'Global College') }}</p>
            <p class="text-xs mt-2" style="color: #666;">Please keep this receipt for your records</p>
            <p class="text-xs mt-4" style="color: #666;">For inquiries, contact: {{ \App\Models\Setting::get('school_email', 'info@globalcollege.edu') }} | {{ \App\Models\Setting::get('school_phone', '+254 700 000 000') }}</p>
        </div>

        <!-- Print Buttons -->
        <div class="no-print mt-8 text-center space-x-4">
            <button onclick="window.print()" class="px-8 py-4 bg-gray-800 text-white rounded-lg font-bold hover:bg-gray-900 transition-colors text-lg shadow-lg">
                Print (Black & White)
            </button>
            <a href="{{ route('receipts.print', $receipt->id) }}" target="_blank" class="inline-block px-8 py-4 bg-gray-600 text-white rounded-lg font-bold hover:bg-gray-700 transition-colors text-lg shadow-lg">
                View Color Version
            </a>
            <a href="{{ route('receipts.thermal', $receipt->id) }}" target="_blank" class="inline-block px-8 py-4 bg-gray-500 text-white rounded-lg font-bold hover:bg-gray-600 transition-colors text-lg shadow-lg">
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $receipt->receipt_number }} - Global College</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A5;
            margin: 0;
        }
        body {
            width: 148mm;
            height: 210mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #000;
            background: #fff;
            font-size: 0.65rem; /* Smaller base font size for A5 */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .receipt-container {
            width: 100%;
            max-width: 130mm;
            padding: 0 5mm;
        }
        .max-w-3xl {
            max-width: 130mm !important; /* Adjusted max-width for A5 centering */
        }
        @media print {
            @page {
                size: A5;
                margin-top: 60mm !important; /* Larger space for letterhead header */
                margin-bottom: 10mm !important; /* Reduced footer space */
                margin-left: 5mm !important;
                margin-right: 5mm !important;
            }
            body { 
                margin: 0 !important; 
                padding: 0 !important; 
                height: 140mm !important; /* A5 height (210mm) minus top (60mm) and bottom (10mm) margins */
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                align-items: center !important;
                padding-top: 0 !important; /* Content starts after header margin */
            }
            .receipt-container {
                width: 100% !important;
                max-width: 130mm !important;
                padding: 0 2mm !important;
                margin-top: 0 !important;
                max-height: 138mm !important; /* Adjusted for larger header (60mm) */
            }
            .no-print { display: none !important; }
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
            h1 {
                font-size: 1.2rem !important;
            }
            .text-5xl {
                font-size: 1.2rem !important;
            }
            .text-4xl {
                font-size: 1.3rem !important;
            }
            .text-2xl {
                font-size: 1rem !important;
            }
            .text-xl {
                font-size: 0.95rem !important;
            }
            .text-lg {
                font-size: 0.9rem !important;
            }
            .text-base {
                font-size: 0.8rem !important;
            }
            .text-sm {
                font-size: 0.75rem !important;
            }
            .text-xs {
                font-size: 0.7rem !important;
            }
            .mb-8 {
                margin-bottom: 0.4rem !important;
            }
            .mb-6 {
                margin-bottom: 0.35rem !important;
            }
            .mb-4 {
                margin-bottom: 0.3rem !important;
            }
            .mb-3 {
                margin-bottom: 0.25rem !important;
            }
            .mt-12 {
                margin-top: 0.4rem !important;
            }
            .mt-8 {
                margin-top: 0.35rem !important;
            }
            .pb-6 {
                padding-bottom: 0.1rem !important;
            }
            .p-4 {
                padding: 0.35rem !important;
            }
            .p-5 {
                padding: 0.4rem !important;
            }
            .px-6 {
                padding-left: 0.4rem !important;
                padding-right: 0.4rem !important;
            }
            .px-4 {
                padding-left: 0.35rem !important;
                padding-right: 0.35rem !important;
            }
            .px-3 {
                padding-left: 0.3rem !important;
                padding-right: 0.3rem !important;
            }
            .py-5 {
                padding-top: 0.25rem !important;
                padding-bottom: 0.25rem !important;
            }
            .py-4 {
                padding-top: 0.2rem !important;
                padding-bottom: 0.2rem !important;
            }
            .py-3 {
                padding-top: 0.18rem !important;
                padding-bottom: 0.18rem !important;
            }
            .py-2 {
                padding-top: 0.12rem !important;
                padding-bottom: 0.12rem !important;
            }
            .py-1\.5 {
                padding-top: 0.1rem !important;
                padding-bottom: 0.1rem !important;
            }
            .gap-6 {
                gap: 0.35rem !important;
            }
            .gap-4 {
                gap: 0.3rem !important;
            }
            .gap-3 {
                gap: 0.25rem !important;
            }
            .gap-2 {
                gap: 0.2rem !important;
            }
            .pt-6 {
                padding-top: 0.1rem !important;
            }
            .p-6 {
                padding: 0.45rem !important;
            }
            .p-3 {
                padding: 0.3rem !important;
            }
            .p-2 {
                padding: 0.25rem !important;
            }
            .mb-2 {
                margin-bottom: 0.2rem !important;
            }
            .mb-1 {
                margin-bottom: 0.15rem !important;
            }
            .mb-0\.5 {
                margin-bottom: 0.1rem !important;
            }
            .pb-2 {
                padding-bottom: 0.15rem !important;
            }
            .pb-1 {
                padding-bottom: 0.1rem !important;
            }
            /* Prevent page breaks and ensure single page */
            .max-w-3xl {
                page-break-inside: avoid !important;
                max-height: 138mm !important; /* Adjusted for larger header (60mm) */
                overflow: hidden !important;
            }
            .receipt-container {
                max-height: 138mm !important; /* Adjusted for larger header (60mm) */
                page-break-inside: avoid !important;
            }
            table {
                page-break-inside: avoid !important;
            }
            tr {
                page-break-inside: avoid !important;
            }
            /* Ensure all content fits */
            body > * {
                max-height: 175mm !important;
            }
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
    <body class="bg-white">
    <div class="receipt-container max-w-3xl mx-auto">
        <!-- Receipt Details -->
        <div class="grid grid-cols-2 gap-2 mb-3">
            <div class="p-3 rounded-lg bw-box">
                <p class="text-xs mb-1 font-bold uppercase" style="color: #000;">Receipt Number</p>
                <p class="text-xl font-bold" style="color: #000;">{{ $receipt->receipt_number }}</p>
            </div>
            <div class="p-3 rounded-lg bw-box text-right">
                <p class="text-xs mb-1 font-bold uppercase" style="color: #000;">Date</p>
                <p class="text-xl font-bold" style="color: #000;">{{ $receipt->receipt_date->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Student Information Section -->
        <div class="mb-3 p-3 bw-bg-light rounded-lg bw-border">
            <h3 class="text-xs font-bold mb-1 uppercase tracking-wide bw-divider pb-1" style="color: #000;">
                Student Information
            </h3>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p class="text-xs mb-0.5 font-medium" style="color: #666;">Full Name</p>
                    <p class="text-base font-bold" style="color: #000;">{{ $receipt->payment->student->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs mb-0.5 font-medium" style="color: #666;">Admission Number</p>
                    <p class="text-base font-bold" style="color: #000;">{{ $receipt->payment->student->admission_number }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Details Section -->
        <div class="mb-3">
            <h3 class="text-xs font-bold mb-1 uppercase tracking-wide bw-divider pb-1" style="color: #000;">
                Payment Details
            </h3>
            <div class="bw-border rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bw-bg-light">
                        <tr>
                            <th class="px-3 py-1.5 text-left text-xs font-bold uppercase" style="color: #000; border-bottom: 2px solid #000;">Description</th>
                            <th class="px-3 py-1.5 text-right text-xs font-bold uppercase" style="color: #000; border-bottom: 2px solid #000;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bw-divider">
                            <td class="px-3 py-2">
                                <p class="font-bold text-base" style="color: #000;">{{ $receipt->payment->course->name }}</p>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <p class="font-bold text-base" style="color: #000;">KES {{ number_format($receipt->payment->agreed_amount, 2) }}</p>
                                <p class="text-xs" style="color: #666;">Agreed Amount</p>
                            </td>
                        </tr>
                        <tr class="bw-bg-light bw-divider">
                            <td class="px-3 py-1.5">
                                <p class="text-xs font-medium" style="color: #000;">Cash/M-Pesa Payment</p>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <p class="text-xs font-semibold" style="color: #000;">KES {{ number_format($receipt->payment->amount_paid, 2) }}</p>
                            </td>
                        </tr>
                        @php
                            $walletAmountUsed = $receipt->payment->wallet_amount_used ?? 0;
                            $totalPayment = $receipt->payment->amount_paid + $walletAmountUsed;
                            $overpayment = $receipt->payment->overpayment_amount ?? 0;
                            $balance = max(0, $receipt->payment->agreed_amount - $totalPayment);
                        @endphp
                        @if($walletAmountUsed > 0)
                        <tr class="bw-bg-light bw-divider">
                            <td class="px-3 py-1.5">
                                <p class="text-xs font-medium" style="color: #000;">Amount from Wallet</p>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <p class="text-xs font-bold" style="color: #000;">KES {{ number_format($walletAmountUsed, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                        <tr class="bw-bg-light bw-divider" style="border-top: 2px solid #000;">
                            <td class="px-3 py-1.5">
                                <p class="text-xs font-bold" style="color: #000;">Total Payment</p>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <p class="text-base font-bold" style="color: #000;">KES {{ number_format($totalPayment, 2) }}</p>
                            </td>
                        </tr>
                        @if($balance > 0)
                        <tr class="bw-bg-light bw-divider">
                            <td class="px-3 py-1.5">
                                <p class="text-xs font-medium" style="color: #000;">Balance Due</p>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <p class="text-xs font-bold" style="color: #000;">KES {{ number_format($balance, 2) }}</p>
                            </td>
                        </tr>
                        @elseif($overpayment > 0)
                        <tr class="bw-bg-light bw-divider">
                            <td class="px-3 py-1.5">
                                <p class="text-xs font-medium" style="color: #000;">Credit Added to Wallet</p>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <p class="text-xs font-bold" style="color: #000;">KES {{ number_format($overpayment, 2) }}</p>
                            </td>
                        </tr>
                        @else
                        <tr class="bw-bg-light bw-divider">
                            <td class="px-3 py-1.5">
                                <p class="text-xs font-medium" style="color: #000;">Balance</p>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <p class="text-xs font-bold" style="color: #000;">KES 0.00</p>
                            </td>
                        </tr>
                        @endif
                        <tr class="bw-divider" style="border-top: 2px solid #000;">
                            <td class="px-3 py-2">
                                <p class="font-bold text-base" style="color: #000;">Total Amount Paid</p>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <p class="text-lg font-bold" style="color: #000;">KES {{ number_format($totalPayment, 2) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Method & Additional Info -->
        <div class="grid grid-cols-2 gap-2 mb-2">
            <div class="p-2 rounded-lg bw-box">
                <p class="text-xs mb-0.5 font-bold uppercase" style="color: #666;">Payment Method</p>
                <p class="text-sm font-bold" style="color: #000;">
                    @if($receipt->payment->payment_method === 'mpesa')
                        M-Pesa
                    @elseif($receipt->payment->payment_method === 'bank_transfer')
                        Bank Transfer
                    @else
                        Cash
                    @endif
                </p>
            </div>
            <div class="p-2 rounded-lg bw-box">
                <p class="text-xs mb-0.5 font-bold uppercase" style="color: #666;">Served By</p>
                <p class="text-sm font-bold" style="color: #000;">{{ $receipt->payment->cashier->name }}</p>
            </div>
        </div>

        @if($receipt->payment->notes)
        <div class="mb-2 p-2 bw-bg-light rounded-lg bw-border">
            <p class="text-xs mb-0.5 font-bold uppercase" style="color: #666;">Additional Notes</p>
            <p class="text-xs font-medium" style="color: #000;">{{ $receipt->payment->notes }}</p>
        </div>
        @endif

        <!-- Print Buttons -->
        <div class="no-print mt-8 text-center space-x-4">
            <button onclick="window.print()" class="px-8 py-4 bg-gray-800 text-white rounded-lg font-bold hover:bg-gray-900 transition-colors text-lg shadow-lg">
                Print (Black & White)
            </button>
            <!-- <a href="{{ route('receipts.print', $receipt->id) }}" target="_blank" class="inline-block px-8 py-4 bg-gray-600 text-white rounded-lg font-bold hover:bg-gray-700 transition-colors text-lg shadow-lg">
                View Color Version
            </a> -->
            <!-- <a href="{{ route('receipts.thermal', $receipt->id) }}" target="_blank" class="inline-block px-8 py-4 bg-gray-500 text-white rounded-lg font-bold hover:bg-gray-600 transition-colors text-lg shadow-lg">
                Print (Thermal)
            </a> -->
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


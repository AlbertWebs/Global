<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Income Receipt #{{ $receipt->receipt_number }} - Global College</title>
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
            font-size: 0.65rem;
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
            max-width: 130mm !important;
        }
        @media print {
            @page {
                size: A5;
                margin-top: 20mm !important;
                margin-bottom: 15mm !important;
                margin-left: 5mm !important;
                margin-right: 5mm !important;
            }
            body { 
                margin: 0 !important; 
                padding: 0 !important; 
                height: 175mm !important;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
            }
            .receipt-container {
                width: 100% !important;
                max-width: 130mm !important;
                padding: 0 2mm !important;
            }
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
            * {
                color: #000 !important;
                background: #fff !important;
            }
        }
        .header-section {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-section h1 {
            font-size: 1.4rem;
            font-weight: 900;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-section .subtitle {
            font-size: 0.75rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px dashed #666;
        }
        .receipt-info-item {
            flex: 1;
        }
        .receipt-info-label {
            font-size: 0.7rem;
            color: #666;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .receipt-info-value {
            font-size: 0.85rem;
            font-weight: 700;
            color: #000;
        }
        .income-details {
            background: #f9f9f9;
            border: 2px solid #000;
            padding: 12px;
            margin: 12px 0;
            border-radius: 4px;
        }
        .income-details-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            padding: 4px 0;
        }
        .detail-row:last-child {
            margin-bottom: 0;
            border-top: 1px solid #000;
            padding-top: 6px;
            margin-top: 6px;
        }
        .detail-label {
            font-size: 0.75rem;
            color: #333;
            font-weight: 600;
        }
        .detail-value {
            font-size: 0.85rem;
            font-weight: 700;
            color: #000;
        }
        .amount-highlight {
            font-size: 1.1rem;
            color: #059669;
            font-weight: 900;
        }
        .footer-section {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 2px dashed #666;
            text-align: center;
        }
        .footer-section p {
            font-size: 0.65rem;
            color: #666;
            margin: 4px 0;
        }
        .thank-you {
            text-align: center;
            margin: 12px 0;
            font-size: 0.8rem;
            font-weight: 600;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="max-w-3xl mx-auto bg-white">
            <!-- Header -->
            <div class="header-section">
                <h1>{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
                <p class="subtitle">Income Receipt</p>
            </div>

            <!-- Receipt Info -->
            <div class="receipt-info">
                <div class="receipt-info-item">
                    <div class="receipt-info-label">Receipt No:</div>
                    <div class="receipt-info-value">{{ $receipt->receipt_number }}</div>
                </div>
                <div class="receipt-info-item" style="text-align: right;">
                    <div class="receipt-info-label">Date:</div>
                    <div class="receipt-info-value">{{ $receipt->receipt_date->format('M d, Y') }}</div>
                </div>
            </div>

            <!-- Income Details -->
            <div class="income-details">
                <div class="income-details-title">Income Details</div>
                
                <div class="detail-row">
                    <span class="detail-label">Description:</span>
                    <span class="detail-value">{{ $receipt->otherIncome->description }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">{{ $receipt->otherIncome->payment_method_label }}</span>
                </div>
                
                @if($receipt->otherIncome->notes)
                <div class="detail-row">
                    <span class="detail-label">Notes:</span>
                    <span class="detail-value" style="font-size: 0.7rem;">{{ $receipt->otherIncome->notes }}</span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Amount Received:</span>
                    <span class="detail-value amount-highlight">KES {{ number_format($receipt->otherIncome->amount, 2) }}</span>
                </div>
            </div>

            <!-- Thank You -->
            <div class="thank-you">
                Thank you for your payment!
            </div>

            <!-- Footer -->
            <div class="footer-section">
                <p>Recorded by: {{ $receipt->otherIncome->recorder->name }}</p>
                <p>Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
                <p style="font-size: 0.6rem; margin-top: 6px;">This is a computer-generated receipt</p>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print fixed bottom-4 right-4">
        <button 
            onclick="window.print()" 
            class="px-6 py-3 bg-green-600 text-white rounded-lg shadow-lg hover:bg-green-700 transition-colors font-semibold flex items-center"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Receipt
        </button>
    </div>
</body>
</html>

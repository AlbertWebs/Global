<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $receipt->receipt_number }} - Thermal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.3;
            width: 80mm; /* Standard thermal printer width */
            max-width: 80mm;
            margin: 0 auto;
            padding: 2mm;
            background: white;
        }
        
        @media print {
            body {
                width: 80mm;
                max-width: 80mm;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
            * {
                margin: 0;
                padding: 0;
            }
            img {
                max-width: 70mm;
                max-height: 25mm;
                height: auto;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .section {
            margin: 8px 0;
            padding: 4px 0;
            border-bottom: 1px dashed #ccc;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 11px;
        }
        
        .label {
            font-weight: bold;
        }
        
        .value {
            text-align: right;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        .total {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            margin: 8px 0;
            padding: 4px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        
        .footer {
            text-align: center;
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            font-size: 9px;
        }
        
        .center {
            text-align: center;
        }
        
        .barcode-area {
            text-align: center;
            margin: 8px 0;
            padding: 4px 0;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(\App\Models\Setting::get('receipt_logo') || \App\Models\Setting::get('school_logo'))
        <div style="text-align: center; margin-bottom: 8px;">
            <img src="{{ url('storage/' . (\App\Models\Setting::get('receipt_logo') ?? \App\Models\Setting::get('school_logo'))) }}" alt="School Logo" style="max-width: 60mm; max-height: 30mm; object-fit: contain; display: block; margin: 0 auto;">
        </div>
        @endif
        <h1>{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
        <p>{{ \App\Models\Setting::get('school_address', 'P.O. Box 12345, Nairobi, Kenya') }}</p>
        <p>Tel: {{ \App\Models\Setting::get('school_phone', '+254 700 000 000') }}</p>
        <p>OFFICIAL RECEIPT</p>
    </div>

    <div class="section">
        <div class="row">
            <span class="label">Receipt #:</span>
            <span class="value">{{ $receipt->receipt_number }}</span>
        </div>
        <div class="row">
            <span class="label">Date:</span>
            <span class="value">{{ $receipt->receipt_date->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Student Information</div>
        <div class="row">
            <span>{{ $receipt->payment->student->full_name }}</span>
        </div>
        <div class="row">
            <span>ID: {{ $receipt->payment->student->student_number }}</span>
        </div>
        @if($receipt->payment->student->phone)
        <div class="row">
            <span>Phone: {{ $receipt->payment->student->phone }}</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    <div class="section">
        <div class="section-title">Payment Details</div>
        <div class="row">
            <span class="label">Course:</span>
            <span class="value">{{ $receipt->payment->course->name }}</span>
        </div>
        <div class="row">
            <span class="label">Code:</span>
            <span class="value">{{ $receipt->payment->course->code }}</span>
        </div>
        @if($receipt->payment->agreed_amount)
        <div class="row">
            <span class="label">Amount:</span>
            <span class="value">KES {{ number_format($receipt->payment->agreed_amount, 2) }}</span>
        </div>
        @endif
        <div class="row">
            <span class="label">Paid:</span>
            <span class="value">KES {{ number_format($receipt->payment->amount_paid, 2) }}</span>
        </div>
        @php
            $balance = max(0, ($receipt->payment->agreed_amount ?? 0) - $receipt->payment->amount_paid);
        @endphp
        @if($balance > 0)
        <div class="row">
            <span class="label">Balance:</span>
            <span class="value">KES {{ number_format($balance, 2) }}</span>
        </div>
        @endif
        <div class="row">
            <span class="label">Method:</span>
            <span class="value">
                @if($receipt->payment->payment_method === 'mpesa')
                    M-Pesa
                @elseif($receipt->payment->payment_method === 'bank_transfer')
                    Bank Transfer
                @else
                    Cash
                @endif
            </span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="total">
        TOTAL PAID: KES {{ number_format($receipt->payment->amount_paid, 2) }}
    </div>

    <div class="section">
        <div class="row">
            <span class="label">Served By:</span>
            <span class="value">{{ $receipt->payment->cashier->name }}</span>
        </div>
    </div>

    @if($receipt->payment->notes)
    <div class="section">
        <div class="section-title">Notes</div>
        <div>{{ $receipt->payment->notes }}</div>
    </div>
    @endif

    <div class="barcode-area">
        <div>{{ $receipt->receipt_number }}</div>
    </div>

    <div class="footer">
        <p>Thank you for your payment!</p>
        <p>Keep this receipt for your records</p>
        <p>For inquiries: {{ \App\Models\Setting::get('school_email', 'info@globalcollege.edu') }}</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Print Button -->
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Print Thermal Receipt
        </button>
    </div>

    <script>
        // Auto-print for thermal printers
        window.onload = function() {
            // Uncomment to auto-print
            // setTimeout(function() { window.print(); }, 500);
        }
    </script>
</body>
</html>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #000000 !important; /* Dark text on white paper */
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 25px 20px;
            background: #1e40af !important; /* Solid blue background for visibility */
            border: 2px solid #1e3a8a;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #ffffff !important;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            color: #ffffff !important;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .summary-row.total {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th,         td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            color: #000000 !important; /* Dark text on white paper */
        }
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
        <p>Financial Report</p>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>

    <div class="summary">
        <h2>Financial Summary</h2>
        <div class="summary-row">
            <span>Total Payments:</span>
            <span>{{ $summary['total_payments'] }}</span>
        </div>
        <div class="summary-row">
            <span>Total Income:</span>
            <span>KES {{ number_format($summary['total_amount_paid'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span>Total Expenses:</span>
            <span>KES {{ number_format($summary['total_expenses'], 2) }}</span>
        </div>
        <div class="summary-row total">
            <span>Net Income:</span>
            <span>KES {{ number_format($summary['net_income'], 2) }}</span>
        </div>
    </div>

    <div class="summary">
        <h2>Payment Method Breakdown</h2>
        <div class="summary-row">
            <span>M-Pesa:</span>
            <span>KES {{ number_format($paymentMethodBreakdown['mpesa'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span>Cash:</span>
            <span>KES {{ number_format($paymentMethodBreakdown['cash'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span>Bank Transfer:</span>
            <span>KES {{ number_format($paymentMethodBreakdown['bank_transfer'], 2) }}</span>
        </div>
    </div>

    @if($payments->count() > 0)
    <h2>Payment Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student</th>
                <th>Course</th>
                <th>Payment Method</th>
                <th class="text-right">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                <td>{{ $payment->student->full_name }}</td>
                <td>{{ $payment->course->name }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                <td class="text-right">KES {{ number_format($payment->amount_paid, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="text-right">KES {{ number_format($summary['total_amount_paid'], 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($expenses->count() > 0)
    <h2>Expense Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Description</th>
                <th>Payment Method</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                <td>{{ $expense->title }}</td>
                <td>{{ $expense->description ?? 'N/A' }}</td>
                <td>{{ $expense->payment_method_label }}</td>
                <td class="text-right">KES {{ number_format($expense->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="4" class="text-right">TOTAL EXPENSES:</td>
                <td class="text-right">KES {{ number_format($summary['total_expenses'], 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>This is a computer-generated report from Global College Billing System</p>
        <p>Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>

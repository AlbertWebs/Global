<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Income Statement - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #000000 !important;
            background: #ffffff;
            padding: 30px 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 20px;
            background: #1e40af !important;
            border: 2px solid #1e3a8a;
            border-radius: 8px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .subtitle {
            font-size: 16px;
            color: #ecf0f1;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .header .period {
            font-size: 13px;
            color: #ecf0f1;
            margin-top: 10px;
            opacity: 0.95;
        }
        .header .generated {
            font-size: 11px;
            color: #ecf0f1;
            margin-top: 8px;
            opacity: 0.85;
        }
        .summary-section {
            margin: 30px 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .summary-card {
            background: #f8f9fa;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }
        .summary-card.income {
            border-color: #059669;
        }
        .summary-card.expenses {
            border-color: #dc2626;
        }
        .summary-card.net-profit {
            grid-column: 1 / -1;
            background: #1e40af;
            border-color: #1e3a8a;
        }
        .summary-card.net-profit .summary-label,
        .summary-card.net-profit .summary-value {
            color: #ffffff !important;
        }
        .summary-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: 700;
            color: #000000 !important;
        }
        .summary-card.income .summary-value {
            color: #059669 !important;
        }
        .summary-card.expenses .summary-value {
            color: #dc2626 !important;
        }
        .table-container {
            margin: 30px 0;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        thead {
            background: #1e40af !important;
        }
        th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
        th.text-right {
            text-align: right;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
            color: #000000 !important;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background: #1e40af !important;
            font-weight: 700;
            color: #ffffff !important;
        }
        .total-row td {
            color: #ffffff;
            border-bottom: none;
            padding: 14px 16px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
        <div class="subtitle">Income Statement</div>
        <div class="period">
            @if($dateFrom && $dateTo)
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}
            @elseif($dateFrom)
            From: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }}
            @elseif($dateTo)
            Until: {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}
            @else
            All Time
            @endif
        </div>
        <div class="generated">Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-section">
        <div class="summary-card income">
            <div class="summary-label">Total Income</div>
            <div class="summary-value">KES {{ number_format($totalIncome, 2) }}</div>
        </div>
        <div class="summary-card expenses">
            <div class="summary-label">Total Expenses</div>
            <div class="summary-value">KES {{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="summary-card net-profit">
            <div class="summary-label">Net Profit / Loss</div>
            <div class="summary-value">KES {{ number_format($netProfit, 2) }}</div>
        </div>
    </div>

    <!-- Income Breakdown -->
    <div class="table-container">
        <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: #000000;">Income Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th class="text-right">Amount (KES)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $incomeTotal = 0;
                @endphp
                @foreach($payments as $payment)
                <tr>
                    <td>Fee Payment</td>
                    <td>{{ $payment->student->full_name }} - {{ $payment->course->name }}</td>
                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                    <td class="text-right">{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
                @php $incomeTotal += $payment->amount_paid; @endphp
                @endforeach
                @foreach($otherIncomes as $income)
                <tr>
                    <td>Other Income</td>
                    <td>{{ $income->description }}</td>
                    <td>{{ $income->income_date->format('M d, Y') }}</td>
                    <td class="text-right">{{ number_format($income->amount, 2) }}</td>
                </tr>
                @php $incomeTotal += $income->amount; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right"><strong>Total Income:</strong></td>
                    <td class="text-right"><strong>{{ number_format($incomeTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Expenses Breakdown -->
    <div class="table-container">
        <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: #000000;">Expenses Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th class="text-right">Amount (KES)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $expensesTotal = 0;
                @endphp
                @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->title }}</td>
                    <td>{{ Str::limit($expense->description ?? 'N/A', 50) }}</td>
                    <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                    <td class="text-right">{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @php $expensesTotal += $expense->amount; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right"><strong>Total Expenses:</strong></td>
                    <td class="text-right"><strong>{{ number_format($expensesTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated report from Global College Billing System</p>
        <p>Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>

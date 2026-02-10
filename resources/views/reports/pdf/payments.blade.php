<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Payment Report - {{ $dateFrom }} to {{ $dateTo }}</title>
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
            color: #000000 !important; /* Dark text on white paper */
            background: #ffffff;
            padding: 30px 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 20px;
            background: #1e40af !important; /* Solid blue background for visibility */
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
        }
        thead {
            background: #1e40af !important; /* Solid blue background for visibility */
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
        th:first-child {
            border-top-left-radius: 8px;
        }
        th:last-child {
            border-top-right-radius: 8px;
        }
        tbody tr {
            transition: background-color 0.2s ease;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        tbody tr:hover {
            background-color: #e8f4f8;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
            color: #000000 !important; /* Dark text on white paper */
            font-size: 11px;
        }
        tbody tr:last-child td {
            border-bottom: none;
        }
        .total-row {
            background: #1e40af !important; /* Solid blue background for visibility */
            font-weight: 700;
            color: #ffffff !important;
        }
        .total-row td {
            color: #ffffff;
            border-bottom: none;
            padding: 14px 16px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
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
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
            font-size: 14px;
            font-style: italic;
        }
        .payment-date {
            color: #7f8c8d;
            font-size: 10px;
        }
        .student-name {
            font-weight: 500;
            color: #2c3e50;
        }
        .admission-number {
            font-weight: 600;
            color: #3498db;
        }
        .course-name {
            color: #27ae60;
        }
        .payment-method {
            color: #8e44ad;
            text-transform: capitalize;
        }
        .amount-paid {
            font-weight: 600;
            color: #27ae60;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Setting::get('school_name', 'Global College') }}</h1>
        <div class="subtitle">Fee Payment Report</div>
        <div class="period">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}
        </div>
        <div class="generated">Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</div>
    </div>

    @if($payments->count() > 0)
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 25%;">Student Name</th>
                    <th style="width: 15%;">Admission Number</th>
                    <th style="width: 25%;">Course</th>
                    <th style="width: 13%;">Payment Method</th>
                    <th style="width: 10%;" class="text-right">Amount Paid (KES)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td class="payment-date">{{ $payment->created_at->format('M d, Y') }}</td>
                    <td class="student-name">{{ $payment->student->full_name }}</td>
                    <td class="admission-number">{{ $payment->student->admission_number ?? 'N/A' }}</td>
                    <td class="course-name">{{ $payment->course->name }}</td>
                    <td class="payment-method">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td class="text-right amount-paid">{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-right" style="font-size: 12px;">TOTAL PAYMENTS:</td>
                    <td class="text-right" style="font-size: 12px;">{{ number_format($payments->sum('amount_paid'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="no-data">
        <p>No payments found for the selected period.</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated report from Global College Billing System</p>
        <p>Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Records - {{ $student->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.7;
            color: #000000 !important;
            background: #ffffff;
            padding: 35px 45px;
            min-height: 100vh;
        }
        .header {
            text-align: center;
            margin-bottom: 45px;
            padding: 35px 25px;
            background: #1e40af !important;
            border: 2px solid #1e3a8a;
            border-radius: 12px;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .header .subtitle {
            font-size: 18px;
            color: #ecf0f1;
            margin-bottom: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .header .student-info {
            font-size: 15px;
            color: #ffffff;
            margin-top: 15px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: inline-block;
            backdrop-filter: blur(10px);
        }
        .header .generated {
            font-size: 11px;
            color: #e0e7ff;
            margin-top: 12px;
            opacity: 0.9;
            font-weight: 500;
        }
        .table-container {
            margin: 35px 0;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        thead {
            background: #1e40af !important;
        }
        th {
            padding: 16px 14px;
            text-align: left;
            font-weight: 700;
            font-size: 12px;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
        }
        th.text-right {
            text-align: right;
        }
        th:first-child {
            border-top-left-radius: 12px;
        }
        th:last-child {
            border-top-right-radius: 12px;
        }
        tbody tr {
            transition: background-color 0.2s ease;
        }
        tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.98);
        }
        tbody tr:nth-child(odd) {
            background-color: rgba(255, 255, 255, 0.95);
        }
        tbody tr:hover {
            background-color: rgba(255, 255, 255, 1);
        }
        td {
            padding: 14px 14px;
            border-bottom: 1px solid #e5e7eb;
            color: #000000 !important;
            font-size: 12px;
            vertical-align: top;
        }
        td.text-right {
            text-align: right;
            font-weight: 600;
        }
        .date-cell {
            color: #1e40af !important;
            font-weight: 600;
        }
        .student-cell {
            color: #1e3a8a !important;
            font-weight: 700;
        }
        .course-cell {
            color: #2563eb !important;
            font-weight: 600;
        }
        .description-cell {
            color: #000000 !important;
            max-width: 300px;
        }
        .amount-cell {
            color: #059669 !important;
            font-weight: 700;
            font-size: 13px;
        }
        .total-row {
            background: #1e40af !important;
            font-weight: 800;
        }
        .total-row td {
            border-top: 3px solid #ffffff;
            border-bottom: none;
            padding: 16px 14px;
            font-size: 13px;
            color: #ffffff;
        }
        .total-row td:first-child {
            border-bottom-left-radius: 12px;
        }
        .total-row td:last-child {
            border-bottom-right-radius: 12px;
        }
        .no-data {
            text-align: center;
            padding: 80px 20px;
            color: #000000 !important;
            font-size: 18px;
            font-weight: 500;
            background: #f3f4f6;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 55px;
            padding-top: 25px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280 !important;
            font-size: 11px;
        }
        .footer p {
            margin: 6px 0;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Records</h1>
        <div class="subtitle">Global College Billing System</div>
        <div class="student-info">
            <strong>Student:</strong> {{ $student->full_name }}<br>
            <strong>Admission Number:</strong> {{ $student->admission_number ?? 'N/A' }}
        </div>
        <div class="generated">
            Generated on {{ now()->format('F d, Y \a\t h:i A') }}
        </div>
    </div>

    @if($paymentLogs->count() > 0)
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 25%;">Student</th>
                    <th style="width: 20%;">Course</th>
                    <th style="width: 25%;">Description</th>
                    <th style="width: 15%;" class="text-right">Amount Paid (KES)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentLogs as $log)
                <tr>
                    <td class="date-cell">{{ $log->payment_date->format('M d, Y') }}</td>
                    <td class="student-cell">{{ $log->student ? $log->student->full_name : 'Student Deleted' }}</td>
                    <td class="course-cell">{{ $log->course->name ?? 'N/A' }}</td>
                    <td class="description-cell">{{ $log->description }}</td>
                    <td class="text-right amount-cell">{{ number_format($log->amount_paid, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right" style="font-size: 13px;"><strong>TOTAL:</strong></td>
                    <td class="text-right" style="font-size: 14px;"><strong>{{ number_format($paymentLogs->sum('amount_paid'), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="no-data">
        <p>No payment records found for this student.</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated report from Global College Billing System</p>
        <p>Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>

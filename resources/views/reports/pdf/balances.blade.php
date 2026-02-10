<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balances Report</title>
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
        <p>Balances Report - Students with Outstanding Balances</p>
        @if($dateFrom && $dateTo)
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}</p>
        @elseif($dateFrom)
        <p>From: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }}</p>
        @elseif($dateTo)
        <p>Until: {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}</p>
        @else
        <p>All Students with Outstanding Balances</p>
        @endif
        <p>Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>

    @if($students->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Admission Number</th>
                <th>Student Name</th>
                <th>Total Payments</th>
                <th class="text-right">Total Amount Paid (KES)</th>
                <th class="text-right">Outstanding Balance (KES)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPaid = 0;
                $totalOutstanding = 0;
            @endphp
            @foreach($students as $student)
            @php
                $totalPaidByStudent = $student->payments->sum('amount_paid');
                $totalOutstandingByStudent = 0;
                
                // Calculate outstanding balance from balances table
                $balances = \App\Models\Balance::where('student_id', $student->id)->get();
                $totalOutstandingByStudent = $balances->sum('outstanding_balance');
                
                $totalPaid += $totalPaidByStudent;
                $totalOutstanding += $totalOutstandingByStudent;
            @endphp
            <tr>
                <td>{{ $student->admission_number ?? 'N/A' }}</td>
                <td>{{ $student->full_name }}</td>
                <td>{{ $student->payments->count() }}</td>
                <td class="text-right">{{ number_format($totalPaidByStudent, 2) }}</td>
                <td class="text-right">{{ number_format($totalOutstandingByStudent, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="3" class="text-right">TOTALS:</td>
                <td class="text-right">{{ number_format($totalPaid, 2) }}</td>
                <td class="text-right">{{ number_format($totalOutstanding, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <p>No students with outstanding balances found for the selected period.</p>
    @endif

    <div class="footer">
        <p>This is a computer-generated report from Global College Billing System</p>
        <p>Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>

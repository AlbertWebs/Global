<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Registrations Report - {{ $dateFrom }} to {{ $dateTo }}</title>
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
        <p>Course Registrations Report</p>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>

    @if($registrations->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Registration Date</th>
                <th>Student Name</th>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Academic Year</th>
                <th>Term</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                <td>{{ $registration->registration_date->format('Y-m-d') }}</td>
                <td>{{ $registration->student->full_name }}</td>
                <td>{{ $registration->course->code }}</td>
                <td>{{ $registration->course->name }}</td>
                <td>{{ $registration->academic_year ?? 'N/A' }}</td>
                <td>{{ $registration->term ?? 'N/A' }}</td>
                <td>{{ ucfirst($registration->status ?? 'active') }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="6" class="text-right">TOTAL REGISTRATIONS:</td>
                <td>{{ $registrations->count() }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <p>No course registrations found for the selected period.</p>
    @endif

    <div class="footer">
        <p>This is a computer-generated report from Global College Billing System</p>
        <p>Report generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>

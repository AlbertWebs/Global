<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - {{ $schoolName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .payment-details {
            background: white;
            padding: 20px;
            border-left: 4px solid #10b981;
            margin: 20px 0;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Confirmation</h1>
    </div>
    <div class="content">
        <p>Dear {{ $student->full_name }},</p>
        
        <p>This email confirms that we have received your payment.</p>
        
        <div class="payment-details">
            <h3>Payment Details:</h3>
            <p><strong>Receipt Number:</strong> {{ $receiptNumber }}</p>
            <p><strong>Course:</strong> {{ $courseName }}</p>
            <p><strong>Amount Paid:</strong></p>
            <p class="amount">KES {{ number_format($amount, 2) }}</p>
            <p><strong>Date:</strong> {{ now()->format('F d, Y') }}</p>
        </div>
        
        <p>Thank you for your payment. Please keep this confirmation for your records.</p>
        
        <p>If you have any questions or concerns about this payment, please contact us:</p>
        <ul>
            @if($schoolEmail)
            <li>Email: {{ $schoolEmail }}</li>
            @endif
            @if($schoolPhone)
            <li>Phone: {{ $schoolPhone }}</li>
            @endif
        </ul>
        
        <p>Best regards,<br>{{ $schoolName }} Administration</p>
    </div>
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>


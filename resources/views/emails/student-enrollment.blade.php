<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $schoolName }}</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .credentials {
            background: white;
            padding: 20px;
            border-left: 4px solid #667eea;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
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
        <h1>Welcome to {{ $schoolName }}!</h1>
    </div>
    <div class="content">
        <p>Dear {{ $student->full_name }},</p>
        
        <p>Congratulations! You have been successfully enrolled at {{ $schoolName }}.</p>
        
        <div class="credentials">
            <h3>Your Login Credentials:</h3>
            <p><strong>Student Number:</strong> {{ $student->student_number }}</p>
            <p><strong>Username:</strong> {{ $student->student_number }}</p>
            <p><strong>Password:</strong> {{ $student->student_number }}</p>
            <p><em>Please change your password after your first login for security.</em></p>
        </div>
        
        <p>You can access your student portal using the following link:</p>
        <p style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Access Student Portal</a>
        </p>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact us:</p>
        <ul>
            @if($schoolEmail)
            <li>Email: {{ $schoolEmail }}</li>
            @endif
            @if($schoolPhone)
            <li>Phone: {{ $schoolPhone }}</li>
            @endif
        </ul>
        
        <p>We look forward to having you as part of our community!</p>
        
        <p>Best regards,<br>{{ $schoolName }} Administration</p>
    </div>
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>


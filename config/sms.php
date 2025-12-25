<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Provider
    |--------------------------------------------------------------------------
    |
    | This option controls which SMS provider to use. Supported providers:
    | - africastalking: AfricasTalking SMS API
    | - twilio: Twilio SMS API
    | - zettatel: Zettatel SMS API
    | - log: Log SMS messages (for development/testing)
    |
    */

    'provider' => env('SMS_PROVIDER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum number of SMS messages that can be sent to the same phone number
    | within the rate limit window (default: 1 hour).
    |
    */

    'rate_limit' => env('SMS_RATE_LIMIT', 50), // Max 50 SMS per hour per number

    /*
    |--------------------------------------------------------------------------
    | AfricasTalking Configuration
    |--------------------------------------------------------------------------
    |
    | The SMS_API environment variable is used as the API key for AfricasTalking.
    | Set SMS_API in your .env file with your AfricasTalking API key.
    |
    */

    'africastalking' => [
        'username' => env('AFRICASTALKING_USERNAME'),
        'api_key' => env('SMS_API'), // Use SMS_API token from .env
        'sender_id' => env('AFRICASTALKING_SENDER_ID', 'SCHOOL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    */

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN', env('SMS_API')), // Fallback to SMS_API if TWILIO_AUTH_TOKEN not set
        'from_number' => env('TWILIO_FROM_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Zettatel Configuration
    |--------------------------------------------------------------------------
    */

    'zettatel' => [
        'userid' => env('ZETTATEL_USERID', env('SMS_API')), // User ID/Username, fallback to SMS_API if not set
        'password' => env('ZETTATEL_PASSWORD', env('SMS_PASSWORD')), // Password for authentication
        'sender_id' => env('ZETTATEL_SENDER_ID', 'SCHOOL'),
        'base_url' => env('ZETTATEL_BASE_URL', 'https://portal.zettatel.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Templates
    |--------------------------------------------------------------------------
    |
    | These templates are used for sending SMS notifications.
    | Available placeholders:
    | - {student_name}: Full name of the student
    | - {first_name}: First name of the student
    | - {last_name}: Last name of the student
    | - {student_number}: Student number
    | - {admission_number}: Admission number
    | - {phone}: Phone number
    | - {school_name}: School name (from settings)
    | - {amount}: Payment amount (for payment SMS)
    | - {course_name}: Course name (for payment SMS)
    | - {receipt_number}: Receipt number (for payment SMS)
    | - {login_url}: Student portal login URL
    | - {username}: Student portal username (student number)
    | - {password}: Student portal password (student number)
    |
    */

    'templates' => [
        'enrollment' => env('SMS_TEMPLATE_ENROLLMENT', 
            "Welcome {student_name}! You have been enrolled at {school_name}. Student No: {student_number}. Access your portal: {login_url} Login: {username} Password: {password}"
        ),
        
        'payment' => env('SMS_TEMPLATE_PAYMENT',
            "Dear {student_name}, payment of KES {amount} for {course_name} has been received. Receipt Number: {receipt_number}. Thank you for your payment!"
        ),
        
        'teacher_enrollment' => env('SMS_TEMPLATE_TEACHER_ENROLLMENT',
            "Welcome {teacher_name}! You have been onboarded at {school_name}. Employee No: {employee_number}. Access your portal: {login_url} Username: {username} Password: {password}"
        ),
    ],
];


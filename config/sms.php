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

    'rate_limit' => env('SMS_RATE_LIMIT', 5), // Max 5 SMS per hour per number

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
        'api_key' => env('SMS_API'), // Use SMS_API token from .env
        'sender_id' => env('ZETTATEL_SENDER_ID', 'SCHOOL'),
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
    |
    */

    'templates' => [
        'enrollment' => env('SMS_TEMPLATE_ENROLLMENT', 
            "Welcome {student_name}! You have been successfully enrolled at {school_name}. Your student number is {student_number}. We look forward to your success!"
        ),
        
        'payment' => env('SMS_TEMPLATE_PAYMENT',
            "Dear {student_name}, payment of KES {amount} for {course_name} has been received. Receipt Number: {receipt_number}. Thank you for your payment!"
        ),
    ],
];


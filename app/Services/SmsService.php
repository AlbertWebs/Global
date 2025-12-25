<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\SmsProviders\AfricasTalkingProvider;
use App\Services\SmsProviders\LogProvider;
use App\Services\SmsProviders\TwilioProvider;
use App\Services\SmsProviders\ZettatelProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SmsService
{
    protected SmsProviderInterface $provider;
    protected PhoneNumberFormatter $formatter;

    public function __construct(?SmsProviderInterface $provider = null)
    {
        $this->formatter = new PhoneNumberFormatter();
        $this->provider = $provider ?? $this->resolveProvider();
    }

    /**
     * Send SMS to a student
     * 
     * @param string $message The message to send
     * @param Student $student The student to send the message to
     * @return bool
     */
    public function sendSMSs($message, $student): bool
    {
        try {
            // Validate student has phone number
            if (!$student->phone) {
                Log::warning("SMS not sent: Student {$student->id} has no phone number", [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                ]);
                return false;
            }

            // Validate phone number format
            if (!PhoneNumberFormatter::isValid($student->phone)) {
                Log::warning("SMS not sent: Invalid phone number format", [
                    'student_id' => $student->id,
                    'phone' => $student->phone,
                ]);
                return false;
            }

            // Format phone number to international format
            $phoneNumber = PhoneNumberFormatter::format($student->phone);
            
            // Remove plus sign before sending (some providers don't accept it)
            $phoneNumberForSending = ltrim($phoneNumber, '+');
            
            // Replace placeholders in message
            $formattedMessage = $this->replacePlaceholders($message, $student);

            // Check rate limiting (use original format for cache key)
            if ($this->isRateLimited($phoneNumber)) {
                Log::warning("SMS rate limited", [
                    'student_id' => $student->id,
                    'phone' => $phoneNumber,
                ]);
                return false;
            }

            // Send SMS via provider (without plus sign)
            $result = $this->provider->send($phoneNumberForSending, $formattedMessage);

            // Record rate limit
            $this->recordRateLimit($phoneNumber);

            if ($result['success']) {
                // Log successful SMS
                Log::info("SMS sent successfully", [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'phone' => $phoneNumber,
                    'message_id' => $result['message_id'] ?? null,
                    'provider' => config('sms.provider', 'log'),
                ]);

                return true;
            } else {
                Log::error("SMS send failed", [
                    'student_id' => $student->id,
                    'phone' => $phoneNumber,
                    'error' => $result['error'] ?? 'Unknown error',
                    'provider_response' => $result['provider_response'] ?? null,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception while sending SMS", [
                'student_id' => $student->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send enrollment confirmation SMS
     * 
     * @param Student $student
     * @return bool
     */
    public function sendEnrollmentSMS(Student $student): bool
    {
        $message = config('sms.templates.enrollment', 
            "Welcome {student_name}! You have been enrolled at {school_name}. Student No: {student_number}. Login to your portal at {login_url} Username: {username} Password: {password}"
        );

        // Replace school name placeholder
        $schoolName = \App\Models\Setting::get('school_name', 'Global College');
        $message = str_replace('{school_name}', $schoolName, $message);
        
        // Replace login credentials placeholders
        $loginUrl = url('/student/login');
        $message = str_replace('{login_url}', $loginUrl, $message);
        $message = str_replace('{username}', $student->student_number, $message);
        $message = str_replace('{password}', $student->student_number, $message);

        return $this->sendSMSs($message, $student);
    }

    /**
     * Send payment confirmation SMS
     * 
     * @param Student $student
     * @param float $amount
     * @param string $courseName
     * @param string $receiptNumber
     * @return bool
     */
    public function sendPaymentSMS(Student $student, float $amount, string $courseName, string $receiptNumber): bool
    {
        $message = config('sms.templates.payment',
            "Dear {student_name}, payment of KES {amount} for {course_name} has been received. Receipt Number: {receipt_number}. Thank you for your payment!"
        );

        // Replace payment-specific placeholders
        $message = str_replace('{amount}', number_format($amount, 2), $message);
        $message = str_replace('{course_name}', $courseName, $message);
        $message = str_replace('{receipt_number}', $receiptNumber, $message);

        return $this->sendSMSs($message, $student);
    }

    /**
     * Send teacher enrollment/welcome SMS
     * 
     * @param Teacher $teacher
     * @return bool
     */
    public function sendTeacherEnrollmentSMS(Teacher $teacher): bool
    {
        try {
            // Validate teacher has phone number
            if (!$teacher->phone) {
                Log::warning("SMS not sent: Teacher {$teacher->id} has no phone number", [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->full_name,
                ]);
                return false;
            }

            // Validate phone number format
            if (!PhoneNumberFormatter::isValid($teacher->phone)) {
                Log::warning("SMS not sent: Invalid phone number format", [
                    'teacher_id' => $teacher->id,
                    'phone' => $teacher->phone,
                ]);
                return false;
            }

            // Format phone number to international format
            $phoneNumber = PhoneNumberFormatter::format($teacher->phone);
            
            // Remove plus sign before sending (some providers don't accept it)
            $phoneNumberForSending = ltrim($phoneNumber, '+');

            // Get message template
            $message = config('sms.templates.teacher_enrollment', 
                "Welcome {teacher_name}! You have been onboarded at {school_name}. Employee No: {employee_number}. Access your portal: {login_url} Username: {username} Password: {password}"
            );

            // Replace placeholders
            $schoolName = \App\Models\Setting::get('school_name', 'Global College');
            $loginUrl = route('teacher.login');
            $username = $teacher->employee_number;
            $password = $teacher->employee_number; // Default password is employee number

            $message = str_replace('{teacher_name}', $teacher->full_name, $message);
            $message = str_replace('{school_name}', $schoolName, $message);
            $message = str_replace('{employee_number}', $teacher->employee_number, $message);
            $message = str_replace('{login_url}', $loginUrl, $message);
            $message = str_replace('{username}', $username, $message);
            $message = str_replace('{password}', $password, $message);

            // Check rate limiting (use original format for cache key)
            if ($this->isRateLimited($phoneNumber)) {
                Log::warning("SMS rate limited", [
                    'teacher_id' => $teacher->id,
                    'phone' => $phoneNumber,
                ]);
                return false;
            }

            // Send SMS via provider (without plus sign)
            $result = $this->provider->send($phoneNumberForSending, $message);

            // Record rate limit
            $this->recordRateLimit($phoneNumber);

            if ($result['success']) {
                Log::info("Teacher enrollment SMS sent successfully", [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->full_name,
                    'phone' => $phoneNumber,
                    'message_id' => $result['message_id'] ?? null,
                    'provider' => config('sms.provider', 'log'),
                ]);

                return true;
            } else {
                Log::error("Teacher enrollment SMS send failed", [
                    'teacher_id' => $teacher->id,
                    'phone' => $phoneNumber,
                    'error' => $result['error'] ?? 'Unknown error',
                    'provider_response' => $result['provider_response'] ?? null,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception while sending teacher enrollment SMS", [
                'teacher_id' => $teacher->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Replace placeholders in message with student data
     * 
     * @param string $message
     * @param Student $student
     * @return string
     */
    protected function replacePlaceholders(string $message, Student $student): string
    {
        $schoolName = \App\Models\Setting::get('school_name', 'Global College');
        $schoolPhone = \App\Models\Setting::get('school_phone', '');
        $loginUrl = url('/student/login');

        $placeholders = [
            '{student_name}' => $student->full_name,
            '{first_name}' => $student->first_name,
            '{last_name}' => $student->last_name,
            '{student_number}' => $student->student_number,
            '{admission_number}' => $student->admission_number ?? '',
            '{phone}' => $student->phone ?? '',
            '{school_name}' => $schoolName,
            '{school_phone}' => $schoolPhone,
            '{login_url}' => $loginUrl,
            '{username}' => $student->student_number,
            '{password}' => $student->student_number, // Default password is student number
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $message);
    }

    /**
     * Send SMS to a custom phone number (not tied to a student or teacher)
     * 
     * @param string $phoneNumber The phone number to send to
     * @param string $message The message to send
     * @return bool
     */
    public function sendToPhoneNumber(string $phoneNumber, string $message): bool
    {
        try {
            // Validate phone number format
            if (!PhoneNumberFormatter::isValid($phoneNumber)) {
                Log::warning("SMS not sent: Invalid phone number format", [
                    'phone' => $phoneNumber,
                ]);
                return false;
            }

            // Format phone number to international format
            $formattedPhone = PhoneNumberFormatter::format($phoneNumber);
            
            // Remove plus sign before sending (some providers don't accept it)
            $phoneNumberForSending = ltrim($formattedPhone, '+');

            // Check rate limiting (use original format for cache key)
            if ($this->isRateLimited($formattedPhone)) {
                Log::warning("SMS rate limited", [
                    'phone' => $formattedPhone,
                ]);
                return false;
            }

            // Send SMS via provider (without plus sign)
            $result = $this->provider->send($phoneNumberForSending, $message);

            // Record rate limit
            $this->recordRateLimit($formattedPhone);

            if ($result['success']) {
                // Log successful SMS
                Log::info("SMS sent successfully to custom number", [
                    'phone' => $formattedPhone,
                    'message_id' => $result['message_id'] ?? null,
                    'provider' => config('sms.provider', 'log'),
                ]);

                return true;
            } else {
                Log::error("SMS send failed to custom number", [
                    'phone' => $formattedPhone,
                    'error' => $result['error'] ?? 'Unknown error',
                    'provider_response' => $result['provider_response'] ?? null,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception while sending SMS to custom number", [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Resolve SMS provider based on configuration
     * 
     * @return SmsProviderInterface
     */
    protected function resolveProvider(): SmsProviderInterface
    {
        $provider = config('sms.provider', 'log');

        return match ($provider) {
            'africastalking' => new AfricasTalkingProvider(),
            'twilio' => new TwilioProvider(),
            'zettatel' => new ZettatelProvider(),
            default => new LogProvider(),
        };
    }

    /**
     * Check if phone number is rate limited
     * 
     * @param string $phoneNumber
     * @return bool
     */
    protected function isRateLimited(string $phoneNumber): bool
    {
        $rateLimit = config('sms.rate_limit', 50); // Max 50 SMS per hour per number
        $key = "sms_rate_limit:{$phoneNumber}";
        
        $count = Cache::get($key, 0);
        
        return $count >= $rateLimit;
    }

    /**
     * Record SMS send for rate limiting
     * 
     * @param string $phoneNumber
     * @return void
     */
    protected function recordRateLimit(string $phoneNumber): void
    {
        try {
            $key = "sms_rate_limit:{$phoneNumber}";
            $ttl = 3600; // Rate limit window: 1 hour in seconds
            
            // Use increment if key exists, otherwise set it
            if (Cache::has($key)) {
                Cache::increment($key);
            } else {
                Cache::put($key, 1, $ttl);
            }
        } catch (\Exception $e) {
            // If cache fails (e.g., SQLite compatibility issues), log and continue
            // Rate limiting is not critical enough to fail SMS sending
            Log::warning("Failed to record SMS rate limit", [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send custom SMS to a teacher
     * 
     * @param string $message The message to send
     * @param Teacher $teacher The teacher to send the message to
     * @return bool
     */
    public function sendTeacherSMS(string $message, Teacher $teacher): bool
    {
        try {
            // Validate teacher has phone number
            if (!$teacher->phone) {
                Log::warning("SMS not sent: Teacher {$teacher->id} has no phone number", [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->full_name,
                ]);
                return false;
            }

            // Validate phone number format
            if (!PhoneNumberFormatter::isValid($teacher->phone)) {
                Log::warning("SMS not sent: Invalid phone number format", [
                    'teacher_id' => $teacher->id,
                    'phone' => $teacher->phone,
                ]);
                return false;
            }

            // Format phone number to international format
            $phoneNumber = PhoneNumberFormatter::format($teacher->phone);
            
            // Remove plus sign before sending (some providers don't accept it)
            $phoneNumberForSending = ltrim($phoneNumber, '+');

            // Check rate limiting (use original format for cache key)
            if ($this->isRateLimited($phoneNumber)) {
                Log::warning("SMS rate limited", [
                    'teacher_id' => $teacher->id,
                    'phone' => $phoneNumber,
                ]);
                return false;
            }

            // Send SMS via provider (without plus sign)
            $result = $this->provider->send($phoneNumberForSending, $message);

            // Record rate limit
            $this->recordRateLimit($phoneNumber);

            if ($result['success']) {
                Log::info("Teacher SMS sent successfully", [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->full_name,
                    'phone' => $phoneNumber,
                    'message_id' => $result['message_id'] ?? null,
                    'provider' => config('sms.provider', 'log'),
                ]);

                return true;
            } else {
                Log::error("Teacher SMS send failed", [
                    'teacher_id' => $teacher->id,
                    'phone' => $phoneNumber,
                    'error' => $result['error'] ?? 'Unknown error',
                    'provider_response' => $result['provider_response'] ?? null,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception while sending teacher SMS", [
                'teacher_id' => $teacher->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Send SMS to multiple students (batch)
     * 
     * @param string $message
     * @param array $students Array of Student models
     * @return array Results with success/failure for each student
     */
    public function sendBatchSMSs(string $message, array $students): array
    {
        $results = [];

        foreach ($students as $student) {
            $results[$student->id] = [
                'student' => $student->full_name,
                'phone' => $student->phone,
                'success' => $this->sendSMSs($message, $student),
            ];
        }

        return $results;
    }
}

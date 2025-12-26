<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send enrollment confirmation email to student
     * 
     * @param Student $student
     * @return bool
     */
    public function sendEnrollmentEmail(Student $student): bool
    {
        try {
            if (!$student->email) {
                Log::warning("Email not sent: Student {$student->id} has no email address", [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                ]);
                return false;
            }

            $schoolName = Setting::get('school_name', 'Global College');
            $schoolEmail = Setting::get('school_email', config('mail.from.address'));
            $schoolPhone = Setting::get('school_phone', '');
            $loginUrl = url('/student/login');

            $subject = "Welcome to {$schoolName} - Enrollment Confirmation";
            
            $message = view('emails.student-enrollment', [
                'student' => $student,
                'schoolName' => $schoolName,
                'schoolEmail' => $schoolEmail,
                'schoolPhone' => $schoolPhone,
                'loginUrl' => $loginUrl,
            ])->render();

            Mail::html($message, function($mail) use ($student, $subject, $schoolEmail, $schoolName) {
                $mail->to($student->email, $student->full_name)
                     ->subject($subject)
                     ->from($schoolEmail, $schoolName);
            });

            Log::info("Enrollment email sent successfully", [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'email' => $student->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send enrollment email", [
                'student_id' => $student->id,
                'email' => $student->email ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send payment confirmation email to student
     * 
     * @param Student $student
     * @param float $amount
     * @param string $courseName
     * @param string $receiptNumber
     * @return bool
     */
    public function sendPaymentEmail(Student $student, float $amount, string $courseName, string $receiptNumber): bool
    {
        try {
            if (!$student->email) {
                Log::warning("Email not sent: Student {$student->id} has no email address", [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                ]);
                return false;
            }

            $schoolName = Setting::get('school_name', 'Global College');
            $schoolEmail = Setting::get('school_email', config('mail.from.address'));
            $schoolPhone = Setting::get('school_phone', '');

            $subject = "Payment Confirmation - Receipt #{$receiptNumber}";
            
            $message = view('emails.payment-confirmation', [
                'student' => $student,
                'amount' => $amount,
                'courseName' => $courseName,
                'receiptNumber' => $receiptNumber,
                'schoolName' => $schoolName,
                'schoolEmail' => $schoolEmail,
                'schoolPhone' => $schoolPhone,
            ])->render();

            Mail::html($message, function($mail) use ($student, $subject, $schoolEmail, $schoolName) {
                $mail->to($student->email, $student->full_name)
                     ->subject($subject)
                     ->from($schoolEmail, $schoolName);
            });

            Log::info("Payment confirmation email sent successfully", [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'email' => $student->email,
                'receipt_number' => $receiptNumber,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment confirmation email", [
                'student_id' => $student->id,
                'email' => $student->email ?? null,
                'receipt_number' => $receiptNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send email to guardian (if email available)
     * 
     * @param Student $student
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendToGuardian(Student $student, string $subject, string $message): bool
    {
        // Note: Guardian email is not currently stored, but this method is ready for future use
        // For now, we can send to student's email if guardian email is not available
        if (!$student->email) {
            return false;
        }

        try {
            $schoolName = Setting::get('school_name', 'Global College');
            $schoolEmail = Setting::get('school_email', config('mail.from.address'));

            Mail::raw($message, function($mail) use ($student, $subject, $schoolEmail, $schoolName) {
                $mail->to($student->email, $student->next_of_kin_name ?? $student->full_name)
                     ->subject($subject)
                     ->from($schoolEmail, $schoolName);
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email to guardian", [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}


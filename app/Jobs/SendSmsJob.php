<?php

namespace App\Jobs;

use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Student $student,
        public string $message,
        public string $type = 'general' // 'enrollment', 'payment', 'general'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        try {
            $success = match ($this->type) {
                'enrollment' => $smsService->sendEnrollmentSMS($this->student),
                'payment' => false, // Payment SMS requires additional parameters, handle separately
                default => $smsService->sendSMSs($this->message, $this->student),
            };

            if (!$success) {
                throw new \Exception('SMS sending failed');
            }
        } catch (\Exception $e) {
            Log::error('SMS job failed', [
                'student_id' => $this->student->id,
                'type' => $this->type,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SMS job failed after all retries', [
            'student_id' => $this->student->id,
            'type' => $this->type,
            'error' => $exception->getMessage(),
        ]);
    }
}


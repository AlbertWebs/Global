<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Log;

class LogProvider implements SmsProviderInterface
{
    public function send(string $phoneNumber, string $message): array
    {
        Log::info('SMS Sent (Log Provider)', [
            'to' => $phoneNumber,
            'message' => $message,
            'length' => strlen($message),
        ]);

        return [
            'success' => true,
            'message_id' => 'log-' . now()->timestamp,
            'status' => 'logged',
            'provider_response' => ['mode' => 'log'],
        ];
    }
}


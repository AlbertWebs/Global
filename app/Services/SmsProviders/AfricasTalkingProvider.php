<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AfricasTalkingProvider implements SmsProviderInterface
{
    protected string $username;
    protected string $apiKey;
    protected string $senderId;

    public function __construct()
    {
        $this->username = config('sms.africastalking.username');
        $this->apiKey = config('sms.africastalking.api_key');
        $this->senderId = config('sms.africastalking.sender_id', 'SCHOOL');
    }

    public function send(string $phoneNumber, string $message): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('AfricasTalking SMS provider is not configured');
        }

        try {
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post('https://api.africastalking.com/version1/messaging', [
                'username' => $this->username,
                'to' => $phoneNumber,
                'message' => $message,
                'from' => $this->senderId,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['SMSMessageData']['Recipients'][0]['statusCode'])) {
                $statusCode = $result['SMSMessageData']['Recipients'][0]['statusCode'];
                
                if ($statusCode == 101) {
                    return [
                        'success' => true,
                        'message_id' => $result['SMSMessageData']['Recipients'][0]['messageId'] ?? null,
                        'status' => 'sent',
                        'provider_response' => $result,
                    ];
                }
            }

            return [
                'success' => false,
                'error' => $result['SMSMessageData']['Message'] ?? 'Unknown error',
                'provider_response' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('AfricasTalking SMS send failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to send SMS via AfricasTalking: ' . $e->getMessage());
        }
    }

    protected function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->apiKey);
    }
}


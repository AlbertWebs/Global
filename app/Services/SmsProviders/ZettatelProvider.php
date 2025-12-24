<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZettatelProvider implements SmsProviderInterface
{
    protected string $apiKey;
    protected string $senderId;

    public function __construct()
    {
        $this->apiKey = config('sms.zettatel.api_key');
        $this->senderId = config('sms.zettatel.sender_id', 'SCHOOL');
    }

    public function send(string $phoneNumber, string $message): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Zettatel SMS provider is not configured');
        }

        try {
            $url = 'https://portal.zettatel.com/SMSApi/send';

            $response = Http::asForm()->post($url, [
                'apikey' => $this->apiKey,
                'senderid' => $this->senderId,
                'number' => $phoneNumber,
                'message' => $message,
            ]);

            $result = $response->json();

            // Check if response is successful
            // Adjust this based on Zettatel's actual response format
            if ($response->successful()) {
                // Check the actual response structure from Zettatel
                // Common patterns: status: 'success', statusCode: 200, etc.
                if (isset($result['status']) && $result['status'] === 'success') {
                    return [
                        'success' => true,
                        'message_id' => $result['messageId'] ?? $result['id'] ?? null,
                        'status' => 'sent',
                        'provider_response' => $result,
                    ];
                } elseif (isset($result['statusCode']) && $result['statusCode'] == 200) {
                    return [
                        'success' => true,
                        'message_id' => $result['messageId'] ?? $result['id'] ?? null,
                        'status' => 'sent',
                        'provider_response' => $result,
                    ];
                }
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? $result['error'] ?? 'Unknown error',
                'provider_response' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Zettatel SMS send failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to send SMS via Zettatel: ' . $e->getMessage());
        }
    }

    protected function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}


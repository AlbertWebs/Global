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

            // Ensure phone number doesn't have plus sign
            $phoneNumber = ltrim($phoneNumber, '+');

            Log::info('Sending SMS via Zettatel', [
                'phone' => $phoneNumber,
                'sender_id' => $this->senderId,
                'message_length' => strlen($message),
            ]);

            $response = Http::timeout(30)->asForm()->post($url, [
                'apikey' => $this->apiKey,
                'senderid' => $this->senderId,
                'number' => $phoneNumber,
                'message' => $message,
            ]);

            $statusCode = $response->status();
            $responseBody = $response->body();
            $result = $response->json();

            Log::info('Zettatel API Response', [
                'status_code' => $statusCode,
                'response_body' => $responseBody,
                'parsed_json' => $result,
            ]);

            // Check if response is successful
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
                } elseif (isset($result['status']) && strtolower($result['status']) === 'success') {
                    return [
                        'success' => true,
                        'message_id' => $result['messageId'] ?? $result['id'] ?? null,
                        'status' => 'sent',
                        'provider_response' => $result,
                    ];
                }
            }

            // If we get here, the request didn't succeed
            $errorMessage = 'Unknown error';
            if (is_array($result)) {
                $errorMessage = $result['message'] ?? $result['error'] ?? $result['status'] ?? 'Unknown error';
            } elseif (!empty($responseBody)) {
                $errorMessage = $responseBody;
            }

            return [
                'success' => false,
                'error' => $errorMessage,
                'provider_response' => $result ?: $responseBody,
                'status_code' => $statusCode,
            ];
        } catch (\Exception $e) {
            Log::error('Zettatel SMS send exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider_response' => null,
            ];
        }
    }

    protected function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}


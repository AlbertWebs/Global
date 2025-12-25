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
            // Try different possible endpoints
            $endpoints = [
                'https://portal.zettatel.com/SMSApi/send',
                'https://portal.zettatel.com/api/send',
                'https://portal.zettatel.com/api/SendSMS',
            ];

            // Ensure phone number doesn't have plus sign
            $phoneNumber = ltrim($phoneNumber, '+');

            Log::info('Sending SMS via Zettatel', [
                'phone' => $phoneNumber,
                'sender_id' => $this->senderId,
                'message_length' => strlen($message),
            ]);

            $lastError = null;
            $lastResponse = null;

            foreach ($endpoints as $url) {
                try {
                    // Try POST with form data
                    $response = Http::timeout(30)->asForm()->post($url, [
                        'apikey' => $this->apiKey,
                        'senderid' => $this->senderId,
                        'number' => $phoneNumber,
                        'message' => $message,
                    ]);

                    $statusCode = $response->status();
                    $responseBody = $response->body();
                    
                    Log::info('Zettatel API Response', [
                        'url' => $url,
                        'status_code' => $statusCode,
                        'response_body' => $responseBody,
                    ]);

                    // Parse pipe-delimited response format: "status=success | messageId=12345"
                    $parsed = $this->parseResponse($responseBody);
                    
                    if (isset($parsed['status']) && strtolower($parsed['status']) === 'success') {
                        return [
                            'success' => true,
                            'message_id' => $parsed['messageId'] ?? $parsed['messageid'] ?? $parsed['id'] ?? null,
                            'status' => 'sent',
                            'provider_response' => $parsed,
                        ];
                    }

                    // If error, try next endpoint
                    if (isset($parsed['status']) && strtolower($parsed['status']) === 'error') {
                        $lastError = $parsed['reason'] ?? $parsed['error'] ?? $responseBody;
                        $lastResponse = $parsed;
                        continue;
                    }

                    // Try JSON parsing
                    $result = $response->json();
                    if (is_array($result)) {
                        if (isset($result['status']) && strtolower($result['status']) === 'success') {
                            return [
                                'success' => true,
                                'message_id' => $result['messageId'] ?? $result['messageid'] ?? $result['id'] ?? null,
                                'status' => 'sent',
                                'provider_response' => $result,
                            ];
                        }
                        $lastError = $result['message'] ?? $result['error'] ?? $result['reason'] ?? 'Unknown error';
                        $lastResponse = $result;
                    }
                } catch (\Exception $e) {
                    Log::warning('Zettatel endpoint failed', [
                        'url' => $url,
                        'error' => $e->getMessage(),
                    ]);
                    $lastError = $e->getMessage();
                    continue;
                }
            }

            // If all endpoints failed, try GET method as last resort
            $url = 'https://portal.zettatel.com/SMSApi/send';
            $response = Http::timeout(30)->get($url, [
                'apikey' => $this->apiKey,
                'senderid' => $this->senderId,
                'number' => $phoneNumber,
                'message' => $message,
            ]);

            $responseBody = $response->body();
            $parsed = $this->parseResponse($responseBody);
            
            if (isset($parsed['status']) && strtolower($parsed['status']) === 'success') {
                return [
                    'success' => true,
                    'message_id' => $parsed['messageId'] ?? $parsed['messageid'] ?? $parsed['id'] ?? null,
                    'status' => 'sent',
                    'provider_response' => $parsed,
                ];
            }

            return [
                'success' => false,
                'error' => $lastError ?? ($parsed['reason'] ?? $parsed['error'] ?? 'Unknown error'),
                'provider_response' => $lastResponse ?: $parsed ?: $responseBody,
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

    /**
     * Parse Zettatel API response (pipe-delimited format)
     * Format: "status=success | messageId=12345" or "status=error | errorCode=152 | reason=Invalid method"
     */
    protected function parseResponse(string $response): array
    {
        $parsed = [];
        
        // Handle pipe-delimited format
        if (strpos($response, '|') !== false) {
            $parts = explode('|', $response);
            foreach ($parts as $part) {
                $part = trim($part);
                if (strpos($part, '=') !== false) {
                    [$key, $value] = explode('=', $part, 2);
                    $parsed[trim($key)] = trim($value);
                }
            }
        } else {
            // Try to parse as key=value format
            if (preg_match_all('/(\w+)=([^|]+)/', $response, $matches)) {
                foreach ($matches[1] as $index => $key) {
                    $parsed[$key] = trim($matches[2][$index]);
                }
            }
        }
        
        return $parsed;
    }
}


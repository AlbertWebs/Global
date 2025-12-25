<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZettatelProvider implements SmsProviderInterface
{
    protected ?string $userid;
    protected ?string $password;
    protected string $senderId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->userid = config('sms.zettatel.userid') ?: null;
        $this->password = config('sms.zettatel.password') ?: null;
        $this->senderId = config('sms.zettatel.sender_id', 'SCHOOL') ?: 'SCHOOL';
        $this->baseUrl = config('sms.zettatel.base_url', 'https://portal.zettatel.com') ?: 'https://portal.zettatel.com';
    }

    public function send(string $phoneNumber, string $message): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Zettatel SMS provider is not configured');
        }

        try {
            // Ensure phone number doesn't have plus sign
            $phoneNumber = ltrim($phoneNumber, '+');

            Log::info('Sending SMS via Zettatel', [
                'phone' => $phoneNumber,
                'sender_id' => $this->senderId,
                'message_length' => strlen($message),
            ]);

            // Configure HTTP client with SSL verification disabled for local development
            // Note: In production, ensure proper SSL certificates are configured
            $httpClient = Http::timeout(30)->withoutVerifying();

            // Use the correct endpoint and parameters according to Zettatel API documentation
            // Endpoint: /SMSApi/send or /send (POST method with form-data)
            $endpoints = [
                $this->baseUrl . '/SMSApi/send',
                $this->baseUrl . '/send',
            ];

            // Prepare parameters according to API documentation
            $params = [
                'userid' => $this->userid,
                'password' => $this->password,
                'mobile' => $phoneNumber, // Use 'mobile' not 'number'
                'senderid' => $this->senderId,
                'msg' => $message, // Use 'msg' not 'message'
                'sendMethod' => 'quick', // Required: quick, group, or bulkupload
                'msgType' => 'text', // Required: text or unicode
                'output' => 'json', // Response format: json, plain, or xml
                'duplicatecheck' => 'true', // Check for duplicates
            ];

            $lastError = null;
            $lastResponse = null;

            // Try each endpoint
            foreach ($endpoints as $url) {
                try {
                    Log::info('Zettatel API attempt', [
                        'url' => $url,
                        'method' => 'POST',
                        'mobile' => $phoneNumber,
                        'senderid' => $this->senderId,
                    ]);

                    // Send POST request with form-data
                    $response = $httpClient->asForm()->post($url, $params);

                    $statusCode = $response->status();
                    $responseBody = $response->body();
                    
                    Log::info('Zettatel API Response', [
                        'url' => $url,
                        'status_code' => $statusCode,
                        'response_body' => substr($responseBody, 0, 500), // Limit log size
                    ]);

                    // Try JSON parsing first (since output=json)
                    $result = $response->json();
                    if (is_array($result)) {
                        // Check for success in JSON response
                        if (isset($result['status']) && (strtolower($result['status']) === 'success' || $result['status'] === '1')) {
                            Log::info('Zettatel SMS sent successfully (JSON)', [
                                'phone' => $phoneNumber,
                                'message_id' => $result['messageId'] ?? $result['messageid'] ?? $result['id'] ?? $result['msgid'] ?? null,
                            ]);
                            return [
                                'success' => true,
                                'message_id' => $result['messageId'] ?? $result['messageid'] ?? $result['id'] ?? $result['msgid'] ?? null,
                                'status' => 'sent',
                                'provider_response' => $result,
                            ];
                        }
                        
                        // Check for error in JSON response
                        if (isset($result['status']) && strtolower($result['status']) === 'error') {
                            $errorMsg = $result['reason'] ?? $result['error'] ?? $result['message'] ?? 'Unknown error';
                            Log::warning('Zettatel API error response (JSON)', [
                                'url' => $url,
                                'error' => $errorMsg,
                                'error_code' => $result['errorCode'] ?? null,
                            ]);
                            $lastError = $errorMsg;
                            $lastResponse = $result;
                            continue;
                        }
                    }

                    // Fallback: Parse pipe-delimited response format: "status=success | messageId=12345"
                    $parsed = $this->parseResponse($responseBody);
                    
                    if (isset($parsed['status']) && strtolower($parsed['status']) === 'success') {
                        Log::info('Zettatel SMS sent successfully (pipe-delimited)', [
                            'phone' => $phoneNumber,
                            'message_id' => $parsed['messageId'] ?? $parsed['messageid'] ?? $parsed['id'] ?? null,
                        ]);
                        return [
                            'success' => true,
                            'message_id' => $parsed['messageId'] ?? $parsed['messageid'] ?? $parsed['id'] ?? null,
                            'status' => 'sent',
                            'provider_response' => $parsed,
                        ];
                    }

                    // If error in pipe-delimited format
                    if (isset($parsed['status']) && strtolower($parsed['status']) === 'error') {
                        $errorMsg = $parsed['reason'] ?? $parsed['error'] ?? $responseBody;
                        Log::warning('Zettatel API error response (pipe-delimited)', [
                            'url' => $url,
                            'error' => $errorMsg,
                            'error_code' => $parsed['errorCode'] ?? null,
                        ]);
                        $lastError = $errorMsg;
                        $lastResponse = $parsed;
                        continue;
                    }

                    // If we got here, response format is unexpected
                    $lastError = 'Unexpected response format';
                    $lastResponse = $responseBody;
                    
                } catch (\Exception $e) {
                    Log::warning('Zettatel endpoint exception', [
                        'url' => $url,
                        'error' => $e->getMessage(),
                    ]);
                    $lastError = $e->getMessage();
                    continue;
                }
            }

            // All attempts failed
            Log::error('Zettatel SMS send failed - all attempts exhausted', [
                'phone' => $phoneNumber,
                'last_error' => $lastError,
                'last_response' => $lastResponse,
            ]);

            return [
                'success' => false,
                'error' => $lastError ?? 'All API endpoints failed. Please check your Zettatel API configuration.',
                'provider_response' => $lastResponse,
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
        return !empty($this->userid) && !empty($this->password);
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


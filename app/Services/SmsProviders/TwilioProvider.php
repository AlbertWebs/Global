<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioProvider implements SmsProviderInterface
{
    protected string $accountSid;
    protected string $authToken;
    protected string $fromNumber;

    public function __construct()
    {
        $this->accountSid = config('sms.twilio.account_sid');
        $this->authToken = config('sms.twilio.auth_token');
        $this->fromNumber = config('sms.twilio.from_number');
    }

    public function send(string $phoneNumber, string $message): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Twilio SMS provider is not configured');
        }

        try {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";

            $response = Http::asForm()->withBasicAuth($this->accountSid, $this->authToken)
                ->post($url, [
                    'From' => $this->fromNumber,
                    'To' => $phoneNumber,
                    'Body' => $message,
                ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] != 'failed') {
                return [
                    'success' => true,
                    'message_id' => $result['sid'] ?? null,
                    'status' => $result['status'],
                    'provider_response' => $result,
                ];
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Unknown error',
                'provider_response' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Twilio SMS send failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to send SMS via Twilio: ' . $e->getMessage());
        }
    }

    protected function isConfigured(): bool
    {
        return !empty($this->accountSid) && !empty($this->authToken) && !empty($this->fromNumber);
    }
}


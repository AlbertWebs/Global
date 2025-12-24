<?php

namespace App\Contracts;

interface SmsProviderInterface
{
    /**
     * Send SMS message
     * 
     * @param string $phoneNumber Phone number in international format
     * @param string $message Message content
     * @return array Response from SMS provider
     */
    public function send(string $phoneNumber, string $message): array;
}


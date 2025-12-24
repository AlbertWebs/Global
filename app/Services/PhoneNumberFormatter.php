<?php

namespace App\Services;

class PhoneNumberFormatter
{
    /**
     * Format phone number to international format
     * 
     * @param string $phoneNumber
     * @param string $defaultCountryCode Default country code (e.g., '254' for Kenya)
     * @return string Formatted phone number with + prefix
     */
    public static function format(string $phoneNumber, string $defaultCountryCode = '254'): string
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // If already in international format with +
        if (strpos($phoneNumber, '+') === 0) {
            return $phoneNumber;
        }
        
        // Remove leading + if present
        $phoneNumber = ltrim($phoneNumber, '+');
        
        // If starts with 0, replace with country code
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = $defaultCountryCode . substr($phoneNumber, 1);
        }
        
        // If doesn't start with country code, add it
        if (substr($phoneNumber, 0, strlen($defaultCountryCode)) !== $defaultCountryCode) {
            $phoneNumber = $defaultCountryCode . $phoneNumber;
        }
        
        return '+' . $phoneNumber;
    }

    /**
     * Validate phone number format
     * 
     * @param string $phoneNumber
     * @return bool
     */
    public static function isValid(string $phoneNumber): bool
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Check if it's a valid international format with +
        // Should start with + and have 10-15 digits
        if (preg_match('/^\+[1-9]\d{9,14}$/', $cleaned)) {
            return true;
        }
        
        // Check if it's a valid international format without + (e.g., 254790841987)
        // Should start with country code (1-3 digits) and have 10-15 total digits
        if (preg_match('/^[1-9]\d{9,14}$/', $cleaned)) {
            return true;
        }
        
        // Check if it's a valid local format (will be formatted)
        // Kenyan format: 0XXXXXXXXX (10 digits starting with 0)
        if (preg_match('/^0\d{9}$/', $cleaned)) {
            return true;
        }
        
        // Check if it's a valid local format without leading 0 (9-10 digits)
        // Will be formatted with country code
        if (preg_match('/^\d{9,10}$/', $cleaned)) {
            return true;
        }
        
        return false;
    }
}


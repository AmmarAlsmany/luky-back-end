<?php

namespace App\Services;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class PhoneNumberService
{
    protected PhoneNumberUtil $phoneUtil;
    protected string $defaultRegion = 'SA'; // Saudi Arabia

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * Standardize phone number to international format
     */
    public function standardize(string $phoneNumber): string
    {
        try {
            // Parse the number with Saudi Arabia as default region
            $number = $this->phoneUtil->parse($phoneNumber, $this->defaultRegion);

            // Return in international format (+966XXXXXXXXX)
            return $this->phoneUtil->format($number, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            throw new \InvalidArgumentException("Invalid phone number format: {$phoneNumber}");
        }
    }

    /**
     * Validate if phone number is valid Saudi number
     */
    public function isValidSaudiNumber(string $phoneNumber): bool
    {
        try {
            $number = $this->phoneUtil->parse($phoneNumber, $this->defaultRegion);
            return $this->phoneUtil->isValidNumberForRegion($number, $this->defaultRegion);
        } catch (NumberParseException $e) {
            return false;
        }
    }

    /**
     * Format for display (national format without country code)
     */
    public function formatForDisplay(string $phoneNumber): string
    {
        try {
            $number = $this->phoneUtil->parse($phoneNumber, $this->defaultRegion);
            return $this->phoneUtil->format($number, PhoneNumberFormat::NATIONAL);
        } catch (NumberParseException $e) {
            return $phoneNumber; // Return original if parsing fails
        }
    }

    /**
     * Format for SMS (without + symbol)
     */
    public function formatForSms(string $phoneNumber): string
    {
        $standardized = $this->standardize($phoneNumber);
        return str_replace('+', '', $standardized);
    }

    /**
     * Convert various phone number formats to standardized format
     * Handles: 05XXXXXXXX, 5XXXXXXXX, 9665XXXXXXXX, +9665XXXXXXXX, 05XXXXXXXX
     */
    public function normalize(string $phoneNumber): string
    {
        // Remove any whitespace, dashes, or parentheses
        $cleaned = preg_replace('/[\s\-\(\)]/', '', $phoneNumber);

        // Handle different input formats
        if (preg_match('/^0([5-9]\d{8})$/', $cleaned, $matches)) {
            // Format: 05XXXXXXXX -> +9665XXXXXXXX
            return '+966' . $matches[1];
        } elseif (preg_match('/^([5-9]\d{8})$/', $cleaned, $matches)) {
            // Format: 5XXXXXXXX -> +9665XXXXXXXX
            return '+966' . $matches[1];
        } elseif (preg_match('/^966([5-9]\d{8})$/', $cleaned, $matches)) {
            // Format: 9665XXXXXXXX -> +9665XXXXXXXX
            return '+966' . $matches[1];
        } elseif (preg_match('/^\+966([5-9]\d{8})$/', $cleaned, $matches)) {
            // Format: +9665XXXXXXXX -> +9665XXXXXXXX (already correct)
            return $cleaned;
        }

        // If no pattern matches, try the libphonenumber parser
        return $this->standardize($cleaned);
    }

    /**
     * Get country code from phone number
     */
    public function getCountryCode(string $phoneNumber): ?int
    {
        try {
            $number = $this->phoneUtil->parse($phoneNumber, $this->defaultRegion);
            return $number->getCountryCode();
        } catch (NumberParseException $e) {
            return null;
        }
    }

    /**
     * Get national number (without country code)
     */
    public function getNationalNumber(string $phoneNumber): ?string
    {
        try {
            $number = $this->phoneUtil->parse($phoneNumber, $this->defaultRegion);
            return (string) $number->getNationalNumber();
        } catch (NumberParseException $e) {
            return null;
        }
    }
}
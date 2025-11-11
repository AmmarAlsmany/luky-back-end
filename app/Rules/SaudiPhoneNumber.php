<?php

namespace App\Rules;

use App\Services\PhoneNumberService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SaudiPhoneNumber implements ValidationRule
{
    protected PhoneNumberService $phoneService;

    public function __construct()
    {
        $this->phoneService = new PhoneNumberService();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a valid phone number.');
            return;
        }

        try {
            // Try to normalize and validate the phone number
            $normalized = $this->phoneService->normalize($value);

            if (!$this->phoneService->isValidSaudiNumber($normalized)) {
                $fail('The :attribute must be a valid Saudi Arabian phone number.');
                return;
            }
        } catch (\InvalidArgumentException $e) {
            $fail('The :attribute must be a valid Saudi Arabian phone number.');
        }
    }

    /**
     * Get the normalized phone number (useful for accessing after validation)
     */
    public function getNormalizedNumber(string $phoneNumber): string
    {
        return $this->phoneService->normalize($phoneNumber);
    }
}
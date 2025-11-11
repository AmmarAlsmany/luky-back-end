<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $appId;
    protected $appSecret;
    protected $sender;
    protected PhoneNumberService $phoneService;

    public function __construct(PhoneNumberService $phoneService)
    {
        $this->apiUrl = config('services.sms.api_url');
        $this->appId = config('services.sms.app_id');
        $this->appSecret = config('services.sms.app_secret');
        $this->sender = config('services.sms.sender', 'Luky');
        $this->phoneService = $phoneService;
    }

    /**
     * Send SMS message
     */
    public function send(string $phone, string $message): bool
    {
        // Format phone number for SMS (removes + and ensures proper format)
        $cleanPhone = $this->phoneService->formatForSms($phone);

        try {
            // For development, log instead of sending real SMS
            if (config('app.env') === 'local') {
                Log::info("SMS to {$phone}: {$message}");
                return true;
            }

            // Send SMS using 4jawaly API
            // API: https://api-sms.4jawaly.com/api/v1
            // Uses Basic Authentication
            $authHash = base64_encode("{$this->appId}:{$this->appSecret}");
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => "Basic {$authHash}"
            ])->post($this->apiUrl, [
                'messages' => [
                    [
                        'text' => $message,
                        'numbers' => [$cleanPhone],
                        'sender' => $this->sender
                    ]
                ]
            ]);

            // Log the full response for debugging
            Log::info("4jawaly API Response", [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500),
                'headers' => $response->headers()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Check for job_id which indicates success
                if (isset($responseData['job_id'])) {
                    Log::info("SMS sent successfully to {$phone}", [
                        'job_id' => $responseData['job_id']
                    ]);
                    return true;
                }
            }

            // Handle API errors
            $responseData = $response->json();
            $errorMessage = $responseData['message'] ?? 'Unknown error';
            
            // Common error messages
            $errorTranslations = [
                'لم يتم العثور على باقات' => 'No SMS packages found. Please purchase SMS credits from 4jawaly.',
                'رصيد غير كافي' => 'Insufficient balance. Please top up your account.',
                'المرسل غير موجود' => 'Sender name not found. Please verify your sender name.',
            ];
            
            $translatedError = $errorTranslations[$errorMessage] ?? $errorMessage;

            Log::error("SMS send failed", [
                'phone' => $phone,
                'status' => $response->status(),
                'error' => $translatedError,
                'original_error' => $errorMessage,
                'response' => $responseData
            ]);
            
            return false;

        } catch (\Exception $e) {
            Log::error("SMS service exception", [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Format phone number for SMS gateway
     * @deprecated Use PhoneNumberService::formatForSms() instead
     */
    protected function formatPhoneNumber(string $phone): string
    {
        return $this->phoneService->formatForSms($phone);
    }

    /**
     * Validate phone number format
     * @deprecated Use PhoneNumberService::isValidSaudiNumber() instead
     */
    public function isValidSaudiPhone(string $phone): bool
    {
        return $this->phoneService->isValidSaudiNumber($phone);
    }
}
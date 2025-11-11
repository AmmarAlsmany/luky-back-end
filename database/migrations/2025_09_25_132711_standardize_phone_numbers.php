<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PhoneNumberService;
use App\Models\User;
use App\Models\OtpVerification;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $phoneService = new PhoneNumberService();

        // Standardize phone numbers in users table
        User::chunk(100, function ($users) use ($phoneService) {
            foreach ($users as $user) {
                try {
                    if ($user->phone) {
                        $normalizedPhone = $phoneService->normalize($user->phone);
                        if ($normalizedPhone !== $user->phone) {
                            // Use updateQuietly to bypass mutators and events
                            $user->updateQuietly(['phone' => $normalizedPhone]);
                        }
                    }
                } catch (\Exception $e) {
                    // Log invalid phone numbers but don't fail the migration
                    \Log::warning("Could not normalize phone number for user {$user->id}: {$user->phone} - {$e->getMessage()}");
                }
            }
        });

        // Standardize phone numbers in otp_verifications table
        OtpVerification::chunk(100, function ($otps) use ($phoneService) {
            foreach ($otps as $otp) {
                try {
                    if ($otp->phone) {
                        $normalizedPhone = $phoneService->normalize($otp->phone);
                        if ($normalizedPhone !== $otp->phone) {
                            $otp->updateQuietly(['phone' => $normalizedPhone]);
                        }
                    }
                } catch (\Exception $e) {
                    // Log invalid phone numbers but don't fail the migration
                    \Log::warning("Could not normalize phone number for OTP {$otp->id}: {$otp->phone} - {$e->getMessage()}");
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily reversed as we don't store the original formats
        // The phone numbers are already normalized and we don't know the original format
    }
};

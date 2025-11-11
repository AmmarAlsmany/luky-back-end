<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PhoneNumberService;

class TestPhoneNormalization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phone:test {phone?} {--batch : Test with common formats}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test phone number normalization with various formats';

    protected PhoneNumberService $phoneService;

    public function __construct(PhoneNumberService $phoneService)
    {
        parent::__construct();
        $this->phoneService = $phoneService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('batch')) {
            return $this->testBatch();
        }

        $phone = $this->argument('phone');

        if (!$phone) {
            $phone = $this->ask('Enter a phone number to test');
        }

        $this->testSinglePhone($phone);
    }

    protected function testSinglePhone(string $phone)
    {
        $this->info("Testing phone number: $phone");
        $this->line('');

        try {
            $normalized = $this->phoneService->normalize($phone);
            $this->info("✅ Normalized: $normalized");

            $isValid = $this->phoneService->isValidSaudiNumber($normalized);
            $this->info("✅ Valid Saudi number: " . ($isValid ? 'Yes' : 'No'));

            $forDisplay = $this->phoneService->formatForDisplay($normalized);
            $this->info("📱 Display format: $forDisplay");

            $forSms = $this->phoneService->formatForSms($normalized);
            $this->info("📧 SMS format: $forSms");

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }

    protected function testBatch()
    {
        $testCases = [
            '0501234567',      // With leading 0
            '501234567',       // Without leading 0
            '+966501234567',   // International with +
            '966501234567',    // International without +
            '0050123456',      // Invalid (too short)
            '05012345678',     // Invalid (too long)
            '0401234567',      // Invalid (doesn't start with 5-9)
            '+1234567890',     // Invalid (wrong country)
        ];

        $this->info('Testing common phone number formats:');
        $this->line('');

        $headers = ['Input', 'Normalized', 'Valid', 'Display', 'SMS'];
        $rows = [];

        foreach ($testCases as $phone) {
            try {
                $normalized = $this->phoneService->normalize($phone);
                $isValid = $this->phoneService->isValidSaudiNumber($normalized) ? '✅' : '❌';
                $display = $this->phoneService->formatForDisplay($normalized);
                $sms = $this->phoneService->formatForSms($normalized);

                $rows[] = [$phone, $normalized, $isValid, $display, $sms];
            } catch (\Exception $e) {
                $rows[] = [$phone, 'Error', '❌', $e->getMessage(), '-'];
            }
        }

        $this->table($headers, $rows);
    }
}

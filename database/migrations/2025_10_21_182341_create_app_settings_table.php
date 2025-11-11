<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // provider_acceptance_timeout_minutes, payment_timeout_minutes, etc.
            $table->text('value');
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('group')->nullable(); // bookings, payments, notifications, general
            $table->text('description')->nullable();
            $table->timestamps();

            // Index
            $table->index('key');
            $table->index('group');
        });

        // Insert default settings
        DB::table('app_settings')->insert([
            [
                'key' => 'provider_acceptance_timeout_minutes',
                'value' => '30',
                'type' => 'integer',
                'group' => 'bookings',
                'description' => 'Time limit for provider to accept/reject booking request (minutes)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_timeout_minutes',
                'value' => '5',
                'type' => 'integer',
                'group' => 'payments',
                'description' => 'Time limit for client to complete payment after booking acceptance (minutes)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'review_reminder_hours',
                'value' => '1',
                'type' => 'integer',
                'group' => 'reviews',
                'description' => 'Hours after booking completion to show review prompt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_rate',
                'value' => '0.15',
                'type' => 'decimal',
                'group' => 'payments',
                'description' => 'VAT tax rate (15% = 0.15)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_commission_rate',
                'value' => '15',
                'type' => 'integer',
                'group' => 'payments',
                'description' => 'Default commission percentage for providers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};

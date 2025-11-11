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
        // Add cancellation policy settings
        DB::table('app_settings')->insert([
            [
                'key' => 'cancellation_fee_percentage',
                'value' => '20',
                'type' => 'integer',
                'group' => 'bookings',
                'description' => 'Cancellation fee percentage if client cancels after provider acceptance (%)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_cancellation_hours',
                'value' => '24',
                'type' => 'integer',
                'group' => 'bookings',
                'description' => 'Hours before booking start time for free cancellation (0 = only before acceptance)',
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
        DB::table('app_settings')
            ->whereIn('key', ['cancellation_fee_percentage', 'free_cancellation_hours'])
            ->delete();
    }
};

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
        // For PostgreSQL, we need to drop and recreate the column with new enum values
        // First, drop the constraint if it exists
        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_cancelled_by_check");

        // Change the column to VARCHAR temporarily
        DB::statement("ALTER TABLE bookings ALTER COLUMN cancelled_by TYPE VARCHAR(20)");

        // Add constraint with new 'system' value
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_cancelled_by_check CHECK (cancelled_by IN ('client', 'provider', 'admin', 'system'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'system' from allowed values
        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_cancelled_by_check");
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_cancelled_by_check CHECK (cancelled_by IN ('client', 'provider', 'admin'))");
    }
};

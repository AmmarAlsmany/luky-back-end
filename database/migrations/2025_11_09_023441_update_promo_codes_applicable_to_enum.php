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
        DB::statement("ALTER TABLE promo_codes DROP CONSTRAINT IF EXISTS promo_codes_applicable_to_check");
        DB::statement("ALTER TABLE promo_codes ADD CONSTRAINT promo_codes_applicable_to_check CHECK (applicable_to IN ('all', 'clients_only', 'providers_only', 'specific_services', 'specific_categories'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE promo_codes DROP CONSTRAINT IF EXISTS promo_codes_applicable_to_check");
        DB::statement("ALTER TABLE promo_codes ADD CONSTRAINT promo_codes_applicable_to_check CHECK (applicable_to IN ('all', 'clients_only', 'providers_only'))");
    }
};

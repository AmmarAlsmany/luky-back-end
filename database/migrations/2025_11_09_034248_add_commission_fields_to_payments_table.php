<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('platform_commission', 10, 2)->nullable()->after('amount');
            $table->decimal('provider_amount', 10, 2)->nullable()->after('platform_commission');
            $table->decimal('tax_amount', 10, 2)->nullable()->after('provider_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['platform_commission', 'provider_amount', 'tax_amount']);
        });
    }
};

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
        Schema::create('provider_payment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('service_providers')->onDelete('cascade');
            $table->decimal('tax_rate', 5, 2)->default(15.00); // Tax percentage
            $table->decimal('commission_rate', 5, 2)->default(10.00); // Commission percentage
            $table->string('currency', 3)->default('SAR'); // Currency code
            $table->timestamps();
            
            $table->unique('provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_payment_settings');
    }
};

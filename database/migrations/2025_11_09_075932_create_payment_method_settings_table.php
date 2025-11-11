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
        Schema::create('payment_method_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('method_id')->unique(); // MyFatoorah method ID
            $table->string('method_code', 50); // e.g., 'md', 'vm', 'ap'
            $table->string('method_name_en', 100); // e.g., 'MADA'
            $table->string('method_name_ar', 100); // e.g., 'مدى'
            $table->boolean('is_enabled')->default(true); // Toggle ON/OFF
            $table->boolean('is_default')->default(false); // Selected as default
            $table->integer('display_order')->default(0); // Order in checkout
            $table->string('image_url')->nullable();
            $table->decimal('service_charge', 10, 2)->nullable();
            $table->string('currency_iso', 3)->default('SAR');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_settings');
    }
};

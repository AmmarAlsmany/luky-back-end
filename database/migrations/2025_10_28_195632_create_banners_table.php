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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('provider_name')->nullable();
            $table->string('offer_text');
            $table->string('banner_template');
            $table->string('title_color');
            $table->string('title_font');
            $table->string('title_size');
            $table->string('provider_color');
            $table->string('provider_font');
            $table->string('provider_size');
            $table->string('offer_text_color');
            $table->string('offer_bg_color');
            $table->string('offer_font');
            $table->string('offer_size');
            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'scheduled', 'expired'])->default('scheduled');
            $table->integer('display_order')->default(0);
            $table->enum('display_location', ['home', 'services', 'providers', 'all'])->default('home');
            $table->integer('click_count')->default(0);
            $table->integer('impression_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
            $table->index('display_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};

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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->enum('business_type', ['salon', 'clinic', 'makeup_artist', 'hair_stylist']);
            $table->text('description')->nullable();
            $table->string('license_number')->nullable();
            $table->string('commercial_register')->nullable();
            $table->string('municipal_license')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->json('working_hours')->nullable(); // {monday: {start: "09:00", end: "18:00"}, ...}
            $table->json('off_days')->nullable(); // ["friday", "saturday"]
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('commission_rate', 5, 2)->default(15.00); // percentage
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('city_id')->constrained()->onDelete('restrict');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['verification_status', 'is_active']);
            $table->index(['business_type', 'city_id']);
            $table->index(['latitude', 'longitude']);
            $table->index('average_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};

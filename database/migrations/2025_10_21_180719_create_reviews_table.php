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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('service_providers')->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // 1-5 stars
            $table->text('comment')->nullable(); // Optional text review (future feature)
            $table->boolean('is_visible')->default(true); // Admin can hide inappropriate reviews
            $table->timestamps();

            // Indexes for performance
            $table->index('provider_id');
            $table->index('rating');
            $table->index('created_at');
            $table->index(['provider_id', 'is_visible']); // For fetching visible provider reviews
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

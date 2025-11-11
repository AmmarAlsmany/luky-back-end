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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // e.g., "Home", "Work", "Mom's House"
            $table->enum('type', ['home', 'work', 'other'])->default('other');
            $table->string('address'); // Full address text
            $table->string('building_number')->nullable();
            $table->string('street')->nullable();
            $table->string('district')->nullable(); // حي
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('notes')->nullable(); // Additional directions
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Index for faster queries
            $table->index('user_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};

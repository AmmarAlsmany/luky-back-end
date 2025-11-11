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
        Schema::table('users', function (Blueprint $table) {
            // Add email_verified_at for admin email verification
            $table->timestamp('email_verified_at')->nullable()->after('phone_verified_at');

            // Add created_by to track who created this user (for employees)
            $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->onDelete('set null');

            // Add status enum for more granular control (active, inactive, suspended)
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('is_active');

            // Add index for created_by
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['email_verified_at', 'created_by', 'status']);
        });
    }
};

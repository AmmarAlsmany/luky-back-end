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
        Schema::table('reviews', function (Blueprint $table) {
            // Add approval status: pending, approved, rejected
            $table->string('approval_status', 20)->default('pending')->after('is_visible');

            // Add approved_by and approved_at for tracking
            $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Add rejection reason
            $table->text('rejection_reason')->nullable()->after('approved_at');

            // Index for filtering by status
            $table->index('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
};

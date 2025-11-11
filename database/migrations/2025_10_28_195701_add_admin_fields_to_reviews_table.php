<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_flagged')->default(false)->after('is_visible');
            $table->text('flag_reason')->nullable()->after('is_flagged');
            $table->foreignId('flagged_by')->nullable()->after('flag_reason')->constrained('users')->onDelete('set null');
            $table->timestamp('flagged_at')->nullable()->after('flagged_by');

            $table->text('admin_response')->nullable()->after('flagged_at');
            $table->foreignId('responded_by')->nullable()->after('admin_response')->constrained('users')->onDelete('set null');
            $table->timestamp('responded_at')->nullable()->after('responded_by');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['flagged_by']);
            $table->dropForeign(['responded_by']);
            $table->dropColumn([
                'is_flagged',
                'flag_reason',
                'flagged_by',
                'flagged_at',
                'admin_response',
                'responded_by',
                'responded_at',
            ]);
        });
    }
};

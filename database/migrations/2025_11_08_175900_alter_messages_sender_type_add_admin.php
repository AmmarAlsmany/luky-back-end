<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Extend sender_type check constraint to allow 'admin'
        DB::statement("ALTER TABLE messages DROP CONSTRAINT IF EXISTS messages_sender_type_check;");
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_sender_type_check CHECK (sender_type IN ('client','provider','admin'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original constraint (no 'admin')
        DB::statement("ALTER TABLE messages DROP CONSTRAINT IF EXISTS messages_sender_type_check;");
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_sender_type_check CHECK (sender_type IN ('client','provider'));");
    }
};

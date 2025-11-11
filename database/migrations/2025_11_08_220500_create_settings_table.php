<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->timestamps();
            
            // Unique constraint on key+group
            $table->unique(['key', 'group']);
        });
        
        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'contact_email', 'value' => 'support@luky.app', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_phone', 'value' => '+966 5XXXXXXXX', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_address', 'value' => 'Riyadh, Saudi Arabia', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

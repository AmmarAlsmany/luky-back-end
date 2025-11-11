<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->integer('max_concurrent_bookings')->default(1)->after('commission_rate');
        });
    }

    public function down()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn('max_concurrent_bookings');
        });
    }
};
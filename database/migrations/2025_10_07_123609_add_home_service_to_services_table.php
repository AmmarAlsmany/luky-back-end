
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('available_at_home')->default(false)->after('price');
            $table->decimal('home_service_price', 10, 2)->nullable()->after('available_at_home');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['available_at_home', 'home_service_price']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Add bilingual support
            $table->string('name_ar')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_ar');

            // Add service enhancements
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->decimal('average_rating', 3, 2)->nullable()->after('is_featured');
            $table->integer('total_bookings')->default(0)->after('average_rating');

            // Add media fields
            $table->string('image_url')->nullable()->after('total_bookings');
            $table->json('gallery')->nullable()->after('image_url'); // Service portfolio images
        });

        // Migrate existing data: copy 'name' to 'name_ar' and 'name_en'
        DB::statement("UPDATE services SET name_ar = name, name_en = name WHERE name_ar IS NULL");
        DB::statement("UPDATE services SET description_ar = description, description_en = description WHERE description IS NOT NULL");
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'name_en',
                'description_ar',
                'description_en',
                'is_featured',
                'average_rating',
                'total_bookings',
                'image_url',
                'gallery',
            ]);
        });
    }
};

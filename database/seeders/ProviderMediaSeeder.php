<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;

class ProviderMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample salon/beauty images URLs (using placeholder images)
        $logoUrls = [
            'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1487412912498-0447578fcca8?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1600948836101-f9ffda59d250?w=400&h=400&fit=crop',
        ];

        $galleryUrls = [
            'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1487412912498-0447578fcca8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1600948836101-f9ffda59d250?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1519415387722-a1c3bbef716c?w=800&h=600&fit=crop',
        ];

        $providers = ServiceProvider::all();

        foreach ($providers as $index => $provider) {
            // Add logo from URL
            $logoIndex = $index % count($logoUrls);
            try {
                $provider->addMediaFromUrl($logoUrls[$logoIndex])
                    ->toMediaCollection('logo');

                $this->command->info("Added logo for provider: {$provider->business_name}");
            } catch (\Exception $e) {
                $this->command->error("Failed to add logo for {$provider->business_name}: {$e->getMessage()}");
            }

            // Add 2-3 gallery images
            $galleryCount = rand(2, 3);
            for ($i = 0; $i < $galleryCount; $i++) {
                $galleryIndex = ($index * 3 + $i) % count($galleryUrls);
                try {
                    $provider->addMediaFromUrl($galleryUrls[$galleryIndex])
                        ->toMediaCollection('gallery');
                } catch (\Exception $e) {
                    $this->command->error("Failed to add gallery image for {$provider->business_name}: {$e->getMessage()}");
                }
            }

            $this->command->info("Added {$galleryCount} gallery images for provider: {$provider->business_name}");
        }

        $this->command->info('Provider media seeding completed!');
    }
}

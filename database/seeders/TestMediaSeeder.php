<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TestMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder adds test images from the Flutter assets folder to providers and services
     * to visualize how the media will look in the app.
     */
    public function run(): void
    {
        // Base URL for accessing Flutter assets (you can change this to match your setup)
        // For testing, we'll use placeholder image URLs that look similar
        $placeholderBase = 'https://picsum.photos';

        // Get all service providers
        $providers = ServiceProvider::with('services')->get();

        if ($providers->isEmpty()) {
            Log::info('No service providers found. Please run ProviderSeeder first.');
            return;
        }

        Log::info("Adding test images to {$providers->count()} providers...");

        // Test images for providers
        $providerLogos = [
            'saloon1' => $placeholderBase . '/800/600?random=1',
            'saloon2' => $placeholderBase . '/800/600?random=2',
            'saloon3' => $placeholderBase . '/800/600?random=3',
            'top1' => $placeholderBase . '/800/600?random=4',
            'top2' => $placeholderBase . '/800/600?random=5',
        ];

        $galleryImages = [
            $placeholderBase . '/400/400?random=10', // ex1
            $placeholderBase . '/400/400?random=11', // ex2
            $placeholderBase . '/400/400?random=12', // ex3
        ];

        // Test images for services
        $serviceImages = [
            'service1' => $placeholderBase . '/300/300?random=20',
            'service2' => $placeholderBase . '/300/300?random=21',
            'service3' => $placeholderBase . '/300/300?random=22',
            'cut1' => $placeholderBase . '/300/300?random=23',
            'cut2' => $placeholderBase . '/300/300?random=24',
            'cut3' => $placeholderBase . '/300/300?random=25',
        ];

        $logoIndex = 0;
        $serviceImageIndex = 0;

        foreach ($providers as $provider) {
            // Add logo using media library
            $logoUrl = array_values($providerLogos)[$logoIndex % count($providerLogos)];

            try {
                // Add logo to media collection
                $provider->addMediaFromUrl($logoUrl)
                    ->toMediaCollection('logo');

                // Add gallery images
                foreach (array_slice($galleryImages, 0, 2) as $galleryUrl) {
                    $provider->addMediaFromUrl($galleryUrl)
                        ->toMediaCollection('gallery');
                }

                Log::info("Added media to provider: {$provider->business_name}");
            } catch (\Exception $e) {
                Log::warning("Could not add media to provider {$provider->id}: {$e->getMessage()}");
            }

            $logoIndex++;

            // Add images to provider's services
            foreach ($provider->services as $service) {
                $imageUrl = array_values($serviceImages)[$serviceImageIndex % count($serviceImages)];

                $service->update([
                    'image_url' => $imageUrl,
                    'gallery' => [
                        $placeholderBase . '/300/300?random=' . (30 + $serviceImageIndex * 3),
                        $placeholderBase . '/300/300?random=' . (31 + $serviceImageIndex * 3),
                        $placeholderBase . '/300/300?random=' . (32 + $serviceImageIndex * 3),
                    ],
                ]);

                $serviceImageIndex++;
            }
        }

        Log::info('Test media seeder completed successfully!');
    }
}

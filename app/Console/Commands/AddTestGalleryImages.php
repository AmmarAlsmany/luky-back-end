<?php

namespace App\Console\Commands;

use App\Models\ServiceProvider;
use Illuminate\Console\Command;

class AddTestGalleryImages extends Command
{
    protected $signature = 'test:add-gallery';
    protected $description = 'Add test gallery images to providers';

    public function handle()
    {
        $this->info('Adding test gallery images to providers...');

        $providers = ServiceProvider::all();

        foreach ($providers as $provider) {
            try {
                // Add 2 gallery images per provider
                $provider->addMediaFromUrl('https://picsum.photos/400/400?random=' . ($provider->id * 10 + 1))
                    ->toMediaCollection('gallery');

                $provider->addMediaFromUrl('https://picsum.photos/400/400?random=' . ($provider->id * 10 + 2))
                    ->toMediaCollection('gallery');

                $this->info("✓ Added gallery to provider {$provider->id}: {$provider->business_name}");
            } catch (\Exception $e) {
                $this->error("✗ Error for provider {$provider->id}: " . $e->getMessage());
            }
        }

        $this->info('Done!');
        return 0;
    }
}

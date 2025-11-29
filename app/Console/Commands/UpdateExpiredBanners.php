<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UpdateExpiredBanners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update banner statuses - mark expired banners and activate scheduled ones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        // 1. Mark expired banners (end_date has passed)
        $expiredCount = DB::table('banners')
            ->where('status', '!=', 'expired')
            ->where('end_date', '<', $today)
            ->update([
                'status' => 'expired',
                'is_active' => false,
                'updated_at' => now()
            ]);

        // 2. Activate scheduled banners (start_date has arrived)
        $activatedCount = DB::table('banners')
            ->where('status', 'scheduled')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->update([
                'status' => 'active',
                'is_active' => true,
                'updated_at' => now()
            ]);

        // 3. Optional: Delete old expired banner images (older than 30 days)
        $oldExpiredBanners = DB::table('banners')
            ->where('status', 'expired')
            ->where('end_date', '<', now()->subDays(30)->toDateString())
            ->whereNotNull('image_url')
            ->get();

        $deletedImages = 0;
        foreach ($oldExpiredBanners as $banner) {
            // Extract file path from URL
            if ($banner->image_url && strpos($banner->image_url, 'storage/') !== false) {
                $path = str_replace(url('storage/'), '', $banner->image_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deletedImages++;
                }
            }
        }

        Log::info('Banner status updated', [
            'expired' => $expiredCount,
            'activated' => $activatedCount,
            'images_deleted' => $deletedImages
        ]);

        $this->info("✓ {$expiredCount} banner(s) marked as expired");
        $this->info("✓ {$activatedCount} banner(s) activated");
        $this->info("✓ {$deletedImages} old banner image(s) deleted");

        return Command::SUCCESS;
    }
}

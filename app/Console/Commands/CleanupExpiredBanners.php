<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredBanners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:cleanup-expired {--days=30 : Delete banners expired more than X days ago}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired banners and their images that have been expired for a specified number of days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days)->format('Y-m-d');

        $this->info("Cleaning up banners expired before: {$cutoffDate}");

        // Get expired banners
        $expiredBanners = DB::table('banners')
            ->where('end_date', '<', $cutoffDate)
            ->get();

        if ($expiredBanners->isEmpty()) {
            $this->info('No expired banners found to cleanup.');
            return 0;
        }

        $deletedCount = 0;
        $failedCount = 0;

        foreach ($expiredBanners as $banner) {
            try {
                // Delete image file
                if ($banner->image_url) {
                    if (Storage::disk('public')->exists($banner->image_url)) {
                        Storage::disk('public')->delete($banner->image_url);
                        $this->line("Deleted image: {$banner->image_url}");
                    }
                }

                // Delete banner clicks
                DB::table('banner_clicks')->where('banner_id', $banner->id)->delete();

                // Delete banner record
                DB::table('banners')->where('id', $banner->id)->delete();

                $deletedCount++;
                $this->info("✓ Deleted banner ID {$banner->id}: {$banner->title}");
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("✗ Failed to delete banner ID {$banner->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Cleanup completed!");
        $this->info("Deleted: {$deletedCount} banners");

        if ($failedCount > 0) {
            $this->warn("Failed: {$failedCount} banners");
        }

        return 0;
    }
}

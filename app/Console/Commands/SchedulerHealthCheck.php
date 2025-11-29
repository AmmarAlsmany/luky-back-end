<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SchedulerHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:health-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Laravel scheduler is running properly and all critical jobs are executing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏥 Scheduler Health Check');
        $this->newLine();

        $allHealthy = true;

        // 1. Check if scheduler heartbeat is recent (updated every minute)
        $lastRun = Cache::get('scheduler:last_run');
        $now = now();

        if ($lastRun) {
            $minutesAgo = $now->diffInMinutes($lastRun);
            if ($minutesAgo <= 2) {
                $this->info("✓ Scheduler heartbeat: Running (last run {$minutesAgo} minute(s) ago)");
            } else {
                $this->error("✗ Scheduler heartbeat: NOT RUNNING (last run {$minutesAgo} minutes ago)");
                $allHealthy = false;
            }
        } else {
            $this->warn("⚠ Scheduler heartbeat: No data (first run or cache cleared)");
        }

        // 2. Check critical jobs
        $this->newLine();
        $this->info('Critical Jobs Status:');

        // Check pending bookings
        $pendingBookings = DB::table('bookings')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->count();

        if ($pendingBookings > 0) {
            $this->warn("⚠ {$pendingBookings} booking(s) pending > 30 minutes (should be auto-cancelled)");
        } else {
            $this->info("✓ No stale pending bookings");
        }

        // Check unpaid bookings
        $unpaidBookings = DB::table('bookings')
            ->where('status', 'confirmed')
            ->where('payment_status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(15))
            ->count();

        if ($unpaidBookings > 0) {
            $this->warn("⚠ {$unpaidBookings} unpaid booking(s) > 15 minutes old");
        } else {
            $this->info("✓ No stale unpaid bookings");
        }

        // Check finished bookings not completed
        $finishedBookings = DB::table('bookings')
            ->where('status', 'in_progress')
            ->where('end_time', '<=', now()->subMinutes(5))
            ->count();

        if ($finishedBookings > 0) {
            $this->warn("⚠ {$finishedBookings} booking(s) should be auto-completed");
        } else {
            $this->info("✓ No bookings needing auto-completion");
        }

        // 3. Check scheduler log file
        $this->newLine();
        $logPath = storage_path('logs/scheduler.log');
        if (file_exists($logPath)) {
            $logSize = filesize($logPath);
            $lastModified = filemtime($logPath);
            $minutesSinceModified = floor((time() - $lastModified) / 60);

            if ($minutesSinceModified <= 2) {
                $this->info("✓ Scheduler log is active (updated {$minutesSinceModified} minute(s) ago)");
            } else {
                $this->error("✗ Scheduler log is stale (last updated {$minutesSinceModified} minutes ago)");
                $allHealthy = false;
            }

            $this->info("  Log size: " . round($logSize / 1024, 2) . " KB");
        } else {
            $this->warn("⚠ Scheduler log file not found");
        }

        // 4. Summary
        $this->newLine();
        if ($allHealthy) {
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('✓ ALL SYSTEMS HEALTHY');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            Log::info('Scheduler health check: All systems healthy');
            return 0;
        } else {
            $this->error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->error('✗ SCHEDULER ISSUES DETECTED');
            $this->error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            Log::error('Scheduler health check: Issues detected');
            return 1;
        }
    }
}

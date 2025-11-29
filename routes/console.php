<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
 use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler heartbeat - updates cache every minute to verify scheduler is running
Schedule::call(function () {
    \Illuminate\Support\Facades\Cache::put('scheduler:last_run', now(), now()->addHours(24));
})->everyMinute()->name('scheduler-heartbeat');

// Run every minute to auto-cancel bookings if provider doesn't respond
Schedule::job(new \App\Jobs\CancelPendingBookings)->everyMinute();

// Run every minute to check for expired bookings (payment not completed)
Schedule::job(new \App\Jobs\CancelUnpaidBookings)->everyMinute();

// Run every minute to check for expired payments and send FCM timeout notifications
Schedule::command('payments:check-expired')->everyMinute();

// Run every 5 minutes to auto-complete bookings after service end_time has passed
Schedule::job(new \App\Jobs\CompleteFinishedBookings)->everyFiveMinutes();

// Run hourly to update banner statuses (expire old, activate scheduled)
// This keeps the status field in sync, though the API checks dates directly
Schedule::command('banners:update-expired')->hourly();

// Run weekly to cleanup expired banners (delete banners expired more than 30 days ago)
Schedule::command('banners:cleanup-expired')->weekly();

// Run daily health check at 9 AM to monitor scheduler
Schedule::command('scheduler:health-check')->dailyAt('09:00');

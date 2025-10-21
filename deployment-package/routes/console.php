<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule expiry notifications
Schedule::command('notifications:send-expiry')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/expiry-notifications.log'));

// Schedule urgent notifications for critical expiries (within 3 days)
Schedule::command('notifications:send-expiry --days=3')
    ->twiceDaily(8, 16)
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/critical-notifications.log'));

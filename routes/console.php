<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('app:dispatch-pay-slip-generation')->dailyAt('11:22')->timezone('Asia/Kolkata')->emailOutputOnFailure()->runInBackground()->appendOutputTo(storage_path('logs/dispatch-pay-slip-generation.log'))->onFailure(function () {
//     $this->error('Failed to dispatch pay slip generation jobs.');
// })->onSuccess(function () {
//     $this->info('Pay slip generation jobs dispatched successfully.');
// });
Schedule::command('app:dispatch-pay-slip-generation')
    ->dailyAt('02:00')
    ->timezone('Asia/Kolkata')
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/dispatch-pay-slip-generation.log'))
    ->onFailure(function () {
        $this->error('CONSOLE: Failed to dispatch pay slip generation jobs.');
    })
    ->onSuccess(function () {
        $this->info('CONSOLE: Pay slip generation jobs dispatched successfully.');
    });

Schedule::command('app:prune-db-sessions')
    ->daily()
    ->at('02:00')
    ->appendOutputTo(storage_path('logs/session_cleanup.log'));

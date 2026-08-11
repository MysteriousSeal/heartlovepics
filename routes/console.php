<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('view-events:prune')->daily();
Schedule::command('images:prune-trashed')->hourly();
// Sprinkle 10–20 views and 1–2 likes across public posts so the gallery stays lively.
Schedule::command('engagement:seed-random')->everyMinute();
// Keep the admin Cron log from growing forever (every-minute jobs add up quickly).
Schedule::command('cron-logs:prune')->daily();

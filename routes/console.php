<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Scheduler: expiration automatique des inscriptions
$pending = Artisan::command('lms:expire-enrollments:run', function () {
    Artisan::call('lms:expire-enrollments');
})->purpose('Expire enrollments per deadlines');

if (app()->environment('production')) {
    $pending->dailyAt('02:00');
} else {
    // En dev, exécuter plus souvent pour tests
    $pending->everyFiveMinutes();
}

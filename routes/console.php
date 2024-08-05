<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})
    ->purpose("Display an inspiring quote")
    ->hourly();

Schedule::call(function () {
    \App\Http\Controllers\NotificationController::enviarNotificaciones();
})->dailyAt("08:00");

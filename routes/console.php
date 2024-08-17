<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    \App\Http\Controllers\NotificationController::enviarNotificaciones();
})->dailyAt("09:00");
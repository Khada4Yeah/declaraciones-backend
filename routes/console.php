<?php

use Illuminate\Support\Facades\Schedule;
use App\Services\NotificacionService;

Schedule::call(function () {
    app(NotificacionService::class)->enviarNotificaciones();
})->dailyAt("09:00");
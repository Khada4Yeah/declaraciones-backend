<?php

namespace App\Http\Controllers;

use App\Services\NotificacionService;

/**
 * Controlador de notificaciones.
 * Delega la lógica de envío al NotificacionService.
 */
class NotificationController extends Controller
{
    public function __construct(
        private NotificacionService $notificacionService,
    ) {}

    /**
     * Ejecuta el envío de notificaciones de vencimiento de declaraciones.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarNotificaciones()
    {
        $this->notificacionService->enviarNotificaciones();

        return response()->json([
            "status" => "success",
            "message" => "Notificaciones enviadas correctamente",
        ]);
    }
}
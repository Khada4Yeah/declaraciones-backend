<?php

namespace App\Services;

use App\Models\PersonaJuridica;
use App\Models\PersonaNatural;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Servicio que encapsula la lógica de envío de notificaciones de vencimiento
 * de declaraciones del SRI. Es invocado por el Artisan Command y el Scheduler.
 */
class NotificacionService
{
    /**
     * Tabla de días de declaración según el noveno dígito del RUC/cédula.
     * Basada en el calendario del SRI de Ecuador.
     */
    private const TABLA_DIAS = [
        1 => 10, 2 => 12, 3 => 14, 4 => 16, 5 => 18,
        6 => 20, 7 => 22, 8 => 24, 9 => 26, 0 => 28,
    ];

    /**
     * Envía notificaciones de vencimiento de declaraciones.
     * Revisa 2 días antes, 1 día antes y el mismo día de vencimiento.
     *
     * @return void
     */
    public function enviarNotificaciones(): void
    {
        $dia_actual = Carbon::today();
        $intervalos = [2, 1, 0];

        foreach ($intervalos as $intervalo) {
            $dia_notificacion = $dia_actual->copy()->addDays($intervalo);
            $this->chequearYEnviarNotificaciones($dia_notificacion, $intervalo);
        }
    }

    /**
     * Recorre todas las personas (naturales y jurídicas) y envía notificaciones
     * a quienes les corresponda declarar en el día indicado.
     *
     * @param Carbon $diaNotificacion Día de declaración a comprobar.
     * @param int $intervalo Días restantes para el vencimiento (0, 1 o 2).
     * @return void
     */
    private function chequearYEnviarNotificaciones(Carbon $diaNotificacion, int $intervalo): void
    {
        $personas_juridicas = PersonaJuridica::with("usuario")->get();
        $personas_naturales = PersonaNatural::with("usuario")->get();

        foreach ($personas_juridicas as $persona) {
            $dia_declaracion = $this->obtenerDiaDeclaracion($persona->ruc);

            if ($dia_declaracion && $diaNotificacion->day === $dia_declaracion) {
                $this->enviarCorreo($persona, $intervalo);
            }
        }

        foreach ($personas_naturales as $persona) {
            $dia_declaracion = $this->obtenerDiaDeclaracion($persona->identificacion);

            if ($dia_declaracion && $diaNotificacion->day === $dia_declaracion) {
                $this->enviarCorreo($persona, $intervalo);
            }
        }
    }

    /**
     * Calcula el día de declaración según el noveno dígito de la identificación.
     *
     * @param string $identificacion Cédula (10 dígitos) o RUC (13 dígitos).
     * @return int Día del mes en que vence la declaración.
     */
    public function obtenerDiaDeclaracion(string $identificacion): int
    {
        $noveno_digito = (int) substr($identificacion, 8, 1);

        return self::TABLA_DIAS[$noveno_digito] ?? 0;
    }

    /**
     * Envía un correo electrónico de recordatorio de vencimiento de declaración.
     *
     * @param PersonaJuridica|PersonaNatural $persona Persona a notificar.
     * @param int $intervalo Días restantes para el vencimiento.
     * @return void
     */
    private function enviarCorreo(PersonaJuridica|PersonaNatural $persona, int $intervalo): void
    {
        $sujeto = $persona instanceof PersonaJuridica
            ? $persona->razon_social
            : "{$persona->nombres} {$persona->apellido_p} {$persona->apellido_m}";

        $email = config("app.notification_email", env("NOTIFICATION_EMAIL", "admin@example.com"));

        $dias_restantes = match ($intervalo) {
            0 => "hoy",
            1 => "mañana",
            default => "en dos días",
        };

        try {
            Mail::raw(
                "Estimada Vivina, recuerde que la declaración de $sujeto vence $dias_restantes.",
                function ($message) use ($email) {
                    $message
                        ->to($email)
                        ->subject("Recordatorio de Vencimiento de Declaración");
                },
            );
        } catch (\Exception $e) {
            Log::error("Error al enviar correo de notificación a {$email}: " . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PersonaJuridica;
use App\Models\PersonaNatural;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public static function enviarNotificaciones()
    {
        $dia_actual = Carbon::today();
        $dia_intervalos = [2, 1, 0]; // 2 dias antes, 1 dia antes, el mismo día

        foreach ($dia_intervalos as $intervalo) {
            $dia_notificacion = $dia_actual->copy()->addDays($intervalo);
            self::chequearYEnviarNotificaciones($dia_notificacion, $intervalo);
        }
    }

    private static function chequearYEnviarNotificaciones(
        $diaNotificacion,
        $intevalo,
    ) {
        $personas_juridicas = PersonaJuridica::all();
        $personas_naturales = PersonaNatural::all();

        foreach ($personas_juridicas as $persona_juridica) {
            $dia_declaracion = self::obtenerDiaDeclaracion(
                $persona_juridica->ruc,
            );

            if (
                $dia_declaracion &&
                $diaNotificacion->day === $dia_declaracion
            ) {
                self::enviarCorreo($persona_juridica, $intevalo);
            }
        }

        foreach ($personas_naturales as $persona_natural) {
            $dia_declaracion = self::obtenerDiaDeclaracion(
                $persona_natural->identificacion,
            );

            if (
                $dia_declaracion &&
                $diaNotificacion->day === $dia_declaracion
            ) {
                self::enviarCorreo($persona_natural, $intevalo);
            }
        }
    }

    /**
     *
     */
    private static function obtenerDiaDeclaracion($identifiacion): int
    {
        $noveno_digito = substr($identifiacion, 8, 1);
        $tabla_dias = [
            1 => 10,
            2 => 12,
            3 => 14,
            4 => 16,
            5 => 18,
            6 => 20,
            7 => 22,
            8 => 24,
            9 => 26,
            0 => 28,
        ];

        return $tabla_dias[$noveno_digito];
    }

    private static function enviarCorreo($persona, $intervalo)
    {
        $sujeto = "";

        if ($persona instanceof PersonaJuridica) {
            $sujeto = $persona->razon_social;
        } elseif ($persona instanceof PersonaNatural) {
            $sujeto = "{$persona->nombres} {$persona->apellido_p} {$persona->apellido_m}";
        }

        $email = "dveras2487@gmail.com";
        $dias_restantes =
            $intervalo === 0
                ? "hoy"
                : ($intervalo === 1
                    ? "mañana"
                    : "en dos días");

        Mail::raw(
            "Estimada Vivina, recuerde que la declaración de $sujeto vence $dias_restantes.",
            function ($message) use ($email) {
                $message
                    ->to($email)
                    ->subject("Recordatorio de Vencimiento de Declaración");
            },
        );
    }
}
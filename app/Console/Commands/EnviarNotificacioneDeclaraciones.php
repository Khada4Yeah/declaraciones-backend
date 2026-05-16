<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificacionService;

/**
 * Comando Artisan para enviar notificaciones de vencimiento de declaraciones.
 * Uso: php artisan declaraciones:notificar-vencimientos
 */
class EnviarNotificacioneDeclaraciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'declaraciones:notificar-vencimientos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa fechas de declaración y envía correos de recordatorio';

    /**
     * Execute the console command.
     */
    public function handle(NotificacionService $notificacionService): void
    {
        $this->info('Iniciando envío de notificaciones...');

        try {
            $notificacionService->enviarNotificaciones();
            $this->info('Notificaciones enviadas con éxito.');
        } catch (\Exception $e) {
            $this->error('Error al enviar: ' . $e->getMessage());
        }
    }
}

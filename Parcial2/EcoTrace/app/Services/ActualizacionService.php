<?php
namespace App\Services;

use Pusher\Pusher;
use Throwable;

/** Publicación DIRECTA a Reverb mediante su API Pusher. Sin clases App\Events. */
class ActualizacionService
{
    public function notificar(string $mensaje): void
    {
        try {
            $app = config('reverb.apps.apps.0');
            $pusher = new Pusher(
                $app['key'], $app['secret'], $app['app_id'],
                [
                    'host' => $app['options']['host'],
                    'port' => (int) $app['options']['port'],
                    'scheme' => 'http',
                    'useTLS' => false,
                ]
            );
            // Reverb usa el nombre "event" como parte del protocolo WebSocket;
            // esto NO crea ni despacha eventos de Laravel.
            $pusher->trigger('muestras', 'muestras.actualizadas', [
                'mensaje' => $mensaje,
                'hora' => now()->toDateTimeString(),
            ]);
        } catch (Throwable $e) {
            // La operación de negocio ya quedó guardada; no depende de Reverb.
            logger()->warning('Notificación en tiempo real no disponible', ['error'=>$e->getMessage()]);
        }
    }
}

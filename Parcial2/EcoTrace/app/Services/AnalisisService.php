<?php
namespace App\Services;

use App\Models\Analisis;
use App\Models\Muestra;
use DomainException;
use Illuminate\Support\Facades\DB;

class AnalisisService
{
    public function __construct(private ActualizacionService $actualizaciones) {}

    public function iniciar(Muestra $muestra): void
    {
        DB::transaction(function () use ($muestra) {
            $actual = Muestra::lockForUpdate()->findOrFail($muestra->id);
            if ($actual->estado !== 'almacenada') {
                throw new DomainException('Solo se analizan muestras almacenadas.');
            }
            $actual->estado = 'en_analisis';
            $actual->save();
        });
        $this->actualizaciones->notificar('Análisis iniciado');
    }

    public function finalizar(Muestra $muestra, int $investigadorId, string $resultado): Analisis
    {
        $analisis = DB::transaction(function () use ($muestra, $investigadorId, $resultado) {
            $actual = Muestra::lockForUpdate()->findOrFail($muestra->id);
            if ($actual->estado !== 'en_analisis') {
                throw new DomainException('Primero debes iniciar el análisis de la muestra.');
            }
            if ($actual->responsable_id !== $investigadorId) {
                throw new DomainException('Solo el responsable actual puede registrar el análisis.');
            }
            $registro = Analisis::create([
                'muestra_id' => $actual->id,
                'investigador_id' => $investigadorId,
                'resultado' => $resultado,
                'fecha_analisis' => now(),
            ]);
            $actual->estado = 'analizada';
            $actual->save();
            return $registro;
        });
        $this->actualizaciones->notificar('Análisis finalizado');
        return $analisis;
    }
}

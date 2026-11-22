<?php
namespace App\Services;

use App\Models\Investigador;
use App\Models\Muestra;
use App\Models\Transferencia;
use DomainException;
use Illuminate\Support\Facades\DB;

class CustodiaService
{
    public function __construct(private ActualizacionService $actualizaciones) {}

    public function transferir(Muestra $muestra, int $destinoId, string $motivo): Transferencia
    {
        $transferencia = DB::transaction(function () use ($muestra, $destinoId, $motivo) {
            $actual = Muestra::lockForUpdate()->findOrFail($muestra->id);
            Investigador::findOrFail($destinoId);

            if (!in_array($actual->estado, ['recolectada', 'almacenada'], true)) {
                throw new DomainException('Esta muestra no admite transferencias en su estado actual.');
            }
            if ($actual->responsable_id === $destinoId) {
                throw new DomainException('La muestra ya pertenece a ese investigador.');
            }

            $registro = Transferencia::create([
                'muestra_id' => $actual->id,
                'origen_investigador_id' => $actual->responsable_id,
                'destino_investigador_id' => $destinoId,
                'motivo' => $motivo,
            ]);
            $actual->responsable_id = $destinoId;
            $actual->save();
            return $registro;
        });
        $this->actualizaciones->notificar('Custodia transferida');
        return $transferencia;
    }
}

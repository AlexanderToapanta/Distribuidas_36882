
<?php

namespace App\Services;

use App\Models\Investigador;
use App\Models\Muestra;
use App\Models\Transferencia;
use App\Domain\PoliticaCustodia;
use Illuminate\Support\Facades\DB;

class CustodiaService
{
    public function __construct(
        private ActualizacionService $actualizaciones
    ) {}

    public function transferir(
        Muestra $muestra,
        int $destinoId,
        string $motivo
    ): Transferencia {

        $transferencia = DB::transaction(function () use ($muestra, $destinoId, $motivo) {

            $actual = Muestra::lockForUpdate()
                ->findOrFail($muestra->id);

            Investigador::findOrFail($destinoId);

            // Validar las reglas de negocio
            PoliticaCustodia::validarTransferencia(
                $actual->estado,
                (int) $actual->responsable_id,
                $destinoId
            );

            // Registrar el historial de transferencia
            $registro = Transferencia::create([
                'muestra_id' => $actual->id,
                'origen_investigador_id' => $actual->responsable_id,
                'destino_investigador_id' => $destinoId,
                'motivo' => $motivo,
            ]);

            // Actualizar el responsable
            $actual->responsable_id = $destinoId;
            $actual->save();

            return $registro;
        });

        // Notificar después de confirmar la transacción
        $this->actualizaciones->notificar('Custodia transferida');

        return $transferencia;
    }
}

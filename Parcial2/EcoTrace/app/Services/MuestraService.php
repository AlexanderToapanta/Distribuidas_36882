<?php
namespace App\Services;

use App\Models\Muestra;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MuestraService
{
    public function __construct(private ActualizacionService $actualizaciones) {}

   
public function crear(array $datos, ?int $idReservado = null): Muestra
{
    unset($datos['codigo']);

    $muestra = DB::transaction(function () use ($datos, $idReservado) {

        if ($idReservado !== null) {
            // Utilizar el ID reservado en el formulario.
            $muestra = new Muestra($datos);
            $muestra->id = $idReservado;

            $muestra->codigo = 'M-' . str_pad(
                (string) $muestra->id,
                3,
                '0',
                STR_PAD_LEFT
            );

            $muestra->save();
        } else {
            // Compatibilidad con comandos y pruebas existentes.
            $muestra = Muestra::create($datos);

            $muestra->codigo = 'M-' . str_pad(
                (string) $muestra->id,
                3,
                '0',
                STR_PAD_LEFT
            );

            $muestra->save();
        }

        return $muestra;
    });

    $this->actualizaciones->notificar(
        'Muestra ' . $muestra->codigo . ' registrada'
    );

    return $muestra;
}


    public function consultar(string $codigo): ?Muestra
    {
        return Muestra::with('responsable')->where('codigo', Str::upper($codigo))->first();
    }

    public function almacenar(Muestra $muestra): void
    {
        DB::transaction(function () use ($muestra) {
            $actual = Muestra::lockForUpdate()->findOrFail($muestra->id);
            if ($actual->estado !== 'recolectada') {
                throw new DomainException('Solo se puede almacenar una muestra recién recolectada.');
            }
            $actual->estado = 'almacenada';
            $actual->save();
        });
        $this->actualizaciones->notificar('Muestra almacenada');
    }

    public function descartar(Muestra $muestra): void
    {
        DB::transaction(function () use ($muestra) {
            $actual = Muestra::lockForUpdate()->findOrFail($muestra->id);
            if (!in_array($actual->estado, ['recolectada', 'almacenada'], true)) {
                throw new DomainException('No se puede descartar una muestra en análisis, analizada o descartada.');
            }
            $actual->estado = 'descartada';
            $actual->save();
        });
        $this->actualizaciones->notificar('Muestra descartada');
    }
}

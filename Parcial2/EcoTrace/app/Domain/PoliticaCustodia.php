<?php

namespace App\Domain;

use DomainException;

final class PoliticaCustodia
{
    public static function validarTransferencia(
        string $estado,
        int $custodioActualId,
        int $nuevoCustodioId
    ): void {
        if (!in_array($estado, ['recolectada', 'almacenada'], true)) {
            throw new DomainException('El estado de la muestra no permite transferencias.');
        }

        if ($custodioActualId === $nuevoCustodioId) {
            throw new DomainException('El nuevo custodio debe ser diferente del actual.');
        }
    }
}

<?php

namespace Tests\Unit;

use App\Domain\PoliticaCustodia;
use DomainException;
use PHPUnit\Framework\TestCase;

class PoliticaCustodiaTest extends TestCase
{
    public function test_permite_transferir_una_muestra_recolectada_a_otro_custodio(): void
    {
        PoliticaCustodia::validarTransferencia('recolectada', 1, 2);
        $this->addToAssertionCount(1);
    }

    public function test_permite_transferir_una_muestra_almacenada_a_otro_custodio(): void
    {
        PoliticaCustodia::validarTransferencia('almacenada', 1, 2);
        $this->addToAssertionCount(1);
    }

    public function test_rechaza_una_muestra_descartada(): void
    {
        $this->expectException(DomainException::class);
        PoliticaCustodia::validarTransferencia('descartada', 1, 2);
    }

    public function test_rechaza_transferencia_al_mismo_custodio(): void
    {
        $this->expectException(DomainException::class);
        PoliticaCustodia::validarTransferencia('recolectada', 1, 1);
    }
}
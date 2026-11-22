<?php
use App\Models\Muestra;
use App\Services\CustodiaService;
use App\Services\MuestraService;
use Illuminate\Support\Facades\Artisan;

// Artisan llama a los mismos servicios que los controladores web.
Artisan::command('muestras:consultar {codigo}', function (string $codigo) {
    $muestra = app(MuestraService::class)->consultar($codigo);
    if (!$muestra) { $this->error('No se encontró la muestra.'); return; }
    $this->info("{$muestra->codigo}: {$muestra->tipo} | {$muestra->estado} | {$muestra->responsable->nombre}");
})->purpose('Consultar una muestra desde la terminal');

Artisan::command('muestras:transferir {codigo} {investigador}', function (string $codigo, int $investigador) {
    $muestra = app(MuestraService::class)->consultar($codigo);
    if (!$muestra) { $this->error('No se encontró la muestra.'); return; }
    try {
        app(CustodiaService::class)->transferir($muestra, $investigador, 'Transferencia vía Artisan');
        $this->info('Custodia transferida correctamente.');
    } catch (DomainException $e) { $this->error($e->getMessage()); }
})->purpose('Transferir una muestra usando el servicio de custodia');

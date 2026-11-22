<?php
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\CustodiaController;
use App\Http\Controllers\MuestraController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/muestras');
Route::get('/muestras', [MuestraController::class, 'index'])->name('muestras.index');
Route::get('/muestras/crear', [MuestraController::class, 'create'])->name('muestras.create');
Route::post('/muestras', [MuestraController::class, 'store'])->name('muestras.store');
Route::get('/muestras/{muestra}', [MuestraController::class, 'show'])->name('muestras.show');
Route::post('/muestras/{muestra}/almacenar', [MuestraController::class, 'almacenar'])->name('muestras.almacenar');
Route::post('/muestras/{muestra}/descartar', [MuestraController::class, 'descartar'])->name('muestras.descartar');
Route::post('/muestras/{muestra}/transferir', [CustodiaController::class, 'transferir'])->name('muestras.transferir');
Route::post('/muestras/{muestra}/analisis/iniciar', [AnalisisController::class, 'iniciar'])->name('analisis.iniciar');
Route::post('/muestras/{muestra}/analisis/finalizar', [AnalisisController::class, 'finalizar'])->name('analisis.finalizar');

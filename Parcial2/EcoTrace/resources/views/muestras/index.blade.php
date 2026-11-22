@extends('layouts.app')
@section('title', 'Muestras — EcoTrace')
@section('content')
<div class="toolbar">
  <div><h1>Panel de muestras</h1><div class="muted">Seguimiento del ciclo de vida y custodia de muestras ambientales.</div></div>
  <a class="btn" href="{{ route('muestras.create') }}">+ Registrar muestra</a>
</div>
<div class="grid">
  <section class="card"><div class="muted small">Muestras totales</div><div class="number">{{ $muestras->count() }}</div></section>
  <section class="card"><div class="muted small">Almacenadas</div><div class="number">{{ $totales['almacenada'] ?? 0 }}</div></section>
  <section class="card"><div class="muted small">En análisis</div><div class="number">{{ $totales['en_analisis'] ?? 0 }}</div></section>
  <section class="card"><div class="muted small">Analizadas</div><div class="number">{{ $totales['analizada'] ?? 0 }}</div></section>
</div>
<section class="card">
  <h2>Registro de muestras</h2>
  <div class="table-container"><table>
    <thead><tr><th>Código</th><th>Tipo</th><th>Origen</th><th>Responsable actual</th><th>Estado</th><th>Acción</th></tr></thead>
    <tbody>
      @forelse($muestras as $muestra)
        <tr>
          <td><strong>{{ $muestra->codigo }}</strong></td>
          <td>{{ $muestra->tipo }}</td>
          <td>{{ $muestra->origen }}</td>
          <td>{{ $muestra->responsable->nombre }}</td>
          <td><span class="pill {{ $muestra->estado }}">{{ str_replace('_',' ',$muestra->estado) }}</span></td>
          <td><a href="{{ route('muestras.show',$muestra) }}">Abrir detalle</a></td>
        </tr>
      @empty
        <tr><td colspan="6">Todavía no hay muestras. Registra la primera.</td></tr>
      @endforelse
    </tbody>
  </table></div>
</section>
@endsection

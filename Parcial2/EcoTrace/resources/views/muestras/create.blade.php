@extends('layouts.app')
@section('title', 'Registrar muestra — EcoTrace')
@section('content')
<div class="toolbar"><div><h1>Registrar muestra</h1><div class="muted">Los registros nuevos empiezan en estado «recolectada».</div></div><a class="btn outline" href="{{ route('muestras.index') }}">Volver</a></div>
<form class="card" action="{{ route('muestras.store') }}" method="POST">
  @csrf
  <div class="formgrid">
   
<div class="field">
    <label for="codigo">Código único (automático)</label>

    <input
        id="codigo"
        type="text"
        value="{{ $codigo }}"
        readonly
        style="background:#f2f5f3;cursor:not-allowed;"
    >

    <input
        type="hidden"
        name="reserva"
        value="{{ $reserva }}"
    >

    <div class="muted small">
        Código reservado automáticamente por EcoTrace.
    </div>
</div>

    <div class="field"><label for="tipo">Tipo de muestra</label><select id="tipo" name="tipo" required>@foreach(['Agua','Suelo','Aire','Vegetación'] as $tipo)<option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ $tipo }}</option>@endforeach</select></div>
    <div class="field"><label for="origen">Origen</label><input id="origen" name="origen" required maxlength="120" placeholder="Río, bosque, quebrada..." value="{{ old('origen') }}"></div>
    <div class="field"><label for="lugar_recoleccion">Lugar de recolección</label><input id="lugar_recoleccion" name="lugar_recoleccion" required maxlength="120" placeholder="Zamora, Loja..." value="{{ old('lugar_recoleccion') }}"></div>
    <div class="field"><label for="fecha_recoleccion">Fecha de recolección</label><input id="fecha_recoleccion" type="date" name="fecha_recoleccion" required max="{{ now()->toDateString() }}" value="{{ old('fecha_recoleccion',now()->toDateString()) }}"></div>
    <div class="field"><label for="responsable_id">Investigador responsable</label><select id="responsable_id" name="responsable_id" required>@foreach($investigadores as $investigador)<option value="{{ $investigador->id }}" @selected(old('responsable_id') == $investigador->id)>{{ $investigador->nombre }}</option>@endforeach</select></div>
  </div>
  <div class="field"><label for="observaciones">Observaciones (opcional)</label><textarea id="observaciones" name="observaciones" maxlength="1000">{{ old('observaciones') }}</textarea></div>
  <button class="btn" type="submit">Guardar muestra</button>
</form>
@endsection

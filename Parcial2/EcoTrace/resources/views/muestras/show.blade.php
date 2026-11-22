@extends('layouts.app')
@section('title', $muestra->codigo.' — EcoTrace')
@section('content')
<div class="toolbar">
  <div><div class="muted small"><a href="{{ route('muestras.index') }}">Muestras</a> / Detalle</div><h1>{{ $muestra->codigo }} · {{ $muestra->tipo }}</h1><span class="pill {{ $muestra->estado }}">{{ str_replace('_', ' ', $muestra->estado) }}</span></div>
  <a class="btn outline" href="{{ route('muestras.index') }}">Volver al listado</a>
</div>
<div class="two">
  <div class="stack">
    <section class="card">
      <h2>Datos de la muestra</h2>
      <dl class="meta">
        <div><dt>Origen</dt><dd>{{ $muestra->origen }}</dd></div>
        <div><dt>Lugar</dt><dd>{{ $muestra->lugar_recoleccion }}</dd></div>
        <div><dt>Fecha</dt><dd>{{ $muestra->fecha_recoleccion->format('d/m/Y') }}</dd></div>
        <div><dt>Custodio actual</dt><dd>{{ $muestra->responsable->nombre }}</dd></div>
      </dl>
      @if($muestra->observaciones)<p class="muted small" style="margin-top:20px">{{ $muestra->observaciones }}</p>@endif
      <div class="actions" style="margin-top:20px">
        @if($muestra->estado === 'recolectada')
          <form method="POST" action="{{ route('muestras.almacenar',$muestra) }}">@csrf<button class="btn" type="submit">Almacenar muestra</button></form>
        @endif
        @if(in_array($muestra->estado,['recolectada','almacenada']))
          <form method="POST" action="{{ route('muestras.descartar',$muestra) }}" onsubmit="return confirm('¿Descartar esta muestra?')">@csrf<button class="btn danger" type="submit">Descartar</button></form>
        @endif
      </div>
    </section>
    <section class="card">
      <h2>Análisis de laboratorio</h2>
      @if($muestra->estado === 'almacenada')
        <p class="muted">La muestra está lista para comenzar su análisis.</p>
        <form method="POST" action="{{ route('analisis.iniciar',$muestra) }}">@csrf<button class="btn" type="submit">Iniciar análisis</button></form>
      @elseif($muestra->estado === 'en_analisis')
        <p class="muted">Registra el resultado del investigador responsable actual.</p>
        <form method="POST" action="{{ route('analisis.finalizar',$muestra) }}">
          @csrf
          <div class="field"><label for="investigador_id">Investigador que realiza el análisis</label><select id="investigador_id" name="investigador_id" required>@foreach($investigadores as $inv)<option value="{{ $inv->id }}" @selected($inv->id === $muestra->responsable_id)>{{ $inv->nombre }}</option>@endforeach</select></div>
          <div class="field"><label for="resultado">Resultado</label><textarea id="resultado" name="resultado" required minlength="5" maxlength="2000" placeholder="Describe el resultado del análisis...">{{ old('resultado') }}</textarea></div>
          <button class="btn" type="submit">Finalizar análisis</button>
        </form>
      @elseif($muestra->analisis)
        <div class="pill analizada">Finalizado</div>
        <p><strong>Investigador:</strong> {{ $muestra->analisis->investigador->nombre }}</p>
        <p><strong>Resultado:</strong> {{ $muestra->analisis->resultado }}</p>
        <div class="muted small">{{ $muestra->analisis->fecha_analisis->format('d/m/Y H:i') }}</div>
      @else
        <p class="muted">Estado actual: {{ str_replace('_',' ',$muestra->estado) }}. Todavía no hay resultado registrado.</p>
      @endif
    </section>
  </div>
  <div class="stack">
    <section class="card">
      <h2>Transferencia de custodia</h2>
      @if(in_array($muestra->estado,['recolectada','almacenada']))
        <form method="POST" action="{{ route('muestras.transferir',$muestra) }}">
          @csrf
          <div class="field"><label for="destino_investigador_id">Nuevo responsable</label><select id="destino_investigador_id" name="destino_investigador_id" required>
            @foreach($investigadores as $inv)<option value="{{ $inv->id }}" @selected(old('destino_investigador_id') == $inv->id)>{{ $inv->nombre }}</option>@endforeach
          </select></div>
          <div class="field"><label for="motivo">Motivo del traslado</label><input id="motivo" name="motivo" required maxlength="200" value="{{ old('motivo') }}" placeholder="Envío al laboratorio"></div>
          <button class="btn" type="submit">Transferir custodia</button>
        </form>
      @else
        <p class="muted">La custodia ya no se puede transferir en este estado.</p>
      @endif
    </section>
    <section class="card">
      <h2>Historial de custodia</h2>
      @forelse($muestra->transferencias as $t)
        <div style="padding:11px 0;border-bottom:1px solid #eef1ef">
          <strong>{{ $t->origen->nombre }} → {{ $t->destino->nombre }}</strong>
          <div class="muted small">{{ $t->motivo }}</div>
          <div class="muted small">{{ $t->created_at->format('d/m/Y H:i') }}</div>
        </div>
      @empty
        <p class="muted">Todavía no existen transferencias.</p>
      @endforelse
    </section>
  </div>
</div>
@endsection

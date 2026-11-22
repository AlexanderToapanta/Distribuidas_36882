<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EcoTrace')</title>
    <style>
        :root { color-scheme: light; --verde:#12624a; --verde2:#e5f4ed; --bg:#f3f6f5; --texto:#17332c; --borde:#dde7e1; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--texto); font-family:system-ui,-apple-system,Segoe UI,Arial,sans-serif; line-height:1.48; }
        header { background:#103e32; color:#fff; padding:17px max(18px,calc((100vw - 1100px)/2)); display:flex; align-items:center; justify-content:space-between; gap:16px; }
        header a { color:#fff; text-decoration:none; }
        .logo { font-weight:800; font-size:23px; letter-spacing:-.6px; }
        .sublogo { font-size:12px; color:#b8d7c9; display:block; font-weight:400; letter-spacing:0; }
        .wrap { max-width:1100px; margin:36px auto; padding:0 18px 42px; }
        h1 { font-size:29px; margin:0 0 6px; letter-spacing:-.6px; }
        h2 { margin:0 0 16px; font-size:19px; }
        p { margin:6px 0 18px; }
        a { color:var(--verde); }
        .muted { color:#687d75; }
        .muted.small,.small { font-size:13px; }
        .toolbar { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:22px; }
        .card { background:white; border:1px solid var(--borde); border-radius:12px; padding:22px; box-shadow:0 3px 12px rgba(12,45,33,.035); }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:14px; margin-bottom:21px; }
        .stack { display:grid; gap:18px; }
        .two { display:grid; grid-template-columns:1.15fr .85fr; gap:20px; }
        .number { font-size:31px; font-weight:800; margin:8px 0 0; }
        .btn { cursor:pointer; display:inline-block; border:1px solid transparent; background:var(--verde); color:#fff; border-radius:8px; padding:10px 16px; font-size:14px; text-decoration:none; font-weight:650; line-height:1.3; }
        .btn:hover { filter:brightness(.94); }
        .btn.outline { background:white; border-color:#a2c5b3; color:var(--verde); }
        .btn.danger { background:#ab3d3a; }
        .btn:disabled { opacity:.5; cursor:not-allowed; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th { background:#f7faf8; color:#60746b; font-weight:650; text-align:left; }
        td, th { padding:12px 11px; border-bottom:1px solid var(--borde); vertical-align:top; }
        tr:last-child td { border-bottom:none; }
        .table-container { overflow-x:auto; }
        .pill { display:inline-block; font-size:12px; font-weight:700; padding:4px 10px; border-radius:20px; background:#edf1ef; color:#485c52; white-space:nowrap; }
        .pill.analizada { background:#dcf3e3; color:#166137; }
        .pill.en_analisis { background:#fff0cf; color:#7b5200; }
        .pill.descartada { background:#f9e5e5; color:#973b35; }
        .pill.almacenada { background:#deedf9; color:#24608a; }
        label { display:block; font-size:13px; font-weight:650; margin-bottom:6px; }
        input,select,textarea { width:100%; font:inherit; border:1px solid #bdcec4; border-radius:8px; background:white; padding:10px 12px; color:var(--texto); }
        textarea { min-height:90px; resize:vertical; }
        .field { margin-bottom:15px; }
        .formgrid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:0 16px; }
        .alert { padding:13px 16px; border:1px solid #aed7b7; background:#e9f8ef; border-radius:8px; margin-bottom:18px; }
        .alert.error { border-color:#edb6b0; background:#ffefed; color:#8a3027; }
        .errors { color:#ac302c; font-size:13px; margin-top:3px; }
        .actions { display:flex; flex-wrap:wrap; gap:9px; align-items:center; }
        .meta { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .meta dt { color:#657b70; font-size:12px; }
        .meta dd { margin:3px 0 0; font-weight:650; }
        footer { text-align:center; font-size:12px; color:#7b8c84; margin-top:25px; }
        .ws-status { font-size:12px; font-weight:650; color:#dfd1a3; }
        @media(max-width:740px){.two,.formgrid,.meta { grid-template-columns:1fr; } .wrap{margin-top:23px;} }
    </style>
</head>
<body>
<header>
    <a href="{{ route('muestras.index') }}" class="logo">EcoTrace <span class="sublogo">Custodia y trazabilidad ambiental</span></a>
    <div style="text-align:right"><a href="{{ route('muestras.index') }}">Muestras</a> &nbsp; <a href="{{ route('muestras.create') }}">Nueva muestra</a><div id="ws-status" class="ws-status">Tiempo real: conectando…</div></div>
</header>
<main class="wrap">
    @if(session('ok'))<div class="alert">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="alert error"><strong>Revisa los datos ingresados:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
    <footer>Proyecto académico · Laravel + PostgreSQL + Docker + Reverb · Sin clases de eventos Laravel</footer>
</main>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>

<script>
(function () {
    const estado = document.getElementById('ws-status');

    if (typeof Pusher === 'undefined') {
        estado.textContent = 'Tiempo real: biblioteca no cargada';
        return;
    }

    const pusher = new Pusher(
        @json(config('reverb.apps.apps.0.key')),
        {
            wsHost: window.location.hostname,
            wsPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws'],
            cluster: 'mt1'
        }
    );

    pusher.connection.bind('state_change', (cambio) => {
        console.log('Estado WebSocket:', cambio.current);

        estado.textContent = 'Tiempo real: ' +
            (cambio.current === 'connected'
                ? 'conectado'
                : cambio.current);
    });

    pusher.connection.bind('error', (error) => {
        console.error('Error WebSocket:', error);
    });

    const canal = pusher.subscribe('muestras');

    canal.bind('pusher:subscription_succeeded', () => {
        console.log('Suscrito correctamente al canal muestras');
    });

    canal.bind('pusher:subscription_error', (error) => {
        console.error('Error al suscribirse:', error);
    });

let actualizacionPendiente = false;

canal.bind('pusher:subscription_succeeded', () => {
    console.log('Conectado al canal de muestras');
});

canal.bind('muestras.actualizadas', (datos) => {
    console.log('Actualización recibida:', datos);

    if (document.visibilityState === 'visible') {
        window.location.reload();
    } else {
        actualizacionPendiente = true;
    }
});

document.addEventListener('visibilitychange', () => {
    if (
        document.visibilityState === 'visible' &&
        actualizacionPendiente
    ) {
        window.location.reload();
    }
});
})();
</script>

</body>
</html>

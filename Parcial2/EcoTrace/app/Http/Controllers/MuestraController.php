<?php
namespace App\Http\Controllers;

use App\Models\Investigador;
use App\Models\Muestra;
use App\Services\MuestraService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MuestraController extends Controller
{
    public function index()
    {
        $muestras = Muestra::with('responsable')->orderByDesc('id')->get();
        $totales = Muestra::selectRaw('estado, COUNT(*) as cantidad')->groupBy('estado')->pluck('cantidad', 'estado');
        return view('muestras.index', compact('muestras', 'totales'));
    }

    
public function create()
{
    // Reservar el siguiente ID de PostgreSQL.
    $resultado = DB::selectOne(
        "SELECT nextval(pg_get_serial_sequence('muestras', 'id')) AS id"
    );

    $id = (int) $resultado->id;

    // Generar el código que se mostrará.
    $codigo = 'M-' . str_pad(
        (string) $id,
        3,
        '0',
        STR_PAD_LEFT
    );

    return view('muestras.create', [
        'investigadores' => Investigador::orderBy('nombre')->get(),
        'codigo' => $codigo,
        'reserva' => Crypt::encryptString((string) $id),
    ]);
}


   
public function store(Request $request, MuestraService $servicio)
{
    $datos = $request->validate([
        'tipo' => ['required', Rule::in(['Agua','Suelo','Aire','Vegetación'])],
        'origen' => ['required', 'string', 'max:120'],
        'lugar_recoleccion' => ['required', 'string', 'max:120'],
        'fecha_recoleccion' => ['required', 'date', 'before_or_equal:today'],
        'observaciones' => ['nullable', 'string', 'max:1000'],
        'responsable_id' => ['required', 'exists:investigadores,id'],
        'reserva' => ['nullable', 'string'],
    ]);

    $idReservado = null;

    if (!empty($datos['reserva'])) {
        try {
            $idReservado = (int) Crypt::decryptString($datos['reserva']);

            if ($idReservado <= 0) {
                throw new DecryptException('Reserva inválida');
            }
        } catch (DecryptException $e) {
            return back()->withErrors([
                'codigo' => 'Código inválido. Abre nuevamente el formulario.'
            ]);
        }
    }

    unset($datos['reserva']);

    $muestra = $servicio->crear($datos, $idReservado);

    return redirect()
        ->route('muestras.show', $muestra)
        ->with('ok', 'Muestra registrada correctamente.');
}


    public function show(Muestra $muestra)
    {
        $muestra->load(['responsable','transferencias.origen','transferencias.destino','analisis.investigador']);
        $investigadores = Investigador::orderBy('nombre')->get();
        return view('muestras.show', compact('muestra','investigadores'));
    }

    public function almacenar(Muestra $muestra, MuestraService $servicio)
    {
        try { $servicio->almacenar($muestra); }
        catch (DomainException $e) { return back()->with('error',$e->getMessage()); }
        return back()->with('ok','Muestra almacenada.');
    }

    public function descartar(Muestra $muestra, MuestraService $servicio)
    {
        try { $servicio->descartar($muestra); }
        catch (DomainException $e) { return back()->with('error',$e->getMessage()); }
        return back()->with('ok','Muestra descartada.');
    }
}

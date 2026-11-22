<?php
namespace App\Http\Controllers;

use App\Models\Investigador;
use App\Models\Muestra;
use App\Services\MuestraService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        return view('muestras.create', ['investigadores'=>Investigador::orderBy('nombre')->get()]);
    }

    public function store(Request $request, MuestraService $servicio)
    {
        $request->merge(['codigo'=>Str::upper(trim((string) $request->input('codigo')))]);
        $datos = $request->validate([
            'codigo' => ['required','string','max:30','regex:/^[A-Z0-9-]+$/','unique:muestras,codigo'],
            'tipo' => ['required',Rule::in(['Agua','Suelo','Aire','Vegetación'])],
            'origen' => ['required','string','max:120'],
            'lugar_recoleccion' => ['required','string','max:120'],
            'fecha_recoleccion' => ['required','date','before_or_equal:today'],
            'observaciones' => ['nullable','string','max:1000'],
            'responsable_id' => ['required','exists:investigadores,id'],
        ]);
        $muestra = $servicio->crear($datos);
        return redirect()->route('muestras.show', $muestra)->with('ok','Muestra registrada correctamente.');
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

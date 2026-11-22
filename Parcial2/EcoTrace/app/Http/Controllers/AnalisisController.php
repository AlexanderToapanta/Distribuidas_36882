<?php
namespace App\Http\Controllers;

use App\Models\Muestra;
use App\Services\AnalisisService;
use DomainException;
use Illuminate\Http\Request;

class AnalisisController extends Controller
{
    public function iniciar(Muestra $muestra, AnalisisService $servicio)
    {
        try { $servicio->iniciar($muestra); }
        catch (DomainException $e) { return back()->with('error',$e->getMessage()); }
        return back()->with('ok','Análisis iniciado.');
    }

    public function finalizar(Request $request, Muestra $muestra, AnalisisService $servicio)
    {
        $datos = $request->validate([
            'investigador_id' => ['required','integer','exists:investigadores,id'],
            'resultado' => ['required','string','min:5','max:2000'],
        ]);
        try {
            $servicio->finalizar($muestra,(int)$datos['investigador_id'],$datos['resultado']);
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
        return back()->with('ok','Resultado guardado. Muestra analizada.');
    }
}

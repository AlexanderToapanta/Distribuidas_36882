<?php
namespace App\Http\Controllers;

use App\Models\Muestra;
use App\Services\CustodiaService;
use DomainException;
use Illuminate\Http\Request;

class CustodiaController extends Controller
{
    public function transferir(Request $request, Muestra $muestra, CustodiaService $servicio)
    {
        $datos = $request->validate([
            'destino_investigador_id' => ['required','integer','exists:investigadores,id'],
            'motivo' => ['required','string','max:200'],
        ]);
        try {
            $servicio->transferir($muestra, (int)$datos['destino_investigador_id'], $datos['motivo']);
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
        return back()->with('ok','Custodia transferida y registrada en el historial.');
    }
}

<?php
namespace Database\Seeders;
use App\Models\Analisis;
use App\Models\Investigador;
use App\Models\Muestra;
use Illuminate\Database\Seeder;
class MuestraSeeder extends Seeder
{
    public function run(): void
    {
        $elena = Investigador::where('email', 'elena@ecotrace.test')->firstOrFail();
        $mateo = Investigador::where('email', 'mateo@ecotrace.test')->firstOrFail();
        $sofia = Investigador::where('email', 'sofia@ecotrace.test')->firstOrFail();
        $registros = [
            ['M-001','Agua','Río Zamora','Zamora', 'recolectada', $elena->id],
            ['M-002','Suelo','Páramo andino','Papallacta', 'almacenada', $mateo->id],
            ['M-003','Vegetación','Bosque nublado','Mindo', 'en_analisis', $sofia->id],
            ['M-004','Aire','Zona urbana','Quito', 'analizada', $elena->id],
            ['M-005','Agua','Quebrada local','Loja', 'descartada', $mateo->id],
        ];
        foreach ($registros as [$codigo,$tipo,$origen,$lugar,$estado,$responsable]) {
            $muestra = Muestra::firstOrCreate(['codigo'=>$codigo], [
                'tipo'=>$tipo, 'origen'=>$origen, 'lugar_recoleccion'=>$lugar,
                'fecha_recoleccion'=>now()->subDays(2)->toDateString(),
                'responsable_id'=>$responsable, 'observaciones'=>'Dato de prueba del sistema',
            ]);
            if ($muestra->wasRecentlyCreated) {
                $muestra->estado = $estado;
                $muestra->save();
            }
            if ($estado === 'analizada') {
                Analisis::firstOrCreate(['muestra_id'=>$muestra->id], [
                    'investigador_id'=>$responsable,
                    'resultado'=>'Valores registrados para demostración académica.',
                    'fecha_analisis'=>now(),
                ]);
            }
        }
    }
}

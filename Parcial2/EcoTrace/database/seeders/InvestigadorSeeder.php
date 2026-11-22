<?php
namespace Database\Seeders;
use App\Models\Investigador;
use Illuminate\Database\Seeder;
class InvestigadorSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['nombre'=>'Elena Torres', 'email'=>'elena@ecotrace.test'],
            ['nombre'=>'Mateo Ruiz', 'email'=>'mateo@ecotrace.test'],
            ['nombre'=>'Sofía Jiménez', 'email'=>'sofia@ecotrace.test'],
        ] as $datos) {
            Investigador::updateOrCreate(['email'=>$datos['email']], $datos);
        }
    }
}

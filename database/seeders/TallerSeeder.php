<?php

namespace Database\Seeders;

use App\Models\Taller;
use App\Models\TallerDia;
use Illuminate\Database\Seeder;

class TallerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $talleres = [
            [
                'nombre' => 'Robótica Inicial',
                'responsable' => 'Prof. García',
                'orientado' => 'inicial',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Arte y Pintura',
                'responsable' => 'Prof. López',
                'orientado' => 'primario',
                'dias' => ['martes', 'jueves'],
            ],
            [
                'nombre' => 'Ciencias Divertidas',
                'responsable' => 'Prof. Sánchez',
                'orientado' => 'primario',
                'dias' => ['lunes', 'jueves'],
            ],
            [
                'nombre' => 'Programación Básica',
                'responsable' => 'Ing. Romero',
                'orientado' => 'secundario',
                'dias' => ['martes', 'miercoles', 'viernes'],
            ],
        ];

        foreach ($talleres as $data) {
            $taller = Taller::create([
                'nombre' => $data['nombre'],
                'responsable' => $data['responsable'],
                'orientado' => $data['orientado'],
            ]);

            foreach ($data['dias'] as $dia) {
                TallerDia::create([
                    'taller_id' => $taller->id,
                    'dia_semana' => $dia,
                ]);
            }
        }
    }
}

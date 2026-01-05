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
                'nombre' => 'Frio que quema',
                'espacio_fisico' => 'Lab. Microbiologia',
                'edad_minima' => 6,
                'edad_maxima' => 11,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Estructuras en Equilibrio',
                'espacio_fisico' => 'Lab. Fisica - Matematicas',
                'edad_minima' => 8,
                'edad_maxima' => 11,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO  2 10:30 a 11:15 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Cohete químico',
                'espacio_fisico' => 'Lab.Quimica    ',
                'edad_minima' => 5,
                'edad_maxima' => 12,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Crea tu Mini Mundo 3D',
                'espacio_fisico' => 'Lab.Quimica    ',
                'edad_minima' => 7,
                'edad_maxima' => 10,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO  2 10:30 a 11:15 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Recorriendo nuestro entorno',
                'espacio_fisico' => 'Laboratorio Pedagógico',
                'edad_minima' => 5,
                'edad_maxima' => 7,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Pintar con Café',
                'espacio_fisico' => 'Aula Digital 2',
                'edad_minima' => 9,
                'edad_maxima' => 16,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO  2 10:30 a 11:15 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Scratch Express: De cero a tu primer videojuego',
                'espacio_fisico' => 'sala de computación',
                'edad_minima' => 9,
                'edad_maxima' => 16,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Una Sonrisa en tus Manos - taller de Títeres',
                'espacio_fisico' => 'Biblioteca',
                'edad_minima' => 11,
                'edad_maxima' => 18,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'Monstruos y monstruitos: cuentos de terror en verano',
                'espacio_fisico' => 'Biblioteca',
                'edad_minima' => 9,
                'edad_maxima' => 11,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO  2 10:30 a 11:15 hs',
                'dias' => ['lunes', 'miercoles', 'viernes'],
            ],
            [
                'nombre' => 'MicroExploradores: descubriendo el mundo invisible”',
                'espacio_fisico' => 'Lab. Microbiologia',
                'edad_minima' => 8,
                'edad_maxima' => 11,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['martes', 'jueves'],
            ],
            [
                'nombre' => 'Eco-Genios: El Rally Matemático Reciclado',
                'espacio_fisico' => 'Lab. Fisica - Matematicas',
                'edad_minima' => 6,
                'edad_maxima' => 13,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO  2 10:30 a 11:15 hs',
                'dias' => ['martes', 'jueves'],
            ],
            [
                'nombre' => 'Cuento de Laboratorios',
                'espacio_fisico' => 'Lab. Quimica',
                'edad_minima' => 8,
                'edad_maxima' => 12,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['martes', 'jueves'],
            ],
            [
                'nombre' => 'MiniArquitectos Magnéticos',
                'espacio_fisico' => 'Aula Digital 1',
                'edad_minima' => 4,
                'edad_maxima' => 7,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO  2 10:30 a 11:15 hs',
                'dias' => ['martes', 'jueves'],
            ],
            [
                'nombre' => 'Armando Ideas”, Exploramos la Tecnología con el KIT de atornillados.',
                'espacio_fisico' => 'Aula Digital 1',
                'edad_minima' => 6,
                'edad_maxima' => 10,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO 1 09:00 a 10:00 hs',
                'dias' => ['martes', 'jueves'],
            ],
            [
                'nombre' => 'Taller de Modelado, Animación e Impresión 3D.',
                'espacio_fisico' => 'Aula 6',
                'edad_minima' => 10,
                'edad_maxima' => 19,
                'responsable' => 'Prof. de prueba',
                'orientado' => 'indefinido',
                'cupos' => 100,
                'descripcion' => 'MODULO  2 10:30 a 11:15 hs',
                'dias' => ['martes', 'jueves'],
            ],
        ];

        foreach ($talleres as $data) {
            $taller = Taller::create([
                'nombre' => $data['nombre'],
                'responsable' => $data['responsable'],
                'orientado' => $data['orientado'],
                'cupos' => $data['cupos'],
                'descripcion' => $data['descripcion'],
                'edad_minima' => $data['edad_minima'],
                'edad_maxima' => $data['edad_maxima'],
                'espacio_fisico' => $data['espacio_fisico'],
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

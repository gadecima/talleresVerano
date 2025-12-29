<?php

namespace Database\Seeders;

use App\Models\Cursante;
use Illuminate\Database\Seeder;

class CursanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cursantes = [
            [
                'nombre_apellido' => 'Juan Pérez',
                'dni' => '12345678',
                'fecha_nacimiento' => '2015-03-12',
                'localidad' => 'San Miguel de Tucumán',
                'contacto' => '381-1111111',
                'correo' => 'juan@example.com',
                'escuela' => 'Escuela Nº1',
                'nivel_educativo' => 'primario',
            ],
            [
                'nombre_apellido' => 'María Gómez',
                'dni' => '23456789',
                'fecha_nacimiento' => '2010-07-25',
                'localidad' => 'Yerba Buena',
                'contacto' => '381-2222222',
                'correo' => 'maria@example.com',
                'escuela' => 'Escuela Nº2',
                'nivel_educativo' => 'secundario',
            ],
        ];

        foreach ($cursantes as $data) {
            Cursante::create($data);
        }
    }
}

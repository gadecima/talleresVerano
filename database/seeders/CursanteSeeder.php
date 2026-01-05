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
                'edad' => 8,
                'localidad' => 'San Miguel de Tucumán',
                'tutor' => 'Carlos Pérez',
                'contacto' => '381-1111111',
                'correo' => 'juan@example.com',
                //'escuela' => 'Escuela Nº1',
                //'nivel_educativo' => 'primario',
            ],
            [
                'nombre_apellido' => 'María Gómez',
                'dni' => '23456789',
                'edad' => 12,
                'localidad' => 'Yerba Buena',
                'tutor' => 'Ana Gómez',
                'contacto' => '381-2222222',
                'correo' => 'maria@example.com',
                //'escuela' => 'Escuela Nº2',
                //'nivel_educativo' => 'secundario',
            ],
            [
                'nombre_apellido' => 'Lucía Fernández',
                'dni' => '34567890',
                'edad' => 6,
                'localidad' => 'Tafí Viejo',
                'tutor' => 'Sofía Fernández',
                'contacto' => '381-3333333',
                'correo' => 'lucia@example.com',
            ],
            [
                'nombre_apellido' => 'Matías Rodríguez',
                'dni' => '45678901',
                'edad' => 10,
                'localidad' => 'Concepción',
                'tutor' => 'Jorge Rodríguez',
                'contacto' => '381-4444444',
                'correo' => 'matias@example.com',
            ],
        ];

        foreach ($cursantes as $data) {
            Cursante::create($data);
        }
    }
}

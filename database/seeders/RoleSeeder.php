<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles base
        Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Acceso total al sistema',
        ]);

        Role::create([
            'name' => 'Estándar',
            'slug' => 'standard',
            'description' => 'Usuario estándar con acceso a su dashboard',
        ]);

        Role::create([
            'name' => 'Visor',
            'slug' => 'viewer',
            'description' => 'Usuario solo lectura, puede ver datos pero no modificar',
        ]);
    }
}

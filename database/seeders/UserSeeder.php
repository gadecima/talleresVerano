<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $adminRole = Role::where('slug', 'admin')->first();
        $standardRole = Role::where('slug', 'standard')->first();
        $viewerRole = Role::where('slug', 'viewer')->first();

        // Crear usuario administrador
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mistarter.local',
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'),
            'role_id' => $adminRole->id,
        ]);

        // Crear usuario estándar
        User::create([
            'name' => 'Usuario Estándar',
            'email' => 'standard@mistarter.local',
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'),
            'role_id' => $standardRole->id,
        ]);

        // Crear usuario visor
        User::create([
            'name' => 'Usuario Visor',
            'email' => 'viewer@mistarter.local',
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'),
            'role_id' => $viewerRole->id,
        ]);
    }
}

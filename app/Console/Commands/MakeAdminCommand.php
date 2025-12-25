<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {name} {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un nuevo usuario administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password') ?? 'password';

        // Validar que el email no exista
        if (User::where('email', $email)->exists()) {
            $this->error("El email {$email} ya está registrado");
            return 1;
        }

        // Obtener el rol de administrador
        $adminRole = Role::where('slug', 'admin')->first();

        if (!$adminRole) {
            $this->error('El rol de administrador no existe. Ejecuta las migraciones primero.');
            return 1;
        }

        // Crear el usuario
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $this->info("✓ Usuario administrador creado exitosamente");
        $this->info("  Nombre: {$user->name}");
        $this->info("  Email: {$user->email}");
        $this->info("  Contraseña: {$password}");

        return 0;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cursantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_apellido');
            $table->string('dni')->unique();
            $table->date('fecha_nacimiento');
            $table->string('localidad');
            $table->string('contacto')->nullable();
            $table->string('correo')->nullable();
            $table->string('escuela')->nullable();
            $table->enum('nivel_educativo', ['inicial', 'primario', 'secundario']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursantes');
    }
};

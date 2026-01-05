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
        Schema::create('talleres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->unsignedTinyInteger('edad_minima');
            $table->unsignedTinyInteger('edad_maxima');
            $table->string('espacio_fisico', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('responsable', 120)->nullable();
            $table->unsignedSmallInteger('cupos')->default(100);
            $table->enum('orientado', ['inicial', 'primario', 'secundario', 'indefinido'])->default('indefinido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talleres');
    }
};

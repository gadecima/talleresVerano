<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    use HasFactory;

    protected $table = 'talleres';

    protected $fillable = [
        'nombre',
        'edad_minima',
        'edad_maxima',
        'espacio_fisico',
        'responsable',
        'descripcion',
        'cupos',
        'orientado',
        'disponibilidad',
    ];

    public function dias()
    {
        return $this->hasMany(TallerDia::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }
}

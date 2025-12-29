<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cursante extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_apellido',
        'dni',
        'fecha_nacimiento',
        'localidad',
        'contacto',
        'correo',
        'escuela',
        'nivel_educativo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function talleres()
    {
        return $this->belongsToMany(Taller::class, 'inscripciones')
            ->withPivot('fecha')
            ->withTimestamps();
    }
}

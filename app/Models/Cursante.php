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
        'edad',
        'localidad',
        'tutor',
        'contacto',
        'correo',
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

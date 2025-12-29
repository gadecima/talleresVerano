<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'cursante_id',
        'taller_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function cursante()
    {
        return $this->belongsTo(Cursante::class);
    }

    public function taller()
    {
        return $this->belongsTo(Taller::class);
    }
}

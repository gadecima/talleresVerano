<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TallerDia extends Model
{
    use HasFactory;

    protected $table = 'taller_dias';

    protected $fillable = [
        'taller_id',
        'dia_semana',
    ];

    public function taller()
    {
        return $this->belongsTo(Taller::class);
    }
}

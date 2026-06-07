<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ganado extends Model
{
    protected $table = 'ganado';

    protected $fillable = [
        'nombre',
        'especie',
        'raza',
        'fecha_nac',
        'peso_kg',
        'estado',
    ];

    protected $casts = [
        'fecha_nac' => 'date',
        'peso_kg'   => 'float',
    ];
}
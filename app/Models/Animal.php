<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = 'animales';

    protected $fillable = [
        'user_id',
        'arete',
        'nombre',
        'especie',
        'raza',
        'sexo',
        'fecha_nac',
        'peso_kg',
        'color',
        'origen',
        'precio_adquisicion',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_nac'          => 'date',
        'peso_kg'            => 'float',
        'precio_adquisicion' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
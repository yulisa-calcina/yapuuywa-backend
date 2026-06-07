<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
    protected $table = 'producciones';

    protected $fillable = [
        'user_id',
        'animal_id',
        'fecha',
        'tipo',
        'cantidad',
        'unidad',
        'precio_unitario',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'cantidad'        => 'float',
        'precio_unitario' => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
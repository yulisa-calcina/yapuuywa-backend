<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'user_id',
        'fecha',
        'producto',
        'categoria',
        'cantidad',
        'precio_unitario',
        'total',
        'comprador',
        'modalidad',
        'comprobante',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'cantidad'        => 'float',
        'precio_unitario' => 'decimal:2',
        'total'           => 'decimal:2',
    ];
}
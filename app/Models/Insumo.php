<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $table = 'insumos';

    protected $fillable = [
        'user_id',
        'nombre',
        'categoria',
        'unidad',
        'stock_actual',
        'stock_minimo',
        'proveedor',
        'precio_unitario',
    ];

    protected $casts = [
        'stock_actual'    => 'float',
        'stock_minimo'    => 'float',
        'precio_unitario' => 'float',
    ];

    public function estaCritico(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}
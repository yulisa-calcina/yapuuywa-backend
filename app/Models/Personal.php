<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $table = 'personal';

    protected $fillable = [
        'user_id',
        'nombre',
        'dni',
        'tipo',
        'cargo',
        'salario_diario',
        'telefono',
        'activo',
    ];

    protected $casts = [
        'salario_diario' => 'decimal:2',
        'activo'         => 'boolean',
    ];
}
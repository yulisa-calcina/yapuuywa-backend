<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    protected $table = 'parcelas';

    protected $fillable = [
        'user_id',
        'codigo',
        'nombre',
        'ubicacion',
        'superficie_ha',
        'tipo_suelo',
        'riego',
        'estado',
    ];

    protected $casts = [
        'superficie_ha' => 'float',
    ];

    public function ciclos()
    {
        return $this->hasMany(CicloCultivo::class);
    }
}
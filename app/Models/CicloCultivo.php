<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloCultivo extends Model
{
    protected $table = 'ciclos_cultivo';

    protected $fillable = [
        'parcela_id',
        'cultivo',
        'variedad',
        'fecha_siembra',
        'fecha_cosecha_est',
        'semilla_kg',
        'superficie_ha',
        'estado',
        'cosecha_kg',
        'observaciones',
    ];

    protected $casts = [
        'fecha_siembra'     => 'date',
        'fecha_cosecha_est' => 'date',
        'semilla_kg'        => 'float',
        'superficie_ha'     => 'float',
        'cosecha_kg'        => 'float',
    ];

    public function parcela()
    {
        return $this->belongsTo(Parcela::class);
    }
}
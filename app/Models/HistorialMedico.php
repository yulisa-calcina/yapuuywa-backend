<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    protected $table = 'historial_medicos';

    protected $fillable = [
        'animal_id',
        'fecha',
        'tipo',
        'descripcion',
        'medicamento',
        'dosis',
        'veterinario',
        'costo',
        'proxima_revision',
        'alerta_atendida',
    ];

    protected $casts = [
        'fecha'            => 'date',
        'proxima_revision' => 'date',
        'costo'            => 'float',
        'alerta_atendida'  => 'boolean',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
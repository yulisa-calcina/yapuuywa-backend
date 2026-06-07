<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\HistorialMedico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HistorialController extends Controller
{
    public function index($animal): JsonResponse
    {
        $historial = HistorialMedico::where('animal_id', $animal)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $historial]);
    }

    public function store(Request $request, $animal): JsonResponse
    {
        $validated = $request->validate([
            'fecha'            => 'required|date',
            'tipo'             => 'required|in:Vacunación,Desparasitación,Tratamiento médico,Revisión de rutina,Diagnóstico',
            'descripcion'      => 'nullable|string',
            'medicamento'      => 'nullable|string|max:120',
            'dosis'            => 'nullable|string|max:60',
            'veterinario'      => 'nullable|string|max:100',
            'costo'            => 'nullable|numeric|min:0',
            'proxima_revision' => 'nullable|date',
        ]);

        $validated['animal_id'] = $animal;

        $historial = HistorialMedico::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $historial,
            'message' => 'Atención registrada correctamente',
        ], 201);
    }

    public function update(Request $request, $historial): JsonResponse
    {
        $registro = HistorialMedico::findOrFail($historial);
        $registro->update($request->all());
        return response()->json(['success' => true, 'data' => $registro]);
    }

    public function destroy($historial): JsonResponse
    {
        HistorialMedico::findOrFail($historial)->delete();
        return response()->json(['success' => true, 'message' => 'Registro eliminado']);
    }
}
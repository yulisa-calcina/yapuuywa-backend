<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CicloCultivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CicloController extends Controller
{
    public function index($parcela): JsonResponse
    {
        $ciclos = CicloCultivo::where('parcela_id', $parcela)
            ->orderBy('fecha_siembra', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $ciclos]);
    }

    public function store(Request $request, $parcela): JsonResponse
    {
        $validated = $request->validate([
            'cultivo'           => 'required|string|max:80',
            'variedad'          => 'nullable|string|max:80',
            'fecha_siembra'     => 'required|date',
            'fecha_cosecha_est' => 'nullable|date',
            'semilla_kg'        => 'nullable|numeric|min:0',
            'superficie_ha'     => 'nullable|numeric|min:0',
            'estado'            => 'required|in:crecimiento,cosechado,perdido',
            'cosecha_kg'        => 'nullable|numeric|min:0',
            'observaciones'     => 'nullable|string',
        ]);

        $validated['parcela_id'] = $parcela;
        $ciclo = CicloCultivo::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $ciclo,
            'message' => 'Ciclo registrado correctamente',
        ], 201);
    }

    public function update(Request $request, $ciclo): JsonResponse
    {
        $c = CicloCultivo::findOrFail($ciclo);
        $c->update($request->all());
        return response()->json(['success' => true, 'data' => $c->fresh()]);
    }

    public function destroy($ciclo): JsonResponse
    {
        CicloCultivo::findOrFail($ciclo)->delete();
        return response()->json(['success' => true, 'message' => 'Ciclo eliminado']);
    }
}
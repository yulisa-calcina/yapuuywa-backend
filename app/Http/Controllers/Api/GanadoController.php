<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ganado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GanadoController extends Controller
{
    public function index(): JsonResponse
    {
        $ganado = Ganado::all();

        return response()->json([
            'success' => true,
            'data'    => $ganado,
            'message' => 'Lista de ganado obtenida correctamente'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:100',
            'especie'   => 'required|string|max:50',
            'raza'      => 'nullable|string|max:50',
            'fecha_nac' => 'nullable|date',
            'peso_kg'   => 'nullable|numeric',
            'estado'    => 'required|in:activo,vendido,muerto',
        ]);

        $animal = Ganado::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $animal,
            'message' => 'Animal registrado correctamente'
        ], 201);
    }

    public function show(Ganado $ganado): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $ganado,
        ]);
    }

    public function update(Request $request, Ganado $ganado): JsonResponse
    {
        $validated = $request->validate([
            'nombre'  => 'sometimes|string|max:100',
            'especie' => 'sometimes|string|max:50',
            'peso_kg' => 'sometimes|numeric',
            'estado'  => 'sometimes|in:activo,vendido,muerto',
        ]);

        $ganado->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $ganado,
            'message' => 'Animal actualizado correctamente'
        ]);
    }

    public function destroy(Ganado $ganado): JsonResponse
    {
        $ganado->delete();

        return response()->json([
            'success' => true,
            'message' => 'Animal eliminado correctamente'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produccion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProduccionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $producciones = Produccion::where('user_id', $request->user()->id)
            ->with('animal:id,nombre,arete')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $producciones]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha'           => 'required|date',
            'tipo'            => 'required|in:leche,lana,huevo,carne,otro',
            'cantidad'        => 'required|numeric|min:0',
            'unidad'          => 'required|string|max:20',
            'animal_id'       => 'nullable|exists:animales,id',
            'precio_unitario' => 'nullable|numeric|min:0',
            'observaciones'   => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['total']   = isset($validated['precio_unitario'])
            ? $validated['cantidad'] * $validated['precio_unitario']
            : null;

        $produccion = Produccion::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $produccion->load('animal:id,nombre,arete'),
            'message' => 'Producción registrada correctamente',
        ], 201);
    }

    public function show(Request $request, Produccion $produccion): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $produccion]);
    }

    public function update(Request $request, Produccion $produccion): JsonResponse
    {
        $produccion->update($request->all());
        return response()->json(['success' => true, 'data' => $produccion->fresh()]);
    }

    public function destroy(Produccion $produccion): JsonResponse
    {
        $produccion->delete();
        return response()->json(['success' => true, 'message' => 'Registro eliminado']);
    }

    public function resumen(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $resumen = Produccion::where('user_id', $userId)
            ->selectRaw('tipo, SUM(cantidad) as total_cantidad, SUM(total) as total_ingresos, COUNT(*) as registros')
            ->groupBy('tipo')
            ->get();

        return response()->json(['success' => true, 'data' => $resumen]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InsumoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $insumos = Insumo::where('user_id', $request->user()->id)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $insumos,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'          => 'required|string|max:100',
            'categoria'       => 'required|in:agricola,veterinario,general',
            'unidad'          => 'required|string|max:20',
            'stock_actual'    => 'required|numeric|min:0',
            'stock_minimo'    => 'required|numeric|min:0',
            'proveedor'       => 'nullable|string|max:100',
            'precio_unitario' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = $request->user()->id;
        $insumo = Insumo::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $insumo,
            'message' => 'Insumo registrado correctamente',
        ], 201);
    }

    public function show(Request $request, Insumo $insumo): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $insumo]);
    }

    public function update(Request $request, Insumo $insumo): JsonResponse
    {
        $validated = $request->validate([
            'nombre'          => 'sometimes|string|max:100',
            'stock_actual'    => 'sometimes|numeric|min:0',
            'stock_minimo'    => 'sometimes|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
        ]);

        $insumo->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $insumo->fresh(),
            'message' => 'Insumo actualizado correctamente',
        ]);
    }

    public function destroy(Insumo $insumo): JsonResponse
    {
        $insumo->delete();
        return response()->json(['success' => true, 'message' => 'Insumo eliminado']);
    }

    public function movimiento(Request $request, Insumo $insumo): JsonResponse
    {
        $request->validate([
            'tipo'     => 'required|in:entrada,salida',
            'cantidad' => 'required|numeric|min:0.01',
        ]);

        if ($request->tipo === 'entrada') {
            $insumo->stock_actual += $request->cantidad;
        } else {
            if ($insumo->stock_actual < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente',
                ], 422);
            }
            $insumo->stock_actual -= $request->cantidad;
        }

        $insumo->save();

        return response()->json([
            'success' => true,
            'data'    => $insumo,
            'message' => 'Movimiento registrado correctamente',
        ]);
    }
}
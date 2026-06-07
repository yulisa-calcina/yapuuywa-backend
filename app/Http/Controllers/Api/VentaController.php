<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VentaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ventas = Venta::where('user_id', $request->user()->id)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $ventas]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha'           => 'required|date',
            'producto'        => 'required|string|max:100',
            'categoria'       => 'required|in:leche,lana,ganado,cosecha,otro',
            'cantidad'        => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'comprador'       => 'nullable|string|max:120',
            'modalidad'       => 'required|in:contado,credito',
            'comprobante'     => 'nullable|string|max:40',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['total']   = $validated['cantidad'] * $validated['precio_unitario'];

        $venta = Venta::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $venta,
            'message' => 'Venta registrada correctamente',
        ], 201);
    }

    public function show(Venta $venta): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $venta]);
    }

    public function update(Request $request, Venta $venta): JsonResponse
    {
        $venta->update($request->all());
        return response()->json(['success' => true, 'data' => $venta->fresh()]);
    }

    public function destroy(Venta $venta): JsonResponse
    {
        $venta->delete();
        return response()->json(['success' => true, 'message' => 'Venta eliminada']);
    }
}
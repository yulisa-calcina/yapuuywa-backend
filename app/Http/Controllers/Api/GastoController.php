<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gasto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GastoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $gastos = Gasto::where('user_id', $request->user()->id)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $gastos]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha'       => 'required|date',
            'categoria'   => 'required|in:insumos,personal,veterinaria,maquinaria,servicios,otro',
            'descripcion' => 'required|string|max:200',
            'monto'       => 'required|numeric|min:0',
            'proveedor'   => 'nullable|string|max:120',
            'comprobante' => 'nullable|string|max:40',
        ]);

        $validated['user_id'] = $request->user()->id;
        $gasto = Gasto::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $gasto,
            'message' => 'Gasto registrado correctamente',
        ], 201);
    }

    public function show(Gasto $gasto): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $gasto]);
    }

    public function update(Request $request, Gasto $gasto): JsonResponse
    {
        $gasto->update($request->all());
        return response()->json(['success' => true, 'data' => $gasto->fresh()]);
    }

    public function destroy(Gasto $gasto): JsonResponse
    {
        $gasto->delete();
        return response()->json(['success' => true, 'message' => 'Gasto eliminado']);
    }

    public function balance(Request $request): JsonResponse
    {
        $userId   = $request->user()->id;
        $ingresos = Venta::where('user_id', $userId)->sum('total');
        $egresos  = Gasto::where('user_id', $userId)->sum('monto');
        $balance  = $ingresos - $egresos;

        return response()->json([
            'success' => true,
            'data' => [
                'ingresos' => round($ingresos, 2),
                'egresos'  => round($egresos,  2),
                'balance'  => round($balance,  2),
            ],
        ]);
    }
}
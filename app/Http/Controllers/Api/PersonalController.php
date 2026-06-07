<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PersonalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $personal = Personal::where('user_id', $request->user()->id)
            ->orderBy('nombre')
            ->get();

        return response()->json(['success' => true, 'data' => $personal]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'         => 'required|string|max:120',
            'dni'            => 'required|digits:8',
            'tipo'           => 'required|in:permanente,jornalero',
            'cargo'          => 'nullable|string|max:80',
            'salario_diario' => 'required|numeric|min:0',
            'telefono'       => 'nullable|string|max:15',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['activo']  = true;

        $trabajador = Personal::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $trabajador,
            'message' => 'Trabajador registrado correctamente',
        ], 201);
    }

    public function show(Personal $personal): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $personal]);
    }

    public function update(Request $request, Personal $personal): JsonResponse
    {
        $validated = $request->validate([
            'nombre'         => 'sometimes|string|max:120',
            'cargo'          => 'nullable|string|max:80',
            'salario_diario' => 'sometimes|numeric|min:0',
            'telefono'       => 'nullable|string|max:15',
            'activo'         => 'sometimes|boolean',
        ]);

        $personal->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $personal->fresh(),
            'message' => 'Trabajador actualizado correctamente',
        ]);
    }

    public function destroy(Personal $personal): JsonResponse
    {
        $personal->delete();
        return response()->json(['success' => true, 'message' => 'Trabajador eliminado']);
    }

    public function jornal(Request $request, $trabajador): JsonResponse
    {
        $request->validate([
            'dias_trabajados' => 'required|numeric|min:1',
        ]);

        $trabajador = Personal::findOrFail($trabajador);
        $total = $trabajador->salario_diario * $request->dias_trabajados;

        return response()->json([
            'success' => true,
            'data' => [
                'trabajador'      => $trabajador->nombre,
                'dias_trabajados' => $request->dias_trabajados,
                'salario_diario'  => $trabajador->salario_diario,
                'total_pagar'     => round($total, 2),
            ],
            'message' => 'Jornal calculado correctamente',
        ]);
    }
}
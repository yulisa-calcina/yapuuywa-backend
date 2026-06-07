<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ParcelaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $parcelas = Parcela::where('user_id', $request->user()->id)
            ->orderBy('nombre')
            ->get();

        return response()->json(['success' => true, 'data' => $parcelas]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'        => 'required|string|max:20',
            'nombre'        => 'required|string|max:100',
            'ubicacion'     => 'nullable|string|max:200',
            'superficie_ha' => 'nullable|numeric|min:0',
            'tipo_suelo'    => 'nullable|in:arcilloso,arenoso,franco,limoso,otro',
            'riego'         => 'nullable|in:lluvia,canal,aspersion,goteo,otro',
            'estado'        => 'required|in:activo,descanso,preparacion',
        ]);

        $validated['user_id'] = $request->user()->id;
        $parcela = Parcela::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $parcela,
            'message' => 'Parcela registrada correctamente',
        ], 201);
    }

    public function show(Request $request, $parcela): JsonResponse
    {
        $p = Parcela::where('user_id', $request->user()->id)->findOrFail($parcela);
        return response()->json(['success' => true, 'data' => $p]);
    }

    public function update(Request $request, $parcela): JsonResponse
    {
        $p = Parcela::where('user_id', $request->user()->id)->findOrFail($parcela);

        $validated = $request->validate([
            'nombre'        => 'sometimes|string|max:100',
            'ubicacion'     => 'nullable|string|max:200',
            'superficie_ha' => 'nullable|numeric|min:0',
            'estado'        => 'sometimes|in:activo,descanso,preparacion',
        ]);

        $p->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $p->fresh(),
            'message' => 'Parcela actualizada correctamente',
        ]);
    }

    public function destroy(Request $request, $parcela): JsonResponse
    {
        $p = Parcela::where('user_id', $request->user()->id)->findOrFail($parcela);
        $p->delete();
        return response()->json(['success' => true, 'message' => 'Parcela eliminada']);
    }
}
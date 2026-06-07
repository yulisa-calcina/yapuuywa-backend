<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnimalController extends Controller
{
    /** GET /api/animales — Lista todos los animales del usuario (RF03) */
    public function index(Request $request): JsonResponse
    {
        $query = Animal::where('user_id', $request->user()->id);

        /* Filtros opcionales */
        if ($request->especie) $query->where('especie', $request->especie);
        if ($request->estado)  $query->where('estado',  $request->estado);
        if ($request->q)       $query->where(function($q) use ($request) {
            $q->where('nombre', 'like', "%{$request->q}%")
              ->orWhere('arete',  'like', "%{$request->q}%");
        });

        $animales = $query->orderBy('nombre')->get();

        return response()->json([
            'success' => true,
            'data'    => $animales,
            'total'   => $animales->count(),
            'message' => 'Lista obtenida correctamente',
        ]);
    }

    /** POST /api/animales — Registrar nuevo animal (RF03) */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'arete'     => 'required|string|max:20',
            'nombre'    => 'required|string|max:100',
            'especie'   => 'required|in:bovino,ovino,porcino,alpaca,camélido,caprino,equino',
            'raza'      => 'nullable|string|max:80',
            'sexo'      => 'nullable|in:macho,hembra',
            'fecha_nac' => 'nullable|date',
            'peso_kg'   => 'nullable|numeric|min:0',
            'color'     => 'nullable|string|max:60',
            'origen'    => 'nullable|in:nacido,comprado',
            'precio_adquisicion' => 'nullable|numeric|min:0',
            'estado'    => 'required|in:activo,vendido,muerto',
        ]);

        $validated['user_id'] = $request->user()->id;

        /* Verificar arete único por usuario */
        if (Animal::where('user_id', $request->user()->id)
                  ->where('arete', $validated['arete'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un animal con ese código de arete.',
                'errors'  => ['arete' => ['El arete ya está en uso.']],
            ], 422);
        }

        $animal = Animal::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $animal,
            'message' => 'Animal registrado correctamente',
        ], 201);
    }

    /** GET /api/animales/{id} */
    public function show(Request $request, Animal $animal): JsonResponse
    {
        $this->authorize($request->user(), $animal);
        return response()->json(['success' => true, 'data' => $animal]);
    }

    /** PUT /api/animales/{id} */
    public function update(Request $request, Animal $animal): JsonResponse
    {
        $this->authorize($request->user(), $animal);

        $validated = $request->validate([
            'nombre'    => 'sometimes|string|max:100',
            'especie'   => 'sometimes|in:bovino,ovino,porcino,alpaca,camélido,caprino,equino',
            'raza'      => 'nullable|string|max:80',
            'peso_kg'   => 'nullable|numeric|min:0',
            'estado'    => 'sometimes|in:activo,vendido,muerto',
        ]);

        $animal->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $animal->fresh(),
            'message' => 'Animal actualizado correctamente',
        ]);
    }

    /** DELETE /api/animales/{id} */
    public function destroy(Request $request, Animal $animal): JsonResponse
    {
        $this->authorize($request->user(), $animal);
        $animal->delete();
        return response()->json(['success' => true, 'message' => 'Animal eliminado correctamente']);
    }

    private function authorize($user, $animal): void
    {
        if ($animal->user_id !== $user->id && $user->rol !== 'admin') {
            abort(403, 'Sin permiso para acceder a este recurso.');
        }
    }
}

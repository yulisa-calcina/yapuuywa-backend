<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $usuarios = User::orderBy('nombre')->get(['id','nombre','dni','email','rol','activo','created_at']);

        return response()->json(['success' => true, 'data' => $usuarios]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:120',
            'dni'      => 'required|digits:8|unique:users,dni',
            'email'    => 'required|email|unique:users,email',
            'rol'      => 'required|in:admin,ganadero,veterinario',
            'password' => 'required|min:8',
        ]);

        $validated['name']     = $validated['nombre'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['activo']   = true;

        $usuario = User::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $usuario->only(['id','nombre','dni','email','rol','activo']),
            'message' => 'Usuario creado correctamente',
        ], 201);
    }

    public function show($usuario): JsonResponse
    {
        $user = User::findOrFail($usuario);
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function update(Request $request, $usuario): JsonResponse
    {
        $user = User::findOrFail($usuario);

        $validated = $request->validate([
            'nombre'   => 'sometimes|string|max:120',
            'rol'      => 'sometimes|in:admin,ganadero,veterinario',
            'activo'   => 'sometimes|boolean',
            'password' => 'nullable|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $user->fresh()->only(['id','nombre','dni','email','rol','activo']),
            'message' => 'Usuario actualizado correctamente',
        ]);
    }

    public function destroy($usuario): JsonResponse
    {
        $user = User::findOrFail($usuario);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Usuario eliminado']);
    }
}
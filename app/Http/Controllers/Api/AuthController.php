<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'dni'      => 'required|digits:8',
            'password' => 'required|min:6',
        ]);

        $user = User::where('dni', $request->dni)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'DNI o contraseña incorrectos.',
            ], 401);
        }

        if (!$user->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Cuenta desactivada. Contacta al administrador.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('yapuuywa-token')->plainTextToken;

        Log::info('Login exitoso', ['user_id' => $user->id, 'ip' => $request->ip()]);

        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'user' => [
                'id'     => $user->id,
                'nombre' => $user->nombre,
                'dni'    => $user->dni,
                'email'  => $user->email,
                'rol'    => $user->rol,
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nombre'                => 'required|string|max:120',
            'dni'                   => 'required|digits:8|unique:users,dni',
            'rol'                   => 'required|in:ganadero,veterinario',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::create([
            'name'     => $request->nombre,
            'nombre'   => $request->nombre,
            'dni'      => $request->dni,
            'email'    => $request->dni . '@yapuuywa.com',
            'rol'      => $request->rol,
            'password' => Hash::make($request->password),
            'activo'   => true,
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('yapuuywa-token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'user' => [
                'id'     => $user->id,
                'nombre' => $user->nombre,
                'dni'    => $user->dni,
                'rol'    => $user->rol,
            ],
            'message' => 'Cuenta creada correctamente',
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Sesión cerrada.']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Instrucciones enviadas al correo registrado.',
        ]);
    }
}
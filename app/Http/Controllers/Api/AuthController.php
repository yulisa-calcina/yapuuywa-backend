<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

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
                'id'        => $user->id,
                'nombre'    => $user->nombre,
                'dni'       => $user->dni,
                'email'     => $user->email,
                'rol'       => $user->rol,
                'genero'    => $user->genero,
                'foto_url'  => $user->foto_url,
                'telefono'  => $user->telefono,
                'comunidad' => $user->comunidad,
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nombre'                => 'required|string|max:120',
            'dni'                   => 'required|digits:8|unique:users,dni',
            'email'                 => 'required|email|unique:users,email',
            'telefono'              => 'required|string|max:15',
            'rol'                   => 'required|in:ganadero,veterinario',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'comunidad'             => 'nullable|string|max:100',
            'genero'                => 'nullable|in:masculino,femenino,otro',
            'foto_url'              => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'      => $request->nombre,
            'nombre'    => $request->nombre,
            'dni'       => $request->dni,
            'email'     => $request->email,
            'rol'       => $request->rol,
            'password'  => Hash::make($request->password),
            'activo'    => true,
            'telefono'  => $request->telefono,
            'comunidad' => $request->comunidad,
            'genero'    => $request->genero,
            'foto_url'  => $request->foto_url,
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('yapuuywa-token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'user' => [
                'id'        => $user->id,
                'nombre'    => $user->nombre,
                'dni'       => $user->dni,
                'email'     => $user->email,
                'rol'       => $user->rol,
                'genero'    => $user->genero,
                'foto_url'  => $user->foto_url,
                'telefono'  => $user->telefono,
                'comunidad' => $user->comunidad,
            ],
            'message' => 'Cuenta creada correctamente',
        ], 201);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'dni' => 'required|digits:8',
        ]);

        $user = User::where('dni', $request->dni)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No existe una cuenta con ese DNI.',
            ], 404);
        }

        // Generar código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar código en caché por 10 minutos
        Cache::put('reset_code_' . $user->dni, $codigo, 600);

        // Enviar por email
        try {
            Mail::raw(
                "Hola {$user->nombre},\n\nTu código de recuperación de YapuUywa SGA es:\n\n{$codigo}\n\nEste código expira en 10 minutos.\n\nSi no solicitaste esto, ignora este mensaje.",
                function ($message) use ($user, $codigo) {
                    $message->to($user->email)
                            ->subject('Código de recuperación - YapuUywa SGA');
                }
            );
        } catch (\Exception $e) {
            Log::error('Error enviando email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Código enviado a tu correo registrado.',
            'email'   => substr($user->email, 0, 3) . '***@' . explode('@', $user->email)[1],
        ]);
    }

    public function verifyResetCode(Request $request): JsonResponse
    {
        $request->validate([
            'dni'    => 'required|digits:8',
            'codigo' => 'required|digits:6',
        ]);

        $cached = Cache::get('reset_code_' . $request->dni);

        if (!$cached || $cached !== $request->codigo) {
            return response()->json([
                'success' => false,
                'message' => 'Código incorrecto o expirado.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Código verificado correctamente.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'dni'                   => 'required|digits:8',
            'codigo'                => 'required|digits:6',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $cached = Cache::get('reset_code_' . $request->dni);

        if (!$cached || $cached !== $request->codigo) {
            return response()->json([
                'success' => false,
                'message' => 'Código incorrecto o expirado.',
            ], 400);
        }

        $user = User::where('dni', $request->dni)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        Cache::forget('reset_code_' . $request->dni);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.',
        ]);
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
}
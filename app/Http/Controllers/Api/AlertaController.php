<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlertaController extends Controller
{
    public function vacunacion(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function stock(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function atender(Request $request, $alerta): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Alerta atendida']);
    }
}
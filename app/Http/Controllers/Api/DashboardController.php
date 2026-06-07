<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Alerta;
use App\Models\Insumo;
use App\Models\Venta;
use App\Models\Gasto;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * RF15 — Dashboard KPI con refresco automático cada 60 segundos desde el frontend.
 */
class DashboardController extends Controller
{
    public function kpis(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $hoy    = now();
        $inicioMes = $hoy->copy()->startOfMonth();

        $totalAnimales   = Animal::where('user_id', $userId)->where('estado','activo')->count();
        $alertasActivas  = Alerta::where('usuario_id', $userId)->where('estado','!=','atendido')->count();
        $insumosCriticos = Insumo::where('user_id', $userId)
            ->whereRaw('stock_actual <= stock_minimo')->count();

        $ingresos = Venta::where('user_id', $userId)
            ->whereBetween('fecha', [$inicioMes, $hoy])->sum('total');
        $egresos  = Gasto::where('user_id', $userId)
            ->whereBetween('fecha', [$inicioMes, $hoy])->sum('monto');
        $balance  = $ingresos - $egresos;

        $parcelasActivas = Parcela::where('user_id', $userId)->where('estado','activo')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_animales'   => $totalAnimales,
                'alertas_activas'  => $alertasActivas,
                'insumos_criticos' => $insumosCriticos,
                'ingresos_mes'     => round($ingresos, 2),
                'egresos_mes'      => round($egresos, 2),
                'balance_mes'      => round($balance, 2),
                'parcelas_activas' => $parcelasActivas,
            ],
            'last_updated' => now()->toISOString(),
        ]);
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\AlertaController;
use App\Http\Controllers\Api\ParcelaController;
use App\Http\Controllers\Api\CicloController;
use App\Http\Controllers\Api\InsumoController;
use App\Http\Controllers\Api\PersonalController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\GastoController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\UsuarioController;

/* ══════════════════════════════
   RUTAS PÚBLICAS
══════════════════════════════ */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

/* ══════════════════════════════
   RUTAS PROTEGIDAS (JWT)
══════════════════════════════ */
Route::middleware('auth:sanctum')->group(function () {

    /* Perfil */
    Route::get('/me',                    [AuthController::class, 'me']);
    Route::post('/logout',               [AuthController::class, 'logout']);

    /* Animales — RF03, RF14 */
    Route::apiResource('animales', AnimalController::class);

    /* Historial médico — RF04 */
    Route::get('animales/{animal}/historial',       [HistorialController::class, 'index']);
    Route::post('animales/{animal}/historial',      [HistorialController::class, 'store']);
    Route::put('historial/{historial}',             [HistorialController::class, 'update']);
    Route::delete('historial/{historial}',          [HistorialController::class, 'destroy']);

    /* Alertas — RF05, RF09 */
    Route::get('/alertas/vacunacion',               [AlertaController::class, 'vacunacion']);
    Route::get('/alertas/stock',                    [AlertaController::class, 'stock']);
    Route::put('/alertas/{alerta}/atender',         [AlertaController::class, 'atender']);

    /* Parcelas — RF06 */
    Route::apiResource('parcelas', ParcelaController::class);

    /* Ciclos de cultivo — RF07 */
    Route::get('parcelas/{parcela}/ciclos',         [CicloController::class, 'index']);
    Route::post('parcelas/{parcela}/ciclos',        [CicloController::class, 'store']);
    Route::put('ciclos/{ciclo}',                    [CicloController::class, 'update']);
    Route::delete('ciclos/{ciclo}',                 [CicloController::class, 'destroy']);

    /* Insumos — RF08 */
    Route::apiResource('insumos', InsumoController::class);
    Route::post('insumos/{insumo}/movimiento',      [InsumoController::class, 'movimiento']);

    /* Personal y jornales — RF10 */
    Route::apiResource('personal', PersonalController::class);
    Route::post('personal/{trabajador}/jornal',     [PersonalController::class, 'jornal']);

    /* Ventas — RF11 */
    Route::apiResource('ventas', VentaController::class);

    /* Gastos — RF12 */
    Route::apiResource('gastos', GastoController::class);
    Route::get('/balance',                          [GastoController::class, 'balance']);

    /* Dashboard KPI — RF15 */
    Route::get('/dashboard/kpis',                  [DashboardController::class, 'kpis']);

    /* Reportes PDF — RF13 */
    Route::get('/reportes/ganado',                  [ReporteController::class, 'ganado']);
    Route::get('/reportes/historial',               [ReporteController::class, 'historial']);
    Route::get('/reportes/insumos',                 [ReporteController::class, 'insumos']);
    Route::get('/reportes/finanzas',                [ReporteController::class, 'finanzas']);

    /* Usuarios — RF02 */
    Route::apiResource('usuarios', UsuarioController::class);
});

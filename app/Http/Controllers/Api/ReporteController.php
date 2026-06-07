<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Venta;
use App\Models\Gasto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function ganado(Request $request)
    {
        $animales = Animal::where('user_id', $request->user()->id)
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadHTML($this->templateGanado($animales, $request->user()));
        return $pdf->download('reporte-ganado-yapuuywa.pdf');
    }

    public function finanzas(Request $request)
    {
        $userId   = $request->user()->id;
        $ventas   = Venta::where('user_id', $userId)->orderBy('fecha','desc')->get();
        $gastos   = Gasto::where('user_id', $userId)->orderBy('fecha','desc')->get();
        $ingresos = $ventas->sum('total');
        $egresos  = $gastos->sum('monto');

        $pdf = Pdf::loadHTML($this->templateFinanzas($ventas, $gastos, $ingresos, $egresos, $request->user()));
        return $pdf->download('reporte-finanzas-yapuuywa.pdf');
    }

    public function historial(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Próximamente']);
    }

    public function insumos(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Próximamente']);
    }

    private function templateGanado($animales, $user): string
    {
        $filas = $animales->map(fn($a) => "
            <tr>
                <td>{$a->arete}</td>
                <td>{$a->nombre}</td>
                <td>{$a->especie}</td>
                <td>{$a->raza}</td>
                <td>{$a->peso_kg} kg</td>
                <td>{$a->estado}</td>
            </tr>")->implode('');

        $fecha = now()->format('d/m/Y H:i');
        $total = $animales->count();

        return "
        <html><head><style>
            body { font-family: Arial, sans-serif; font-size: 11pt; color: #1e2e1e; }
            .header { background: #1a5c2a; color: white; padding: 20px; margin-bottom: 20px; }
            .header h1 { margin: 0; font-size: 18pt; }
            .header p  { margin: 4px 0 0; font-size: 10pt; opacity: .85; }
            .meta { margin-bottom: 16px; font-size: 10pt; color: #4a5e4a; }
            table { width: 100%; border-collapse: collapse; font-size: 10pt; }
            th { background: #eef7f0; padding: 8px; text-align: left; border-bottom: 2px solid #1a5c2a; font-size: 9pt; text-transform: uppercase; }
            td { padding: 7px 8px; border-bottom: 1px solid #dde3dd; }
            tr:nth-child(even) td { background: #f7f9f7; }
            .footer { margin-top: 20px; font-size: 9pt; color: #8d9e8d; text-align: center; border-top: 1px solid #dde3dd; padding-top: 10px; }
            .total { background: #eef7f0; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-weight: bold; }
        </style></head><body>
            <div class='header'>
                <h1>YapuUywa SGA</h1>
                <p>Sistema de Gestión Agropecuaria · Reporte de Inventario de Ganado</p>
            </div>
            <div class='meta'>
                <strong>Generado por:</strong> {$user->nombre} &nbsp;|&nbsp;
                <strong>Fecha:</strong> {$fecha} &nbsp;|&nbsp;
                <strong>Total animales:</strong> {$total}
            </div>
            <div class='total'>Total de animales en inventario: {$total}</div>
            <table>
                <thead><tr><th>Arete</th><th>Nombre</th><th>Especie</th><th>Raza</th><th>Peso</th><th>Estado</th></tr></thead>
                <tbody>{$filas}</tbody>
            </table>
            <div class='footer'>YapuUywa SGA · Reporte generado el {$fecha} · Puno, Perú</div>
        </body></html>";
    }

    private function templateFinanzas($ventas, $gastos, $ingresos, $egresos, $user): string
    {
        $balance = $ingresos - $egresos;
        $fecha   = now()->format('d/m/Y H:i');

        $filasV = $ventas->map(fn($v) => "
            <tr>
                <td>{$v->fecha}</td>
                <td>{$v->producto}</td>
                <td>{$v->categoria}</td>
                <td>S/ ".number_format($v->total,2)."</td>
            </tr>")->implode('');

        $filasG = $gastos->map(fn($g) => "
            <tr>
                <td>{$g->fecha}</td>
                <td>{$g->categoria}</td>
                <td>{$g->descripcion}</td>
                <td>S/ ".number_format($g->monto,2)."</td>
            </tr>")->implode('');

        return "
        <html><head><style>
            body { font-family: Arial, sans-serif; font-size: 11pt; color: #1e2e1e; }
            .header { background: #1a5c2a; color: white; padding: 20px; margin-bottom: 20px; }
            .header h1 { margin: 0; font-size: 18pt; }
            .header p  { margin: 4px 0 0; font-size: 10pt; opacity: .85; }
            .meta { margin-bottom: 16px; font-size: 10pt; color: #4a5e4a; }
            .balance { display: flex; gap: 16px; margin-bottom: 20px; }
            .bal-card { flex: 1; padding: 12px; border-radius: 6px; text-align: center; }
            .ing { background: #eef7f0; }
            .egr { background: #fde8ea; }
            .bal { background: #e3f0fb; }
            .bal-card .val { font-size: 16pt; font-weight: bold; margin-top: 4px; }
            .bal-card .lbl { font-size: 9pt; color: #4a5e4a; }
            h3 { color: #1a5c2a; border-bottom: 2px solid #1a5c2a; padding-bottom: 6px; margin: 20px 0 10px; }
            table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 20px; }
            th { background: #eef7f0; padding: 8px; text-align: left; border-bottom: 2px solid #1a5c2a; font-size: 9pt; text-transform: uppercase; }
            td { padding: 7px 8px; border-bottom: 1px solid #dde3dd; }
            .footer { margin-top: 20px; font-size: 9pt; color: #8d9e8d; text-align: center; border-top: 1px solid #dde3dd; padding-top: 10px; }
        </style></head><body>
            <div class='header'>
                <h1>YapuUywa SGA</h1>
                <p>Sistema de Gestión Agropecuaria · Reporte Financiero</p>
            </div>
            <div class='meta'>
                <strong>Generado por:</strong> {$user->nombre} &nbsp;|&nbsp;
                <strong>Fecha:</strong> {$fecha}
            </div>
            <div class='balance'>
                <div class='bal-card ing'><div class='lbl'>Total ingresos</div><div class='val' style='color:#1a5c2a'>S/ ".number_format($ingresos,2)."</div></div>
                <div class='bal-card egr'><div class='lbl'>Total egresos</div><div class='val' style='color:#dc3545'>S/ ".number_format($egresos,2)."</div></div>
                <div class='bal-card bal'><div class='lbl'>Balance</div><div class='val' style='color:".($balance>=0?'#1a5c2a':'#dc3545')."'>S/ ".number_format($balance,2)."</div></div>
            </div>
            <h3>Ventas registradas ({$ventas->count()})</h3>
            <table>
                <thead><tr><th>Fecha</th><th>Producto</th><th>Categoría</th><th>Total</th></tr></thead>
                <tbody>{$filasV}</tbody>
            </table>
            <h3>Gastos registrados ({$gastos->count()})</h3>
            <table>
                <thead><tr><th>Fecha</th><th>Categoría</th><th>Descripción</th><th>Monto</th></tr></thead>
                <tbody>{$filasG}</tbody>
            </table>
            <div class='footer'>YapuUywa SGA · Reporte generado el {$fecha} · Puno, Perú</div>
        </body></html>";
    }
}
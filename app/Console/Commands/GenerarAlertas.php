<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HistorialMedico;
use App\Models\Alerta;
use Illuminate\Support\Facades\Log;

/**
 * RF05 + RNF10 — Motor diario de alertas sanitarias
 * Cron: php artisan alertas:vacunacion
 * Schedule: daily at 06:00 (ver routes/console.php)
 */
class GenerarAlertas extends Command
{
    protected $signature   = 'alertas:vacunacion';
    protected $description = 'Motor diario de alertas de vacunación (06:00 h)';

    public function handle(): int
    {
        $limite = now()->addDays(7);

        $rows = HistorialMedico::where('proxima_revision', '<=', $limite)
            ->where('proxima_revision', '>=', now()->startOfDay())
            ->where('alerta_atendida', false)
            ->with('animal')
            ->get();

        $procesadas = 0;

        foreach ($rows as $r) {
            /* Determinar estado: crítico si vence hoy o está vencida */
            $estado = $r->proxima_revision->isToday() || $r->proxima_revision->isPast()
                ? 'critico'
                : 'pendiente';

            /* updateOrCreate evita duplicados (RNF10) */
            Alerta::updateOrCreate(
                ['historial_id' => $r->id],
                [
                    'estado'      => $estado,
                    'usuario_id'  => $r->animal->user_id,
                    'descripcion' => "Vacunación pendiente: {$r->animal->nombre} ({$r->animal->arete})",
                    'medicamento' => $r->medicamento,
                    'dias_restantes' => now()->diffInDays($r->proxima_revision, false),
                ]
            );
            $procesadas++;
        }

        Log::info("[GenerarAlertas] Alertas procesadas: {$procesadas}");
        $this->info("✓ Alertas procesadas: {$procesadas}");

        return self::SUCCESS;
    }
}

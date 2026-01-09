<?php

namespace App\Http\Controllers\Standard;

use App\Http\Controllers\Controller;
use App\Models\Taller;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TallerController extends Controller
{
    /**
     * Listar talleres disponibles para el día (por defecto hoy).
     */
    public function disponibles(Request $request)
    {
        $fecha = $request->query('fecha');
        $carbon = $fecha ? Carbon::parse($fecha) : Carbon::today();

        $diaNombre = $this->diaSemanaNombre($carbon->dayOfWeekIso); // 1=lunes

        if (!$diaNombre) {
            return response()->json([
                'fecha' => $carbon->toDateString(),
                'fecha_iso' => $carbon->toISOString(true),
                'dia' => null,
                'talleres' => [],
            ]);
        }

        $talleres = Taller::with(['dias' => function ($q) use ($diaNombre) {
            $q->where('dia_semana', $diaNombre);
        }])->get()->filter(function ($taller) use ($diaNombre) {
            return $taller->dias->where('dia_semana', $diaNombre)->count() > 0;
        })->values()->map(function ($taller) use ($carbon) {
            // Anexar información de cupos del día
            $inscriptos = Inscripcion::where('taller_id', $taller->id)
                ->whereDate('fecha', $carbon->toDateString())
                ->count();
            $taller->inscriptos_en_fecha = $inscriptos;
            $taller->cupos = $taller->cupos ?? 0;
            $taller->cupos_restantes = max(0, $taller->cupos - $inscriptos);
            $taller->completo = $taller->cupos_restantes <= 0;
            return $taller;
        });

        return response()->json([
            'fecha' => $carbon->toDateString(),
            'fecha_iso' => $carbon->toISOString(true),
            'dia' => $diaNombre,
            'talleres' => $talleres,
        ]);
    }

    private function diaSemanaNombre(int $isoDay): ?string
    {
        return [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo',
        ][$isoDay] ?? null;
    }
}

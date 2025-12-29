<?php

namespace App\Http\Controllers\Standard;

use App\Http\Controllers\Controller;
use App\Models\Taller;
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
                'dia' => null,
                'talleres' => [],
            ]);
        }

        $talleres = Taller::with(['dias' => function ($q) use ($diaNombre) {
            $q->where('dia_semana', $diaNombre);
        }])->get()->filter(function ($taller) use ($diaNombre) {
            return $taller->dias->where('dia_semana', $diaNombre)->count() > 0;
        })->values();

        return response()->json([
            'fecha' => $carbon->toDateString(),
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
        ][$isoDay] ?? null;
    }
}

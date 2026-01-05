<?php

namespace App\Http\Controllers\Standard;

use App\Http\Controllers\Controller;
use App\Models\Cursante;
use App\Models\Inscripcion;
use App\Models\Taller;
use App\Models\TallerDia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class InscripcionController extends Controller
{
    /**
     * Listar inscripciones del día (por defecto hoy) con cursante y taller.
     */
    public function indexHoy(Request $request)
    {
        $fecha = $request->query('fecha');
        $hoy = $fecha ? Carbon::parse($fecha) : Carbon::today();
        $fechaDate = $hoy->toDateString();

        $inscripciones = Inscripcion::with(['cursante', 'taller'])
            ->whereDate('fecha', $fechaDate)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'fecha' => $fechaDate,
            // ISO 8601 con offset para evitar desfase en el frontend
            'fecha_iso' => $hoy->toISOString(true),
            'inscripciones' => $inscripciones,
        ]);
    }
    /**
     * Crear una inscripción del cursante a un taller en una fecha (por defecto hoy).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'dni' => ['required', 'string'],
            'taller_id' => ['required', 'integer', Rule::exists('talleres', 'id')],
            'fecha' => ['nullable', 'date'],
        ]);

        $fecha = $data['fecha'] ?? Carbon::today()->toDateString();

        $cursante = Cursante::where('dni', $data['dni'])->first();
        if (!$cursante) {
            return response()->json(['message' => 'Cursante no encontrado'], 404);
        }

        $taller = Taller::find($data['taller_id']);
        if (!$taller) {
            return response()->json(['message' => 'Taller no encontrado'], 404);
        }

        // Validar que el taller se dicta en el día de la semana indicado
        $diaIso = Carbon::parse($fecha)->dayOfWeekIso; // 1=lunes
        $diaNombre = [1=>'lunes',2=>'martes',3=>'miercoles',4=>'jueves',5=>'viernes'][$diaIso] ?? null;
        if (!$diaNombre) {
            return response()->json(['message' => 'Inscripciones solo de lunes a viernes'], 422);
        }

        $dictadoDia = TallerDia::where('taller_id', $taller->id)
            ->where('dia_semana', $diaNombre)
            ->exists();
        if (!$dictadoDia) {
            return response()->json(['message' => 'El taller no está disponible ese día'], 422);
        }

        // Regla: no inscribirse dos veces al mismo taller el mismo día
        $existe = Inscripcion::where('cursante_id', $cursante->id)
            ->where('taller_id', $taller->id)
            ->whereDate('fecha', $fecha)
            ->exists();
        if ($existe) {
            return response()->json(['message' => 'Ya está inscripto en este taller para ese día'], 422);
        }

        // Regla: máximo dos talleres por día
        $inscripcionesDia = Inscripcion::where('cursante_id', $cursante->id)
            ->whereDate('fecha', $fecha)
            ->count();
        if ($inscripcionesDia >= 2) {
            return response()->json(['message' => 'Máximo dos talleres por día'], 422);
        }

        $inscripcion = Inscripcion::create([
            'cursante_id' => $cursante->id,
            'taller_id' => $taller->id,
            'fecha' => $fecha,
        ]);

        return response()->json($inscripcion->load('taller'), 201);
    }
}

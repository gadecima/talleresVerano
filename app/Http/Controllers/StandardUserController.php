<?php

namespace App\Http\Controllers;

use App\Models\Cursante;
use App\Models\Inscripcion;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StandardUserController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Standard/Dashboard', [
            'user' => auth()->user()->load('role'),
        ]);
    }

    /**
     * Obtener datos de contadores del dashboard
     */
    public function getContadores()
    {
        $hoy = Carbon::today()->toDateString();

        // Cantidad de cursantes inscriptos hoy
        $inscriptosHoy = Inscripcion::whereDate('fecha', $hoy)
            ->distinct('cursante_id')
            ->count('cursante_id');

        // Nuevos cursantes registrados hoy
        $nuevosHoy = Cursante::whereDate('created_at', $hoy)->count();

        // Total de cursantes registrados en el sistema
        $totalCursantes = Cursante::count();

        return response()->json([
            'inscriptosHoy' => $inscriptosHoy,
            'nuevosHoy' => $nuevosHoy,
            'totalCursantes' => $totalCursantes,
        ]);
    }

    /**
     * Obtener detalles de inscripciones por taller para hoy
     */
    public function detallesInscripcionesHoy()
    {
        $hoy = Carbon::today()->toDateString();

        $detalles = DB::table('inscripciones')
            ->join('talleres', 'inscripciones.taller_id', '=', 'talleres.id')
            ->select(
                'talleres.id as taller_id',
                'talleres.nombre as nombre_taller',
                'talleres.espacio_fisico as espacio',
                'talleres.cupos',
                DB::raw('COUNT(inscripciones.id) as cantidad_inscriptos')
            )
            ->whereDate('inscripciones.fecha', $hoy)
            ->groupBy('talleres.id', 'talleres.nombre', 'talleres.espacio_fisico', 'talleres.cupos')
            ->orderBy('talleres.nombre')
            ->get();

        return response()->json([
            'detalles' => $detalles
        ]);
    }
}

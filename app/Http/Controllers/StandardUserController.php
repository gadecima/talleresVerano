<?php

namespace App\Http\Controllers;

use App\Models\Cursante;
use App\Models\Inscripcion;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
}

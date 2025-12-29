<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    /**
     * Listar todas las inscripciones con cursantes y talleres.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');

        $query = Inscripcion::with(['cursante', 'taller'])
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->whereHas('cursante', function($q) use ($search) {
                $q->where('nombre_apellido', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            })->orWhereHas('taller', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            });
        }

        $inscripciones = $query->paginate($perPage);

        return response()->json($inscripciones);
    }
}

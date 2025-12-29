<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TallerController extends Controller
{
    /**
     * Listar todos los talleres.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDesc = $request->query('sortDesc', 'true') === 'true';

        $allowedSorts = ['nombre', 'responsable', 'orientado', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query = Taller::query()->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('responsable', 'like', "%{$search}%")
                  ->orWhere('orientado', 'like', "%{$search}%");
            });
        }

        $talleres = $query->paginate($perPage);

        return response()->json($talleres);
    }

    /**
     * Registrar (crear) un taller.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'responsable' => ['required', 'string', 'max:255'],
            'orientado' => ['required', 'string', 'max:255'],
        ]);

        // Normalizar datos: cambio a mayusculas la primera letra de cada palabra
        $data['nombre'] = ucwords(strtolower(trim($data['nombre'])));
        $data['responsable'] = ucwords(strtolower(trim($data['responsable'])));
        $data['orientado'] = ucwords(strtolower(trim($data['orientado'])));

        $taller = Taller::create($data);

        return response()->json($taller, 201);
    }

    /**
     * Actualizar taller.
     */
    public function update(Request $request, Taller $taller)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'responsable' => ['required', 'string', 'max:255'],
            'orientado' => ['required', 'string', 'max:255'],
        ]);

        // Normalizar datos
        $data['nombre'] = ucwords(strtolower(trim($data['nombre'])));
        $data['responsable'] = ucwords(strtolower(trim($data['responsable'])));
        $data['orientado'] = ucwords(strtolower(trim($data['orientado'])));

        $taller->update($data);

        return response()->json($taller);
    }

    /**
     * Eliminar taller.
     */
    public function destroy(Taller $taller)
    {
        $taller->delete();
        return response()->json(['message' => 'Taller eliminado correctamente'], 200);
    }
}

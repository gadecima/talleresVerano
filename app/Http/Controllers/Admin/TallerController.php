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

        $allowedSorts = ['nombre', 'espacio_fisico', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query = Taller::with('dias')->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

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
            'nombre' => ['required', 'string', 'max:120'],
            'edad_minima' => ['required', 'integer', 'min:0', 'max:120'],
            'edad_maxima' => ['required', 'integer', 'gte:edad_minima', 'max:120'],
            'espacio_fisico' => ['nullable', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string'],
            'disponibilidad' => ['required', 'in:0,1'],
            'dias' => ['required', 'array', 'min:1'],
            'dias.*' => ['required', Rule::in(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'])],
            'cupos' => ['required', 'integer', 'min:1', 'max:65535'],
            //'responsable' => ['required', 'string', 'max:120'],
            //'orientado' => [ Rule::in(['inicial', 'primario', 'secundario', 'indefinido'])],
        ]);

        // Normalizar datos de texto
        $tallerData = [
            'nombre' => ucwords(strtolower(trim($data['nombre']))),
            'edad_minima' => $data['edad_minima'],
            'edad_maxima' => $data['edad_maxima'],
            'espacio_fisico' => $data['espacio_fisico'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
            'disponibilidad' => $data['disponibilidad'],
            'cupos' => $data['cupos'],
            //'responsable' => ucwords(strtolower(trim($data['responsable']))),
            //'orientado' => strtolower(trim($data['orientado'])),
        ];

        $taller = Taller::create($tallerData);

        // Crear los días del taller
        foreach ($data['dias'] as $dia) {
            $taller->dias()->create(['dia_semana' => $dia]);
        }

        return response()->json($taller->load('dias'), 201);
    }

    /**
     * Actualizar taller.
     */
    public function update(Request $request, Taller $taller)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'edad_minima' => ['required', 'integer', 'min:0', 'max:120'],
            'edad_maxima' => ['required', 'integer', 'gte:edad_minima', 'max:120'],
            'espacio_fisico' => ['nullable', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string'],
            'disponibilidad' => ['required', 'in:0,1'],
            'dias' => ['required', 'array', 'min:1'],
            'dias.*' => ['required', Rule::in(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'])],
            'cupos' => ['integer', 'min:1', 'max:65535'],
            //'responsable' => ['string', 'max:120'],
            //'orientado' => [ Rule::in(['inicial', 'primario', 'secundario', 'indefinido'])],
        ]);

        $tallerData = [
            'nombre' => ucwords(strtolower(trim($data['nombre']))),
            'edad_minima' => $data['edad_minima'],
            'edad_maxima' => $data['edad_maxima'],
            'espacio_fisico' => $data['espacio_fisico'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
            //'responsable' => ucwords(strtolower(trim($data['responsable']))),
            //'orientado' => strtolower(trim($data['orientado'])),
            'cupos' => $data['cupos'] ?? $taller->cupos,
            'disponibilidad' => $data['disponibilidad'],
        ];

        $taller->update($tallerData);

        // Actualizar los días: eliminar los existentes y crear los nuevos
        $taller->dias()->delete();
        foreach ($data['dias'] as $dia) {
            $taller->dias()->create(['dia_semana' => $dia]);
        }

        return response()->json($taller->load('dias'));
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

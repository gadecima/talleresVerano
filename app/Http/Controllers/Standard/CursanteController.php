<?php

namespace App\Http\Controllers\Standard;

use App\Http\Controllers\Controller;
use App\Models\Cursante;
use App\Models\Inscripcion;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class CursanteController extends Controller
{
    /**
     * Listar todos los cursantes.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDesc = $request->query('sortDesc', 'true') === 'true';

        $allowedSorts = ['nombre_apellido', 'dni', 'edad', 'localidad', 'tutor', 'contacto', 'correo', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query = Cursante::query()->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_apellido', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('localidad', 'like', "%{$search}%")
                  ->orWhere('tutor', 'like', "%{$search}%");
            });
        }

        $cursantes = $query->paginate($perPage);

        return response()->json($cursantes);
    }

    /**
     * Registrar (crear) un cursante.
     */
    public function store(Request $request)
    {
        // Limpiar espacios en blanco y modificar el request
        $request->merge([
            'dni' => trim($request->input('dni', '')),
            'correo' => trim($request->input('correo', '')) ?: null,
        ]);

        $data = $request->validate([
            'nombre_apellido' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string', 'regex:/^[0-9]{8}$/', 'unique:cursantes,dni'],
            'edad' => ['required', 'integer', 'min:0', 'max:120'],
            'localidad' => ['required', 'string', 'max:255'],
            'tutor' => ['required', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255', 'unique:cursantes,correo'],
        ],
        //capturo errores de validacion y los personalizo
        [
            'dni.unique' => 'Este DNI ya está registrado',
            'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos',
            'correo.unique' => 'Este correo electrónico ya está registrado',
            'correo.email' => 'El correo electrónico no es válido',
        ]);

        // Normalizar datos: cambio a mayusculas la primera letra de cada palabra
        $data['nombre_apellido'] = ucwords(strtolower(trim($data['nombre_apellido'])));
        if (isset($data['localidad'])) {
            $data['localidad'] = ucwords(strtolower(trim($data['localidad'])));
        }

        $cursante = Cursante::create($data);

        return response()->json($cursante, 201);
    }

    /**
     * Actualizar cursante.
     */
    public function update(Request $request, Cursante $cursante)
    {
        // Limpiar espacios en blanco y modificar el request
        $request->merge([
            'dni' => trim($request->input('dni', '')),
            'correo' => trim($request->input('correo', '')) ?: null,
        ]);

        $data = $request->validate([
            'nombre_apellido' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string', 'regex:/^[0-9]{8}$/', Rule::unique('cursantes')->ignore($cursante->id)],
            'edad' => ['required', 'integer', 'min:0', 'max:120'],
            'localidad' => ['required', 'string', 'max:255'],
            'tutor' => ['required', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255', Rule::unique('cursantes')->ignore($cursante->id)],
        ], [
            'dni.unique' => 'Este DNI ya está registrado',
            'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos',
            'correo.unique' => 'Este correo electrónico ya está registrado',
            'correo.email' => 'El correo electrónico no es válido',
        ]);

        // Normalizar datos
        $data['nombre_apellido'] = ucwords(strtolower(trim($data['nombre_apellido'])));
        if (isset($data['localidad'])) {
            $data['localidad'] = ucwords(strtolower(trim($data['localidad'])));
        }

        $cursante->update($data);

        return response()->json($cursante);
    }

    /**
     * Mostrar un cursante puntual.
     */
    public function show(Cursante $cursante)
    {
        return response()->json($cursante);
    }

    /**
     * Eliminar cursante.
     */
    public function destroy(Cursante $cursante)
    {
        $cursante->delete();
        return response()->json(['message' => 'Cursante eliminado correctamente'], 200);
    }

    /**
     * Buscar cursante por DNI y devolver sus inscripciones del día indicado.
     */
    public function buscarPorDni(Request $request, string $dni)
    {
        $fecha = $request->query('fecha');
        $fechaCarbon = $fecha ? Carbon::parse($fecha) : Carbon::today();
        $fechaDate = $fechaCarbon->toDateString();

        $cursante = Cursante::where('dni', $dni)->first();
        if (!$cursante) {
            return response()->json(['message' => 'Cursante no encontrado'], 404);
        }

        $inscripcionesHoy = Inscripcion::with('taller')
            ->where('cursante_id', $cursante->id)
            ->whereDate('fecha', $fechaDate)
            ->get();

        $diaNombre = $this->diaSemanaNombre($fechaCarbon->dayOfWeekIso);

        // Si ya alcanzó el máximo diario, no hay talleres disponibles.
        $talleresDisponibles = collect();

        if ($diaNombre && $inscripcionesHoy->count() < 2) {
            // Candidatos por día y edad
            $candidatos = Taller::with(['dias' => function ($q) use ($diaNombre) {
                    $q->where('dia_semana', $diaNombre);
                }])
                ->whereHas('dias', function ($q) use ($diaNombre) {
                    $q->where('dia_semana', $diaNombre);
                })
                ->where('edad_minima', '<=', $cursante->edad)
                ->where('edad_maxima', '>=', $cursante->edad)
                ->where('disponibilidad', 1)
                ->whereNotIn('id', $inscripcionesHoy->pluck('taller_id'))
                ->get();

            // Filtrar por cupos disponibles ese día y agregar metadatos
            $talleresDisponibles = $candidatos->filter(function ($taller) use ($fechaDate) {
                $inscriptos = Inscripcion::where('taller_id', $taller->id)
                    ->whereDate('fecha', $fechaDate)
                    ->count();
                return $inscriptos < ($taller->cupos ?? 0);
            })->values()->map(function ($taller) use ($fechaDate) {
                $inscriptos = Inscripcion::where('taller_id', $taller->id)
                    ->whereDate('fecha', $fechaDate)
                    ->count();
                // Añadir campos de ayuda para el frontend
                $taller->inscriptos_en_fecha = $inscriptos;
                $taller->cupos = $taller->cupos ?? 0;
                $taller->cupos_restantes = max(0, $taller->cupos - $inscriptos);
                $taller->completo = $taller->cupos_restantes <= 0;
                return $taller;
            });
        }

        return response()->json([
            'cursante' => $cursante,
            'inscripciones' => $inscripcionesHoy,
            'talleres_disponibles' => $talleresDisponibles,
            'dia' => $diaNombre,
            'fecha' => $fechaDate,
            'fecha_iso' => $fechaCarbon->toISOString(true),
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

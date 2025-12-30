<?php

namespace App\Http\Controllers\Standard;

use App\Http\Controllers\Controller;
use App\Models\Cursante;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
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

        $allowedSorts = ['nombre_apellido', 'dni', 'fecha_nacimiento', 'localidad', 'nivel_educativo', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query = Cursante::query()->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_apellido', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('localidad', 'like', "%{$search}%");
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
            'fecha_nacimiento' => ['required', 'date'],
            'localidad' => ['required', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255', 'unique:cursantes,correo'],
            'escuela' => ['nullable', 'string', 'max:255'],
            'nivel_educativo' => ['required', Rule::in(['inicial', 'primario', 'secundario'])],
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
            'fecha_nacimiento' => ['required', 'date'],
            'localidad' => ['required', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255', Rule::unique('cursantes')->ignore($cursante->id)],
            'escuela' => ['nullable', 'string', 'max:255'],
            'nivel_educativo' => ['required', Rule::in(['inicial', 'primario', 'secundario'])],
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
        $fechaDate = $fecha ? date('Y-m-d', strtotime($fecha)) : date('Y-m-d');

        $cursante = Cursante::where('dni', $dni)->first();
        if (!$cursante) {
            return response()->json(['message' => 'Cursante no encontrado'], 404);
        }

        $inscripcionesHoy = Inscripcion::with('taller')
            ->where('cursante_id', $cursante->id)
            ->whereDate('fecha', $fechaDate)
            ->get();

        return response()->json([
            'cursante' => $cursante,
            'inscripciones' => $inscripcionesHoy,
        ]);
    }
}

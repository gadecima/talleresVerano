<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cursante;
use App\Models\Inscripcion;
use App\Models\Taller;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /**
     * Obtiene contadores generales de cursantes
     */
    public function contadores(Request $request)
    {
        $mes = $request->query('mes', now()->month);
        $anio = $request->query('anio', now()->year);

        // Total de cursantes registrados
        $totalCursantes = Cursante::count();

        // Cursantes que tuvieron al menos una inscripción en el mes/año seleccionado
        $cursantesMes = Inscripcion::whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->distinct('cursante_id')
            ->count('cursante_id');

        return response()->json([
            'total_cursantes' => $totalCursantes,
            'cursantes_mes' => $cursantesMes,
            'mes' => $mes,
            'anio' => $anio,
        ]);
    }

    /**
     * Obtiene cantidad de cursantes por taller en un mes específico
     */
    public function cursantesPorTallerMes(Request $request)
    {
        $mes = $request->query('mes', now()->month);
        $anio = $request->query('anio', now()->year);

        $talleres = Taller::all();

        $datos = [];
        foreach ($talleres as $taller) {
            $cantidad = Inscripcion::where('taller_id', $taller->id)
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', $anio)
                ->distinct('cursante_id')
                ->count('cursante_id');

            // Solo incluir talleres con más de 5 cursantes
            if ($cantidad > 5) {
                $datos[] = [
                    'taller' => $taller->nombre,
                    'cantidad' => $cantidad,
                ];
            }
        }

        return response()->json([
            'data' => $datos,
            'mes' => $mes,
            'anio' => $anio,
        ]);
    }

    /**
     * Obtiene la evolución de cursantes por semanas en un mes
     */
    public function cursantesPorSemana(Request $request)
    {
        $mes = $request->query('mes', now()->month);
        $anio = $request->query('anio', now()->year);

        $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fin = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();

        $semanas = [];
        $actual = $inicio->copy();

        while ($actual <= $fin) {
            $semana_inicio = $actual->copy()->startOfWeek();
            $semana_fin = $actual->copy()->endOfWeek();

            // Contar inscripciones que caigan dentro de la semana (sin importar si están en otro mes)
            // pero que tengan fecha en el mes seleccionado
            $cantidad = Inscripcion::whereBetween('fecha', [$semana_inicio, $semana_fin])
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', $anio)
                ->distinct('cursante_id')
                ->count('cursante_id');

            // También contar inscripciones que caigan en la misma semana pero en otro mes
            // (caso: semana que abarca enero y febrero)
            if ($cantidad == 0) {
                // Si es una semana que abarca dos meses, buscar también en el otro mes
                $cantidad = Inscripcion::whereBetween('fecha', [$semana_inicio, $semana_fin])
                    ->distinct('cursante_id')
                    ->count('cursante_id');
            }

            $semanas[] = [
                'semana' => "Semana del " . $semana_inicio->format('d/m') . " al " . $semana_fin->format('d/m'),
                'cantidad' => $cantidad,
                'fecha_inicio' => $semana_inicio->toDateString(),
            ];

            $actual->addWeek();
        }

        // Eliminar duplicados (en caso de que el mes empiece a mitad de semana)
        $semanas = collect($semanas)->unique('semana')->values()->all();

        return response()->json([
            'data' => $semanas,
            'mes' => $mes,
            'anio' => $anio,
        ]);
    }

    /**
     * Obtiene asistencias por cursante desde el 01 de enero hasta hoy
     */
    public function asistenciasPorCursante(Request $request)
    {
        $inicio = Carbon::createFromDate(now()->year, 1, 1);
        $fin = now();

        $cursantes = Cursante::all();

        $datos = $cursantes->map(function ($cursante) use ($inicio, $fin) {
            $asistencias = Inscripcion::where('cursante_id', $cursante->id)
                ->whereBetween('fecha', [$inicio, $fin])
                ->count();

            return [
                'nombre_apellido' => $cursante->nombre_apellido,
                'dni' => $cursante->dni,
                'edad' => $cursante->edad,
                'localidad' => $cursante->localidad,
                'asistencias' => $asistencias,
            ];
        })
        ->sortByDesc('asistencias')
        ->values()
        ->all();

        return response()->json([
            'data' => $datos,
            'fecha_reporte' => now()->toDateString(),
        ]);
    }

    /**
     * Renderiza la página de reportes
     */
    public function index()
    {
        return inertia('Admin/Reporte');
    }

    /**
     * Exporta reporte de cursantes por taller a PDF
     */
    public function exportCursantesTallerPdf(Request $request)
    {
        $mes = $request->query('mes', now()->month);
        $anio = $request->query('anio', now()->year);

        $talleres = Taller::all();

        $datos = [];
        foreach ($talleres as $taller) {
            $cantidad = Inscripcion::where('taller_id', $taller->id)
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', $anio)
                ->distinct('cursante_id')
                ->count('cursante_id');

            $datos[] = [
                'taller' => $taller->nombre,
                'cantidad' => $cantidad,
            ];
        }

        // Ordenar datos de mayor a menor cantidad
        usort($datos, function($a, $b) {
            return $b['cantidad'] - $a['cantidad'];
        });

        // Calcular total de cursantes únicos en el mes
        $totalCursantes = Inscripcion::whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->distinct('cursante_id')
            ->count('cursante_id');

        $pdf = Pdf::loadView('exports.reporte-cursantes-taller-pdf', [
            'datos' => $datos,
            'mes' => $mes,
            'anio' => $anio,
            'total_cursantes' => $totalCursantes,
        ]);

        $nombreArchivo = "cursantes_taller_{$mes}_{$anio}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exporta reporte de cursantes por semana a PDF
     */
    public function exportCursantesSemanasPdf(Request $request)
    {
        $mes = $request->query('mes', now()->month);
        $anio = $request->query('anio', now()->year);

        $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fin = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();

        $semanas = [];
        $actual = $inicio->copy();

        while ($actual <= $fin) {
            $semana_inicio = $actual->copy()->startOfWeek();
            $semana_fin = $actual->copy()->endOfWeek();

            $cantidad = Inscripcion::whereBetween('fecha', [$semana_inicio, $semana_fin])
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', $anio)
                ->distinct('cursante_id')
                ->count('cursante_id');

            $semanas[] = [
                'semana' => "Semana del " . $semana_inicio->format('d/m') . " al " . $semana_fin->format('d/m'),
                'cantidad' => $cantidad,
            ];

            $actual->addWeek();
        }

        $semanas = collect($semanas)->unique('semana')->values()->all();

        $pdf = Pdf::loadView('exports.reporte-cursantes-semanas-pdf', [
            'datos' => $semanas,
            'mes' => $mes,
            'anio' => $anio,
        ]);

        $nombreArchivo = "cursantes_semanas_{$mes}_{$anio}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exporta reporte de asistencias por cursante a PDF
     */
    public function exportAsistenciasPdf()
    {
        $inicio = Carbon::createFromDate(now()->year, 1, 1);
        $fin = now();

        $cursantes = Cursante::all();

        $datos = $cursantes->map(function ($cursante) use ($inicio, $fin) {
            $asistencias = Inscripcion::where('cursante_id', $cursante->id)
                ->whereBetween('fecha', [$inicio, $fin])
                ->count();

            return [
                'nombre_apellido' => $cursante->nombre_apellido,
                'dni' => $cursante->dni,
                'edad' => $cursante->edad,
                'localidad' => $cursante->localidad,
                'asistencias' => $asistencias,
            ];
        });

        // Ordenar explícitamente por asistencias de mayor a menor
        $datos = collect($datos)->sortByDesc('asistencias')->values()->all();

        $pdf = Pdf::loadView('exports.reporte-asistencias-pdf', [
            'datos' => $datos,
            'fecha_reporte' => now()->toDateString(),
        ]);

        $nombreArchivo = "asistencias_cursantes_" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($nombreArchivo);
    }

}

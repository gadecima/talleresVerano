<?php

namespace App\Http\Controllers;

use App\Exports\TallerInscripcionesExport;
use App\Models\Taller;
use App\Models\Inscripcion;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TallerExportController extends Controller
{
    /**
     * Exportar inscripciones de un taller en un día específico a PDF
     */
    public function exportPdf($tallerId, $fecha)
    {
        $taller = Taller::findOrFail($tallerId);

        $inscripciones = Inscripcion::with('cursante')
            ->where('taller_id', $tallerId)
            ->whereDate('fecha', $fecha)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('exports.taller-inscripciones-pdf', [
            'taller' => $taller,
            'inscripciones' => $inscripciones,
            'fecha' => $fecha
        ]);

        $fechaFormateada = date('Y-m-d', strtotime($fecha));
        $nombreArchivo = "taller_{$taller->nombre}_{$fechaFormateada}.pdf";
        $nombreArchivo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nombreArchivo);

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar inscripciones de un taller en un día específico a Excel
     */
    public function exportExcel($tallerId, $fecha)
    {
        $taller = Taller::findOrFail($tallerId);

        $fechaFormateada = date('Y-m-d', strtotime($fecha));
        $nombreArchivo = "taller_{$taller->nombre}_{$fechaFormateada}.xlsx";
        $nombreArchivo = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $nombreArchivo);

        return Excel::download(
            new TallerInscripcionesExport($tallerId, $fecha, $taller->nombre),
            $nombreArchivo
        );
    }

    /**
     * Exportar todos los talleres con inscriptos en una fecha a PDF
     */
    public function exportTalleresDiaPdf(Request $request)
    {
        $fechaInput = $request->query('fecha');
        $fecha = $fechaInput ? Carbon::parse($fechaInput) : Carbon::today();
        $fechaDate = $fecha->toDateString();

        $inscripciones = Inscripcion::with(['cursante', 'taller'])
            ->whereDate('fecha', $fechaDate)
            ->orderBy('taller_id')
            ->orderBy('created_at')
            ->get();

        $talleres = $inscripciones
            ->groupBy('taller_id')
            ->filter(fn ($items) => $items->count() > 0 && $items->first()->taller)
            ->map(fn ($items) => [
                'taller' => $items->first()->taller,
                'inscripciones' => $items,
            ])
            ->values();

        $pdf = Pdf::loadView('exports.talleres-inscripciones-dia-pdf', [
            'talleres' => $talleres,
            'fecha' => $fechaDate,
            'fecha_legible' => $fecha->locale('es')->translatedFormat('l d \d\e F \d\e Y'),
        ]);

        $nombreArchivo = 'inscripciones_por_taller_' . $fechaDate . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}

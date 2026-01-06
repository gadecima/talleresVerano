<?php

namespace App\Http\Controllers;

use App\Exports\TallerInscripcionesExport;
use App\Models\Taller;
use App\Models\Inscripcion;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

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
}

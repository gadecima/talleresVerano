<?php

namespace App\Exports;

use App\Models\Inscripcion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TallerInscripcionesExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected $tallerId;
    protected $fecha;
    protected $nombreTaller;

    public function __construct($tallerId, $fecha, $nombreTaller)
    {
        $this->tallerId = $tallerId;
        $this->fecha = $fecha;
        $this->nombreTaller = $nombreTaller;
    }

    /**
     * Obtener la colección de inscripciones
     */
    public function collection()
    {
        return Inscripcion::with('cursante')
            ->where('taller_id', $this->tallerId)
            ->whereDate('fecha', $this->fecha)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Encabezados de la tabla
     */
    public function headings(): array
    {
        return [
            'Nombre y Apellido',
            'DNI',
            'Edad',
            'Contacto',
            'Fecha de Inscripción'
        ];
    }

    /**
     * Mapear los datos de cada fila
     */
    public function map($inscripcion): array
    {
        return [
            $inscripcion->cursante->nombre_apellido,
            $inscripcion->cursante->dni,
            $inscripcion->cursante->edad,
            $inscripcion->cursante->contacto ?? 'N/A',
            $inscripcion->created_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return substr($this->nombreTaller, 0, 31); // Excel limita a 31 caracteres
    }

    /**
     * Estilos de la hoja
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila de encabezados
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4A90E2']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],
        ];
    }

    /**
     * Ancho de columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 35,  // Nombre y Apellido
            'B' => 15,  // DNI
            'C' => 10,  // Edad
            'D' => 25,  // Contacto
            'E' => 20,  // Fecha de Inscripción
        ];
    }
}

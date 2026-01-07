<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscriptos por taller - {{ $fecha }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; font-size: 12px; color: #2c3e50; }
        h1 { margin: 0 0 6px 0; font-size: 22px; }
        h2 { margin: 0; font-size: 16px; }
        p { margin: 4px 0; }
        .header { text-align: center; border-bottom: 3px solid #4A90E2; padding-bottom: 12px; margin-bottom: 18px; }
        .taller-card { margin-bottom: 24px; page-break-inside: avoid; }
        .taller-header { display: flex; justify-content: space-between; align-items: baseline; border-bottom: 1px solid #dfe6e9; padding-bottom: 6px; margin-bottom: 8px; }
        .sub { color: #7f8c8d; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #dfe6e9; padding: 8px; text-align: left; }
        thead { background: #4A90E2; color: #fff; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        .empty { text-align: center; color: #95a5a6; padding: 28px 0; border: 1px dashed #dfe6e9; }
        .footer { text-align: center; color: #95a5a6; font-size: 10px; border-top: 1px solid #dfe6e9; padding-top: 10px; margin-top: 28px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listado de inscriptos por taller</h1>
        <p class="sub">Fecha del reporte: {{ $fecha_legible }}</p>
    </div>

    @if($talleres->count() === 0)
        <div class="empty">No hay talleres con inscriptos para la fecha seleccionada.</div>
    @else
        @foreach($talleres as $tallerData)
            <div class="taller-card">
                <div class="taller-header">
                    <div>
                        <h2>{{ $tallerData['taller']->nombre }}</h2>
                        <p class="sub">Espacio: {{ $tallerData['taller']->espacio_fisico ?? 'Sin definir' }}</p>
                    </div>
                    <div class="sub">Total inscriptos: {{ $tallerData['inscripciones']->count() }}</div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 6%;">#</th>
                            <th style="width: 42%;">Nombre y Apellido</th>
                            <th style="width: 16%;">DNI</th>
                            <th style="width: 10%;">Edad</th>
                            <th style="width: 26%;">Contacto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tallerData['inscripciones'] as $index => $inscripcion)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $inscripcion->cursante->nombre_apellido }}</td>
                                <td>{{ $inscripcion->cursante->dni }}</td>
                                <td style="text-align: center;">{{ $inscripcion->cursante->edad }}</td>
                                <td>{{ $inscripcion->cursante->contacto ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} | Sistema de Gestión de Talleres de Verano
    </div>
</body>
</html>

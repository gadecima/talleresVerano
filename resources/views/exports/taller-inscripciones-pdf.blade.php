<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripciones - {{ $taller->nombre }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4A90E2;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-section p {
            margin: 5px 0;
        }
        .info-section strong {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background-color: #4A90E2;
            color: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            font-weight: bold;
            font-size: 13px;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #e8f4f8;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #95a5a6;
            font-style: italic;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #27ae60;
            color: white;
            border-radius: 3px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listado de Inscripciones</h1>
        <p>{{ $taller->nombre }}</p>
    </div>

    <div class="info-section">
        <p><strong>Taller:</strong> {{ $taller->nombre }}</p>
        <p><strong>Descripción:</strong> {{ $taller->descripcion }}</p>
        <p><strong>Rango de edad:</strong> {{ $taller->edad_minima }} - {{ $taller->edad_maxima }} años</p>
        <p><strong>Límite de cupos:</strong> {{ $taller->limite_cupos }}</p>
        <p><strong>Fecha del reporte:</strong> {{ date('d/m/Y', strtotime($fecha)) }}</p>
        <p><strong>Total de inscriptos:</strong> <span class="badge">{{ $inscripciones->count() }}</span></p>
    </div>

    @if($inscripciones->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Nombre y Apellido</th>
                    <th style="width: 15%;">DNI</th>
                    <th style="width: 10%;">Edad</th>
                    <th style="width: 25%;">Contacto</th>
                    <th style="width: 10%;">Hora Inscripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inscripciones as $index => $inscripcion)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $inscripcion->cursante->nombre_apellido }}</td>
                        <td>{{ $inscripcion->cursante->dni }}</td>
                        <td>{{ $inscripcion->cursante->edad }}</td>
                        <td>{{ $inscripcion->cursante->contacto ?? 'N/A' }}</td>
                        <td>{{ $inscripcion->created_at->format('H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>No hay inscripciones registradas para este taller en la fecha seleccionada.</p>
        </div>
    @endif

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y H:i') }} | Sistema de Gestión de Talleres de Verano</p>
    </div>
</body>
</html>

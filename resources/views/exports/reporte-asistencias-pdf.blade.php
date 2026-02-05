<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Asistencias de Cursantes</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 11px;
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
            padding: 10px;
            border-radius: 5px;
        }
        .info-section p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        thead {
            background-color: #4A90E2;
            color: white;
        }
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
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
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Asistencias de Cursantes</h1>
        <p>Período: 01 de Enero hasta {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
    </div>

    <div class="info-section">
        <p><strong>Fecha de Reporte:</strong> {{ $fecha_reporte }}</p>
        <p><strong>Fecha de Generación:</strong> {{ now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }}</p>
        <p><strong>Total de Cursantes:</strong> {{ count($datos) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>DNI</th>
                <th style="text-align: center;">Edad</th>
                <th>Localidad</th>
                <th style="text-align: center;">Asistencias</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $fila)
            <tr>
                <td>{{ $fila['nombre_apellido'] }}</td>
                <td>{{ $fila['dni'] }}</td>
                <td style="text-align: center;">{{ $fila['edad'] }}</td>
                <td>{{ $fila['localidad'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $fila['asistencias'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado automáticamente el {{ now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i:s') }}</p>
    </div>
</body>
</html>

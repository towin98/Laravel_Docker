<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .header p {
            font-size: 12px;
            color: gray;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #dddddd;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f4f4f4;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: gray;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Tecnologias</h1>
    </div>
    <p>Generado el: {{ date('d/m/Y H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            {!! $tabla !!}
        </tbody>

    </table>

    <div class="footer">
        &copy; {{ date('Y') }} - Reporte generado automáticamente.
    </div>
</body>
</html>

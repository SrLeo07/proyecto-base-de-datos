<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Órdenes registradas</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #e0f7fa 0%, #f3e5f5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255,255,255,0.98);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.12);
            border-radius: 18px;
            padding: 36px 24px 24px 24px;
            max-width: 900px;
            width: 100%;
        }
        h1 {
            color: #1976d2;
            text-align: center;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(33,150,243,0.08);
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background: #1976d2;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #e3f2fd;
        }
        tr:nth-child(odd) {
            background: #f8fafc;
        }
        .volver {
            display: block;
            margin: 24px auto 0 auto;
            text-align: center;
            color: #1976d2;
            text-decoration: underline;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Órdenes registradas</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Servicio</th>
                <th>Entrega</th>
                <th>Pago</th>
                <th>Total (Q)</th>
                <th>Fecha</th>
            </tr>
            @forelse($ordenes as $orden)
            <tr>
                <td>{{ $orden->id }}</td>
                <td>{{ $orden->nombre }}</td>
                <td>{{ $orden->direccion }}</td>
                <td>{{ $orden->telefono }}</td>
                <td>{{ $orden->servicio }}</td>
                <td>{{ $orden->entrega === 'entrega_domicilio' ? 'A domicilio' : 'En empresa' }}</td>
                <td>{{ $orden->pago }}</td>
                <td>{{ $orden->total }}</td>
                <td>{{ optional($orden->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;">No hay órdenes registradas.</td>
            </tr>
            @endforelse
        </table>
        <a href="{{ url('/') }}" class="volver">Volver al inicio</a>
    </div>
</body>
</html>

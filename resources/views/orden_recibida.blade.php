<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden Recibida - Confirmación</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f6fbfc; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border-radius: 12px; padding: 28px; max-width: 540px; width: 100%; box-shadow: 0 8px 30px rgba(0,0,0,0.05); text-align: center; }
        h1 { color: #2e7d32; margin-bottom: 12px; }
        .sub { color: #1565c0; margin-bottom: 18px; }
        .info { text-align: left; margin: 12px 0; color: #37474f; }
        .info b { color: #0d47a1; }
        .total { font-size: 1.25rem; color: #1b5e20; margin-top: 14px; font-weight: 700; }
        a.button { display: inline-block; margin-top: 18px; background:#0070ba; color:#fff; padding:10px 18px; border-radius:8px; text-decoration:none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Pago recibido</h1>
        <p class="sub">Gracias por tu compra. Tu pago fue procesado correctamente.</p>

        <div class="info">
            <p><b>Orden #</b> {{ $orden->id }}</p>
            <p><b>Nombre</b> {{ $orden->nombre }}</p>
            <p><b>Servicio</b> {{ $orden->servicio }}</p>
            <p><b>Entrega</b> {{ $orden->entrega }}</p>
            <p><b>Teléfono</b> {{ $orden->telefono }}</p>
            <p class="total">Total pagado: Q{{ number_format($orden->total, 2) }}</p>
            @if(isset($orden->paypal_order_id))
                <p><b>PayPal Order ID:</b> {{ $orden->paypal_order_id }}</p>
            @endif
            <p style="margin-top:10px;color:#616161">Recibirás una notificación por correo y nos pondremos en contacto para coordinar la entrega o recogida.</p>
        </div>

        <a class="button" href="{{ url('/') }}">Volver al inicio</a>
    </div>
</body>
</html>

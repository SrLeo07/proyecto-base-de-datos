<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Orden</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            height: 100vh;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            background-image: url('https://www.transparenttextures.com/patterns/bubbles.png');
            background-repeat: repeat;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: grid;
            place-items: center;
        }
        .container {
            background: rgba(255,255,255,0.97);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.15);
            border-radius: 16px;
            padding: 36px 28px 28px 28px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            margin: 0;
            box-sizing: border-box;
        }
        h1 {
            color: #43a047;
            font-size: 2rem;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        .icon-check {
            margin: 0 auto 18px auto;
            display: block;
        }
        p {
            color: #1976d2;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        strong {
            color: #1565c0;
        }
        .total {
            font-size: 1.3rem;
            color: #388e3c;
            font-weight: bold;
            margin: 18px 0;
        }
        a {
            display: inline-block;
            margin-top: 18px;
            color: #fff;
            background: linear-gradient(90deg, #2196f3 60%, #64b5f6 100%);
            padding: 10px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s, transform 0.2s;
        }
        a:hover {
            background: linear-gradient(90deg, #1976d2 60%, #2196f3 100%);
            transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 600px) {
            .container {
                padding: 12px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <svg class="icon-check" width="64" height="64" fill="none" viewBox="0 0 64 64">
            <circle cx="32" cy="32" r="32" fill="#b2dfdb"/>
            <path d="M18 34l10 10 18-18" stroke="#43a047" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h1>¡Orden recibida!</h1>
    <p>Gracias, <strong>{{ $nombre ?? '' }}</strong>.</p>
    <p>Tu orden para <strong>{{ $servicio ?? '' }}</strong> ha sido registrada.</p>
    <p>Entrega: <strong>{{ (($entrega ?? '') === 'entrega_domicilio') ? 'Entrega a domicilio' : 'Recoger en empresa' }}</strong></p>
        <p>Pago: <strong>Tarjeta de crédito vía Paypal</strong></p>
    <div class="total">Total estimado: Q{{ $total ?? '0' }}</div>
    <p>Nos comunicaremos al teléfono <strong>{{ $telefono ?? '' }}</strong> para coordinar la recogida/entrega.</p>
        
        <!-- Formulario de PayPal -->
        <form action="{{ route('paypal.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="orden_id" value="{{ $orden->id ?? '' }}">
            <button type="submit" style="background-color: #0070ba; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 10px 0;">
                Pagar con PayPal
            </button>
        </form>
        
        <a href="{{ url('/') }}">Volver al inicio</a>
    </div>
</body>
</html>

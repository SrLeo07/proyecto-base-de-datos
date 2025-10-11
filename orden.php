<?php
// Formulario para solicitar servicio de lavandería
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lavandería Express - Orden de Servicio</title>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            background-image: url('https://www.transparenttextures.com/patterns/bubbles.png');
            background-repeat: repeat;
        }
        .container {
            background: rgba(255,255,255,0.97);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.15);
            border-radius: 16px;
            padding: 36px 28px 28px 28px;
            margin-top: 60px;
            max-width: 420px;
        }
        h1 {
            color: #2196f3;
            font-size: 2rem;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 1px;
        }
        label {
            color: #1976d2;
            font-weight: 500;
            margin-top: 12px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #90caf9;
            border-radius: 6px;
            background: #e3f2fd;
            font-size: 1rem;
            margin-bottom: 12px;
            transition: border 0.2s;
        }
        input:focus, select:focus {
            border: 1.5px solid #1976d2;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #2196f3 60%, #64b5f6 100%);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 18px;
            box-shadow: 0 4px 12px rgba(33,150,243,0.12);
            transition: background 0.2s, transform 0.2s;
        }
        button:hover {
            background: linear-gradient(90deg, #1976d2 60%, #2196f3 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #1976d2;
            text-decoration: underline;
            font-size: 1rem;
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
        <h1>Ordena tu Servicio de Lavandería</h1>
        <form action="procesar_orden.php" method="POST">
            <label for="nombre">Nombre completo:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="servicio">Tipo de servicio:</label>
            <select id="servicio" name="servicio" required>
                <option value="lavado">Lavado ($50 MXN)</option>
                <option value="secado">Secado ($30 MXN)</option>
                <option value="aspirado">Aspirado ($40 MXN)</option>
                <option value="lavado_secado">Lavado + Secado ($70 MXN)</option>
            </select>

            <label for="entrega">Método de entrega:</label>
            <select id="entrega" name="entrega" required>
                <option value="recoger_empresa">Recoger en empresa</option>
                <option value="entrega_domicilio">Entrega a domicilio (+$20 MXN)</option>
            </select>

            <label for="pago">Pago:</label>
            <select id="pago" name="pago" required>
                <option value="paypal">Tarjeta de crédito vía Paypal</option>
            </select>

            <button type="submit">Solicitar Servicio</button>
        </form>
        <a href="index.php" class="back-link">Volver al inicio</a>
    </div>
</body>
</html>

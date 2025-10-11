<?php
// Página principal del sistema de lavandería
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lavandería Express - Inicio</title>
    <link rel="stylesheet" href="/Base_de_datos/public/CSS/styles.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenido a Lavandería Express</h1>
        <p>
            Somos una empresa dedicada al <strong>lavado, secado y aspirado de ropa</strong> con servicio a domicilio y en nuestras instalaciones.
        </p>
        <h2>¿Cómo funciona?</h2>
        <ul>
            <li>Solicita tu servicio en línea.</li>
            <li>Nuestros motoristas recogen tu ropa en tu domicilio o puedes traerla a la empresa.</li>
            <li>Elige si deseas recoger tu ropa o recibirla en tu domicilio.</li>
            <li>Paga de forma segura con tarjeta de crédito vía Paypal.</li>
        </ul>
        <h2>Tarifas</h2>
        <table style="width:100%;margin-bottom:20px;">
            <tr>
                <th>Servicio</th>
                <th>Precio</th>
            </tr>
            <tr>
                <td>Lavado</td>
                <td>$50 MXN</td>
            </tr>
            <tr>
                <td>Secado</td>
                <td>$30 MXN</td>
            </tr>
            <tr>
                <td>Aspirado</td>
                <td>$40 MXN</td>
            </tr>
            <tr>
                <td>Lavado + Secado</td>
                <td>$70 MXN</td>
            </tr>
            <tr>
                <td>Recogida o entrega a domicilio</td>
                <td>$20 MXN extra</td>
            </tr>
        </table>
        <a href="orden.php" class="button">Solicitar Servicio</a>
    </div>
</body>
</html>

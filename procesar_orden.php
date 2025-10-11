<?php
// Procesa la orden y muestra confirmación

function calcular_tarifa($servicio, $entrega) {
    $tarifas = [
        'lavado' => 50,
        'secado' => 30,
        'aspirado' => 40,
        'lavado_secado' => 70
    ];
    $total = isset($tarifas[$servicio]) ? $tarifas[$servicio] : 0;
    if ($entrega === 'entrega_domicilio') {
        $total += 20;
    }
    return $total;
}

$nombre = htmlspecialchars($_POST['nombre']);
$servicio = htmlspecialchars($_POST['servicio']);
$entrega = htmlspecialchars($_POST['entrega']);
$telefono = htmlspecialchars($_POST['telefono']);
$total = calcular_tarifa($_POST['servicio'], $_POST['entrega']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Orden</title>
    <link rel="stylesheet" href="/Base_de_datos/public/CSS/styles.css">
</head>
<body>
    <div class="container">
        <h1>¡Orden recibida!</h1>
        <p>Gracias, <?php echo $nombre; ?>.</p>
        <p>Tu orden para <strong><?php echo $servicio; ?></strong> ha sido registrada.</p>
        <p>Entrega: <strong><?php echo $entrega === 'entrega_domicilio' ? 'Entrega a domicilio' : 'Recoger en empresa'; ?></strong></p>
        <p>Pago: <strong>Tarjeta de crédito vía Paypal</strong></p>
        <p>Total estimado: <strong>$<?php echo $total; ?> MXN</strong></p>
        <p>Nos comunicaremos al teléfono <strong><?php echo $telefono; ?></strong> para coordinar la recogida/entrega.</p>
        <a href="index.php">Volver al inicio</a>
    </div>
</body>
</html>

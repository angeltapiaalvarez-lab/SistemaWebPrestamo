<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Pagos - PDF</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/reporte.css'); ?>">
</head>

<body>
    <table id="datos-empresa">
        <tr>
            <td class="logo">
                <img src="<?= base_url('assets/img/logo.png'); ?>" alt="">
            </td>
            <td class="info-empresa">
                <p><?= $empresa['nombre']; ?></p>
                <p><?= $empresa['identidad']; ?></p>
                <p>Teléfono: <?= $empresa['telefono']; ?></p>
                <p>Dirección: <?= $empresa['direccion']; ?></p>
            </td>
            <td class="info-fecha">
                <div class="container-fecha">
                    <span class="contrato"><?= $titulo; ?></span>
                    <p>Fecha Generación: <?= date('Y-m-d'); ?></p>
                    <p>Hora: <?= date('h:i A'); ?></p>
                </div>
            </td>
        </tr>
    </table>
    
    <h5 class="title">Resumen de Pagos / Abonos Recibidos</h5>
    <table id="container-cuotas">
        <thead>
            <tr>
                <th class="text-left">Recibo #</th>
                <th class="text-left">Cliente</th>
                <th class="text-left">Préstamo</th>
                <th class="text-left">Monto Recaudado</th>
                <th class="text-left">Método Pago</th>
                <th class="text-left">Fecha del Depósito</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagos as $p) { ?>
                <tr>
                    <td><?= str_pad($p['id'], 6, '0', STR_PAD_LEFT); ?></td>
                    <td><?= $p['cliente_nombre'] . ' ' . $p['cliente_apellido']; ?></td>
                    <td>#<?= $p['prestamo']; ?> (<?= $p['moneda'] ?>)</td>
                    <td><?= $p['monto_formateado'] ?></td>
                    <td><?= $p['metodo']; ?></td>
                    <td><?= date('Y-m-d g:i A', strtotime($p['fecha_pago'])); ?></td>
                </tr>
            <?php } ?>
            
            <?php if (!empty($totales_moneda)) : ?>
                <?php foreach ($totales_moneda as $codigo => $montoTotal) : ?>
                    <tr>
                        <td colspan="3" class="text-right">
                            <h3>Total Recaudado en <?= currency_name($codigo) . ' (' . $codigo . ')'; ?></h3>
                        </td>
                        <td><strong><?= format_currency($montoTotal, $codigo); ?></strong></td>
                        <td colspan="2"></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="mensaje" style="margin-top: 30px;">
        Generado por: <?= $usuario; ?> el <?= $generado; ?>
    </div>
</body>
</html>

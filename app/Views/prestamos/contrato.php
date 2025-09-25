<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/reporte.css'); ?>">
</head>

<body>
    <table id="datos-empresa">
        <tr>
            <td class="logo">
                <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="">
            </td>
            <td class="info-empresa">
                <p><?php echo $empresa['nombre']; ?></p>
                <p><?php echo $empresa['identidad']; ?></p>
                <p>Teléfono: <?php echo $empresa['telefono']; ?></p>
                <p>Dirección: <?php echo $empresa['direccion']; ?></p>
            </td>
            <td class="info-fecha">
                <div class="container-fecha">
                    <span class="contrato">Contrato</span>
                    <p>N° <strong><?php echo $prestamo['id']; ?></strong></p>
                    <p>Fecha: <?php
                                $dato = $prestamo['fecha'];
                                $fecha = date('Y-m-d', strtotime($dato));
                                $hora = date('h:i A', strtotime($dato));
                                echo fechaPerzo($fecha);
                                ?></p>
                    <p>Hora: <?php echo $hora; ?></p>
                </div>
            </td>
        </tr>
    </table>
    <h5 class="title">Datos del cliente</h5>
    <div class="container-cliente">
        <table id="container-info">
            <tr>
                <td>
                    <strong><?php echo $prestamo['identidad']; ?></strong>
                    <p><?php echo $prestamo['num_identidad']; ?></p>
                </td>
                <td>
                    <strong>Nombre</strong>
                    <p><?php echo $prestamo['cliente'] . ' ' . $prestamo['apellido']; ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Teléfono</strong>
                    <p><?php echo $prestamo['telefono']; ?></p>
                </td>
                <td>
                    <strong>Dirección</strong>
                    <p><?php echo $prestamo['direccion']; ?></p>
                </td>
            </tr>
        </table>
    </div>
    <p><strong>Moneda:</strong> <?php echo $moneda_nombre ?? currency_name($prestamo['moneda'] ?? 'NIO'); ?></p>
    <h5 class="title">Datos de las cuotas</h5>
    <table id="container-cuotas">
        <thead>
            <tr>
                <th class="text-left">Cuotas</th>
                <th class="text-left">Pago</th>
                <th class="text-left">Interés</th>
                <th class="text-left">Capital</th>
                <th class="text-left">Saldo pendiente</th>
                <th class="text-left">Vencimiento</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $item = 1;
            $date = date('Y-m-d');
            foreach ($detalles as $detalle) {
                $estado = '<span class="text-danger">PENDIENTE</span>';
                if ($date > $detalle['fecha_venc'] && $detalle['estado'] == 1) {
                    $class = 'bg-danger';
                } else if ($date == $detalle['fecha_venc'] && $detalle['estado'] == 1) {
                    $class = 'bg-warning';
                } else {
                    $class = '';
                    if ($detalle['estado'] == 1) {
                        $estado = '<span class="text-danger">PENDIENTE</span>';
                    } else {
                        $estado = '<span class="text-success">PAGADO</span>';
                    }
                }

                $desglose = $detalle['desglose'] ?? [];
                $pagoProgramado = (float) ($desglose['pago'] ?? $detalle['importe_cuota'] ?? 0);
                $interesCuota = (float) ($desglose['interes'] ?? 0);
                $capitalCuota = (float) ($desglose['capital'] ?? ($pagoProgramado - $interesCuota));
                $saldoPendiente = (float) ($desglose['saldo'] ?? 0);
            ?>
                <tr class="<?php echo $class; ?>">
                    <td><?php echo $item; ?> (Cuota <?php echo $detalle['cuota']; ?>)</td>
                    <td><?php echo format_currency($pagoProgramado, $prestamo['moneda'] ?? 'NIO'); ?></td>
                    <td><?php echo format_currency($interesCuota, $prestamo['moneda'] ?? 'NIO'); ?></td>
                    <td><?php echo format_currency($capitalCuota, $prestamo['moneda'] ?? 'NIO'); ?></td>
                    <td><?php echo format_currency($saldoPendiente, $prestamo['moneda'] ?? 'NIO'); ?></td>
                    <td><?php echo fechaPerzo($detalle['fecha_venc']); ?></td>
                    <td><?php echo $estado; ?></td>
                </tr>
            <?php $item++;
            } ?>
            <tr>
                <td colspan="2" class="text-right"><strong>Total programado:</strong> <?php echo format_currency($total_programado ?? 0, $prestamo['moneda'] ?? 'NIO'); ?></td>
                <td><strong>Interés:</strong> <?php echo format_currency($total_interes_programado ?? 0, $prestamo['moneda'] ?? 'NIO'); ?></td>
                <td><strong>Capital:</strong> <?php echo format_currency(($prestamo['importe'] ?? 0), $prestamo['moneda'] ?? 'NIO'); ?></td>
                <td><strong>Saldo:</strong> <?php echo format_currency($total_restante ?? 0, $prestamo['moneda'] ?? 'NIO'); ?></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    <div class="mensaje">
        <?php
        echo $empresa['mensaje'];
        if ($prestamo['estado'] == 0) {
            echo '<h1>CONTRATO FINALIZADO</h1>';
        }
        ?>
    </div>
</body>

</html>
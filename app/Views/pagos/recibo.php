<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo</title>
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
                <p>RUC: <?php echo $empresa['identidad']; ?></p>
                <p>Teléfono: <?php echo $empresa['telefono']; ?></p>
                <p>Dirección: <?php echo $empresa['direccion']; ?></p>
            </td>
            <td class="info-fecha">
                <div class="container-fecha">
                    <span class="contrato">Recibo de pago</span>
                    <p>N° <strong><?php echo $pago['id']; ?></strong></p>
                    <p>Fecha: <?php
                                $dato = $pago['fecha_pago'];
                                $fecha = date('Y-m-d', strtotime($dato));
                                echo fechaPerzo($fecha);
                                ?></p>
                    <p>Hora: <?php echo date('h:i A', strtotime($pago['fecha_pago'])); ?></p>
                </div>
            </td>
        </tr>
    </table>
    <h5 class="title">Datos del cliente</h5>
    <div class="container-cliente">
        <table id="container-info">
            <tr>
                <td>
                    <strong><?php echo $pago['identidad']; ?></strong>
                    <p><?php echo $pago['num_identidad']; ?></p>
                </td>
                <td>
                    <strong>Nombre</strong>
                    <p><?php echo $pago['cliente'] . ' ' . $pago['apellido']; ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Teléfono</strong>
                    <p><?php echo $pago['telefono']; ?></p>
                </td>
                <td>
                    <strong>Dirección</strong>
                    <p><?php echo $pago['direccion']; ?></p>
                </td>
            </tr>
        </table>
    </div>
    <h5 class="title">Información del pago</h5>
    <table id="container-cuotas">
        <thead>
            <tr>
                <th class="text-left">Prestamo</th>
                <th class="text-left">Cuota</th>
                <th class="text-left">Monto</th>
                <th class="text-left">Método</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $pago['id_prestamo']; ?></td>
                <td>Cuota <?php echo $pago['cuota']; ?></td>
                <td><?php echo format_currency($pago['monto'], $pago['moneda'] ?? 'NIO'); ?></td>
                <td><?php echo $pago['metodo']; ?></td>
            </tr>
        </tbody>
    </table>
    <div class="mensaje">
        <?php echo $empresa['mensaje']; ?>
    </div>
</body>

</html>

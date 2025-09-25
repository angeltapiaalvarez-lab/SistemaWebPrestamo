<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Detalle del prestamo
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header">
        <h4>Detalle del préstamo</h4>
    </div>
    <div class="card-body">
        <?php
        $respuesta = session()->getFlashdata('respuesta');
        $id_pago   = session()->getFlashdata('id_pago');
        ?>
    <?php if (!empty($respuesta)) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: '<?= $respuesta['type']; ?>',
                title: <?= json_encode($respuesta['title'] ?? ($respuesta['type'] === 'success' ? '¡Pago realizado!' : 'Aviso')); ?>,
                text: '<?= esc($respuesta['msg'], 'js'); ?>',
                <?php if (!empty($id_pago)) : ?>
                showCancelButton: true,
                confirmButtonText: 'Imprimir Recibo',
                cancelButtonText: 'Cerrar',
                <?php else : ?>
                confirmButtonText: 'Aceptar',
                <?php endif; ?>
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                <?php if (!empty($id_pago)) : ?>
                if (result.isConfirmed) {
                    window.open('<?= base_url('pagos/' . $id_pago . '/recibo'); ?>', '_blank');
                }
                <?php endif; ?>
            });
        });
    </script>
    <?php endif; ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><i class="fas fa-id-card"></i> N° identidad: <?php echo $prestamo['num_identidad']; ?></li>
                            <li class="list-group-item"><i class="fas fa-list"></i> Cliente: <?php echo $prestamo['cliente'] . ' ' . $prestamo['apellido']; ?></li>
                            <li class="list-group-item"><i class="fas fa-calendar"></i> Fecha:
                                <?php
                                $dato = $prestamo['fecha'];
                                $fecha = date('Y-m-d', strtotime($dato));
                                $hora = date('h:i A', strtotime($dato));
                                echo fechaPerzo($fecha) . ' ' . $hora;
                                ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><i class="fas fa-tag"></i> Cuotas: <?php echo $prestamo['cuotas']; ?></li>
                            <li class="list-group-item"><i class="fas fa-calendar"></i> Modalidad: <?php echo $prestamo['modalidad']; ?></li>
                            <li class="list-group-item"><i class="fas fa-user"></i> Atendido: <?php echo $prestamo['usuario'] . ' ' . $prestamo['user_apellido']; ?></li>
                            <li class="list-group-item"><i class="fas fa-money-bill"></i> Moneda: <?php echo $moneda_nombre ?? currency_name($prestamo['moneda'] ?? 'NIO'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <a href="<?php echo base_url('prestamos/' . $prestamo['id'] . '/reporte'); ?>" target="_blank" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Estado de cuenta</a>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <span class="text-muted text-uppercase small">Total del préstamo</span>
                                <p class="h5 mb-0"><?php echo format_currency($total_programado ?? 0, $prestamo['moneda'] ?? 'NIO'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <span class="text-muted text-uppercase small">Interés total</span>
                                <p class="h5 mb-0"><?php echo format_currency($total_interes_programado ?? 0, $prestamo['moneda'] ?? 'NIO'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <span class="text-muted text-uppercase small">Pagado</span>
                                <p class="h5 mb-0"><?php echo format_currency($total_pagado ?? 0, $prestamo['moneda'] ?? 'NIO'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <span class="text-muted text-uppercase small">Saldo pendiente</span>
                                <p class="h5 mb-0"><?php echo format_currency($total_restante ?? 0, $prestamo['moneda'] ?? 'NIO'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Pago</th>
                                <th scope="col">Interés</th>
                                <th scope="col">Capital</th>
                                <th scope="col">Saldo pendiente</th>
                                <th scope="col">Vencimiento</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Abono</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $date = date('Y-m-d');
                            foreach ($detalles as $detalle) {
                                $estado = '<span class="badge badge-danger">PENDIENTE</span>';
                                if ($date > $detalle['fecha_venc'] && $detalle['estado'] == 1) {
                                    $class = 'bg-danger';
                                } else if ($date == $detalle['fecha_venc'] && $detalle['estado'] == 1) {
                                    $class = 'bg-warning';
                                } else {
                                    $class = '';
                                    if ($detalle['estado'] == 1) {
                                        $estado = '<span class="badge badge-danger">PENDIENTE</span>';
                                    } else {
                                        $estado = '<span class="badge badge-success">PAGADO</span>';
                                    }
                                }

                                $desglose = $detalle['desglose'] ?? [];
                                $pagoProgramado = (float) ($desglose['pago'] ?? $detalle['importe_cuota'] ?? 0);
                                $interesCuota = (float) ($desglose['interes'] ?? 0);
                                $capitalCuota = (float) ($desglose['capital'] ?? ($pagoProgramado - $interesCuota));
                                $saldoPendiente = (float) ($desglose['saldo'] ?? 0);
                            ?>
                                <tr class="<?php echo $class; ?>">
                                    <td>
                                        <span class="badge bg-secondary">Cuota <?php echo $detalle['cuota']; ?></span>
                                    </td>
                                    <td><?php echo format_currency($pagoProgramado, $prestamo['moneda'] ?? 'NIO'); ?></td>
                                    <td><?php echo format_currency($interesCuota, $prestamo['moneda'] ?? 'NIO'); ?></td>
                                    <td><?php echo format_currency($capitalCuota, $prestamo['moneda'] ?? 'NIO'); ?></td>
                                    <td><?php echo format_currency($saldoPendiente, $prestamo['moneda'] ?? 'NIO'); ?></td>
                                    <td><?php echo fechaPerzo($detalle['fecha_venc']); ?></td>
                                    <td><?php echo $estado; ?></td>
                                    <td>
                                        <?php if ($detalle['estado'] == 1) { ?>
                                            <form action="<?php echo base_url('prestamos/' . $detalle['id']); ?>" method="post" class="formEstado">
                                                <input type="hidden" name="_method" value="PUT">
                                                <?php echo csrf_field(); ?>
                                                <div class="input-group">
                                                    <span class="input-group-text"><?php echo $moneda_simbolo ?? currency_symbol($prestamo['moneda'] ?? 'NIO'); ?></span>
                                                    <input type="number" step="0.01" name="monto" value="<?php echo $detalle['importe_cuota']; ?>" class="form-control" style="max-width:120px" readonly>
                                                    <select name="metodo" class="form-select" style="max-width:150px">
                                                        <option value="EFECTIVO">EFECTIVO</option>
                                                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary">Pagar</button>
                                                </div>
                                            </form>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('modal'); ?>
<div class="modal fade" id="modalMensaje" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModal">Mensaje</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('prestamos/enviarCorreo'); ?>" method="post">
                <div class="modal-body">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id_prestamo" value="<?php echo $prestamo['id']; ?>">
                    <input type="hidden" name="correo" value="<?php echo $prestamo['correo']; ?>">
                    <?php if (isset($validator)) { ?>
                        <span class="text-danger"><?php echo $validator->getError('correo'); ?></span>
                    <?php } ?>
                    <div class="mb-3">
                        <label for="" class="form-label">Mensaje</label>
                        <textarea class="form-control" name="mensaje" id="mensaje" rows="3"></textarea>
                        <?php if (isset($validator)) { ?>
                            <span class="text-danger"><?php echo $validator->getError('mensaje'); ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary m-t-15 waves-effect">Enviar</button>
                </div>
            </form>
</div>
</div>
</div>
<?= $this->endSection('modal'); ?>

<?= $this->section('js'); ?>
<script src="<?php echo base_url('assets/js/pages/prestamo-detail.js'); ?>"></script>
<script>
    <?php if (isset($validator)) { ?>
        myModal.show();
    <?php } ?>
</script>
<?= $this->endSection('js'); ?>
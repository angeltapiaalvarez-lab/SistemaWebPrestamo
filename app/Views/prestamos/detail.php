<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Detalle del prestamo
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header">
        <h4>Detalle del prestamo</h4>
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
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="mb-3">
                    <a href="<?php echo base_url('prestamos/' . $prestamo['id'] . '/reporte'); ?>" target="_blank" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Estado de Cuenta</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Cuotas</th>
                                <th scope="col">Vencimiento</th>
                                <th scope="col">Importe x cuota</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Abono</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0;
                            $date = date('Y-m-d');
                            foreach ($detalles as $detalle) {
                                $total += $detalle['importe_cuota'];
                                $estado = '<span class="badge badge-danger">PENDIENTE</span>';
                                if ($date > $detalle['fecha_venc'] && $detalle['estado'] == 1) {
                                    $class = 'bg-danger';
                                } else if ($date == $detalle['fecha_venc'] && $detalle['estado'] == 1) {
                                    $class = 'bg-warning';
                                } else {
                                    $class = '';
                                    if ($detalle['estado'] == 1) {
                                        $estado = '<span class="badge badge-danger">PENDIENTE</span>';
                                    } elseif ($detalle['estado'] == 2) {
                                        $estado = '<span class="badge badge-warning text-dark">PARCIAL</span>';
                                    } else {
                                        $estado = '<span class="badge badge-success">PAGADO</span>';
                                    }
                                }
                            ?>
                                <tr class="<?php echo $class; ?>">
                                    <td scope="row">
                                        <button type="button" class="btn btn-outline-secondary">
                                            Cuota <span class="badge badge-transparent text-dark"><?php echo $detalle['cuota']; ?></span>
                                        </button>
                                    </td>
                                    <td scope="row"><?php echo fechaPerzo($detalle['fecha_venc']); ?></td>
                                    <td scope="row">
                                        <?php $pendiente = $detalle['importe_cuota'] - $detalle['pagado']; ?>
                                        <span class="badge badge-success text-dark"><?php echo number_format($pendiente, 2); ?></span>
                                    </td>
                                    <td scope="row"><?php echo $estado; ?></td>
                                    <td>
                                        <?php if ($detalle['estado'] == 1) { ?>
                                            <button type="button" class="btn btn-success btnPagoCompleto" data-id="<?php echo $detalle['id']; ?>" data-pendiente="<?php echo number_format($pendiente, 2, '.', ''); ?>">Pagar</button>
                                            <button type="button" class="btn btn-primary btnPagoParcial" data-id="<?php echo $detalle['id']; ?>" data-total="<?php echo number_format($pendiente_total, 2, '.', ''); ?>">Pago parcial</button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="3" class="text-end">
                                    <h3>Total <?php echo number_format($total, 2); ?></h3>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>



            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('modal'); ?>
<div class="modal fade" id="modalPago" tabindex="-1" role="dialog" aria-labelledby="pagoModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagoModal">Pago parcial</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPago" action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="_method" value="PUT">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto</label>
                        <input type="number" step="0.01" name="monto" id="monto" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="metodo" class="form-label">Método</label>
                        <select name="metodo" id="metodo" class="form-select">
                            <option value="EFECTIVO">EFECTIVO</option>
                            <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modo" class="form-label">Modo</label>
                        <select name="modo" id="modo" class="form-select">
                            <option value="ADMIN">ADMIN</option>
                            <option value="TO_PRINCIPAL">TO PRINCIPAL</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Hacer pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalMensaje" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModal">Mensaje</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Historial de préstamos
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header">
        <h4>Historial de préstamos</h4>
    </div>
    <div class="card-body">
        <?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
            <div class="alert alert-<?php echo session()->getFlashdata('respuesta')['type']; ?>">
                <?php echo session()->getFlashdata('respuesta')['msg']; ?>
            </div>
        <?php } ?>
        <form class="row g-3" method="get" action="<?= base_url('reportes/historial'); ?>">
            <div class="col-md-4">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin; ?>" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Generar</button>
            </div>
        </form>
        <?php if ($mensaje != '') { ?>
            <div class="alert alert-warning mt-3"><?= $mensaje; ?></div>
        <?php } ?>
        <?php if (!empty($prestamos)) { ?>
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Moneda</th>
                            <th>Importe</th>
                            <th>Modalidad</th>
                            <th>Tasa de interés</th>
                            <th>F. vencimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($prestamos as $p) { ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $p['cliente']; ?></td>
                                <td><?= $p['moneda_label'] ?? (currency_name($p['moneda'] ?? 'NIO') . ' (' . ($p['moneda'] ?? 'NIO') . ')'); ?></td>
                                <td><?= $p['importe_formateado'] ?? format_currency($p['importe'], $p['moneda'] ?? 'NIO'); ?></td>
                                <td><?= $p['modalidad']; ?></td>
                                <td><?= $p['tasa_interes']; ?></td>
                                <td><?= fechaPerzo($p['fecha_venc']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <?php if (!empty($totales_moneda)) : ?>
                            <?php foreach ($totales_moneda as $codigo => $montoTotal) : ?>
                                <tr>
                                    <th colspan="3" class="text-end">Total <?= currency_name($codigo); ?> (<?= $codigo; ?>)</th>
                                    <th><?= format_currency($montoTotal, $codigo); ?></th>
                                    <th colspan="3"></th>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
        <?php } ?>
    </div>
</div>
<?= $this->endSection('content'); ?>

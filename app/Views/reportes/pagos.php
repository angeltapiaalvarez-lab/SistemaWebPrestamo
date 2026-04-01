<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Generar Reporte de Pagos
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Generador de Reportes: Pagos</h4>
        <div>
            <a href="<?= base_url('reportesPdfPagos?fecha_inicio=' . $fecha_inicio . '&fecha_fin=' . $fecha_fin . '&id_cliente=' . $id_cliente); ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Exportar PDF</a>
            <a href="<?= base_url('reportesExcelPagos?fecha_inicio=' . $fecha_inicio . '&fecha_fin=' . $fecha_fin . '&id_cliente=' . $id_cliente); ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Excel</a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
            <div class="alert alert-<?= session()->getFlashdata('respuesta')['type']; ?>">
                <?= session()->getFlashdata('respuesta')['msg']; ?>
            </div>
        <?php } ?>
        <form class="row g-3" method="get" action="<?= base_url('reportes/pagos'); ?>">
            <div class="col-md-3">
                <label class="form-label">Cliente (Opcional)</label>
                <select name="id_cliente" class="form-control">
                    <option value="">-- Todos los clientes --</option>
                    <?php if(isset($clientes)) { foreach ($clientes as $c) { ?>
                        <option value="<?= $c['id']; ?>" <?= (isset($id_cliente) && $id_cliente == $c['id']) ? 'selected' : ''; ?>>
                            <?= $c['nombre'] . ' ' . $c['apellido']; ?>
                        </option>
                    <?php } } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio; ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin; ?>" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Buscar y Filtrar</button>
            </div>
        </form>
        <?php if ($mensaje != '') { ?>
            <div class="alert alert-warning mt-3"><?= $mensaje; ?></div>
        <?php } ?>
        <?php if (!empty($pagos)) { ?>
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Recibo #</th>
                            <th>Cliente</th>
                            <th>Préstamo M.</th>
                            <th>Monto Abonado</th>
                            <th>Método Exp.</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagos as $p) { ?>
                            <tr>
                                <td><?= str_pad($p['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?= $p['cliente_nombre'] . ' ' . $p['cliente_apellido']; ?></td>
                                <td>#<?= $p['prestamo']; ?> (<?= $p['moneda'] ?? 'NIO' ?>)</td>
                                <td><strong class="text-success"><?= $p['monto_formateado'] ?? format_currency($p['monto'], $p['moneda'] ?? 'NIO'); ?></strong></td>
                                <td><span class="badge bg-secondary"><?= $p['metodo']; ?></span></td>
                                <td><?= date('Y-m-d g:i A', strtotime($p['fecha_pago'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <?php if (!empty($totales_moneda)) : ?>
                            <?php foreach ($totales_moneda as $codigo => $montoTotal) : ?>
                                <tr class="table-active">
                                    <th colspan="3" class="text-end text-primary">Total Recaudado en <?= currency_name($codigo); ?> (<?= $codigo; ?>)</th>
                                    <th><h5 class="text-primary mb-0"><?= format_currency($montoTotal, $codigo); ?></h5></th>
                                    <th colspan="2"></th>
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

<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Editar monto
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<?php $monedaSeleccionada = $caja['moneda'] ?? 'NIO'; ?>
<div class="card">
    <div class="card-header">
        <h4>Editar monto</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('cajas/' . $caja['id']); ?>" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="id_caja" value="<?= $caja['id']; ?>">
            <input type="hidden" name="moneda" value="<?= esc($monedaSeleccionada); ?>">
            <div class="mb-3">
                <label class="form-label">Moneda</label>
                <input type="text" class="form-control" value="<?= currency_name($monedaSeleccionada) . ' (' . $monedaSeleccionada . ')'; ?>" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Monto</label>
                <div class="input-group">
                    <span class="input-group-text"><?= esc(currency_symbol($monedaSeleccionada)); ?></span>
                    <input type="text" name="monto" class="form-control" value="<?= set_value('monto', $caja['monto_inicial']); ?>" placeholder="0.00">
                </div>
                <?php if (!empty($errors['monto_inicial'])) : ?>
                    <span class="text-danger"><?= esc($errors['monto_inicial']); ?></span>
                <?php endif; ?>
            </div>
            <div class="text-end mt-4">
                <a href="<?= base_url('cajas'); ?>" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Monto inicial
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<?php $monedaSeleccionada = old('moneda', $monedaSeleccionada ?? array_key_first($monedas ?? [])); ?>
<div class="card">
    <div class="card-header">
        <h4>Monto inicial</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('cajas'); ?>" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id_caja" value="0">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Moneda</label>
                    <select name="moneda" id="moneda" class="form-select">
                        <?php foreach ($monedas as $codigo => $nombre) : ?>
                            <option value="<?= $codigo; ?>" <?= set_select('moneda', $codigo, $monedaSeleccionada === $codigo); ?>>
                                <?= esc($nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['moneda'])) : ?>
                        <span class="text-danger"><?= esc($errors['moneda']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Monto</label>
                    <div class="input-group">
                        <span class="input-group-text" id="symbolMoneda" data-symbol-target><?= esc(currency_symbol($monedaSeleccionada)); ?></span>
                        <input type="text" name="monto" class="form-control" value="<?= set_value('monto'); ?>" placeholder="0.00">
                    </div>
                    <?php if (!empty($errors['monto_inicial'])) : ?>
                        <span class="text-danger"><?= esc($errors['monto_inicial']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="<?= base_url('cajas'); ?>" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('js'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monedaSelect = document.querySelector('#moneda');
        const symbolTargets = document.querySelectorAll('[data-symbol-target]');
        const obtenerSimbolo = (moneda) => moneda === 'USD' ? '$' : 'C$';

        monedaSelect.addEventListener('change', function (e) {
            const simbolo = obtenerSimbolo(e.target.value);
            symbolTargets.forEach((element) => {
                element.textContent = simbolo;
            });
        });
    });
</script>
<?= $this->endSection('js'); ?>

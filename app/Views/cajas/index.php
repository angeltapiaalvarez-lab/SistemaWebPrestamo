<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Gestion cajas
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<?php $monedaSeleccionada = $monedaSeleccionada ?? array_key_first($monedas ?? []); ?>
<div class="d-flex flex-wrap align-items-end gap-2 mb-3">
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($monedas as $codigo => $nombre) :
            $cajaMoneda = $cajas[$codigo] ?? null;
        ?>
            <?php if ($cajaMoneda) : ?>
                <a href="<?= base_url('cajas/' . $cajaMoneda['id'] . '/edit'); ?>" class="btn btn-primary">
                    Editar <?= esc($nombre); ?>
                </a>
            <?php else : ?>
                <a href="<?= base_url('cajas/new?moneda=' . $codigo); ?>" class="btn btn-success">
                    Apertura <?= esc($nombre); ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="ms-auto" style="min-width: 200px;">
        <label for="monedaSelect" class="form-label mb-1">Moneda</label>
        <select id="monedaSelect" class="form-select">
            <?php foreach ($monedas as $codigo => $nombre) : ?>
                <option value="<?= $codigo; ?>" <?= $codigo === $monedaSeleccionada ? 'selected' : ''; ?>>
                    <?= esc($nombre); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<?php if (!empty(session()->getFlashdata('respuesta'))) : ?>
    <div class="alert alert-<?= session()->getFlashdata('respuesta')['type']; ?>">
        <?= esc(session()->getFlashdata('respuesta')['msg']); ?>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <?php foreach ($monedas as $codigo => $nombre) :
        $datos = $resumen[$codigo] ?? [
            'decimales' => [
                'inicial' => number_format(0, 2),
                'egreso' => number_format(0, 2),
                'ingreso' => number_format(0, 2),
                'saldo' => number_format(0, 2),
            ],
            'simbolo' => currency_symbol($codigo),
        ];
    ?>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="mb-0"><?= esc($nombre); ?></h4>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Saldo actual:</strong> <?= esc($datos['simbolo']); ?> <?= esc($datos['decimales']['saldo']); ?></p>
                    <p class="mb-1"><strong>Monto inicial:</strong> <?= esc($datos['simbolo']); ?> <?= esc($datos['decimales']['inicial']); ?></p>
                    <p class="mb-0"><strong>Egresos:</strong> <?= esc($datos['simbolo']); ?> <?= esc($datos['decimales']['egreso']); ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Monto inicial y Egresos</h4>
                <span class="badge bg-secondary" id="badgeMonedaInicial"></span>
            </div>
            <div class="card-body">
                <canvas id="inicialEgreso"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ingresos y Saldo</h4>
                <span class="badge bg-secondary" id="badgeMonedaSaldo"></span>
            </div>
            <div class="card-body">
                <canvas id="ingresoSaldo"></canvas>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('js'); ?>
<script src="<?= base_url('assets/bundles/chartjs/chart.min.js'); ?>"></script>
<script>
    const resumenCajas = <?= json_encode($resumen ?? []); ?>;
</script>
<script src="<?= base_url('assets/js/pages/cajas.js'); ?>"></script>
<?= $this->endSection('js'); ?>

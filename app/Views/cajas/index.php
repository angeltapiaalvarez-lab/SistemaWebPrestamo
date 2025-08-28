<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Gestion cajas
<?= $this->endSection('title'); ?>

<?= $this->section('content');

if (empty($caja)) { ?>
    <a href="<?php echo base_url('cajas/new'); ?>" class="btn btn-primary mb-2">Apertura</a>
<?php } else { ?>
    <a href="<?php echo base_url('cajas/' . $caja['id'] . '/edit'); ?>" class="btn btn-primary mb-2">Editar monto</a>
<?php } ?>

<?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
    <div class="alert alert-<?php echo session()->getFlashdata('respuesta')['type']; ?>">
        <?php echo session()->getFlashdata('respuesta')['msg']; ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Monto inicial y egresos</h4>
            </div>
            <div class="card-body">
                <canvas id="inicialEgreso"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Ingresos y saldo</h4>
            </div>
            <div class="card-body">
                <canvas id="ingresoSaldo"></canvas>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('js'); ?>
<script src="<?php echo base_url('assets/bundles/chartjs/chart.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/pages/cajas.js'); ?>"></script>
<?= $this->endSection('js'); ?>
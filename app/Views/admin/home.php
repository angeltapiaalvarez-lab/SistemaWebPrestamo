<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Datos de la empresa
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-body">
        <?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
            <div class="alert alert-<?php echo session()->getFlashdata('respuesta')['type']; ?>">
                <?php echo session()->getFlashdata('respuesta')['msg']; ?>
            </div>
        <?php } ?>
        <div class="row ">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Usuarios</h5>
                                        <h2 class="mb-3 font-18"><?php echo $usuarios; ?></h2>
                                        <a class="mb-0" href="<?php echo base_url('usuarios'); ?>">VER DETALLE</a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="<?php echo base_url('assets/img/admin/usuario.png'); ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Clientes</h5>
                                        <h2 class="mb-3 font-18"><?php echo $clientes; ?></h2>
                                        <a class="mb-0" href="<?php echo base_url('clientes'); ?>">VER DETALLE</a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="<?php echo base_url('assets/img/admin/cliente.png'); ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Prestamos</h5>
                                        <h2 class="mb-3 font-18"><?php echo $prestamos; ?></h2>
                                        <a class="mb-0" href="<?php echo base_url('prestamos/historial'); ?>">VER DETALLE</a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="<?php echo base_url('assets/img/admin/prestamo.png'); ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Saldo</h5>
                                        <?php if (!empty($cajas)) : ?>
                                            <ul class="list-unstyled mb-2">
                                                <?php foreach ($cajas as $codigo => $info) : ?>
                                                    <li class="mb-2">
                                                        <span class="d-block font-18 font-weight-bold">
                                                            <?= esc($info['simbolo'] ?? currency_symbol($codigo)); ?>
                                                            <?= esc($info['decimales']['saldo'] ?? number_format(0, 2)); ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <?= esc($info['nombre'] ?? currency_name($codigo)); ?>
                                                            (<?= esc($codigo); ?>)
                                                        </small>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else : ?>
                                            <h2 class="mb-3 font-18"><?= esc(currency_symbol('NIO')); ?> 0.00</h2>
                                        <?php endif; ?>
                                        <a class="mb-0" href="<?php echo base_url('cajas'); ?>">VER DETALLE</a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="<?php echo base_url('assets/img/admin/saldo.png'); ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Flujo Financiero Anual</h4>
                        <div class="d-flex">
                            <select class="form-select" id="year">
                                <?php $anio = date('Y');
                                for ($i = 2020; $i <= $anio; $i++) { ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($anio == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 text-center">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="px-3 py-2 rounded bg-light border border-info shadow-sm">
                                    <h6 class="text-info font-weight-bold mb-1"><i class="fas fa-hand-holding-usd me-1"></i> Capital Colocado (Préstamos)</h6>
                                    <h3 class="mb-0 text-dark" id="kpi-prestamos">C$ 0.00</h3>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="px-3 py-2 rounded bg-light border border-success shadow-sm">
                                    <h6 class="text-success font-weight-bold mb-1"><i class="fas fa-cash-register me-1"></i> Capital Recuperado (Abonos)</h6>
                                    <h3 class="mb-0 text-dark" id="kpi-ingresos">C$ 0.00</h3>
                                </div>
                            </div>
                        </div>
                        <div id="prestamos" class="chartsh"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('js'); ?>
<script src="<?php echo base_url('assets/bundles/apexcharts/apexcharts.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/pages/home.js?v=' . time()); ?>"></script>
<?= $this->endSection('js'); ?>
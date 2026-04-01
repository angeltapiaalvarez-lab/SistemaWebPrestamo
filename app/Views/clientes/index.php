<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Gestion clientes
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Gestion clientes</h4>
        <?php if (verificar('nuevo cliente', $_SESSION['permisos'])) { ?>
            <a href="<?php echo base_url('clientes/new'); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Agregar nuevo cliente</a>
        <?php } ?>
    </div>
    <div class="card-body">
        <?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
            <div class="alert alert-<?php echo session()->getFlashdata('respuesta')['type']; ?>">
                <?php echo session()->getFlashdata('respuesta')['msg']; ?>
            </div>
        <?php } ?>
        <div class="table-responsive">
            <table class="table table-striped nowrap" id="tblClientes" style="width:100%">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th class="text-center">
                            #
                        </th>
                        <th>Identidad</th>
                        <th>N° identidad</th>
                        <th>Nombres</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Direccion</th>
                        <th>Prestamo activo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('js'); ?>
<script src="<?php echo base_url('assets/js/pages/clientes.js'); ?>"></script>
<?= $this->endSection('js'); ?>
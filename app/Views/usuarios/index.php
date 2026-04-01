<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Gestion usuarios
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Gestion usuarios</h4>
        <?php if (verificar('nuevo usuario', $_SESSION['permisos'])) { ?>
            <a href="<?php echo base_url('usuarios/new'); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Agregar nuevo usuario</a>
        <?php } ?>
    </div>
    <div class="card-body">
        <?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
            <div class="alert alert-<?php echo session()->getFlashdata('respuesta')['type']; ?>">
                <?php echo session()->getFlashdata('respuesta')['msg']; ?>
            </div>
        <?php } ?>
        <div class="table-responsive">
            <table class="table table-striped nowrap" id="tblUsers" style="width:100%">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th class="text-center">
                            #
                        </th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Direccion</th>
                        <th>Rol</th>
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
<script src="<?php echo base_url('assets/js/pages/usuarios.js?v=' . time()); ?>"></script>
<?= $this->endSection('js'); ?>
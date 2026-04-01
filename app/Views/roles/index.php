<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Gestion roles
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Gestion roles</h4>
        <?php if (verificar('nuevo rol', $_SESSION['permisos'])) { ?>
            <a href="<?php echo base_url('roles/new'); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Agregar nuevo rol</a>
        <?php } ?>
    </div>
    <div class="card-body">
        <?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
            <div class="alert alert-<?php echo session()->getFlashdata('respuesta')['type']; ?>">
                <?php echo session()->getFlashdata('respuesta')['msg']; ?>
            </div>
        <?php } ?>
        <div class="table-responsive">
            <table class="table table-striped nowrap" id="tblRoles" style="width:100%">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th class="text-center">
                            #
                        </th>
                        <th>Nombre</th>
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
<script src="<?php echo base_url('assets/js/pages/roles.js'); ?>"></script>
<?= $this->endSection('js'); ?>
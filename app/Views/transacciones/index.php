<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Historial de transacciones
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header">
        <h4>Historial de transacciones</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped nowrap" id="tblTransacciones" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Accion</th>
                        <th>Descripcion</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
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
<script src="<?= base_url('assets/js/pages/transacciones.js'); ?>"></script>
<?= $this->endSection('js'); ?>


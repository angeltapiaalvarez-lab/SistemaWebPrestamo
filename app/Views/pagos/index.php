<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Historial de pagos
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header">
        <h4>Historial de pagos</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped nowrap" id="tblPagos" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Prestamo</th>
                        <th>Cuota</th>
                        <th>Monto</th>
                        <th>Metodo</th>
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
<script src="<?= base_url('assets/js/pages/pagos.js'); ?>"></script>
<?= $this->endSection('js'); ?>

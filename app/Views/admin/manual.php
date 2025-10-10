<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Manual de usuario
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Manual de usuario</h4>
            </div>
            <div class="card-body">
                <?php if (! empty($manualExists) && $manualExists && ! empty($manualPdfUrl)) : ?>
                    <div class="ratio ratio-16x9">
                        <iframe
                            src="<?= esc($manualPdfUrl); ?>"
                            title="Manual de usuario"
                            frameborder="0"
                            style="border: 0;"
                        ></iframe>
                    </div>
                    <p class="text-muted mt-3">
                        El archivo se carga desde <code>public/<?= esc($manualRelativePath); ?></code>. Reemplázalo por la versión más reciente cuando sea necesario.
                    </p>
                    <p class="mb-0">
                        Si necesitas abrir el documento en una pestaña nueva, haz clic en
                        <a href="<?= esc($manualPdfUrl); ?>" target="_blank" rel="noopener noreferrer">este enlace directo al manual</a>.
                    </p>
                <?php else : ?>
                    <div class="alert alert-warning mb-0" role="alert">
                        No se encontró el archivo del manual. Coloca el documento en <code>public/<?= esc($manualRelativePath ?? 'manual/manual.pdf'); ?></code> para habilitar la visualización dentro del sistema.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

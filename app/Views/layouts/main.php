<?= $this->include('layouts/head.php'); ?>
<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?= $this->include('layouts/menu.php'); ?>

      <!-- Main Content -->
      <?php
      $currentUri = service('uri');
      $manualRelativePath = 'manual/manual.pdf';
      $manualFullPath = FCPATH . $manualRelativePath;
      $manualExists = is_file($manualFullPath);
      $manualPdfUrl = $manualExists ? base_url($manualRelativePath) : null;
      ?>
      <div class="main-content">
        <section class="section">
            <!-- contenido -->
            <?= $this->renderSection('content'); ?>
        </section>

        <?= $this->renderSection('modal'); ?>

        <?= $this->include('layouts/paint.php'); ?>
      </div>
      <?php if ($currentUri->getSegment(1) !== 'manual') : ?>
        <a
          href="<?= base_url('manual'); ?>"
          class="btn btn-primary rounded-circle manual-help-btn"
          aria-label="Abrir manual de ayuda"
          <?php if ($manualExists && $manualPdfUrl) : ?>
            data-manual-url="<?= esc($manualPdfUrl); ?>"
          <?php endif; ?>
        >
          <i class="fa-solid fa-circle-question"></i>
        </a>
      <?php endif; ?>
      <?php if ($manualExists && $manualPdfUrl) : ?>
        <div class="modal fade" id="manualModal" tabindex="-1" role="dialog" aria-labelledby="manualModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content manual-modal">
              <div class="modal-header">
                <h5 class="modal-title" id="manualModalLabel">Manual de usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="ratio ratio-16x9 manual-modal__frame">
                  <iframe src="" title="Manual de usuario" frameborder="0" allowfullscreen></iframe>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <?= $this->include('layouts/footer.php'); ?>
    </div>
  </div>
  <?= $this->include('layouts/scripts.php'); ?>
</body>

</html>
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
      $manualButtonHref = base_url('manual');
      $manualButtonAttributes = '';

      if ($manualExists && $manualPdfUrl) {
        $manualButtonHref = $manualPdfUrl;
        $manualButtonAttributes = 'target="_blank" rel="noopener noreferrer"';
      }
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
          href="<?= esc($manualButtonHref); ?>"
          class="btn btn-primary rounded-circle manual-help-btn"
          aria-label="Abrir manual de ayuda"
          title="Manual del sistema"
          <?= $manualButtonAttributes; ?>
        >
          <i class="fa-solid fa-circle-question"></i>
        </a>
      <?php endif; ?>
      <?= $this->include('layouts/footer.php'); ?>
    </div>
  </div>
  <?= $this->include('layouts/scripts.php'); ?>
</body>

</html>
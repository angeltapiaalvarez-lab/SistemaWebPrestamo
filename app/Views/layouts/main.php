<?= $this->include('layouts/head.php'); ?>
<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?= $this->include('layouts/menu.php'); ?>
      
      <!-- Main Content -->
      <?php $currentUri = service('uri'); ?>
      <div class="main-content">
        <section class="section">
            <!-- contenido -->
            <?= $this->renderSection('content'); ?>
        </section>

        <?= $this->renderSection('modal'); ?>

        <?= $this->include('layouts/paint.php'); ?>
      </div>
      <?php if ($currentUri->getSegment(1) !== 'manual') : ?>
        <a href="<?= base_url('manual'); ?>" class="btn btn-primary rounded-circle manual-help-btn" aria-label="Abrir manual de ayuda">
          <i class="fa-solid fa-circle-question"></i>
        </a>
      <?php endif; ?>
      <?= $this->include('layouts/footer.php'); ?>
    </div>
  </div>
  <?= $this->include('layouts/scripts.php'); ?>
</body>

</html>
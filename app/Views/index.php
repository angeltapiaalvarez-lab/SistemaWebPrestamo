<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Acceso al sistema</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/app.min.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/components.css">
  <!-- Custom style CSS -->
  <link rel='shortcut icon' type='image/x-icon' href='<?= base_url(); ?>assets/img/favicon.ico' />
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="card card-primary">
              <div class="card-header">
                <h4>Login</h4>
              </div>
              <div class="card-body">
                <?php if (!empty(session()->getFlashdata('respuesta'))) { ?>
                  <div class="alert alert-<?php echo session()->getFlashdata('respuesta')['type']; ?>">
                    <?php echo session()->getFlashdata('respuesta')['msg']; ?>
                  </div>
                <?php } ?>
                <form method="POST" action="<?= base_url('login'); ?>" autocomplete="off">
                  <?= csrf_field() ?>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="text" class="form-control" name="email" value="<?= set_value('email'); ?>" placeholder="Correo electrónico" tabindex="1" autofocus>
                    <?php if (isset($validator)) { ?>
                      <span class="text-danger"><?php echo $validator->getError('email'); ?></span>
                    <?php } ?>
                  </div>
                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>
                      <div class="float-right">
                        <a href="auth-forgot-password.html" class="text-small">
                          Olvidaste tu contraseña?
                        </a>
                      </div>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña" tabindex="2">
                    <?php if (isset($validator)) { ?>
                      <span class="text-danger"><?php echo $validator->getError('password'); ?></span>
                    <?php } ?>
                    <span class="text-danger"><?= session()->getFlashdata('error') ?></span>
                  </div>
                  <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- General JS Scripts -->
  <script src="<?= base_url(); ?>assets/js/app.min.js"></script>
  <!-- JS Libraies -->
  <!-- Page Specific JS File -->
  <!-- Template JS File -->
  <script src="<?= base_url(); ?>assets/js/scripts.js"></script>
</body>

</html>
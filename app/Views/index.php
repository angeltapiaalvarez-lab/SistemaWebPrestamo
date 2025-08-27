<?= $this->extend('layouts/principal/main'); ?>
<?= $this->section('title'); ?>
login
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="card card-primary">
  <div class="card-header">
    <h4>Login</h4>
  </div>
  <div class="card-body">
    <img src="<?php echo base_url('assets/img/logo.png'); ?>" class="img-fluid rounded-top" alt="LOGO" width="200">
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
            <a href="<?php echo base_url('forgot'); ?>" class="text-small">
              Olvidaste tu contraseña?
            </a>
          </div>
        </div>
        <div class="input-group">
          <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña" tabindex="2">
          <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
            <i class="fa-solid fa-eye-slash"></i>
          </span>
        </div>
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
  <?= $this->endSection('content'); ?>

<?= $this->section('js'); ?>
<script>
  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#password');
  togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.querySelector('i').classList.toggle('fa-eye');
    this.querySelector('i').classList.toggle('fa-eye-slash');
  });
</script>
<?= $this->endSection('js'); ?>
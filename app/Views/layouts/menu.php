<nav class="navbar navbar-expand-lg main-navbar sticky">
    <div class="form-inline me-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-bs-toggle="sidebar" class="nav-link nav-link-lg
									collapse-btn"> <i data-feather="align-justify"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                    <i data-feather="maximize"></i>
                </a></li>
        </ul>
    </div>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown"><a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user"> 
        <?php $perfil = ($_SESSION['perfil'] != null) ? 'assets/img/users/' . $_SESSION['perfil'] : 'assets/img/logo.png'; ?>    
        <img alt="image" src="<?= base_url($perfil); ?>" class="user-img-radious-style"> <span class="d-sm-none d-lg-inline-block"></span></a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
                <div class="dropdown-title">Hola <?php echo $_SESSION['nombre']; ?></div>
                <a href="<?php echo base_url('usuarios/profile'); ?>" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Perfil
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo base_url('usuarios/logout'); ?>" class="dropdown-item has-icon text-danger"> <i class="fas fa-sign-out-alt"></i>
                    Salir
                </a>
            </div>
        </li>
    </ul>
</nav>

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="<?php echo base_url('dashboard'); ?>">
                <img alt="Sistema Préstamo" src="<?= base_url('assets/img/logo.svg'); ?>" class="header-logo" style="width: 200px; max-width: 100%; height: auto; margin-top: 15px;" />
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="dropdown <?php echo ($active == 'dashboard') ? 'active' : ''; ?>">
                <a href="<?php echo base_url('dashboard'); ?>" class="nav-link">
                    <i class="fa-solid fa-chart-pie mx-1"></i>
                    <span>Panel</span></a>
            </li>
            <?php if (
                verificar('actualizar empresa', $_SESSION['permisos'])
                || verificar('listar usuarios', $_SESSION['permisos'])
                || verificar('backup', $_SESSION['permisos'])
            ) { ?>
                <li class="dropdown <?php echo ($active == 'config' || $active == 'usuario') ? 'active' : ''; ?>">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fa-solid fa-screwdriver-wrench mx-1"></i>
                        <span>Administración</span></a>
                    <ul class="dropdown-menu">
                        <?php if (verificar('listar usuarios', $_SESSION['permisos'])) { ?>
                            <li><a class="nav-link <?php echo ($active == 'usuario') ? 'text-success' : ''; ?>" href="<?php echo base_url('usuarios'); ?>">Usuarios</a></li>
                        <?php }
                        if (verificar('actualizar empresa', $_SESSION['permisos'])) { ?>
                            <li><a class="nav-link <?php echo ($active == 'config') ? 'text-success' : ''; ?>" href="<?php echo base_url('admin'); ?>">Configuración</a></li>
                        <?php }
                        if (verificar('backup', $_SESSION['permisos'])) { ?>
                            <li><a class="nav-link" href="<?php echo base_url('backup'); ?>">Respaldo</a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php }
            if (verificar('listar roles', $_SESSION['permisos'])) { ?>
                <li class="<?php echo ($active == 'rol') ? 'active' : ''; ?>">
                    <a href="<?php echo base_url('roles'); ?>" class="nav-link">
                        <i class="fa-solid fa-user-lock mx-1"></i>
                        <span>Roles</span></a>
                </li>
            <?php }
            if (verificar('listar clientes', $_SESSION['permisos'])) { ?>
                <li class="<?php echo ($active == 'cliente') ? 'active' : ''; ?>">
                    <a href="<?php echo base_url('clientes'); ?>" class="nav-link">
                        <i class="fa-solid fa-users mx-1"></i>
                        <span>Clientes</span></a>
                </li>
            <?php }
            if (
                verificar('nuevo prestamo', $_SESSION['permisos'])
                || verificar('historial prestamos', $_SESSION['permisos'])
            ) { ?>
                <li class="dropdown <?php echo (strpos($active, 'prestamo') !== false) ? 'active' : ''; ?>">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fa-regular fa-credit-card mx-1"></i>
                        <span>Préstamos</span></a>
                    <ul class="dropdown-menu">
                        <?php if (verificar('nuevo prestamo', $_SESSION['permisos'])) { ?>
                            <li><a class="nav-link" href="<?php echo base_url('prestamos'); ?>">Nuevo Préstamo</a></li>
                        <?php }
                        if (verificar('historial prestamos', $_SESSION['permisos'])) { ?>
                            <li><a class="nav-link <?php echo (isset($active) && $active == 'prestamo_activos') ? 'text-success' : ''; ?>" href="<?php echo base_url('prestamos/historial/activos'); ?>">Activos</a></li>
                            <li><a class="nav-link <?php echo (isset($active) && $active == 'prestamo_vencidos') ? 'text-danger' : ''; ?>" href="<?php echo base_url('prestamos/historial/vencidos'); ?>">Vencidos</a></li>
                            <li><a class="nav-link <?php echo (isset($active) && $active == 'prestamo_cancelados') ? 'text-success' : ''; ?>" href="<?php echo base_url('prestamos/historial/cancelados'); ?>">Cancelados</a></li>
                            <li><a class="nav-link <?php echo (isset($active) && $active == 'prestamo_anulados') ? 'text-danger' : ''; ?>" href="<?php echo base_url('prestamos/historial/anulados'); ?>">Anulados</a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php }
            if (verificar('ver saldo', $_SESSION['permisos'])) { ?>
                <li class="<?php echo ($active == 'caja') ? 'active' : ''; ?>">
                    <a href="<?php echo base_url('cajas'); ?>" class="nav-link">
                        <i class="fa-solid fa-dollar-sign mx-1"></i>
                        <span>Cajas</span></a>
                </li>
            <?php }
            if (
                verificar('historial pagos', $_SESSION['permisos']) ||
                verificar('historial transacciones', $_SESSION['permisos']) ||
                verificar('historial prestamos', $_SESSION['permisos'])
            ) { ?>
                <li class="dropdown <?php echo (
                                            $active == 'transaccion' ||
                                            $active == 'reportesHistorial' ||
                                            $active == 'reportesPagos'
                                        ) ? 'active' : ''; ?>">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fa-solid fa-chart-line mx-1"></i>
                        <span>Reportes</span></a>
                    <ul class="dropdown-menu">
                        <?php if (verificar('historial transacciones', $_SESSION['permisos'])) { ?>
                            <li><a class="nav-link <?php echo ($active == 'transaccion') ? 'text-success' : ''; ?>" href="<?php echo base_url('transacciones'); ?>">Historial de transacciones</a></li>
                        <?php }
                        if (
                            verificar('historial prestamos', $_SESSION['permisos'])
                        ) { ?>
                            <li><a class="nav-link <?php echo ($active == 'reportesHistorial') ? 'text-success' : ''; ?>" href="<?php echo base_url('reportes/historial'); ?>">Historial Préstamos</a></li>
                        <?php } ?>
                        <?php if (verificar('historial pagos', $_SESSION['permisos'])) { ?>
                            <li><a class="nav-link <?php echo ($active == 'reportesPagos') ? 'text-success' : ''; ?>" href="<?php echo base_url('reportes/pagos'); ?>">Historial de pagos</a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

        </ul>
    </aside>
</div>
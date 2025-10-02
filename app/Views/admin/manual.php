<?= $this->extend('layouts/main'); ?>
<?= $this->section('title'); ?>
Manual de usuario
<?= $this->endSection('title'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h4 class="mb-0">Manual interactivo</h4>
                <button id="guided-tour" class="btn btn-primary" type="button" data-bs-toggle="tooltip" title="Recorre los elementos más importantes del panel">
                    <i class="fa-solid fa-route me-2"></i>Lanzar recorrido guiado
                </button>
            </div>
            <div class="card-body">
                <p class="lead">Este manual reúne las mejores prácticas para operar SisPrey de forma segura. Usa las pestañas inferiores para explorar cada capítulo y apóyate en las ayudas emergentes para resolver dudas puntuales.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-primary" data-bs-toggle="collapse" href="#manualSteps" role="button" aria-expanded="false" aria-controls="manualSteps">
                        <i class="fa-solid fa-list-check me-1"></i> Ver paso a paso
                    </a>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="tooltip" title="Pasa el puntero sobre los íconos <i class='fa-solid fa-circle-info'></i> para descubrir recomendaciones rápidas.">
                        <i class="fa-solid fa-circle-info me-1"></i>Consejos rápidos
                    </button>
                </div>
                <div class="collapse mt-3" id="manualSteps">
                    <div class="card card-body border">
                        <ol class="mb-0">
                            <li id="paso-inicio">Inicia sesión con tu usuario autorizado en <strong><?= base_url(); ?></strong>.</li>
                            <li>Personaliza tu perfil desde <a href="<?= base_url('usuarios/profile'); ?>" class="fw-bold">Mi cuenta</a> y verifica tus permisos.</li>
                            <li>Consulta el panel principal para revisar indicadores clave y atajos hacia los módulos más utilizados.</li>
                            <li>Registra clientes, crea préstamos y realiza seguimientos desde los menús laterales.</li>
                            <li>Descarga reportes o genera respaldos para mantener la operación documentada.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <ul class="nav nav-pills" id="manual-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-primeros-tab" data-bs-toggle="tab" data-bs-target="#pills-primeros" type="button" role="tab" aria-controls="pills-primeros" aria-selected="true">
                            Primeros pasos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-prestamos-tab" data-bs-toggle="tab" data-bs-target="#pills-prestamos" type="button" role="tab" aria-controls="pills-prestamos" aria-selected="false">
                            Gestión de préstamos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-reportes-tab" data-bs-toggle="tab" data-bs-target="#pills-reportes" type="button" role="tab" aria-controls="pills-reportes" aria-selected="false">
                            Reportes y análisis
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="manual-tabContent">
                    <div class="tab-pane fade show active" id="pills-primeros" role="tabpanel" aria-labelledby="pills-primeros-tab">
                        <div class="accordion" id="primerosPasosAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingLogin">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLogin" aria-expanded="true" aria-controls="collapseLogin">
                                        Acceso y panel principal
                                    </button>
                                </h2>
                                <div id="collapseLogin" class="accordion-collapse collapse show" aria-labelledby="headingLogin" data-bs-parent="#primerosPasosAccordion">
                                    <div class="accordion-body">
                                        <p id="primeros-pasos">Una vez autenticado, llegarás al panel con indicadores de <strong data-bs-toggle="tooltip" title="Usuarios activos registrados en la plataforma">usuarios</strong>, <strong data-bs-toggle="tooltip" title="Clientes sin préstamos en mora">clientes</strong> y <strong data-bs-toggle="tooltip" title="Total de préstamos generados en el periodo">préstamos</strong>. Usa los accesos directos para navegar rápidamente hacia cada módulo.</p>
                                        <p>Recuerda que puedes alternar el tema visual desde el selector ubicado en la parte superior derecha si necesitas más contraste.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPerfil">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePerfil" aria-expanded="false" aria-controls="collapsePerfil">
                                        Gestión de perfil
                                    </button>
                                </h2>
                                <div id="collapsePerfil" class="accordion-collapse collapse" aria-labelledby="headingPerfil" data-bs-parent="#primerosPasosAccordion">
                                    <div class="accordion-body">
                                        <p>Actualiza tu foto, contraseña y datos de contacto desde la sección <a href="<?= base_url('usuarios/profile'); ?>">Perfil</a>. Mantener esta información vigente permite auditorías más ágiles y notificaciones oportunas.</p>
                                        <p>Si pierdes acceso, utiliza la opción <a href="<?= base_url('forgot'); ?>">¿Olvidaste tu contraseña?</a> y sigue el asistente de recuperación enviado a tu correo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-prestamos" role="tabpanel" aria-labelledby="pills-prestamos-tab">
                        <div class="accordion" id="prestamosAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingCrearPrestamo">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCrearPrestamo" aria-expanded="true" aria-controls="collapseCrearPrestamo">
                                        Crear un préstamo
                                    </button>
                                </h2>
                                <div id="collapseCrearPrestamo" class="accordion-collapse collapse show" aria-labelledby="headingCrearPrestamo" data-bs-parent="#prestamosAccordion">
                                    <div class="accordion-body">
                                        <p id="gestion-prestamos">Ingresa a <a href="<?= base_url('prestamos'); ?>">Nuevo préstamo</a> para iniciar el proceso. Completa los datos financieros, selecciona al cliente correspondiente y define el calendario de pagos. Los campos cuentan con <span class="badge bg-info text-dark" data-bs-toggle="tooltip" title="Información contextual del campo">ayudas contextuales</span> para guiarte.</p>
                                        <p>Antes de guardar, valida las tasas y revisa el resumen del cálculo. Puedes adjuntar comentarios internos que sólo serán visibles para el equipo autorizado.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSeguimiento">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeguimiento" aria-expanded="false" aria-controls="collapseSeguimiento">
                                        Seguimiento y cobranza
                                    </button>
                                </h2>
                                <div id="collapseSeguimiento" class="accordion-collapse collapse" aria-labelledby="headingSeguimiento" data-bs-parent="#prestamosAccordion">
                                    <div class="accordion-body">
                                        <p>Accede al <a href="<?= base_url('prestamos/historial'); ?>">Historial</a> para revisar los estados de cada préstamo. Usa los filtros superiores para segmentar por fechas, clientes o niveles de mora.</p>
                                        <p>Desde el detalle puedes registrar pagos, emitir recibos y agregar notas de seguimiento. Aprovecha la integración con <a href="<?= base_url('pagos'); ?>">Historial de pagos</a> para conciliar los abonos realizados.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-reportes" role="tabpanel" aria-labelledby="pills-reportes-tab">
                        <div class="accordion" id="reportesAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingReportes">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReportes" aria-expanded="true" aria-controls="collapseReportes">
                                        Tablero analítico
                                    </button>
                                </h2>
                                <div id="collapseReportes" class="accordion-collapse collapse show" aria-labelledby="headingReportes" data-bs-parent="#reportesAccordion">
                                    <div class="accordion-body">
                                        <p id="reportes">En la sección de <a href="<?= base_url('dashboard'); ?>">panel</a> encontrarás gráficas de comportamiento mensual. Sitúa el cursor sobre cada punto para ver cifras exactas y utiliza el selector de año para analizar periodos anteriores.</p>
                                        <p>Para reportes ejecutivos, recurre a <a href="<?= base_url('reportes/historial'); ?>">Historial Préstamos</a>, donde podrás exportar información en PDF o Excel.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingRespaldo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRespaldo" aria-expanded="false" aria-controls="collapseRespaldo">
                                        Respaldo y seguridad
                                    </button>
                                </h2>
                                <div id="collapseRespaldo" class="accordion-collapse collapse" aria-labelledby="headingRespaldo" data-bs-parent="#reportesAccordion">
                                    <div class="accordion-body">
                                        <p>Si tienes permisos administrativos, accede a <a href="<?= base_url('backup'); ?>">Respaldo</a> para generar copias de seguridad. Programa recordatorios recurrentes para asegurar la integridad de la información financiera.</p>
                                        <p>Documenta los respaldos generados en el repositorio corporativo y verifica su integridad en ambientes de prueba.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Recursos adicionales</h4>
            </div>
            <div class="card-body">
                <p>Complementa tu aprendizaje con los siguientes recursos:</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Plantillas de seguimiento</h6>
                            <p class="mb-0">Descarga formatos sugeridos para documentar visitas y compromisos de pago.</p>
                        </div>
                        <span class="badge bg-primary align-self-center" data-bs-toggle="tooltip" title="Muy pronto disponible">Beta</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Glosario de términos</h6>
                            <p class="mb-0">Consulta definiciones clave sobre indicadores financieros y métricas del sistema.</p>
                        </div>
                        <a href="#reportes" class="btn btn-sm btn-outline-primary" data-scroll-target="reportes">Ir al glosario</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mt-4 mt-lg-0">
        <div class="card position-sticky top-0">
            <div class="card-header">
                <h5 class="mb-0">Tabla de contenidos</h5>
            </div>
            <div class="card-body">
                <nav class="nav flex-column manual-toc">
                    <a class="nav-link px-0" href="#primeros-pasos" data-scroll-target="primeros-pasos">
                        <i class="fa-solid fa-compass me-2"></i>Primeros pasos
                    </a>
                    <a class="nav-link px-0" href="#gestion-prestamos" data-scroll-target="gestion-prestamos">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i>Gestión de préstamos
                    </a>
                    <a class="nav-link px-0" href="#reportes" data-scroll-target="reportes">
                        <i class="fa-solid fa-chart-column me-2"></i>Reportes y análisis
                    </a>
                </nav>
                <hr>
                <p class="small text-muted">Utiliza los enlaces para realizar un desplazamiento suave hacia cada apartado del manual.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>

<?= $this->section('js'); ?>
<script src="<?= base_url('assets/js/pages/manual.js'); ?>"></script>
<?= $this->endSection('js'); ?>

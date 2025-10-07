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
                            <li>Desde <a href="<?= base_url('clientes'); ?>" class="fw-bold">Clientes</a> registra a la nueva persona o empresa y adjunta sus soportes.</li>
                            <li>Configura los préstamos desde <a href="<?= base_url('prestamos'); ?>" class="fw-bold">Préstamos</a> y valida el calendario de pagos sugerido.</li>
                            <li>Da seguimiento a la cartera en <a href="<?= base_url('prestamos/historial'); ?>" class="fw-bold">Historial</a> y registra abonos oportunamente.</li>
                            <li>Consulta el <a href="<?= base_url('dashboard'); ?>" class="fw-bold">panel</a> y los reportes descargables para supervisar la operación.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <ul class="nav nav-pills" id="manual-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-clientes-tab" data-bs-toggle="tab" data-bs-target="#pills-clientes" type="button" role="tab" aria-controls="pills-clientes" aria-selected="true">
                            Creación de clientes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-prestamos-tab" data-bs-toggle="tab" data-bs-target="#pills-prestamos" type="button" role="tab" aria-controls="pills-prestamos" aria-selected="false">
                            Gestión y creación de préstamos
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
                    <div class="tab-pane fade show active" id="pills-clientes" role="tabpanel" aria-labelledby="pills-clientes-tab">
                        <div class="accordion" id="clientesAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingNuevoCliente">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNuevoCliente" aria-expanded="true" aria-controls="collapseNuevoCliente">
                                        Registrar un nuevo cliente
                                    </button>
                                </h2>
                                <div id="collapseNuevoCliente" class="accordion-collapse collapse show" aria-labelledby="headingNuevoCliente" data-bs-parent="#clientesAccordion">
                                    <div class="accordion-body">
                                        <p id="creacion-clientes">Ingresa al módulo <a href="<?= base_url('clientes/crear'); ?>">Agregar cliente</a> y completa los datos básicos: identificación, información de contacto y clasificación comercial. Los campos marcados con <span class="badge bg-info text-dark" data-bs-toggle="tooltip" title="Información requerida para cumplir con políticas KYC">obligatorio</span> deben completarse antes de guardar.</p>
                                        <p>Adjunta documentos de soporte desde la sección <strong>Archivos</strong>. Se aceptan formatos PDF o imagen y cada carga queda registrada con fecha y usuario responsable.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingValidaciones">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseValidaciones" aria-expanded="false" aria-controls="collapseValidaciones">
                                        Validaciones y clasificación
                                    </button>
                                </h2>
                                <div id="collapseValidaciones" class="accordion-collapse collapse" aria-labelledby="headingValidaciones" data-bs-parent="#clientesAccordion">
                                    <div class="accordion-body">
                                        <p>Utiliza los indicadores de riesgo para asignar un nivel de confianza al nuevo cliente. El sistema calcula automáticamente la <strong data-bs-toggle="tooltip" title="Promedio de días que tarda el cliente en cumplir compromisos">puntualidad histórica</strong> si ya existen operaciones previas.</p>
                                        <p>En la pestaña <strong>Referencias</strong> registra información adicional (garantes, referencias comerciales) para agilizar evaluaciones futuras.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSeguimientoCliente">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeguimientoCliente" aria-expanded="false" aria-controls="collapseSeguimientoCliente">
                                        Actualización y seguimiento
                                    </button>
                                </h2>
                                <div id="collapseSeguimientoCliente" class="accordion-collapse collapse" aria-labelledby="headingSeguimientoCliente" data-bs-parent="#clientesAccordion">
                                    <div class="accordion-body">
                                        <p>Desde <a href="<?= base_url('clientes'); ?>">Listado de clientes</a> puedes editar información, inactivar registros o asignar ejecutivos responsables. Usa los filtros por estado y zona para localizar rápidamente a un cliente.</p>
                                        <p>Registra notas de visitas o cambios relevantes para conservar una bitácora actualizada. Todas las modificaciones quedan auditadas y se notifican a los supervisores.</p>
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
                                        <p id="gestion-prestamos">Ingresa a <a href="<?= base_url('prestamos'); ?>">Nuevo préstamo</a> para iniciar el proceso. Selecciona al cliente previamente creado, define el tipo de producto, tasa y periodicidad. El simulador mostrará la cuota estimada según los parámetros ingresados.</p>
                                        <p>Antes de confirmar, revisa la tabla de amortización y usa el botón <strong data-bs-toggle="tooltip" title="Permite recalcular cuotas antes de guardar">Recalcular</strong> si modificas montos o plazos. Adjunta condiciones particulares o garantías en el campo de observaciones.</p>
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
                                        <p>Accede al <a href="<?= base_url('prestamos/historial'); ?>">Historial</a> para revisar los estados de cada préstamo. Usa los filtros superiores para segmentar por fechas, clientes, niveles de mora o ejecutivos asignados.</p>
                                        <p>Desde el detalle puedes registrar pagos, programar recordatorios y generar recibos. Aprovecha la integración con <a href="<?= base_url('pagos'); ?>">Historial de pagos</a> para conciliar abonos y detectar atrasos.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingAjustes">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAjustes" aria-expanded="false" aria-controls="collapseAjustes">
                                        Ajustes, renovaciones y cierre
                                    </button>
                                </h2>
                                <div id="collapseAjustes" class="accordion-collapse collapse" aria-labelledby="headingAjustes" data-bs-parent="#prestamosAccordion">
                                    <div class="accordion-body">
                                        <p>Utiliza la opción <strong>Renovar</strong> para generar un nuevo préstamo reutilizando los datos del cliente y del aval. El sistema conservará el historial anterior y marcará la operación como renovada.</p>
                                        <p>Al liquidar un préstamo, registra el estado <strong data-bs-toggle="tooltip" title="Marca el préstamo como finalizado y bloquea nuevos cargos">Cerrado</strong> y adjunta el comprobante final. Así podrás generar cartas de finiquito directamente desde el expediente.</p>
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
                                        <p id="reportes">En la sección de <a href="<?= base_url('dashboard'); ?>">panel</a> encontrarás gráficas de comportamiento mensual, tasa de mora y colocación. Sitúa el cursor sobre cada punto para ver cifras exactas y utiliza el selector de año para analizar periodos anteriores.</p>
                                        <p>Activa los filtros por sucursal o ejecutivo para comparar desempeño y detectar desviaciones rápidamente. Los widgets resaltan alertas cuando la mora supera el umbral definido.</p>
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
                                        <p>Si tienes permisos administrativos, accede a <a href="<?= base_url('backup'); ?>">Respaldo</a> para generar copias de seguridad. Programa recordatorios recurrentes y almacena los archivos en la bóveda corporativa.</p>
                                        <p>Documenta los respaldos generados y verifica su integridad en ambientes de prueba antes de liberarlos al área operativa.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingReportesOperativos">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReportesOperativos" aria-expanded="false" aria-controls="collapseReportesOperativos">
                                        Reportes operativos y exportaciones
                                    </button>
                                </h2>
                                <div id="collapseReportesOperativos" class="accordion-collapse collapse" aria-labelledby="headingReportesOperativos" data-bs-parent="#reportesAccordion">
                                    <div class="accordion-body">
                                        <p>Genera listados personalizados desde <a href="<?= base_url('reportes/historial'); ?>">Historial Préstamos</a> aplicando filtros de fecha, producto y estado. Puedes exportar a PDF o Excel para compartir con el comité de riesgos.</p>
                                        <p>Para análisis profundos, descarga el archivo Excel y utiliza tablas dinámicas. Recuerda resguardar los informes en el repositorio autorizado y clasificar su nivel de confidencialidad.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <a class="nav-link px-0" href="#creacion-clientes" data-scroll-target="creacion-clientes">
                        <i class="fa-solid fa-user-plus me-2"></i>Creación de clientes
                    </a>
                    <a class="nav-link px-0" href="#gestion-prestamos" data-scroll-target="gestion-prestamos">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i>Gestión y creación de préstamos
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

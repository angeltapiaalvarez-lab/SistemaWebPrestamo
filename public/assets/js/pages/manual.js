document.addEventListener('DOMContentLoaded', () => {
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
            new bootstrap.Tooltip(el);
        });
    }

    const smoothScroll = (targetId) => {
        if (!targetId) {
            return;
        }
        const target = document.getElementById(targetId);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    document.querySelectorAll('[data-scroll-target]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const targetId = link.getAttribute('data-scroll-target');
            smoothScroll(targetId);
            if (targetId) {
                history.replaceState(null, '', `#${targetId}`);
            }
        });
    });

    if (window.location.hash) {
        const initialTarget = window.location.hash.replace('#', '');
        smoothScroll(initialTarget);
    }

    const guidedTourButton = document.getElementById('guided-tour');
    if (guidedTourButton && typeof Swal !== 'undefined') {
        guidedTourButton.addEventListener('click', async () => {
            const steps = [
                {
                    title: 'Panel principal',
                    html: 'Consulta indicadores clave y accesos directos desde el <a href="' + base_url + 'dashboard">panel</a>.',
                },
                {
                    title: 'Registrar clientes',
                    html: 'Captura datos y soportes desde <a href="' + base_url + 'clientes/crear">Agregar cliente</a> para habilitar nuevas operaciones.',
                },
                {
                    title: 'Configurar préstamos',
                    html: 'Define montos, tasas y calendarios en <a href="' + base_url + 'prestamos">Nuevo préstamo</a> y realiza seguimiento desde Historial.',
                },
                {
                    title: 'Reportes y respaldos',
                    html: 'Analiza el desempeño en el panel y exporta información desde <a href="' + base_url + 'reportes/historial">Reportes</a> o genera copias en <a href="' + base_url + 'backup">Respaldo</a>.',
                },
            ];

            const swalQueue = Swal.mixin({
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Salir',
                showCancelButton: true,
                progressSteps: steps.map((_, index) => String(index + 1)),
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
            });

            for (let i = 0; i < steps.length; i += 1) {
                const step = steps[i];
                const result = await swalQueue.fire({
                    title: step.title,
                    html: step.html,
                    currentProgressStep: i,
                });
                if (result.dismiss === Swal.DismissReason.cancel) {
                    return;
                }
            }

            Swal.fire({
                icon: 'success',
                title: 'Recorrido finalizado',
                html: 'Ya conoces los puntos esenciales del sistema. ¡Explóralos desde el menú lateral!',
                confirmButtonText: 'Cerrar',
            });
        });
    }
});

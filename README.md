# CodeIgniter 4 Framework

## Recomendaciones  inicial

1. **Seguridad**
   - Cambiar el usuario y contraseña de la base de datos.
   - Activar protección CSRF y cookies seguras.
   - Proteger el archivo `.env` y nunca subirlo a repositorios públicos.

2. **Funcionalidades mínimas**
   - Implementar gestión de usuarios y roles.
   - Crear módulos para préstamos y clientes.
   - Agregar reportes básicos y notificaciones por correo.

3. **Pruebas y calidad**
   - Escribir pruebas unitarias para los controladores y modelos principales.
   - Validar todos los datos de entrada.
   - Configurar logs de errores y auditoría.

4. **Documentación**
   - Completar este README con instrucciones de instalación, uso y estructura.
   - Documentar el código y las funciones principales.

5. **Mantenimiento**
   - Separar funcionalidades en módulos.
   - Mantener dependencias actualizadas.

## Estructura recomendada

- `app/Controllers`: Lógica de negocio y flujo de la aplicación.
- `app/Models`: Acceso y manipulación de datos.
- `app/Views`: Presentación y plantillas.
- `app/Config`: Configuración personalizada.
- `public/`: Recursos públicos y punto de entrada.
- `tests/`: Pruebas unitarias y de integración.
- `writable/`: Archivos generados por la aplicación.
- `vendor/`: Dependencias externas.

## Instalación rápida

1. Clonar el repositorio.
2. Configurar el archivo `.env` con tus credenciales.
3. Instalar dependencias con `composer install`.
4. Ejecutar migraciones y semillas si existen.
5. Levantar el servidor y acceder a la URL configurada.

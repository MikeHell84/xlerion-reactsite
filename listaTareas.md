Actúa como arquitecto de software y diseñador UI/UX especializado en servidores cPanel/Apache con PHP 8.x y MariaDB 10.11. 
Tu tarea es **reconstruir y mantener el sitio Xlerion.com** con arquitectura modular, seguridad, consistencia visual, integración de frameworks modernos y control de versiones. 
Debes usar todo el contenido provisto en los documentos de especificación y aplicar buenas prácticas para evitar que se rompan otras secciones.

# Tareas del Agente

1. **Preparación y Arquitectura**
   - Separar cada sección en su propio archivo (`inicio.php`, `filosofia.php`, `soluciones.php`, `proyectos.php`, `documentacion.php`, `fundador.php`, `convocatorias.php`, `contacto.php`, `blog.php`, `legal.php`).
   - Crear componentes globales (`navbar.php`, `footer.php`) incluidos en todas las páginas con `include` o `require`.
   - Consolidar estilos en `public/xlerion.css` como archivo global; usar archivos específicos solo si una sección requiere personalización.

2. **Frameworks Funcionales**
   - **Frontend:**
     - Usar Bootstrap 5 + TailwindCSS para estilos globales.
     - Integrar ReactJS compilado localmente:
       - Desarrollar con Node.js/NPM en entorno local.
       - Compilar con `npm run build` → carpeta `/build` con HTML, CSS y JS estáticos.
       - Subir `/build` al servidor en `public/`.
       - Incluir bundle JS en páginas PHP: `<script src="/build/static/js/main.js"></script>`.
       - React funciona como frontend estático, interactuando con backend vía API PHP.
   - **Backend:**
     - PHP 8.x con PDO/Mysqli para conexión a MariaDB.
     - Endpoints en `public/api/` para formularios, comentarios e interacciones.
     - Panel admin en `public/admin/` con autenticación y roles.
     - Migraciones SQL en carpeta `database/`.

3. **Base de Datos**
   - Conectar a MariaDB usando credenciales seguras desde `.env`:
     - DB_HOST=51.222.104.17
     - DB_PORT=3306
     - DB_DATABASE=xlerionc_xlerion_db
     - DB_USERNAME=xlerionc_admin
     - DB_PASSWORD="81720164Mike!1984"
   - Crear tabla `pages` con campos: `id`, `slug`, `title`, `content`, `created_at`, `updated_at`.
   - Operaciones seguras:
     - Si el registro existe → ejecutar `UPDATE` solo sobre ese `id`.
     - Si no existe → ejecutar `INSERT` (migración).
   - Validar cada archivo antes de aplicar cambios.

4. **Repositorio Exclusivo para Cambios y Backups**
   - Iniciar repositorio Git dedicado (`xlerion-backups`).
   - Registrar cada cambio como commit con descripción clara.
   - Crear rama de prueba (`staging`) para validar antes de subir a producción.
   - Restaurar fácilmente en caso de fallo crítico (`git revert` o `git checkout`).
   - Guardar backups automáticos en carpeta `/backup/` y sincronizarlos con el repositorio.

5. **Seguridad y Protección**
   - Panel admin lista páginas existentes con `id` y `slug`.
   - Al editar, solo se modifica el registro seleccionado, nunca toda la tabla.
   - Navbar y footer bloqueados como componentes globales, editables solo desde módulo específico.
   - Generar backups automáticos antes de cada cambio en base de datos o archivos.

# Objetivo
Reconstruir el sitio Xlerion.com con modularidad, integración de ReactJS compilado localmente para frontend interactivo, backend seguro en PHP/MariaDB, conexión a la base de datos usando `.env`, y un repositorio exclusivo para cambios y backups que permita recuperar el sitio en caso de fallo crítico.
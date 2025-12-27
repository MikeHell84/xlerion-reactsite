Reglas del Proyecto Xlerion.com
1. Arquitectura Modular
• 	Cada sección debe estar en su propio archivo (, , , etc.).
• 	 y  son componentes globales, no editables desde el panel de contenido.
• 	Prefijo de clases:  para evitar conflictos.
• 	ReactJS compilado localmente para interactividad, integrado con PHP/MariaDB.
2. Estilos
• 	Usar SCSS corporativo () con variables, mixins y breakpoints.
• 	Usar CSS Modules para componentes críticos (, , ).
• 	Usar Bootstrap 5 para estructura responsiva y TailwindCSS para utilidades rápidas.
• 	Compilación obligatoria: siempre compilar SCSS a CSS después de cualquier cambio.
• 	Nunca editar directamente archivos  compilados.
• 	Verificar que el CSS compilado esté vinculado en el HTML/PHP.
3. Tokens Globales
• 	Definir variables globales en  y CSS custom properties:
• 	
• 	
• 	
• 	Todos los módulos deben usar estas variables, nunca hex directos ni variables locales.
4. Diseño Responsive
• 	Aplicar mobile‑first como regla principal.
• 	Breakpoints SCSS:
• 	
• 	
• 	
• 	
• 	
• 	Tipografía fluida con .
• 	Imágenes adaptables con  y .
• 	Cards: una columna en móvil, múltiples columnas en escritorio.
• 	Navbar colapsable en móvil, expandida en escritorio.
• 	Footer apilado en móvil, distribuido en escritorio.
5. Base de Datos
• 	Conexión segura usando credenciales desde .
• 	Tablas:
• 	: id, slug, title, content, created_at, updated_at.
• 	: id, page_id, type, content, order.
• 	Operaciones seguras:
• 	 solo sobre el registro seleccionado.
• 	 para nuevos módulos o páginas.
• 	 con confirmación y backup previo.
6. Panel de Administración (CMS)
• 	Ubicado en  con login seguro y roles (admin, editor).
• 	Dashboard con navegación lateral y topbar.
• 	Funcionalidades:
• 	Crear nuevas páginas o módulos.
• 	Editar contenido existente.
• 	Eliminar módulos o secciones con confirmación.
• 	Organizar módulos mediante drag & drop.
• 	Validación previa antes de aplicar cambios.
• 	Generar backups automáticos antes de cada operación.
7. Control de Versiones
• 	Repositorio Git exclusivo ().
• 	Commit antes de aceptar cualquier cambio.
• 	Rama  para validar antes de producción.
• 	Restaurar fácilmente en caso de fallo crítico ( o ).
• 	Mensajes de commit descriptivos.
8. Seguridad
• 	Autenticación con roles (admin, editor).
• 	Bloquear edición de navbar y footer desde el editor de contenido.
• 	Validar cada archivo antes de aplicar cambios.
• 	Registrar todos los cambios en Git con commit descriptivo.
9. Validación Constante
• 	Probar en móvil real o simulador (Chrome DevTools).
• 	Hard refresh () para evitar caché.
• 	Validar en escritorio y tablet antes de subir a producción.
• 	Documentar cada cambio con capturas y commits.
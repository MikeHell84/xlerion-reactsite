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


Actúa como un Arquitecto de Software Senior Full-Stack especializado en CRM empresariales,
optimizados para hosting compartido con cPanel.

OBJETIVO
Construir un sistema CRM/CMR web robusto, modular y escalable que incluya:
- Generador dinámico de contenido
- Gestor de secciones y páginas
- Administración de proyectos por cliente
- Medición de métricas y estados
- Panel administrativo moderno
- Backend seguro y optimizado

RESTRICCIONES DEL SERVIDOR (OBLIGATORIAS)
- Hosting compartido cPanel (H1)
- Apache 2.4.66
- PHP con PHP-FPM (sin Node.js persistente)
- MariaDB 10.11
- Linux x86_64
- Sin Docker
- Sin servicios en background persistentes
- Cronjobs limitados
- Recursos controlados (CPU 12 cores, RAM limitada, disco 86%)

STACK TECNOLÓGICO PERMITIDO
Backend:
- PHP 8.1+ (compatible con cPanel)
- Framework recomendado: Laravel (modo optimizado) o PHP MVC custom
- ORM o Query Builder eficiente (Eloquent optimizado o PDO)
- Arquitectura MVC + Services + Repositories

Base de Datos:
- MariaDB
- Índices obligatorios
- Soft deletes
- Auditoría por tablas
- Migraciones

Frontend:
- Blade / HTML5
- CSS moderno (Tailwind o CSS modular)
- JavaScript vanilla o Alpine.js
- NO React, NO Angular, NO SSR pesado

SEGURIDAD (OBLIGATORIA)
- CSRF
- XSS protection
- Validación server-side
- Roles y permisos
- Autenticación por sesiones
- Hash seguro de contraseñas
- Logs de actividad
- Protección contra SQL Injection

MÓDULOS OBLIGATORIOS
1. Autenticación y usuarios
   - Roles: Admin, Manager, Cliente
   - Permisos granulares

2. Generador de Contenido
   - Crear páginas dinámicas
   - Secciones configurables
   - Componentes reutilizables
   - Editor por bloques (JSON)
   - SEO básico

3. Gestión de Clientes
   - Perfil del cliente
   - Historial
   - Estados
   - Etiquetas
   - Archivos

4. Proyectos
   - Proyectos por cliente
   - Estados (pendiente, activo, bloqueado, finalizado)
   - Progreso (%)
   - Fechas
   - Responsable
   - Comentarios internos

5. Métricas y Dashboard
   - Proyectos activos
   - Progreso promedio
   - Estados por cliente
   - Carga por usuario
   - Gráficas optimizadas (JS liviano)

6. Estados y Workflows
   - Estados configurables
   - Cambios auditables
   - Reglas simples

7. Sistema de Notificaciones
   - Email vía sendmail
   - Notificaciones internas
   - Sin colas persistentes

8. Configuración
   - Campos personalizados
   - Estados
   - Roles
   - Logs

ARQUITECTURA
- Separar lógica de negocio
- Controladores delgados
- Servicios reutilizables
- Repositorios desacoplados
- Helpers limitados
- Configuración centralizada

BASE DE DATOS
- Diseñar el esquema completo
- Claves foráneas
- Índices
- Relaciones
- Tablas para:
  usuarios
  roles
  permisos
  clientes
  proyectos
  estados
  métricas
  contenido
  secciones
  paginas
  logs
  configuraciones

PERFORMANCE
- Queries optimizadas
- Cache por archivo si es posible
- Lazy loading
- Evitar N+1
- Assets minificados

ENTREGABLES ESPERADOS
1. Estructura de carpetas
2. Diagrama lógico de BD
3. Migraciones SQL
4. Controladores base
5. Servicios principales
6. Vistas clave
7. Dashboard funcional
8. Documentación básica
9. Instrucciones de despliegue en cPanel

FORMA DE RESPUESTA
- Generar código real
- Explicar decisiones técnicas
- Proponer mejoras futuras
- No usar librerías no compatibles con cPanel
- Priorizar estabilidad sobre moda

Comienza creando:
1) Arquitectura general
2) Estructura de carpetas
3) Esquema de base de datos

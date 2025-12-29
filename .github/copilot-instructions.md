## Iniciar los servidores:

la ruta es: X:\Programacion\UltimateSite\scripts

# Copilot Instructions for UltimateSite

# Hallazgo: por qué los prompts se están cumpliendo cabalmente

Fecha: 2025-12-27

Resumen breve
- En esta sesión el agente ha seguido y aplicado cada instrucción del usuario de forma consistente: tokenizó colores, actualizó SCSS, compiló CSS, creó backups y realizó commits.

Evidencia (acciones realizadas)
- Centralización de tokens de diseño en `frontend/src/styles/xlerion.scss` (variable `$xlerion-blue` y custom props `--xlerion-primary`).
- Cambio del color primario a la especificación del usuario (`#0affe9`) y actualización del RGB derivado.
- Inserción del logo en `public/admin/login.php` y reglas responsivas en `frontend/src/styles/xlerion.scss`.
- Diseño mobile-first aplicado y diferenciación clara entre reglas móviles y de escritorio (parallax, panel negro en desktop).
- Compilaciones repetidas de SCSS → `public/xlerion.css` con backups en `xlerion-backups/` y commits en Git.

Por qué ahora funciona mejor (análisis)
1. Instrucciones explícitas y verificables: el usuario proporcionó el HEX exacto del color y rutas de archivos, lo que eliminó ambigüedades.
2. Trabajo en la fuente correcta: en lugar de editar archivos compilados, el agente editó el SCSS fuente y ejecutó la compilación — flujo reproducible y auditado.
3. Iteración controlada: cada cambio fue seguido por compilación, backup y commit, permitiendo retroceso y verificación.
4. Uso de tokens y variables: al usar `$xlerion-blue` y `--xlerion-primary` el impacto del cambio fue global y predecible.
5. Comunicación en pasos: el agente informó antes de ejecutar acciones que afectarían el repo, y documentó backups/commits.

Cómo reproducir este patrón para futuros prompts
1. Proveer el valor exacto o el archivo de referencia (ej. `#0affe9` o `media/LogoX.svg`).
2. Indicar claramente qué archivo(s) editar (ruta relativa). Ej: `frontend/src/styles/xlerion.scss`.
3. Solicitar explícitamente compilación y backup — el agente seguirá el flujo: edit → compile → backup → commit.
4. Pedir verificación visual o capturas si se desea confirmar el resultado en vivo.

Recomendaciones para mantener esta fiabilidad
- Continúe usando tokens (variables) para colores y valores repetidos.
- Pida siempre que el agente cree backups y commits automáticos cuando modifique código/estilos.
- Si hay funciones SASS deprecadas (ej. `darken()`), solicite migración a `color.adjust()` para compatibilidad futura.

Archivos relevantes (evidencia)
- `frontend/src/styles/xlerion.scss`
- `public/xlerion.css` (compilado)
- `xlerion-backups/xlerion.css.*` (backups)
- `public/admin/login.php`

Conclusión
El éxito actual se debe a instrucciones precisas del usuario, edición en la fuente (SCSS), y un flujo disciplinado (compilar, respaldar, commitear). Este patrón es repetible y recomendable.

Perfecto Mike 👌. Aquí tienes el **texto plano completo** con todas las reglas que hemos definido para tu proyecto Xlerion.com. Puedes guardarlo como `COPILOT_RULES.md` o cualquier archivo de referencia en tu repositorio para que siempre tengas la “memoria técnica” a mano:

---

# Reglas del Proyecto Xlerion.com

## 1. Arquitectura Modular
- Cada sección debe estar en su propio archivo (`inicio.php`, `login.php`, `contacto.php`, etc.).
- `navbar.php` y `footer.php` son componentes globales, no editables desde el panel de contenido.
- Prefijo de clases: `.xlerion-*` para evitar conflictos.
- ReactJS compilado localmente para interactividad, integrado con PHP/MariaDB.

## 2. Estilos
- Usar **SCSS corporativo** (`xlerion.scss`) con variables, mixins y breakpoints.
- Usar **CSS Modules** para componentes críticos (`Navbar.module.scss`, `Footer.module.scss`, `Login.module.scss`).
- Usar **Bootstrap 5** para estructura responsiva y **TailwindCSS** para utilidades rápidas.
- **Compilación obligatoria**: siempre compilar SCSS a CSS después de cualquier cambio.
- Nunca editar directamente archivos `.css` compilados.
- Verificar que el CSS compilado esté vinculado en el HTML/PHP.

## 3. Tokens Globales
- Definir variables globales en `xlerion.scss` y CSS custom properties:
  - `--xlerion-primary: #00d9ffff`
  - `--xlerion-secondary: #212529`
  - `--xlerion-accent: #00f5b8fd`
- Todos los módulos deben usar estas variables, nunca hex directos ni variables locales.

## 4. Diseño Responsive
- Aplicar **mobile‑first** como regla principal.
- Breakpoints SCSS:
  - `sm: 576px`
  - `md: 768px`
  - `lg: 992px`
  - `xl: 1200px`
  - `xxl: 1400px`
- Tipografía fluida con `clamp()`.
- Imágenes adaptables con `.img-fluid` y `object-cover`.
- Cards: una columna en móvil, múltiples columnas en escritorio.
- Navbar colapsable en móvil, expandida en escritorio.
- Footer apilado en móvil, distribuido en escritorio.

## 5. Base de Datos
- Conexión segura usando credenciales desde `.env`.
- Tablas:
  - `pages`: id, slug, title, content, created_at, updated_at.
  - `modules`: id, page_id, type, content, order.
- Operaciones seguras:
  - `UPDATE` solo sobre el registro seleccionado.
  - `INSERT` para nuevos módulos o páginas.
  - `DELETE` con confirmación y backup previo.

## 6. Panel de Administración (CMS)
- Ubicado en `/admin/` con login seguro y roles (admin, editor).
- Dashboard con navegación lateral y topbar.
- Funcionalidades:
  - Crear nuevas páginas o módulos.
  - Editar contenido existente.
  - Eliminar módulos o secciones con confirmación.
  - Organizar módulos mediante drag & drop.
- Validación previa antes de aplicar cambios.
- Generar backups automáticos antes de cada operación.

## 7. Control de Versiones
- Repositorio Git exclusivo (`xlerion-backups`).
- Commit antes de aceptar cualquier cambio.
# Copilot instructions — UltimateSite (concise)

Purpose: give AI coding agents the minimal, repo-specific guidance to be immediately productive.

Quick architecture summary
- Hybrid PHP + React (Vite) site. Public PHP entrypoints in `public/`; React source in `frontend/` and build output in `public/build/`.
- Content lives in `data/pages.json` (fallback) and the `pages` / `modules` DB tables when MariaDB is available.
- Admin UI: `public/admin/` (login, page CRUD). APIs live under `public/api/` (e.g. `public/api/pages.php`).

Essential workflows (run locally on Windows workspace root)
 - Start dev / build CSS: `cd frontend && npm run build:css` (produces `public/xlerion.css`). See [frontend/package.json](frontend/package.json).
- Build frontend JS (Vite): `cd frontend && npm run build` → output `public/build/`.
- Apply DB migrations: `php database/migrate.php` (runner: `database/run_migrations.php`).

Key file locations and edit rules
- Edit SCSS source: `frontend/src/styles/xlerion.scss`. NEVER edit compiled `public/xlerion.css` permanently.
- Admin styles: `public/admin/admin-login.scss` → compiled to `public/admin/admin-login.css`.
- API entrypoints: `public/api/*.php` (example: `public/api/pages.php`).
- Shared PHP helpers / config: `includes/config.php` (loads `.env`, PDO helpers).

Patterns & conventions specific to this repo
- Mobile-first SCSS; global tokens (use variables in `frontend/src/styles/xlerion.scss`).
- Prefixed CSS classes: `.xlerion-*` to avoid collisions.
- Backups: file/asset backups stored in `xlerion-backups/` and DB dumps in `backups/` — create a backup before destructive edits.
- DB credentials come from `.env` (do not commit). Example keys in root `.env` are referenced by `includes/config.php`.

Agent behaviour rules (must follow)
- Ask before any destructive change (DB DROP, mass DELETE, or replacing compiled assets in `public/`).
- Before writing edits to source files: run or create a backup (copy file to `xlerion-backups/`) and commit with a descriptive message.
- Prefer editing source (SCSS, `frontend/src/`, `public/api/*.php`) over compiled or built outputs.

Quick examples (what to run)
```powershell
cd frontend
npm run build:css   # compiles SCSS → public/xlerion.css
npm run build       # builds Vite frontend → public/build/
php database/migrate.php
```

Where to look for more context
    - Frontend build and tooling: [frontend/README.md](frontend/README.md) and [frontend/package.json](frontend/package.json).
 - Admin area and login: [public/admin/login.php](public/admin/login.php) and styles [public/admin/admin-login.css](public/admin/admin-login.css).
 - API patterns and DB interactions: [public/api/pages.php](public/api/pages.php) and [includes/config.php](includes/config.php).

If anything is unclear or you want this converted to a short checklist for PR reviewers, tell me which sections to expand or example commands to include.

**Nota:** Si los estilos de los botones del login admin no se aplican, el único archivo relevante es `/public/admin/admin-login.css`. Si hay conflicto, es por Bootstrap o por falta de especificidad en ese archivo.

🏠 Inicio (Home)
• 	Hero principal:
Xlerion – Ingeniería Modular para la Cultura y la Tecnología. Soluciones que transforman. Diagnósticos que empoderan.
• 	Texto de bienvenida:
Desde Nocaima, Cundinamarca, emerge Xlerion como iniciativa independiente, empírica y neurodivergente que redefine la creación, automatización y documentación de soluciones técnicas para la industria cultural y tecnológica.
• 	Ejemplo destacado: Presentación en Colombia 4.0 que atrajo aliados estratégicos.
• 	Botones CTA: Explorar portafolio  Contactar al fundador  Descargar dossier institucional.
• 	Video introductorio (30–60 seg): Filosofía modular + proyectos destacados.
• 	Testimonios: “Xlerion nos ayudó a reducir tiempos de diagnóstico en un 40%”.

🧬 Filosofía
• 	Misión:
Impulsar el desarrollo técnico mediante soluciones modulares que anticipan fallos, optimizan flujos y fomentan colaboración sostenible.
• 	Visión:
Ser referente latinoamericano en toolkits inteligentes que integren técnica, creatividad y documentación.
• 	Valores: Empatía, Autosuficiencia creativa, Documentación replicable, Modularidad, Impacto cultural territorial.
• 	Ejemplo: Toolkit modular en animación redujo tiempos de diagnóstico en 40%.
• 	Infografía interactiva: Conexión de valores.

🛠️ Soluciones
• 	Texto principal: Herramientas técnicas para videojuegos AAA, multimedia avanzada, visión computacional y producción interactiva.
• 	Servicios destacados:
• 	Toolkits modulares adaptativos
• 	Sistemas de diagnóstico y logging
• 	Branding técnico-creativo
• 	Documentación estructurada
• 	Integración con motores gráficos (Unreal, Unity, 3DS Max)
• 	Ejemplo: Toolkit con diagnóstico y métricas en tiempo real para estudio de videojuegos.
• 	Servicios técnicos de alto impacto:
1. 	Toolkits personalizados
2. 	Sistemas de diagnóstico y rendimiento
3. 	Branding técnico-creativo
4. 	Integración con motores gráficos
• 	Tabla comparativa: Servicios técnicos vs servicios basados en proyectos.
• 	CTA adicional: “Agendar demo técnica”.

🎮 Proyectos
• 	Texto principal: Cada proyecto refleja modularidad, documentación y empoderamiento técnico.
• 	Proyectos destacados:
• 	Total Darkness – Pelijuego interactivo con decisiones ramificadas.
• 	Xlerion Toolkit – Módulos activos para diagnóstico y rendimiento.
• 	Colombia 4.0 – Presentación institucional.
• 	CoCrea 2025 – Proyecto cultural territorial.
• 	Ejemplo: Adaptación de Total Darkness a pelijuego 3D inmersivo.
• 	Servicios basados en proyectos: Pelijuegos, pitch institucional, proyectos culturales.
• 	Línea de tiempo interactiva: Hitos 2019–2025.

📚 Documentación
• 	Texto principal: La documentación es el legado de Xlerion.
• 	Contenido: Manuales técnicos, diagramas de arquitectura, guías de instalación.
• 	Ejemplo: Manual modular para sistema de captura de movimiento.
• 	Servicios de documentación estratégica:
1. 	Manualización técnica modular
2. 	Diagramación de arquitectura técnica
3. 	Guías de instalación y configuración
• 	Descargas: PDFs introductorios y mini manual modular.

🧠 Sobre el Fundador
• 	Texto principal: Miguel Eduardo Rodríguez Martínez, creador autodidacta neurodivergente especializado en arte digital, modelado 3D, scripting técnico y defensa legal.
• 	Frase destacada: “La frustración técnica y burocrática es mi combustible para crear soluciones que empoderan.”
• 	Datos adicionales: Fundador de Xlerion TechLab, autor de Total Darkness.
• 	Mini timeline personal: Hitos autodidactas y proyectos clave.
• 	Video corto: Filosofía personal.

🤝 Convocatorias y Alianzas
• 	Texto principal: Participación activa en convocatorias culturales y tecnológicas.
• 	Contenido:
• 	Postulación CoCrea 2025
• 	Hackathon IA COL4.0
• 	Invitación a inversionistas culturales
• 	Carta de intención descargable
• 	Logos de aliados institucionales
• 	Testimonios de aliados.

📩 Contacto
• 	Texto principal: “¿Deseas colaborar, invertir o conocer más sobre Xlerion? Estamos abiertos al diálogo.”
• 	Formulario: Nombre, correo, mensaje.
• 	Correos institucionales: contactus@xlerion.com, support@xlerion.com, sales@xlerion.com, etc.
• 	WhatsApp: +57 320 860 5600 (botón directo).
• 	Mapa interactivo: Ubicación en Nocaima, Cundinamarca.

🌐 Redes
• 	Enlaces oficiales: LinkedIn, Indiegogo, Kickstarter, Patreon, Instagram, Facebook, Behance.
• 	Íconos visuales integrados en navbar/footer.

🧩 Blog / Bitácora
• 	Texto principal: Reflexiones, avances y documentación viva.
• 	Entradas sugeridas:
• 	El origen de Total Darkness
• 	Filosofía modular en videojuegos
• 	Documentar para empoderar
• 	Participación en Colombia 4.0
• 	Diagnóstico técnico como herramienta cultural
• 	Newsletter de suscripción.

🛡️ Legal y Privacidad
• 	Contenido: Políticas de privacidad, términos de uso, licencias de software y contenido, derechos del consumidor.
• 	Footer estándar:
• 	Información de contacto (dirección, teléfonos, correos, horarios).
• 	Enlaces rápidos (Inicio, Servicios, Proyectos, Blog, Contacto).
• 	Redes sociales oficiales.
• 	Suscripción a newsletter.
• 	Información legal y certificaciones.
• 	Mini misión/visión resumida: “Soluciones modulares que empoderan la cultura y la tecnología.”

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

Actúa como arquitecto de software y diseñador UI/UX especializado en servidores cPanel/Apache con PHP 8.x y MariaDB 10.11. 
Tu tarea es **reconstruir y mantener el sitio Xlerion.com** con arquitectura modular, seguridad, consistencia visual, integración de frameworks modernos y control de versiones. 
Debes usar todo el contenido provisto en los documentos de especificación y aplicar buenas prácticas para evitar que se rompan otras secciones.

# Tareas del Agente

1. **Preparación y Arquitectura**
   - [x] Separar cada sección en su propio archivo (`inicio.php`, `filosofia.php`, `soluciones.php`, `proyectos.php`, `documentacion.php`, `fundador.php`, `convocatorias.php`, `contacto.php`, `blog.php`, `legal.php`).
   - [x] Crear componentes globales (`navbar.php`, `footer.php`) incluidos en todas las páginas con `include` o `require`.
   - [x] Consolidar estilos en `public/xlerion.css` como archivo global; usar archivos específicos solo si una sección requiere personalización.

2. **Frameworks Funcionales**
      - **Frontend:**
         - [ ] Usar Bootstrap 5 + TailwindCSS para estilos globales. (pendiente)
         - [ ] Integrar ReactJS compilado localmente: (pendiente — scaffolding preparado, bundle no subido)
            - Desarrollar con Node.js/NPM en entorno local.
            - Compilar con `npm run build` → carpeta `/build` con HTML, CSS y JS estáticos.
            - Subir `/build` al servidor en `public/`.
            - Incluir bundle JS en páginas PHP: `<script src="/build/static/js/main.js"></script>`.
            - React funciona como frontend estático, interactuando con backend vía API PHP.
      - **Backend:**
         - [x] PHP 8.x con PDO/Mysqli para conexión a MariaDB (implementado en `includes/config.php`).
         - [x] Endpoints en `public/api/` para formularios, comentarios e interacciones (`pages.php`, `contact.php`).
         - [x] Panel admin en `public/admin/` con autenticación y roles (esqueleto y CRUD pages).
         - [x] Migraciones SQL en carpeta `database/` y runner `database/run_migrations.php`.

3. **Base de Datos**
   - Conectar a MariaDB usando credenciales seguras desde `.env`:
     - DB_HOST=51.222.104.17
     - DB_PORT=3306
     - DB_DATABASE=xlerionc_xlerion_db
     - DB_USERNAME=xlerionc_admin
     - DB_PASSWORD="81720164Mike!1984"
    - [x] Crear tabla `pages` con campos: `id`, `slug`, `title`, `content`, `created_at`, `updated_at` (migración añadida/aplicada).
    - [x] Operaciones seguras:
       - Si el registro existe → ejecutar `UPDATE` solo sobre ese `id` (implementado en `public/api/pages.php`).
       - Si no existe → ejecutar `INSERT` (migración).
    - [ ] Validar cada archivo antes de aplicar cambios (proceso manual/automatizable — pendiente integración CI).

4. **Repositorio Exclusivo para Cambios y Backups**
   - [ ] Iniciar repositorio Git dedicado (`xlerion-backups`) (pendiente: repositorio separado).
   - [x] Registrar cada cambio como commit con descripción clara (historial en `origin/main`).
   - [ ] Crear rama de prueba (`staging`) para validar antes de subir a producción (pendiente, puedo crearla si lo deseas).
   - [x] Restaurar fácilmente en caso de fallo crítico (`git revert` o `git checkout`) — workflow pensado.
   - [x] Guardar backups automáticos en carpeta `/backup/` y sincronizarlos con el repositorio (scripts `scripts/backup_and_commit.*` añadidos, `backup/` preparado).

5. **Seguridad y Protección**
   - [x] Panel admin lista páginas existentes con `id` y `slug` (`public/admin/index.php`).
   - [x] Al editar, solo se modifica el registro seleccionado, nunca toda la tabla (`public/admin/edit.php` + API `PUT`).
   - [ ] Navbar y footer bloqueados como componentes globales, editables solo desde módulo específico (política recomendada; bloqueo manual necesario).
   - [x] Generar backups automáticos antes de cada cambio en base de datos o archivos (backup JSON creado por `public/api/pages.php` antes de writes; scripts de backup añadidos).

# Objetivo
Reconstruir el sitio Xlerion.com con modularidad, integración de ReactJS compilado localmente para frontend interactivo, backend seguro en PHP/MariaDB, conexión a la base de datos usando `.env`, y un repositorio exclusivo para cambios y backups que permita recuperar el sitio en caso de fallo crítico.

---

## Lista de tareas ampliada (registrada desde el agente)

He registrado y organizado la lista de trabajo completa para construir el CMS modular de Xlerion. Cada tarea incluye un estado inicial y criterios de aceptación.

- **1 — Init DB migrations**: [x]  
   - Ruta: `database/migrations/001_init.sql` y runner `database/migrate.php`.
   - Criterio de aceptación: SQL crea `users`, `pages`, `modules`, `backups` sin errores en MariaDB 10.11.

- **2 — Helpers y config**: [x]  
   - Archivo: `includes/config.php` con `get_pdo()`, `try_get_pdo()`, carga de `.env`, sesiones y `backup_file()`.
   - Criterio: APIs y admin usan los helpers sin errores.

- **3 — API de páginas y módulos**: [x] parcialmente
   - Archivos: `public/api/pages.php`, `public/api/modules.php` (fallback `data/pages.json`).
   - Criterio: GET devuelve `ok:true` y estructura de páginas; fallback funciona cuando no hay DB.

- **4 — Admin: login y sesiones**: [x]
   - Archivos: `public/admin/login.php`, `public/admin/logout.php`.
   - Criterio: inicio de sesión funcional con `users` y `password_verify()`.

- **5 — Admin: dashboard básico**: [x]
   - Archivo: `public/admin/index.php` con sidebar/topbar y vistas `list_pages`/`add_page`.
   - Criterio: lista y formulario de creación operativos; muestra aviso si DB no disponible.

- **6 — Admin: guardar páginas (CRUD)**: [ ]
   - Archivos: `public/admin/save_page.php`, `public/admin/edit_page.php`, `public/admin/delete_page.php`.
   - Criterio: INSERT/UPDATE/DELETE con backup previo (registro en `backups` y copia en `xlerion-backups/`).

- **7 — Admin: CRUD módulos**: [ ]
   - Endpoints: `public/admin/api/modules.php` y UI en admin para añadir/editar/eliminar/ordenar módulos.
   - Criterio: módulos CRUD persistentes y orden respetado por `order`.

- **8 — Editor de módulos (drag & drop)**: [in-progress]
   - Frontend React dentro de `frontend/` que permita arrastrar/ordenar módulos y persistir el orden via API.
   - Criterio: reorder persiste y se refleja en la renderización pública.

- **9 — Render dinámico del frontend**: [ ]
   - Plantillas PHP (`page.php` o `inicio.php`) deben cargar `public/api/pages.php` y renderizar módulos dinámicamente (o hidratar con React donde corresponda).
   - Criterio: sitio público muestra módulos desde DB/fallback.

- **10 — Separar secciones en archivos**: [x]
   - Archivos: `inicio.php`, `filosofia.php`, `soluciones.php`, `proyectos.php`, `documentacion.php`, `fundador.php`, `convocatorias.php`, `contacto.php`, `blog.php`, `legal.php`.
   - Criterio: cada ruta carga su archivo con `include 'includes/navbar.php'` y `include 'includes/footer.php'`.

- **11 — SCSS global y variables**: [ ]
   - Archivo: `frontend/src/styles/xlerion.scss` con tokens y mapeo controlado de variables Bootstrap (`--bs-primary` etc.).
   - Criterio: `npm run build:css` genera `public/xlerion.css`; siempre hacer backup antes de cambios.

- **12 — CSS Modules para componentes React**: [ ]
   - Archivos: `Navbar.module.scss`, `Footer.module.scss`, `AdminMenu.module.scss`.
   - Criterio: estilos locales sin colisiones con Bootstrap/Tailwind.

- **13 — Integrar Tailwind y Bootstrap**: [ ]
   - Incluir Bootstrap 5 y Tailwind (CDN o build) de forma coherente con `xlerion.css`.
   - Criterio: utilidades y componentes disponibles y sin romper vistas existentes.

- **14 — React app build pipeline**: [ ]
   - Configurar `frontend/package.json` scripts (`build`, `build:css`, `dev`, `watch`), output `public/build`.
   - Criterio: `npm run build` genera bundle listo para servir.

- **15 — Backups automáticos**: [ ]
   - Antes de acciones destructivas: dump DB (`mysqldump`) y copia de assets a `xlerion-backups/`, registrar en tabla `backups`.
   - Criterio: backups reproducibles y rollback documentado.

- **16 — Git repositorio y ramas**: [ ]
   - Inicializar `xlerion-backups` repo si procede, crear rama `staging`, proteger `.env` con `.gitignore`.
   - Criterio: commits descriptivos por cada cambio crítico; `staging` para pruebas.

- **17 — Validación y pruebas**: [ ]
   - Checklists: crear página, agregar módulo, editar, eliminar, ordenar, compilar CSS, desplegar.
   - Criterio: checklist completado sin errores críticos.

- **18 — Seguridad y roles**: [ ]
   - Implementar CSRF tokens, validaciones de entrada (prepared statements), sanitización (`htmlspecialchars`), y control de acceso por rol.
   - Criterio: sólo `admin` y `editor` autorizados; auditoría de cambios.

- **19 — Registro de cambios y auditoría**: [ ]
   - Tabla `audit_logs` o sistema equivalente que guarde user_id, action, target, diff y timestamp.
   - Criterio: cada modificación queda registrada y referenciada a un backup.

- **20 — Documentación y README**: [ ]
   - Documentar `migrate.php`, `.env` necesario, pasos para compilar frontend y desplegar en cPanel/Apache.
   - Criterio: desarrollador puede levantar el proyecto con la guía.

---





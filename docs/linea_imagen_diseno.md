# Línea de Imagen y Guía de Diseño — Xlerion

Última actualización: 2025-12-27

Este documento centraliza las reglas de identidad visual de Xlerion para desarrolladores, diseñadores y administradores. Incluye tokens, tipografías, componentes básicos, normas de uso y comandos de compilación obligatorios.

---

## 1. Objetivo

Proveer una referencia única y práctica para mantener la coherencia visual en todas las piezas digitales de Xlerion (web, emails, campañas). Debe ser fácil de aplicar por desarrolladores y respetada por diseñadores.

## 2. Ubicación de archivos clave

- SCSS global (fuente de verdad): `frontend/src/styles/xlerion.scss`
- CSS compilado para producción: `public/xlerion.css`
- SCSS modular de componentes: `frontend/src/components/*.module.scss` (ej.: `Footer.module.scss`)
- Componentes React: `frontend/src/components/*.jsx` (ej.: `Footer.jsx`, `Navbar.jsx`)
- Backups / historial de compilaciones: carpeta sugerida `xlerion-backups/` (crear si no existe)


## 3. Tokens (colores, espaciado, tipografía)

### Colores principales Xlerion

- **Primario (textos, bordes, botones, links):**
  - Color: Azul láser — `#00f0ff`
  - Token SCSS: `$xlerion-primary`
  - Custom property: `--xlerion-primary`
  - Ejemplo de uso:
    - Texto: `color: var(--xlerion-primary);`
    - Borde: `border-color: var(--xlerion-primary);`
    - Fondo botón: `background: var(--xlerion-primary); color: #000;`

- **Acento (detalles, hover, highlights):**
  - Color: Verde neón — `#00FF88`
  - Token SCSS: `$xlerion-accent`
  - Custom property: `--xlerion-accent`
  - Ejemplo de uso:
    - Texto/acento: `color: var(--xlerion-accent);`
    - Sombra: `box-shadow: 0 0 8px var(--xlerion-accent);`

- **Fondo principal (background):**
  - Color: Gris oscuro — `#181a1b`
  - Token SCSS: `$xlerion-bg`
  - Custom property: `--xlerion-bg`
  - Ejemplo de uso:
    - Fondo general: `background: var(--xlerion-bg);`

- **Card y paneles (fondos secundarios):**
  - Color: Negro — `#000`
  - Token SCSS: `$xlerion-card-bg`
  - Custom property: `--xlerion-card-bg`
  - Ejemplo de uso:
    - Fondo de tarjetas: `background: var(--xlerion-card-bg);`

- **Muted (textos secundarios):**
  - Color: Gris medio — `#6c757d`
  - Token SCSS: `$xlerion-muted`
  - Custom property: `--xlerion-muted`
  - Ejemplo de uso:
    - Texto secundario: `color: var(--xlerion-muted);`

> **Nota:** Usar siempre los tokens SCSS o custom properties para mantener la coherencia visual. No usar valores hex directos en componentes.

- Espaciado (escala modular):
  - `$space-1`: 4px
  - `$space-2`: 8px
  - `$space-3`: 16px
  - `$space-4`: 32px

- Tipografía:
  - Primaria: `Work Sans` (Google Fonts)
  - Secundaria: `work Sans` (Google Fonts)
  - Token SCSS: `$font-sans` y `--xlerion-font-sans`

## 4. Breakpoints (mobile-first)

- Mobile: 0 — comportamiento por defecto
- Tablet: `768px` (`$bp-tablet`)
- Desktop: `1200px` (`$bp-desktop`)

Usar `@media (min-width: $bp-tablet)` para reglas que escalen hacia pantallas mayores.

## 5. Mixins y utilidades (resumen)

- `@mixin xlerion-button($bg, $color, $radius)` — practica creación de botones coherentes.
- `@mixin xlerion-card()` — card base con sombra y borde redondeado.
- `@mixin xlerion-transition()` — transición prefijada para interacciones.

Documenta y publica nuevos mixins en `xlerion.scss` o en partials dentro de `frontend/src/styles/utilities/`.

## 6. Componentes globales y convenciones

- Prefijo obligatorio: todas las clases globales deben usar el prefijo `.xlerion-` (ej.: `.xlerion-navbar`, `.xlerion-footer`, `.xlerion-card`).
- CSS Modules: para estilos de componentes React usar `*.module.scss` y nombrar clases sin el prefijo global (el empaquetador generará hashes). Ejemplo: `Footer.module.scss` contiene `.footer-container` — se importa como `styles.footerContainer`.
- Evitar estilos inline; usar utilidades SCSS/Tailwind cuando sea posible.

## 7. Reglas de logo y tratamiento de marca

- Logos y variantes (svg, png) deben guardarse en `public/icons/` o `public/assets/brand/`.
- Evitar recolorizar el logo salvo variantes oficiales (positivo/negativo).
- Mantener espacio exterior mínimo alrededor del logo: 1x la altura del logo.

## 8. Accesibilidad

- Contraste: asegurar contraste texto/fondo >= 4.5:1 para texto normal.
- Enlaces y botones deben tener foco visible (`outline` o `box-shadow` accesible).
- Formularios: usar labels asociados, mensajes de error accesibles y `aria-*` donde aplique.

## 9. Integración con Bootstrap y Tailwind

- Estructura: usar Bootstrap para grid y layout (`container`, `row`, `col-*`).
- Utilidades: usar Tailwind donde se necesiten utilidades rápidas (espaciado, tipografía), pero mapear visualmente las utilidades a tokens SCSS cuando sea necesario para consistencia.
- Evitar mezclar reglas que compitan; preferir los tokens (`$xlerion-*` / `--xlerion-*`) para colores y espaciado.

## 10. Comandos de compilación obligatorios

- Compilar SCSS a CSS (obligatorio tras cualquier cambio en SCSS):

```powershell
cd X:\Programacion\UltimateSite\frontend
npm run build:css
```

- `npm run build:css` compila `frontend/src/styles/xlerion.scss` → `public/xlerion.css`.
- Si se editan CSS Modules o React, ejecutar `npm run build` para regenerar `public/build/`.

## 11. Flujo Git y backups

- Antes de desplegar cambios visuales (nuevos tokens, mixins que rompan layout), crear rama `staging` y validar localmente.
- Registrar cada compilación de `xlerion.css` con un commit claro: `build(styles): compile xlerion.css — <fecha>` y opcionalmente copiar el artefacto al repositorio de backups `xlerion-backups/`.
- Mantener `.env` fuera del repo y `public/build/` en `.gitignore` si prefieres no versionar bundles.

## 12. Buenas prácticas y prohibiciones

- Evitar `!important` salvo emergencia documentada.
- Mantener especificidad baja: preferir clases sobre selectores tipo y anidación profunda.
- Documentar cada cambio grande en este archivo y/o en `docs/CHANGELOG.md`.

## 13. Ejemplos rápidos

- Botón primario (SCSS):

```scss
.xlerion-btn-primary { @include xlerion-button($xlerion-blue); }
```

- Uso en React (CSS Modules):

```jsx
import styles from './Footer.module.scss'
<footer className={styles['footer-container']}></footer>
```

## 14. Assets y recursos

- Tipografías: cargar desde Google Fonts en `xlerion.scss` (ya incluido).
- Iconografía: preferir SVG y guardarlos en `public/icons/`.

## 15. Contacto y soporte de diseño

Para dudas de diseño o decisiones de marca, contactar a: diseño@xlerion.com (si no existe, usar el repositorio de issues en Git).

---

Este documento es la referencia canónica local; cualquier cambio mayor debe validarse en `staging` antes de promover a `main`/producción.


## 16. Medios
aca estan los medios para el sitio:
X:\Programacion\UltimateSite\media
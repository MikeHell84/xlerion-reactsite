# Mapa de control de estilos CSS/SCSS para el login admin y botones

## /public/admin/login.php
- **Botones de login y recuperación:**
  - Clases: `.xlerion-btn-primary`, `.forgot-password-link`
  - Controlados por: `/public/admin/admin-login.css` (compilado de `/public/admin/admin-login.scss`)
  - Bootstrap 5 también está enlazado, pero los estilos personalizados tienen prioridad si la especificidad es suficiente.

## /public/xlerion.css
- **Botones generales:**
  - Clase: `.xlerion-btn`
  - No afecta a los botones del login admin (a menos que se use esa clase).

## /total-darkness/styles.css
- **Botones genéricos:**
  - Clases: `.btn`, `.btn-primary`, `.btn-secondary`
  - No afectan al login admin (a menos que se usen esas clases en el HTML).

## /frontend/src/styles/xlerion.scss
- **Botones generales para frontend React:**
  - Clase: `.xlerion-btn` (con mixin)
  - No afecta al login admin PHP.

## Resumen visual rápido
| Sección/Componente         | Archivo CSS/SCSS que controla |
|---------------------------|-------------------------------|
| Login admin (botones)     | admin-login.css / .scss       |
| Botones generales (web)   | xlerion.css                   |
| Botones Total Darkness    | total-darkness/styles.css     |
| Botones React frontend    | frontend/src/styles/xlerion.scss |

---

**Nota:** Si los estilos de los botones del login admin no se aplican, el único archivo relevante es `/public/admin/admin-login.css`. Si hay conflicto, es por Bootstrap o por falta de especificidad en ese archivo.
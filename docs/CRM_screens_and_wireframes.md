## CRM — Mapa de pantallas y wireframes (resumen)

Este documento resume las pantallas principales, rutas propuestas, componentes y prioridades para el CRM de Xlerion.

1) Resumen de módulos (MVP)
- Autenticación y usuarios (login, recover, profile)
- Clientes / Contactos (lista, ficha 360)
- Leads / Prospectos (bandeja, detalle, conversión)
- Oportunidades / Ventas (pipeline Kanban, detalle)
- Actividades y tareas (agenda, lista, recordatorios)
- Comunicaciones (historial, plantillas)
- Productos / Servicios (catálogo)
- Facturación / Cotizaciones (crear, facturas, PDF)
- Reportes y métricas (dashboards)
- Automatizaciones (reglas básicas)
- Configuración del sistema

2) Rutas y archivos (propuesta)
- Admin UI (serán páginas PHP o rutas que cargan bundles React):
  - `/public/admin/index.php` — panel general
  - `/public/admin/crm/dashboard.php` — CRM dashboard (widgets)
  - `/public/admin/crm/customers.php` — Lista clientes
  - `/public/admin/crm/customers/view.php?id=...` — Ficha cliente
  - `/public/admin/crm/leads.php` — Bandeja leads
  - `/public/admin/crm/opportunities.php` — Pipeline
  - `/public/admin/crm/activities.php` — Agenda
  - `/public/admin/crm/communications.php` — Comunicaciones
  - `/public/admin/crm/products.php` — Catálogo
  - `/public/admin/crm/invoices.php` — Facturación
  - `/public/admin/crm/reports.php` — Reportes
  - `/public/admin/crm/automations.php` — Automatizaciones
  - `/public/admin/crm/settings.php` — Ajustes

3) API (endpoints mínimos)
- `GET /api/crm/customers.php` → lista de clientes (paginada)
- `GET /api/crm/customers.php?id=NN` → detalle cliente (JSON)
- `POST /api/crm/customers.php` → crear cliente
- `PUT /api/crm/customers.php?id=NN` → actualizar cliente
- `DELETE /api/crm/customers.php?id=NN` → eliminar

- `GET /api/crm/leads.php` — lista leads
- `POST /api/crm/leads.php` — crear lead / `PUT` para convertir

- `GET /api/crm/opportunities.php` — pipeline data (por etapa)
- `POST /api/crm/opportunities.php` — crear oportunidad

- `GET /api/crm/activities.php` — tareas/agenda
- `POST /api/crm/activities.php` — crear tarea

- `GET /api/crm/invoices.php` — facturas / cotizaciones
- `POST /api/crm/invoices.php` — generar factura / exportar PDF

4) Wireframes y layout (alto nivel)
- Layout general: sidebar izquierdo (navegación), header superior (buscador global + notifs + perfil), panel central con cards/listados y panel derecho opcional para detalle rápido.
- Mobile-first: listas apiladas; en móvil el detalle se abre como pantalla completa / modal.
- Kanban (Oportunidades): columnas por etapa; drag & drop para mover etapas.
- Cliente 360: sección superior con datos clave y acciones, pestañas: Interacciones, Oportunidades, Facturación, Archivos, Notas.
- Lead inbox: lista con filtros (origen, score, asignado); vista rápida a la derecha.

5) Prioridades y MVP (fase 1)
- Autenticación (login + sesiones seguras)
- Clientes: lista + ficha básica
- Leads: bandeja básica + conversión manual a cliente
- Oportunidades: vista de lista (antes de Kanban)
- API stubs para los módulos anteriores

6) Recomendaciones técnicas y siguientes pasos
- Empezar por proteger rutas admin con la capa de autenticación en `includes/config.php`.
- Implementar primero las APIs CRUD (customers, leads, opportunities, invoices) con respuestas JSON.
- Crear componentes React pequeños para listas y detalles si planeas migrar frontend progresivamente; mientras tanto, mantener plantillas PHP server-rendered es aceptable.
- Añadir pruebas E2E básicas (puedes usar Playwright o Cypress) para flujos: login → ver clientes → crear lead → convertir.

7) Assets y entregables que puedo generar si confirmas
- Mapas de pantalla por módulo (PNG / SVG) — wireframes de baja fidelidad.
- Especificación OpenAPI mínima para los endpoints CRUD.
- Mock JSON con ejemplos de payloads para el front.

---

Documento generado por el agente — fecha: 2025-12-28.

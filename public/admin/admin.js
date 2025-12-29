(function () {
    var listEl = document.getElementById('admin-sections-list');
    var detailCard = document.getElementById('admin-detail');
    var detailTitle = document.getElementById('detail-title');
    var detailBody = document.getElementById('detail-body');
    var pages = [];
    // Global error handlers to surface promise rejections and window errors in the admin detail panel
    try {
        window.addEventListener('unhandledrejection', function (ev) {
            try {
                console.warn('Unhandled promise rejection in admin UI:', ev.reason);
                var msg = (ev.reason && (ev.reason.message || ev.reason.toString())) || String(ev.reason);
                if (msg && msg.indexOf('A listener indicated an asynchronous response') !== -1) {
                    // Likely a browser extension background listener — surface minimally
                    console.info('Likely browser extension message channel warning (can be ignored).');
                    return;
                }
                if (detailBody) detailBody.innerHTML = '<pre style="white-space:pre-wrap;">Unhandled promise rejection:\n' + escapeHtml(msg) + '</pre>';
            } catch (e) { console.error(e); }
        });
        window.addEventListener('error', function (ev) {
            try {
                console.warn('Window error', ev.error || ev.message || ev);
                var m = ev.message || (ev.error && ev.error.message) || String(ev.error || ev);
                if (m && m.indexOf('A listener indicated an asynchronous response') !== -1) {
                    console.info('Likely browser extension message channel warning (can be ignored).');
                    return;
                }
                if (detailBody) detailBody.innerHTML = '<pre style="white-space:pre-wrap;">Window error:\n' + escapeHtml(m) + '</pre>';
            } catch (e) { console.error(e); }
        });
    } catch (e) { /* ignore in very old browsers */ }
    // CRM sections shareable across functions (used by showMainSections and showCRMSections)
    var crmSections = [
        { id: 'crm-panel', title: 'CRM: Panel', href: '/public/admin/crm/index.php', desc: 'Acceso rápido al panel del CRM' },
        { id: 'crm-customers', title: 'CRM: Clientes / Contactos', href: '/public/admin/crm/customers.php', desc: 'Gestión de contactos y clientes' },
        { id: 'crm-leads', title: 'CRM: Leads / Prospectos', href: '/public/admin/crm/leads.php', desc: 'Bandeja de leads y conversión' },
        { id: 'crm-opportunities', title: 'CRM: Oportunidades / Ventas', href: '/public/admin/crm/opportunities.php', desc: 'Pipeline y oportunidades' },
        { id: 'crm-activities', title: 'CRM: Actividades / Tareas', href: '/public/admin/crm/activities.php', desc: 'Agenda y tareas' },
        { id: 'crm-communications', title: 'CRM: Comunicaciones', href: '/public/admin/crm/communications.php', desc: 'Email, WhatsApp y SMS' },
        { id: 'crm-products', title: 'CRM: Productos / Servicios', href: '/public/admin/crm/products.php', desc: 'Catálogo y precios' },
        { id: 'crm-invoices', title: 'CRM: Cotizaciones / Facturación', href: '/public/admin/crm/invoices.php', desc: 'Cotizaciones y facturación' },
        { id: 'crm-reports', title: 'CRM: Reportes / Métricas', href: '/public/admin/crm/reports.php', desc: 'Dashboards y exportes' },
        { id: 'crm-automations', title: 'CRM: Automatizaciones', href: '/public/admin/crm/automations.php', desc: 'Reglas y flujos automáticos' },
        { id: 'crm-settings', title: 'CRM: Configuración', href: '/public/admin/crm/settings.php', desc: 'Ajustes e integraciones' }
    ];

    function createSectionItem(p) {
        var li = document.createElement('li');
        var a = document.createElement('a');
        a.href = '/public/admin/index.php?page=edit_page&slug=' + encodeURIComponent(p.slug || p.id);
        a.textContent = p.title || p.slug || ('Página ' + (p.id || ''));
        a.className = 'admin-nav-item';
        a.dataset.slug = p.slug || '';
        a.dataset.id = p.id || '';
        a.addEventListener('click', function (ev) { ev.preventDefault(); showDetail(p); });
        li.appendChild(a);
        return li;
    }

    function showPagesList() {
        var server = document.getElementById('server-content'); if (server) server.style.display = 'none';
        detailCard.style.display = '';
        detailTitle.textContent = 'Todas las páginas';
        var html = '';
        if (!pages || pages.length === 0) {
            html = '<p>No hay páginas disponibles.</p>';
        } else {
            html = '<table border="1" cellpadding="6" cellspacing="0"><tr><th>ID</th><th>Slug</th><th>Title</th><th>Acciones</th></tr>';
            pages.forEach(function (r) {
                html += '<tr>' +
                    '<td>' + (r.id || '') + '</td>' +
                    '<td>' + (r.slug || '') + '</td>' +
                    '<td>' + (r.title || '') + '</td>' +
                    '<td><a href="/public/admin/index.php?page=edit_page&id=' + (r.id || '') + '">Editar</a></td>' +
                    '</tr>';
            });
            html += '</table>';
        }
        detailBody.innerHTML = html;
    }

    // Generic table view navigator: shows a dedicated section for the given table and rows
    function showTableList(table, rows) {
        detailCard.style.display = '';
        detailTitle.textContent = 'Sección: ' + table;
        rows = rows || [];
        // Special-case pages: reuse existing pages list UI
        if (table === 'pages') {
            // Render pages directly as cards from the fetched rows (avoid relying on global `pages` state)
            return renderPagesAsCards(rows);
        }

        // Generic table renderer
        var html = '';
        html += '<div style="display:flex;flex-direction:column;gap:8px">';
        html += '<div class="admin-card">';
        html += '<h3 style="margin:0 0 8px 0">' + escapeHtml(table) + ' — ' + rows.length + ' filas</h3>';
        if (!rows || rows.length === 0) {
            html += '<p>No hay filas disponibles para esta sección.</p>';
        } else {
            var cols = Object.keys(rows[0] || {});
            html += '<div style="overflow:auto;max-height:520px;padding:6px;background:transparent">';
            html += '<table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse">';
            html += '<thead><tr>';
            cols.forEach(function (c) { html += '<th style="text-align:left;padding:6px">' + escapeHtml(c) + '</th>'; });
            html += '</tr></thead><tbody>';
            rows.forEach(function (r, idx) {
                if (idx >= 500) return; // safety limit
                html += '<tr>';
                cols.forEach(function (c) { var v = r[c]; html += '<td style="padding:6px;max-width:320px;overflow:auto">' + escapeHtml(v === null || v === undefined ? '' : String(v)) + '</td>'; });
                html += '</tr>';
            });
            html += '</tbody></table>';
            html += '</div>';
        }
        html += '</div>'; // admin-card
        html += '</div>';
        detailBody.innerHTML = html;
    }

    function renderPagesAsCards(rows) {
        rows = rows || [];
        detailCard.style.display = '';
        detailTitle.textContent = 'Todas las páginas';
        if (!rows || rows.length === 0) {
            detailBody.innerHTML = '<p>No hay páginas disponibles.</p>';
            return;
        }
        var html = '<div style="display:flex;flex-wrap:wrap;gap:12px">';
        rows.forEach(function (r) {
            var id = r.id || r.ID || r.page_id || r.pk || '';
            var slug = r.slug || r.Slug || r.slug_text || r.path || r.permalink || '';
            var title = r.title || r.Title || r.name || (slug ? slug.replace(/[-_]/g, ' ') : ('Página ' + id));
            var preview = r.content || r.content_html || r.html || r.body || '';
            html += '<div class="section-card" style="width:220px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
            html += '<h3 style="margin:0 0 6px 0">' + escapeHtml(title) + '</h3>';
            html += '<p style="margin:0 0 8px 0;color:rgba(255,255,255,0.6);font-size:0.9em">' + escapeHtml(slug) + '</p>';
            html += '<div style="padding:8px;background:#0b0b0b;color:#fff;border-radius:6px;margin-top:6px;min-height:64px;max-height:120px;overflow:auto">' + (preview ? preview : '<em>Sin contenido</em>') + '</div>';
            html += '<p style="margin-top:8px"><button class="open-editor" data-slug="' + encodeURIComponent(slug) + '" style="background:none;border:0;color:var(--xlerion-primary);cursor:pointer;padding:0">Abrir editor</button></p>';
            html += '</div>';
        });
        html += '</div>';
        detailBody.innerHTML = html;
        Array.from(detailBody.querySelectorAll('.open-editor')).forEach(function (btn) { btn.addEventListener('click', function (ev) { ev.preventDefault(); var slug = decodeURIComponent(this.dataset.slug || ''); var page = rows.find(function (x) { return (x.slug || x.Slug || x.slug_text || x.path) === slug; }); if (page) { showDetail({ id: page.id || page.ID || page.page_id, slug: slug, title: page.title || page.Title || page.name, content: page.content || page.html || page.body }); } else { alert('Página no encontrada para editar: ' + slug); } }); });
    }

    // Render the replicated-data panel (counts, backups, and table placeholder)
    // If `append` is true, the panel is appended to `detailBody`; otherwise it replaces it.
    function displayReplicatedData(data, append) {
        try {
            var html = '<div style="display:flex;flex-direction:column;gap:12px">';
            // counts
            html += '<div style="display:flex;gap:12px;flex-wrap:wrap">';
            Object.keys(data.counts || {}).forEach(function (t) {
                html += '<div style="min-width:180px;padding:8px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
                html += '<strong>' + escapeHtml(t) + '</strong><div style="font-size:1.1rem;margin-top:6px">' + (data.counts[t] === null ? 'N/A' : escapeHtml(String(data.counts[t]))) + '</div>';
                html += '<div style="margin-top:8px"><button data-table="' + escapeHtml(t) + '" class="show-table" style="background:none;border:0;color:var(--xlerion-primary);cursor:pointer;padding:0">Ver filas</button></div>';
                html += '</div>';
            });
            html += '</div>';

            // backups
            html += '<div style="padding:8px;background:rgba(255,255,255,0.02);border-radius:6px;border:1px dashed rgba(255,255,255,0.04);">';
            html += '<h4>Backups recientes</h4>';
            if (data.backups && data.backups.length) {
                html += '<ul style="padding-left:16px">';
                data.backups.forEach(function (b) { html += '<li>' + escapeHtml(b.name || b) + ' — ' + escapeHtml(b.mtime || '') + '</li>'; });
                html += '</ul>';
            } else { html += '<p>No hay backups recientes.</p>'; }
            html += '</div>';

            html += '<div id="replicated-table-content" style="max-height:360px;overflow:auto;padding:6px;background:rgba(255,255,255,0.01);border-radius:6px;margin-top:6px"></div>';
            html += '</div>';

            if (append) {
                detailBody.insertAdjacentHTML('beforeend', html);
            } else {
                detailBody.innerHTML = html;
            }

            // wire show-table buttons to navigate into a table view using the already-fetched data
            Array.from(detailBody.querySelectorAll('.show-table')).forEach(function (btn) {
                // avoid attaching duplicate handlers
                if (btn.dataset._replicatedBound === '1') return;
                btn.dataset._replicatedBound = '1';
                btn.addEventListener('click', function (ev) {
                    var table = this.dataset.table;
                    var rows = (data.tables && data.tables[table]) || [];
                    try { showTableList(table, rows); } catch (e) { console.error('showTableList error', e); }
                });
            });
        } catch (e) { console.error('displayReplicatedData error', e); }
    }

    // Escape helper for inserting default values into HTML
    function escapeHtml(s) { return (s === null || s === undefined) ? '' : String(s).replace(/[&"'<>]/g, function (c) { return { '&': '&amp;', '"': '&quot;', "'": '&#39;', '<': '&lt;', '>': '&gt;' }[c]; }); }

    // Quick-create form renderer + CKEditor5 init
    function openCreateForm(prefill) {
        detailCard.style.display = '';
        detailTitle.textContent = 'Crear nueva página';
        var slugVal = prefill && prefill.slug ? prefill.slug : '';
        var titleVal = prefill && prefill.title ? prefill.title : '';
        var formHtml = '<form id="quick-create-form" method="post" action="/public/admin/save_page.php">' +
            '<input type="hidden" name="csrf_token" value="' + (window.XLERION_CSRF || '') + '">' +
            '<div><label>Slug<br><input name="slug" required value="' + escapeHtml(slugVal) + '"></label></div>' +
            '<div><label>Title<br><input name="title" required value="' + escapeHtml(titleVal) + '"></label></div>' +
            '<div><label>Content HTML<br><textarea id="quick-create-textarea" name="content" rows="6"></textarea></label></div>' +
            '<div style="margin-top:8px"><button type="submit">Guardar</button></div>' +
            '</form>';
        detailBody.innerHTML = formHtml;

        // initialize CKEditor5 on the quick-create textarea
        try {
            window.CKEDITOR5_EDITORS = window.CKEDITOR5_EDITORS || {};
            if (window.ClassicEditor && document.getElementById('quick-create-textarea')) {
                ClassicEditor.create(document.getElementById('quick-create-textarea'), { removePlugins: ['Elementspath', 'Resize'] })
                    .then(function (editor) { window.CKEDITOR5_EDITORS['quick-create'] = editor; })
                    .catch(function (err) { console.warn('CKEditor5 init error (quick-create)', err); });
            }
        } catch (e) { }

        var form = document.getElementById('quick-create-form');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            // sync editor data into textarea
            try {
                var ed = window.CKEDITOR5_EDITORS && window.CKEDITOR5_EDITORS['quick-create'];
                var ta = document.getElementById('quick-create-textarea');
                if (ed && ta) ta.value = ed.getData();
            } catch (err) { }

            var fd = new FormData(form);
            fetch('/public/admin/save_page.php', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (res) {
                var ct = res.headers.get('Content-Type') || '';
                if (ct.indexOf('application/json') !== -1) return res.json();
                return res.text();
            }).then(function (resp) {
                if (resp && resp.ok) {
                    detailTitle.textContent = 'Guardado';
                    detailBody.innerHTML = '<p>' + (resp.message || 'Página guardada') + '</p>';
                    if (resp.id) {
                        pages.push({ id: resp.id, slug: resp.slug || fd.get('slug'), title: resp.title || fd.get('title'), content: fd.get('content') });
                    }
                } else {
                    detailBody.innerHTML = '<p>Error al guardar.</p>' + (typeof resp === 'string' ? ('<pre>' + resp + '</pre>') : '');
                }
            }).catch(function (err) { detailBody.innerHTML = '<p>Error de red al guardar.</p>'; console.error(err); });
        });
    }

    function showDetail(p) {
        var server = document.getElementById('server-content'); if (server) server.style.display = 'none';
        detailCard.style.display = '';
        detailTitle.textContent = p.title || p.slug || ('Página ' + (p.id || ''));
        var html = '';
        html += '<p><strong>Slug:</strong> ' + (p.slug || '') + '</p>';
        html += '<p><strong>ID:</strong> ' + (p.id || '') + '</p>';
        html += '<div style="margin-top:8px"><strong>Contenido (vista previa):</strong><div style="padding:8px;background:#0b0b0b;color:#fff;border-radius:6px;margin-top:6px">' + (p.content ? p.content : '<em>Sin contenido</em>') + '</div></div>';

        // Modules area (will be populated via API)
        html += '<div id="modules-area" style="margin-top:12px">';
        html += '<strong>Módulos</strong>';
        html += '<div id="modules-list" style="margin-top:6px"><em>Cargando módulos…</em></div>';
        html += '<div style="margin-top:10px">' +
            '<form id="add-module-form">' +
            '<input type="hidden" name="csrf_token" value="' + (window.XLERION_CSRF || '') + '">' +
            '<input type="hidden" name="page_id" value="' + (p.id || '') + '">' +
            '<div><label>Tipo<br><input name="type" value="html"></label></div>' +
            '<div><label>Contenido<br><textarea id="module-content" name="content" rows="3"></textarea></label></div>' +
            '<div style="margin-top:6px"><button type="submit">Agregar módulo</button></div>' +
            '</form>' +
            '</div>';
        html += '</div>';
        html += '<div style="margin-top:12px"><a href="/public/admin/index.php?page=edit_page&slug=' + encodeURIComponent(p.slug || '') + '" style="color:var(--xlerion-primary)">Abrir editor completo</a></div>';
        detailBody.innerHTML = html;

        // after rendering, load modules for this page and wire add form
        loadModulesForPage(p.id);
        var addForm = document.getElementById('add-module-form');
        if (addForm) {
            addForm.addEventListener('submit', function (ev) {
                ev.preventDefault();
                // if CKEditor5 editor exists, sync data
                if (window.CKEDITOR5_EDITORS && window.CKEDITOR5_EDITORS['module-content']) {
                    try { var ed = window.CKEDITOR5_EDITORS['module-content']; var ta = document.getElementById('module-content'); if (ta) ta.value = ed.getData(); } catch (e) { }
                }
                var fd = new FormData(addForm);
                fetch('/api/modules.php', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (r) { return r.json(); }).then(function (resp) { if (resp && resp.ok) { loadModulesForPage(p.id); } else { alert('Error al crear módulo: ' + (resp && resp.error ? resp.error : 'unknown')); } }).catch(function (err) { console.error(err); alert('Error de red al crear módulo'); });
            });
        }

        // initialize CKEditor5 for module-content
        try {
            window.CKEDITOR5_EDITORS = window.CKEDITOR5_EDITORS || {};
            if (window.ClassicEditor && document.getElementById('module-content')) {
                ClassicEditor.create(document.getElementById('module-content'), { removePlugins: ['Elementspath', 'Resize'] })
                    .then(function (editor) { window.CKEDITOR5_EDITORS['module-content'] = editor; })
                    .catch(function (err) { console.warn('CKEditor5 init error', err); });
            }
        } catch (e) { }
    }

    // fetch pages (we keep them in memory and render on demand as cards)
    fetch('/api/pages.php').then(function (res) { if (!res.ok) throw new Error('API error'); return res.json(); }).then(function (data) { if (!data.ok) throw new Error(data.error || 'No data'); pages = data.pages || []; }).catch(function (err) { console.error('No se pudieron cargar las páginas:', err); });

    // Render main sections as cards in the detail panel
    function showMainSections() {
        detailCard.style.display = '';
        detailTitle.textContent = 'Secciones principales';
        var pageOrder = ['inicio', 'filosofia', 'soluciones', 'proyectos', 'documentacion', 'fundador', 'convocatorias', 'contacto', 'blog', 'legal'];
        // use crmSections defined at module level

        var html = '<div style="display:flex;flex-wrap:wrap;gap:12px">';
        pageOrder.forEach(function (slug) {
            var p = pages.find(function (x) { return x.slug === slug; });
            if (p) {
                var imgs = window.PARALLAX_IMAGES || [];
                var img = imgs.length ? imgs[pages.indexOf(p) % imgs.length] : null;
                html += '<div class="section-card" style="width:220px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
                if (img) html += '<div style="height:100px;border-radius:6px;overflow:hidden;margin-bottom:8px;background-image:url(' + img + ');background-size:cover;background-position:center;"></div>';
                html += '<h3 style="margin:0 0 6px 0">' + (p.title || p.slug) + '</h3>';
                html += '<p style="margin:0 0 8px 0;color:rgba(255,255,255,0.6);font-size:0.9em">Contenido editable</p>';
                html += '<p style="margin-top:8px"><button class="open-editor" data-slug="' + encodeURIComponent(p.slug) + '" style="background:none;border:0;color:var(--xlerion-primary);cursor:pointer;padding:0">Abrir editor</button></p>';
                html += '</div>';
            } else {
                var imgs = window.PARALLAX_IMAGES || [];
                var img = imgs.length ? imgs[pageOrder.indexOf(slug) % imgs.length] : null;
                html += '<div class="section-card" style="width:220px;padding:12px;background:rgba(255,255,255,0.02);border-radius:6px;border:1px dashed rgba(255,255,255,0.04);">';
                if (img) html += '<div style="height:100px;border-radius:6px;overflow:hidden;margin-bottom:8px;background-image:url(' + img + ');background-size:cover;background-position:center;"></div>';
                html += '<h3 style="margin:0 0 6px 0">' + slug + '</h3>';
                html += '<p style="margin:0 0 8px 0;color:rgba(255,255,255,0.5);font-size:0.9em">Página no creada</p>';
                html += '<p style="margin-top:8px"><button class="create-page" data-slug="' + encodeURIComponent(slug) + '" style="background:none;border:0;color:var(--xlerion-primary);cursor:pointer;padding:0">Crear página</button></p>';
                html += '</div>';
            }
        });

        crmSections.forEach(function (c, idx) {
            var imgs = window.PARALLAX_IMAGES || [];
            var img = imgs.length ? imgs[(pages.length + idx) % imgs.length] : null;
            html += '<div class="section-card" style="width:220px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
            if (img) html += '<div style="height:100px;border-radius:6px;overflow:hidden;margin-bottom:8px;background-image:url(' + img + ');background-size:cover;background-position:center;"></div>';
            html += '<h3 style="margin:0 0 6px 0">' + c.title + '</h3>';
            html += '<p style="margin:0 0 8px 0;color:rgba(255,255,255,0.6);font-size:0.9em">' + c.desc + '</p>';
            html += '<p style="margin-top:8px"><button class="open-crm" data-href="' + c.href + '" style="background:none;border:0;color:var(--xlerion-primary);cursor:pointer;padding:0">Abrir</button></p>';
            html += '</div>';
        });

        html += '</div>';
        detailBody.innerHTML = html;

        Array.from(detailBody.querySelectorAll('.open-editor')).forEach(function (btn) { btn.addEventListener('click', function (ev) { ev.preventDefault(); var slug = decodeURIComponent(this.dataset.slug || ''); var page = pages.find(function (x) { return x.slug === slug; }); if (page) { showDetail(page); } else { alert('Página no encontrada para editar: ' + slug); } }); });
        Array.from(detailBody.querySelectorAll('.create-page')).forEach(function (btn) { btn.addEventListener('click', function (ev) { ev.preventDefault(); var slug = decodeURIComponent(this.dataset.slug || ''); openCreateForm({ slug: slug, title: slug.replace(/[-_]/g, ' ').replace(/\b\w/g, function (m) { return m.toUpperCase(); }) }); }); });
        Array.from(detailBody.querySelectorAll('.open-crm')).forEach(function (btn) { btn.addEventListener('click', function (ev) { ev.preventDefault(); var href = this.dataset.href; detailCard.style.display = ''; detailTitle.textContent = 'Cargando…'; detailBody.innerHTML = '<iframe src="' + href + '" style="width:100%;height:720px;border:0;border-radius:6px"></iframe>'; }); });

        // Make clicking the whole section card act like clicking its inner action button
        detailBody.addEventListener('click', function (ev) {
            try {
                var card = ev.target.closest && ev.target.closest('.section-card');
                if (!card) return;
                // if user actually clicked a real actionable element, let its handler run
                if (ev.target.tagName === 'BUTTON' || ev.target.tagName === 'A' || ev.target.closest('button') || ev.target.closest('a')) return;
                // prefer editor, then create, then crm
                var actionBtn = card.querySelector('.open-editor, .create-page, .open-crm');
                if (actionBtn) { actionBtn.click(); }
            } catch (e) { /* ignore */ }
        });

        // expose openCreateForm globally for quick access
        try { window.openCreateForm = openCreateForm; } catch (e) { }
    }

    var showMainBtn = document.getElementById('show-main-sections');
    if (showMainBtn) { showMainBtn.addEventListener('click', function (ev) { ev.preventDefault(); showMainSections(); }); }

    // New: open CRM sections in the detail panel when sidebar button is clicked
    function showCRMSections() {
        detailCard.style.display = '';
        detailTitle.textContent = 'CRM - Secciones';
        var html = '<div style="display:flex;flex-wrap:wrap;gap:12px">';
        var imgs = window.PARALLAX_IMAGES || [];
        crmSections.forEach(function (c, idx) {
            var img = imgs.length ? imgs[idx % imgs.length] : null;
            html += '<div class="section-card" style="width:220px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
            if (img) html += '<div style="height:100px;border-radius:6px;overflow:hidden;margin-bottom:8px;background-image:url(' + img + ');background-size:cover;background-position:center;"></div>';
            html += '<h3 style="margin:0 0 6px 0">' + c.title + '</h3>';
            html += '<p style="margin:0 0 8px 0;color:rgba(255,255,255,0.6);font-size:0.9em">' + c.desc + '</p>';
            html += '<p style="margin-top:8px"><button class="open-crm" data-href="' + c.href + '" style="background:none;border:0;color:var(--xlerion-primary);cursor:pointer;padding:0">Abrir</button></p>';
            html += '</div>';
        });
        html += '</div>';
        detailBody.innerHTML = html;
        Array.from(detailBody.querySelectorAll('.open-crm')).forEach(function (btn) {
            btn.addEventListener('click', function (ev) { ev.preventDefault(); var href = this.dataset.href; detailCard.style.display = ''; detailTitle.textContent = 'Cargando…'; detailBody.innerHTML = '<iframe src="' + href + '" style="width:100%;height:720px;border:0;border-radius:6px"></iframe>'; });
        });
    }

    var openCrmBtn = document.getElementById('open-crm-panel');
    if (openCrmBtn) { openCrmBtn.addEventListener('click', function (ev) { ev.preventDefault(); showCRMSections(); }); }

    // Dashboard: fetch and render CRM/local vs remote stats
    var openDashboardBtn = document.getElementById('open-dashboard');
    function showDashboard() {
        detailCard.style.display = '';
        detailTitle.textContent = 'Dashboard — Estadísticas';
        detailBody.innerHTML = '<p>Cargando estadísticas…</p>';
        fetch('/admin/api/dashboard_stats.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (res) {
            if (!res.ok) throw new Error('API error: ' + res.status + ' ' + res.statusText);
            var ct = (res.headers.get('Content-Type') || '').toLowerCase();
            if (ct.indexOf('application/json') !== -1) return res.json();
            // non-json response: return text for debugging
            return res.text().then(function (txt) { throw new Error('Non-JSON response:\n' + txt); });
        }).then(function (data) {
            if (!data || !data.ok) throw new Error(data && data.error ? data.error : 'Sin datos');
            var html = '';
            html += '<div style="display:flex;gap:12px;flex-wrap:wrap">';
            // summary cards
            html += '<div style="flex:1;min-width:240px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
            html += '<h3>Tablas (local)</h3>';
            html += '<ul style="padding-left:16px">';
            Object.keys(data.local || {}).forEach(function (t) { html += '<li><strong><a href="#" class="open-table" data-table="' + escapeHtml(t) + '" style="color:var(--xlerion-primary)">' + escapeHtml(t) + '</a>:</strong> ' + (data.local[t] || 0) + '</li>'; });
            html += '</ul>';
            html += '</div>';

            html += '<div style="flex:1;min-width:240px;padding:12px;background:rgba(255,255,255,0.02);border-radius:6px;border:1px dashed rgba(255,255,255,0.04);">';
            html += '<h3>Tablas (remote snapshot)</h3>';
            html += '<ul style="padding-left:16px">';
            Object.keys(data.remote || {}).forEach(function (t) { html += '<li><strong>' + escapeHtml(t) + ':</strong> ' + (data.remote[t] || 0) + '</li>'; });
            html += '</ul>';
            html += '</div>';

            html += '<div style="flex-basis:100%;height:1px"></div>';

            // mismatches
            html += '<div style="flex:1 1 480px;padding:12px;background:rgba(0,0,0,0.25);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
            html += '<h3>Discrepancias</h3>';
            if (data.mismatches && data.mismatches.length) {
                html += '<ul style="padding-left:16px">';
                data.mismatches.forEach(function (m) { html += '<li><strong>' + m.table + ':</strong> ' + (m.local || 0) + ' vs ' + (m.remote || 0) + '</li>'; });
                html += '</ul>';
                html += '<p style="margin-top:8px"><a href="/public/admin/migrations_review.php" style="color:var(--xlerion-primary)">Revisar migraciones sugeridas</a></p>';
            } else {
                html += '<p>No se detectaron discrepancias entre el snapshot remoto y la base local.</p>';
            }
            html += '</div>';

            // backups
            html += '<div style="flex:1;min-width:240px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">';
            html += '<h3>Backups recientes</h3>';
            if (data.backups && data.backups.length) {
                html += '<ul style="padding-left:16px">';
                data.backups.forEach(function (b) { html += '<li>' + b + '</li>'; });
                html += '</ul>';
            } else {
                html += '<p>No hay backups recientes.</p>';
            }
            html += '</div>';

            html += '</div>';
            detailBody.innerHTML = html;

            // attach click handlers to local table links (loads rows via replicated_data.php and navigates)
            try {
                Array.from(detailBody.querySelectorAll('.open-table')).forEach(function (a) {
                    a.addEventListener('click', function (ev) {
                        ev.preventDefault();
                        var table = this.dataset.table;
                        if (!table) return;
                        detailCard.style.display = '';
                        detailTitle.textContent = 'Cargando ' + table + '…';
                        detailBody.innerHTML = '<p>Cargando datos de ' + escapeHtml(table) + '…</p>';
                        fetch('/admin/api/replicated_data.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (res) {
                            if (!res.ok) throw new Error('API error ' + res.status);
                            return res.json();
                        }).then(function (rd) {
                            if (!rd || !rd.ok) throw new Error(rd && rd.error ? rd.error : 'No data');
                            var rows = (rd.tables && rd.tables[table]) || [];
                            showTableList(table, rows);
                        }).catch(function (err) {
                            detailBody.innerHTML = '<p>Error cargando tabla.</p><pre>' + escapeHtml(err && err.message ? err.message : String(err)) + '</pre>';
                            console.error(err);
                        });
                    });
                });
            } catch (e) { console.error('attach open-table handlers failed', e); }

            // Also fetch and display replicated local snapshot (same UI as user-info)
            fetch('/admin/api/replicated_data.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (res) {
                if (!res.ok) throw new Error('API error ' + res.status);
                return res.json();
            }).then(function (rep) {
                if (!rep || !rep.ok) return; // non-critical
                try { displayReplicatedData(rep, true); } catch (e) { console.error('displayReplicatedData failed', e); }
            }).catch(function (e) { /* non-fatal: ignore */ });
        }).catch(function (err) { detailBody.innerHTML = '<p>Error cargando estadísticas.</p><pre>' + (err && err.message ? err.message : String(err)) + '</pre>'; console.error(err); });
    }
    if (openDashboardBtn) { openDashboardBtn.addEventListener('click', function (ev) { ev.preventDefault(); showDashboard(); }); }

    // clicking the user info will show replicated local data (copied from remote)
    var adminUserInfo = document.getElementById('admin-user-info');
    if (adminUserInfo) {
        adminUserInfo.addEventListener('click', function (ev) {
            ev.preventDefault();
            detailCard.style.display = '';
            detailTitle.textContent = 'Datos replicados (local)';
            detailBody.innerHTML = '<p>Cargando datos replicados…</p>';
            fetch('/admin/api/replicated_data.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (res) {
                if (!res.ok) throw new Error('API error ' + res.status);
                return res.json();
            }).then(function (data) {
                if (!data || !data.ok) throw new Error(data && data.error ? data.error : 'No data');
                displayReplicatedData(data, false);
            }).catch(function (err) { detailBody.innerHTML = '<p>Error cargando datos replicados.</p><pre>' + escapeHtml(err && err.message ? err.message : String(err)) + '</pre>'; console.error(err); });
        });
    }
    // Fallback: intercept sidebar links that point to CRM pages and open them in the detail panel
    // This ensures anchors in the sidebar don't navigate away and behave consistently inside the admin panel.
    try {
        var sidebarLinks = document.querySelectorAll('.admin-sidebar a[href*="/public/admin/crm/"]');
        Array.from(sidebarLinks).forEach(function (a) {
            // avoid double-binding if already processed
            if (a.dataset.crmfallback === '1') return;
            a.dataset.crmfallback = '1';
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                var href = this.getAttribute('href');
                if (!href) return;
                try { detailCard.style.display = ''; detailTitle.textContent = 'Cargando…'; detailBody.innerHTML = '<iframe src="' + href + '" style="width:100%;height:720px;border:0;border-radius:6px"></iframe>'; } catch (e) { window.location.href = href; }
            });
        });
    } catch (e) { console.warn('CRM sidebar fallback init failed', e); }

    // Load modules for a page and render into #modules-list
    function loadModulesForPage(pageId) {
        var container = document.getElementById('modules-list'); if (!container) return; container.innerHTML = '<em>Cargando módulos…</em>';
        fetch('/api/modules.php?page_id=' + encodeURIComponent(pageId)).then(function (res) { if (!res.ok) throw new Error('API error'); return res.json(); }).then(function (data) { if (!data.ok) throw new Error(data.error || 'No data'); renderModules(container, data.modules || []); }).catch(function (err) { container.innerHTML = '<p>Error cargando módulos.</p>'; console.error(err); });
    }

    function renderModules(container, modules) {
        if (!modules || modules.length === 0) { container.innerHTML = '<p><em>Sin módulos para esta página.</em></p>'; return; }
        var html = '<ul id="modules-ul">';
        modules.forEach(function (m) {
            html += '<li draggable="true" data-id="' + m.id + '" style="padding:6px;border:1px solid rgba(255,255,255,0.04);margin-bottom:6px;background:rgba(0,0,0,0.2)">' +
                '<strong>' + (m.type || 'module') + '</strong>: ' + (m.content ? m.content : '') + ' <small style="color:rgba(255,255,255,0.6)">(#' + m.id + ')</small>' +
                ' <a href="#" class="delete-module" data-id="' + m.id + '" style="margin-left:8px;color:#ff8888">Eliminar</a>' +
                '</li>';
        });
        html += '</ul>';
        container.innerHTML = html;
        var ul = document.getElementById('modules-ul'); var dragSrcEl = null;
        function handleDragStart(e) { this.style.opacity = '0.4'; dragSrcEl = this; e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', this.dataset.id); }
        function handleDragOver(e) { if (e.preventDefault) e.preventDefault(); e.dataTransfer.dropEffect = 'move'; return false; }
        function handleDragEnter(e) { this.classList.add('over'); }
        function handleDragLeave(e) { this.classList.remove('over'); }
        function handleDrop(e) { if (e.stopPropagation) e.stopPropagation(); var srcId = e.dataTransfer.getData('text/plain'); if (dragSrcEl != this) { ul.insertBefore(dragSrcEl, this); persistModuleOrder(ul); } return false; }
        function handleDragEnd(e) { this.style.opacity = '1'; Array.from(ul.querySelectorAll('li')).forEach(function (li) { li.classList.remove('over'); }); }
        Array.from(ul.querySelectorAll('li')).forEach(function (li) { li.addEventListener('dragstart', handleDragStart, false); li.addEventListener('dragenter', handleDragEnter, false); li.addEventListener('dragover', handleDragOver, false); li.addEventListener('dragleave', handleDragLeave, false); li.addEventListener('drop', handleDrop, false); li.addEventListener('dragend', handleDragEnd, false); });
        function persistModuleOrder(ulEl) { var items = Array.from(ulEl.querySelectorAll('li')); items.forEach(function (li, idx) { var id = li.dataset.id; var body = 'id=' + encodeURIComponent(id) + '&order=' + encodeURIComponent(idx) + '&csrf_token=' + encodeURIComponent(window.XLERION_CSRF || ''); fetch('/api/modules.php', { method: 'PUT', body: body, headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }).then(function (r) { r.json().then(function (res) { if (!res.ok) console.warn('Order update failed', res); }).catch(function () { }); }).catch(function (err) { console.error('Order update error', err); }); }); }
    }

    // delegate delete clicks
    document.addEventListener('click', function (ev) {
        var t = ev.target;
        if (t && t.classList && t.classList.contains('delete-page')) {
            ev.preventDefault(); var pageId = t.dataset.id; if (!pageId) return; if (!confirm('Confirmar eliminación de la página #' + pageId + '?')) return;
            var fd = new FormData(); fd.append('id', pageId); fd.append('csrf_token', window.XLERION_CSRF || '');
            fetch('/public/admin/delete_page.php', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (r) { r.json().then(function (res) { if (res && res.ok) { var li = t.closest('li'); if (li) li.remove(); if (detailTitle.textContent.indexOf('#' + pageId) !== -1 || detailTitle.textContent === res.slug) { detailTitle.textContent = 'Página eliminada'; detailBody.innerHTML = '<p>' + (res.message || 'Eliminada') + '</p>'; } } else { alert('Error al eliminar: ' + (res && res.error ? res.error : 'unknown')); } }).catch(function () { alert('Error de respuesta al eliminar'); }); }).catch(function (err) { console.error(err); alert('Error de red al eliminar'); });
        }

        if (t && t.classList && t.classList.contains('delete-module')) {
            ev.preventDefault(); var moduleId = t.dataset.id; if (!moduleId) return; if (!confirm('Confirmar eliminación del módulo #' + moduleId + '?')) return;
            var body = 'id=' + encodeURIComponent(moduleId) + '&csrf_token=' + encodeURIComponent(window.XLERION_CSRF || '');
            fetch('/api/modules.php', { method: 'DELETE', body: body, headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }).then(function (r) { r.json().then(function (res) { if (res && res.ok) { var li = t.closest('li'); if (li) li.remove(); } else { alert('Error al eliminar módulo: ' + (res && res.error ? res.error : 'unknown')); } }).catch(function () { alert('Error de respuesta al eliminar módulo'); }); }).catch(function (err) { console.error(err); alert('Error de red al eliminar módulo'); });
        }
    });

    try { window.showMainSections = showMainSections; } catch (e) { }
})();

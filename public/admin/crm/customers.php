<?php
require_once __DIR__ . '/../../../includes/config.php';
require_login();
require_once __DIR__ . '/../../../includes/header.php';
?>
<main style="padding:24px;">
  <h1>Clientes / Contactos</h1>
  <p>Lista de clientes. Usa el API en <code>/api/crm/customers.php</code>.</p>

  <div style="margin-bottom:16px;">
    <form id="new-customer-form">
      <input type="text" name="name" placeholder="Nombre" required>
      <input type="email" name="email" placeholder="Email">
      <select name="status"><option value="active">Activo</option><option value="lead">Lead</option><option value="inactive">Inactivo</option></select>
      <button type="submit" class="xlerion-btn-primary">Crear</button>
    </form>
  </div>

  <div id="crm-customers">
    <p>Cargando...</p>
  </div>

  <script>
    const CSRF_TOKEN = '<?php echo htmlspecialchars(get_csrf_token(), ENT_QUOTES); ?>';
    async function loadCustomers() {
      const r = await fetch('/api/crm/customers.php');
      const j = await r.json();
      const el = document.getElementById('crm-customers');
      if (!j.ok) { el.innerHTML = '<div class="alert alert-danger">Error cargando clientes</div>'; return; }
      if (!j.data || j.data.length === 0) { el.innerHTML = '<div>No hay clientes registrados.</div>'; return; }
      let html = '<table class="table"><thead><tr><th>Id</th><th>Nombre</th><th>Email</th><th>Status</th><th>Acciones</th></tr></thead><tbody>';
      for (const c of j.data) {
        html += `<tr><td>${c.id}</td><td>${c.name}</td><td>${c.email||''}</td><td>${c.status||''}</td><td><button data-id="${c.id}" class="btn btn-sm btn-danger crm-delete">Eliminar</button></td></tr>`;
      }
      html += '</tbody></table>';
      el.innerHTML = html;
      document.querySelectorAll('.crm-delete').forEach(btn=>btn.addEventListener('click', async e=>{
        if (!confirm('Eliminar este cliente?')) return;
        const id = btn.dataset.id;
        const res = await fetch('/api/crm/customers.php', {method:'DELETE', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF_TOKEN}, body: JSON.stringify({id: id})});
        const out = await res.json(); if (out.ok) loadCustomers(); else alert('Error: '+(out.error||''));
      }));
    }

    document.getElementById('new-customer-form').addEventListener('submit', async function(e){
      e.preventDefault();
      const fd = new FormData(this);
      const obj = {};
      fd.forEach((v,k)=>obj[k]=v);
      obj.csrf_token = CSRF_TOKEN;
      const res = await fetch('/api/crm/customers.php', {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF_TOKEN}, body: JSON.stringify(obj)});
      const j = await res.json(); if (j.ok) { this.reset(); loadCustomers(); } else alert('Error: '+(j.error||''));
    });

    loadCustomers().catch(e=>console.error(e));
  </script>
</main>
<?php require_once __DIR__ . '/../../../includes/footer.php';

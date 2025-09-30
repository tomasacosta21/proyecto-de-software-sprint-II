<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ABM Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
    background: #f6f8fa;
    font-family: Inter, system-ui, Arial;
     }

    .app-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 6px 22px rgba(15,23,42,0.06);
    }
    .table thead th {
    border-bottom: 2px solid #eef2f7;
    }
    .table tbody tr:hover {
    background: #fbfdff;
    }
    .footer-actions {
    display:flex;
    gap:10px;
    justify-content:flex-end;
    margin-top: 12px;
    }
    .small-muted { font-size:0.9rem;
     color:#6b7280;
     }
    .radio-cell { width:48px;
    text-align:center;
    }
    .btn-primary { --bs-btn-bg: #0ea5a0; }
    .btn-danger { --bs-btn-bg: #ef4444; }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="app-card mx-auto" style="max-width:1100px;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h3 class="mb-0">Usuarios</h3>
          <div class="small-muted">Gestiona usuarios y sus roles</div>
        </div>
        <!-- Me parece inecesario tener dos botones de agregar
        <div>
          <button id="btnAddTop" class="btn btn-success">+ Agregar usuario</button>
        </div>-->
      </div>

      <!-- Alert placeholder -->
      <div id="alertPlaceholder"></div>

      <!-- Tabla -->
      <div class="table-responsive">
        <table class="table align-middle" id="usersTable">
          <thead>
            <tr>
              <th class="radio-cell"></th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Nickname</th>
              <th>Email</th>
              <th>Rol</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody id="usersTableBody">
            <!-- filas generadas por JS -->
          </tbody>
        </table>
        <div id="emptyMessage" class="p-3 small-muted" style="display:none;">No hay usuarios cargados.</div>
      </div>

      <!-- Footer con botones (pie de la tabla) -->
      <div class="footer-actions mt-2">
        <button id="btnAgregar" class="btn btn-primary">Agregar</button>
        <button id="btnEditar" class="btn btn-outline-primary">Editar seleccionado</button>
        <button id="btnEliminar" class="btn btn-outline-danger">Eliminar seleccionado</button>
      </div>
    </div>
  </div>

  <!-- Modal: Crear / Editar -->
  <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form id="userForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle">Crear usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="userId">
          <div class="mb-2">
            <label class="form-label">Nombre</label>
            <input id="nombre" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Apellido</label>
            <input id="apellido" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Nickname</label>
            <input id="nickname" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input id="email" type="email" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Rol</label>
            <select id="rol_id" class="form-select" required>
              <option value="">-- seleccionar --</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success" id="userFormSubmit">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal: Confirmar (Eliminar) -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <p id="confirmText" class="mb-3">¿Confirmás la acción?</p>
          <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button id="confirmYes" class="btn btn-danger">Sí, eliminar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS bundle (Popper incluido) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  // --- Config & estado ---
  let users = [];
  let roles = [];
  let selectedUserId = null;
  let userModal, confirmModal;

  document.addEventListener('DOMContentLoaded', () => {
    userModal = new bootstrap.Modal(document.getElementById('userModal'));
    confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    // botones
    document.getElementById('btnAgregar').addEventListener('click', () => openUserModal('create'));
    //document.getElementById('btnAddTop').addEventListener('click', () => openUserModal('create')); Este es el boton comentado en la parte superior
    document.getElementById('btnEditar').addEventListener('click', () => openUserModal('editSelected'));
    document.getElementById('btnEliminar').addEventListener('click', () => confirmAction('deleteSelected'));

    // form submit
    document.getElementById('userForm').addEventListener('submit', submitUserForm);

    // confirm yes
    document.getElementById('confirmYes').addEventListener('click', () => {
      confirmModal.hide();
      if (confirmModal._action === 'delete') deleteUser(confirmModal._id);
    });

    loadRoles();
    loadUsers();
  });

  // --- Helpers: alert ---
  function showAlert(message, type = 'success') {
    const id = 'a' + Date.now();
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `<div id="${id}" class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    document.getElementById('alertPlaceholder').append(wrapper);
    setTimeout(()=>{ const el = document.getElementById(id); if(el) bootstrap.Alert.getOrCreateInstance(el).close(); }, 4500);
  }

  // --- Cargar roles (para select) ---
  async function loadRoles() {
    try {
      const res = await fetch('usuarios/crear.php?format=json');
      roles = await res.json();
      const select = document.getElementById('rol_id');
      select.innerHTML = '<option value="">-- seleccionar --</option>';
      roles.forEach(r => select.innerHTML += `<option value="${r.id_rol}">${escapeHtml(r.nombre)}</option>`);
    } catch (e) { console.error(e); showAlert('No se pudieron cargar roles','danger'); }
  }

  // --- Cargar usuarios y renderizar tabla ---
  async function loadUsers() {
    try {
      const res = await fetch('usuarios/lista.php?format=json');
      users = await res.json();
      renderTable();
    } catch (e) {
      console.error(e); showAlert('Error al cargar usuarios','danger');
    }
  }

  function renderTable() {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '';
    if (users.length === 0) {
      document.getElementById('emptyMessage').style.display = 'block';
    } else {
      document.getElementById('emptyMessage').style.display = 'none';
    }
    users.forEach(u => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="radio-cell"><input type="radio" name="selectedUser" value="${u.id_usuario}" ${selectedUserId==u.id_usuario?'checked':''}></td>
        <td>${escapeHtml(u.nombre)}</td>
        <td>${escapeHtml(u.apellido)}</td>
        <td>${escapeHtml(u.nickname)}</td>
        <td>${escapeHtml(u.email)}</td>
        <td>${escapeHtml(u.rol ?? '')}</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-primary me-1" data-action="edit" data-id="${u.id_usuario}">Editar</button>
          <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${u.id_usuario}">Eliminar</button>
        </td>
      `;
      tbody.appendChild(tr);

      // radio listener
      tr.querySelector('input[type=radio]').addEventListener('change', (e) => {
        selectedUserId = Number(e.target.value);
      });

      // per-row buttons
      tr.querySelector('[data-action="edit"]').addEventListener('click', () => openUserModal('edit', u.id_usuario));
      tr.querySelector('[data-action="delete"]').addEventListener('click', () => confirmAction('delete', u.id_usuario));
    });
  }

  // --- Abrir modal de usuario (create | edit | editSelected) ---
  async function openUserModal(mode, id = null) {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    if (mode === 'create') {
      document.getElementById('userModalTitle').textContent = 'Crear usuario';
      document.getElementById('userFormSubmit').textContent = 'Crear';
      userModal.show();
      return;
    }

    // editSelected: usar selectedUserId
    if (mode === 'editSelected') {
      if (!selectedUserId) { showAlert('Seleccioná un usuario para editar','warning'); return; }
      id = selectedUserId;
    }

    // cargar datos del usuario y abrir modal
    try {
      const res = await fetch(`usuarios/editar.php?id=${id}`);
      const text = await res.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(text, 'text/html');

      // Extraer datos del formulario HTML
      const form = doc.querySelector('form');
      if (!form) {
        showAlert('Usuario no encontrado','danger');
        return;
      }

      document.getElementById('userModalTitle').textContent = 'Editar usuario';
      document.getElementById('userFormSubmit').textContent = 'Guardar cambios';
      document.getElementById('userId').value = id;
      document.getElementById('nombre').value = form.querySelector('input[name="nombre"]')?.value || '';
      document.getElementById('apellido').value = form.querySelector('input[name="apellido"]')?.value || '';
      document.getElementById('nickname').value = form.querySelector('input[name="nickname"]')?.value || '';
      document.getElementById('email').value = form.querySelector('input[name="email"]')?.value || '';
      document.getElementById('rol_id').value = form.querySelector('select[name="rol_id"]')?.value || '';
      userModal.show();
    } catch (e) { console.error(e); showAlert('Error al cargar usuario','danger'); }
  }

  // --- Confirm modal ---
  function confirmAction(action, id = null) {
    if (action === 'delete') {
      if (!id && !selectedUserId) { showAlert('Seleccioná un usuario para eliminar','warning'); return; }
      confirmModal._action = 'delete';
      confirmModal._id = id || selectedUserId;
      document.getElementById('confirmText').textContent = '¿Estás seguro que querés eliminar este usuario? Esta acción no se puede deshacer.';
      confirmModal.show();
    }
  }

  // --- Submit create / update ---
  async function submitUserForm(evt) {
    evt.preventDefault();
    const id = document.getElementById('userId').value || null;
    const formData = new FormData();
    formData.append('nombre', document.getElementById('nombre').value.trim());
    formData.append('apellido', document.getElementById('apellido').value.trim());
    formData.append('nickname', document.getElementById('nickname').value.trim());
    formData.append('email', document.getElementById('email').value.trim());
    formData.append('rol_id', document.getElementById('rol_id').value || '');

    // Validaciones simples
    if (!formData.get('nombre') || !formData.get('apellido') || !formData.get('nickname') || !formData.get('email') || !formData.get('rol_id')) {
      showAlert('Completá todos los campos obligatorios','warning');
      return;
    }

    try {
      const url = id ? `usuarios/editar.php?id=${id}` : 'usuarios/crear.php';
      const res = await fetch(url, {
        method: 'POST',
        body: formData
      });
      const text = await res.text();

      // Verificar si la respuesta contiene mensajes de éxito o error
      if (text.includes('✅') || text.includes('exitosamente') || text.includes('creado') || text.includes('actualizado')) {
        userModal.hide();
        showAlert(id ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente');
        await loadUsers();
      } else if (text.includes('Error') || text.includes('❌')) {
        showAlert('Error al procesar la solicitud','danger');
      } else {
        // Si no hay mensaje claro, asumir éxito
        userModal.hide();
        showAlert(id ? 'Usuario actualizado' : 'Usuario creado');
        await loadUsers();
      }
    } catch (e) { console.error(e); showAlert('Error al guardar','danger'); }
  }

  // --- Delete user ---
  async function deleteUser(id) {
    try {
      const res = await fetch(`usuarios/eliminar.php?id=${id}`, { method: 'GET' });
      const text = await res.text();

      // Verificar si la respuesta contiene mensajes de éxito o error
      if (text.includes('✅') || text.includes('eliminado') || text.includes('exitosamente')) {
        showAlert('Usuario eliminado exitosamente');
        selectedUserId = null;
        await loadUsers();
      } else if (text.includes('Error') || text.includes('❌')) {
        showAlert('Error al eliminar usuario','danger');
      } else {
        // Si no hay mensaje claro, asumir éxito
        showAlert('Usuario eliminado');
        selectedUserId = null;
        await loadUsers();
      }
    } catch (e) { console.error(e); showAlert('Error al eliminar','danger'); }
  }

  // --- Util: escapar html ---
  function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#039;');
  }
  </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Usuarios — Decomobil</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../styles/style.css">
</head>
<body>

<!-- ═══ NAVBAR ═══ -->
<nav id="mainNav">
  <a class="nav-brand" href="home.php">
    <div class="nav-logo">DC</div>
    <span class="nav-name">DECOMOBIL</span>
  </a>
  <div class="nav-sep"></div>
  <div id="navLinks">
    <a class="nav-link" href="home.php">🏠 Inicio</a>
    <a class="nav-link" href="new.php">🎫 Nuevo Ticket</a>
    <a class="nav-link" href="company.php">🏢 Nosotros</a>
    <a class="nav-link" href="org.php">🗂 Organigrama</a>
    <a class="nav-link" href="admin.php" data-role="agent">⚙️ Administración</a>
    <a class="nav-link active" href="users.php" data-role="admin">👥 Usuarios</a>
    <a class="nav-link" href="metrics.php" data-role="admin">📊 Métricas</a>
  </div>
  <div id="navRight"></div>
</nav>

<!-- ═══ CONTENIDO PRINCIPAL ═══ -->
<div class="page">
  <div id="usersContent" style="flex:1;overflow-y:auto;padding-top:60px;margin:auto;width:100%"></div>
</div>

<!-- ═══ MODAL: CREAR / EDITAR USUARIO ═══ -->
<div class="overlay" id="userModal" onclick="if(event.target===this)closeModal('userModal')">
  <div class="modal modal-wide">
    <div class="mh">
      <span class="mt" id="userModalTitle">Usuario</span>
      <div class="mc" onclick="closeModal('userModal')">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 16 16">
          <line x1="3" y1="3" x2="13" y2="13"/><line x1="13" y1="3" x2="3" y2="13"/>
        </svg>
      </div>
    </div>
    <div class="mb" id="userModalBody"></div>
    <div class="mf">
      <button class="btn btn-ghost" onclick="closeModal('userModal')">Cancelar</button>
      <button class="btn btn-primary" id="btnSaveUser" onclick="saveUser()">Guardar cambios</button>
    </div>
  </div>
</div>

<!-- ═══ MODAL: CAMBIAR CONTRASEÑA ═══ -->
<div class="overlay" id="changePassModal" onclick="if(event.target===this)closeModal('changePassModal')">
  <div class="modal" style="max-width:420px">
    <div class="mh">
      <span class="mt" id="cpModalTitle">Cambiar contraseña</span>
      <div class="mc" onclick="closeModal('changePassModal')">✕</div>
    </div>
    <div class="mb">
      <input type="hidden" id="cp-uid">
      <div style="background:var(--amber-light);border:1px solid #FDE68A;border-radius:7px;padding:10px 14px;font-size:13px;color:var(--amber);margin-bottom:4px">
        ⚠ Esta acción cambiará la contraseña del usuario inmediatamente.
      </div>
      <div class="fg">
        <label class="fl">Nueva contraseña <span class="r">*</span></label>
        <input class="fi" id="cp-new" type="password" placeholder="Mínimo 6 caracteres"
          onkeydown="if(event.key==='Enter')document.getElementById('cp-confirm').focus()">
      </div>
      <div class="fg">
        <label class="fl">Confirmar contraseña <span class="r">*</span></label>
        <input class="fi" id="cp-confirm" type="password" placeholder="Repite la contraseña"
          onkeydown="if(event.key==='Enter')saveChangePass()">
      </div>
      <div class="fe" id="cp-err" style="display:none;font-size:12.5px;padding:8px 12px;background:var(--red-light);border-radius:6px;border-left:3px solid var(--red)"></div>
    </div>
    <div class="mf">
      <button class="btn btn-ghost" onclick="closeModal('changePassModal')">Cancelar</button>
      <button class="btn btn-primary" id="btnSavePass" onclick="saveChangePass()">🔑 Cambiar contraseña</button>
    </div>
  </div>
</div>

<!-- ═══ MODAL: CONFIRMAR ELIMINACIÓN ═══ -->
<div class="overlay" id="confirmUserDelete" onclick="if(event.target===this)closeModal('confirmUserDelete')">
  <div class="modal" style="max-width:380px">
    <div class="mh">
      <span class="mt">⚠ Eliminar usuario</span>
      <div class="mc" onclick="closeModal('confirmUserDelete')">✕</div>
    </div>
    <div class="mb">
      <p style="font-size:13.5px;color:var(--ink2);line-height:1.6">
        ¿Eliminar este usuario? Sus tickets se mantendrán pero ya no podrá iniciar sesión.
      </p>
    </div>
    <div class="mf">
      <button class="btn btn-ghost" onclick="closeModal('confirmUserDelete')">Cancelar</button>
      <button class="btn btn-danger" id="btnConfirmDel" onclick="confirmUserDelete()">Sí, eliminar</button>
    </div>
  </div>
</div>

<div id="toastContainer"></div>

<script>
/* ══════════════════════════════════════════════
   DECOMOBIL — users.php
   Gestión de usuarios vía API real
   ══════════════════════════════════════════════ */

// ── Ruta base de la API (ajustar según despliegue) ──
const API_BASE = '../xampp_project/api';

// ── Estado local ──
let editUserId   = null;   // ID del usuario en edición (null = nuevo)
let pendingDelUser = null; // ID pendiente de eliminación

// ── Helpers generales ──
const $  = id => document.getElementById(id);
const $$ = s  => document.querySelectorAll(s);
const getInitials = name => String(name || '').split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();

function formatDate(d) {
  if (!d) return '—';
  const dt = new Date(d + 'T12:00:00');
  const m  = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  return `${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear()}`;
}

function roleBadge(r) {
  const m = { admin: ['b-critical','👑 Admin'], agent: ['b-progress','🛠 Agente'], user: ['b-open','👤 Usuario'] };
  const [c, l] = m[r] || ['b-pending', 'Desconocido'];
  return `<span class="badge ${c}">${l}</span>`;
}

function showToast(msg, type = '', icon = '') {
  const c = $('toastContainer');
  const icons = { success: '✓', error: '✕', warning: '⚠' };
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<span style="font-size:15px">${icon || icons[type] || 'ℹ'}</span><span>${msg}</span>`;
  c.appendChild(el);
  setTimeout(() => {
    el.style.opacity = '0';
    el.style.transform = 'translateX(20px) scale(.95)';
    el.style.transition = 'all .3s';
    setTimeout(() => el.remove(), 300);
  }, 3200);
}

function openModal(id)  { $(id).classList.add('open'); }
function closeModal(id) { $(id).classList.remove('open'); }

// ── Obtener token de sesión ──
function getAuthToken() {
  return sessionStorage.getItem('auth_token') || '';
}

// ── Headers comunes para todas las peticiones a la API ──
function apiHeaders(extra = {}) {
  return {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + getAuthToken(),
    ...extra
  };
}

/* ══════════════════════════════════════════════
   INICIALIZACIÓN Y NAVEGACIÓN
   ══════════════════════════════════════════════ */

(function initNav() {
  const sessionData = sessionStorage.getItem('user_session');
  const authToken   = sessionStorage.getItem('auth_token');

  // Sin sesión válida → login
  if (!sessionData || !authToken) {
    window.location.href = 'login.php';
    return;
  }

  const s = JSON.parse(sessionData);

  // Ocultar enlaces según rol
  document.querySelectorAll('[data-role]').forEach(el => {
    const r  = el.dataset.role;
    const ok = (r === 'agent' && (s.role === 'agent' || s.role === 'admin')) ||
               (r === 'admin' && s.role === 'admin');
    if (!ok) el.style.display = 'none';
  });

  // Pill de usuario en navbar
  $('navRight').innerHTML = `
    <div class="nav-user">
      <div class="avatar" style="width:28px;height:28px;background:${s.avatar};font-size:10px">${getInitials(s.name)}</div>
      <span style="font-size:13px;font-weight:600;color:rgba(255,255,255,.85)">${s.name.split(' ')[0]}</span>
    </div>
    <button class="nav-btn nb-ghost" onclick="logout()">Salir</button>
  `;
})();

function logout() {
  sessionStorage.removeItem('auth_token');
  sessionStorage.removeItem('user_session');
  window.location.href = 'login.php';
}

// Tecla Escape cierra modales
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.overlay').forEach(o => o.classList.remove('open'));
  }
});

/* ══════════════════════════════════════════════
   CARGA INICIAL — solo admins pueden ver esta página
   ══════════════════════════════════════════════ */

window.addEventListener('DOMContentLoaded', () => {
  const sessionData = sessionStorage.getItem('user_session');
  if (!sessionData) { window.location.href = 'login.php'; return; }
  const s = JSON.parse(sessionData);
  if (s.role !== 'admin') { window.location.href = 'home.php'; return; }
  loadUsers();
});

/* ══════════════════════════════════════════════
   API: LISTAR USUARIOS
   GET /api/users.php
   ══════════════════════════════════════════════ */

async function loadUsers() {
  // Mostrar skeleton mientras carga
  $('usersContent').innerHTML = `
    <div class="sh">
      <div>
        <div class="sey">Administración</div>
        <div class="sti">Gestión de Usuarios</div>
        <div class="sde" id="usersSubtitle">Cargando...</div>
      </div>
      <div class="sa">
        <button class="btn btn-primary" onclick="openUM()">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 16 16">
            <line x1="8" y1="2" x2="8" y2="14"/><line x1="2" y1="8" x2="14" y2="8"/>
          </svg>
          Nuevo usuario
        </button>
      </div>
    </div>
    <div style="padding:0 28px 28px">
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
        <table class="dt">
          <thead><tr>
            <th>USUARIO</th><th>CORREO</th><th>ROL</th><th>ÁREA</th><th>CREADO</th><th>ESTADO</th><th>ACCIONES</th>
          </tr></thead>
          <tbody id="usersTableBody">
            <tr class="loading-row"><td colspan="7">⏳ Cargando usuarios...</td></tr>
          </tbody>
        </table>
      </div>
    </div>`;

  try {
    const res  = await fetch(`${API_BASE}/users.php`, { headers: apiHeaders() });
    const json = await res.json();

    if (!json.success) {
      showToast(json.error || 'Error al cargar usuarios', 'error');
      renderUsersTable([]);
      return;
    }

    renderUsersTable(json.data || []);
    $('usersSubtitle').textContent = `${json.total} usuario${json.total !== 1 ? 's' : ''} registrado${json.total !== 1 ? 's' : ''} en el sistema.`;

  } catch (err) {
    showToast('No se pudo conectar con la API', 'error');
    renderUsersTable([]);
  }
}

/* ══════════════════════════════════════════════
   RENDERIZAR TABLA DE USUARIOS
   ══════════════════════════════════════════════ */

function renderUsersTable(users) {
  const tbody = $('usersTableBody');
  if (!tbody) return;

  if (!users.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--ink3)">No hay usuarios registrados.</td></tr>`;
    return;
  }

  tbody.innerHTML = users.map(u => `
    <tr>
      <td>
        <div style="display:flex;align-items:center;gap:9px">
          <div class="avatar" style="width:32px;height:32px;background:${u.avatar || '#0F52BA'};font-size:12px">
            ${getInitials(u.name)}
          </div>
          <div>
            <div style="font-weight:600;font-size:13px">${escHtml(u.name)}</div>
            <div style="font-size:11px;color:var(--ink3);font-family:var(--mono)">ID-${u.id}</div>
          </div>
        </div>
      </td>
      <td style="font-size:12.5px">${escHtml(u.email)}</td>
      <td>${roleBadge(u.role)}</td>
      <td style="font-size:12.5px">${escHtml(u.dept || '—')}</td>
      <td style="font-size:11.5px;color:var(--ink3)">${formatDate(u.created_at)}</td>
      <td>
        <span class="badge ${u.active == 1 ? 'b-resolved' : 'b-red'}">
          ${u.active == 1 ? '✓ Activo' : '✕ Inactivo'}
        </span>
      </td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          <button class="btn btn-sm btn-ghost" onclick="openUM(${u.id})">✏️ Editar</button>
          <button class="btn btn-sm btn-ghost" onclick="openChangePass(${u.id})" title="Cambiar contraseña">🔑</button>
          <button class="btn btn-sm btn-ghost"
            onclick="toggleActive(${u.id}, ${u.active == 1 ? 0 : 1})"
            style="color:${u.active == 1 ? 'var(--amber)' : 'var(--green)'}"
            title="${u.active == 1 ? 'Desactivar' : 'Activar'}">
            ${u.active == 1 ? '🔒' : '🔓'}
          </button>
          ${u.id != 1
            ? `<button class="btn btn-sm btn-danger" onclick="askDelUser(${u.id})" title="Eliminar">✕</button>`
            : `<button class="btn btn-sm btn-ghost" disabled title="Admin principal protegido">✕</button>`}
        </div>
      </td>
    </tr>`).join('');
}

// Escapar HTML para evitar XSS
function escHtml(str) {
  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ══════════════════════════════════════════════
   MODAL: ABRIR FORMULARIO CREAR / EDITAR
   ══════════════════════════════════════════════ */

async function openUM(id = null) {
  editUserId = id;
  const colors = ['#0F52BA','#15803D','#B45309','#6D28D9','#D93025','#0D9488','#0891B2','#7C3AED','#DB2777','#EA580C'];
  const depts  = ['TI','Administración','Producción','Ventas','Logística','Finanzas','RRHH','Dirección','Contabilidad','Diseño'];

  $('userModalTitle').textContent = id ? 'Editar usuario' : 'Nuevo usuario';

  // Si es edición, cargar datos del usuario desde la API
  let u = null;
  if (id) {
    try {
      const res  = await fetch(`${API_BASE}/users.php?id=${id}`, { headers: apiHeaders() });
      const json = await res.json();
      if (json.success) u = json.data;
      else { showToast(json.error || 'No se pudo cargar el usuario', 'error'); return; }
    } catch {
      showToast('Error de conexión con la API', 'error');
      return;
    }
  }

  const currentAvatar = u?.avatar || colors[0];

  $('userModalBody').innerHTML = `
    <div class="fg">
      <label class="fl">Nombre completo <span class="r">*</span></label>
      <input class="fi" id="um-n" value="${escHtml(u?.name || '')}" placeholder="Ej: Ana García">
      <div class="fe" id="um-ne">Requerido.</div>
    </div>
    <div class="fg">
      <label class="fl">Correo electrónico <span class="r">*</span></label>
      <input class="fi" id="um-e" type="email" value="${escHtml(u?.email || '')}" placeholder="usuario@empresa.com">
      <div class="fe" id="um-ee">Requerido.</div>
    </div>
    <div class="fg">
      <label class="fl">${u ? 'Nueva contraseña <span style="color:var(--ink3);font-weight:400">(dejar vacío para no cambiar)</span>' : 'Contraseña <span class="r">*</span>'}</label>
      <input class="fi" id="um-p" type="password" placeholder="${u ? 'Nueva contraseña...' : 'Mínimo 6 caracteres'}">
      <div class="fe" id="um-pe">Mínimo 6 caracteres.</div>
    </div>
    <div class="fg2">
      <div class="fg">
        <label class="fl">Rol</label>
        <select class="fs" id="um-r">
          <option value="user"  ${u?.role === 'user'  ? 'selected' : ''}>👤 Usuario</option>
          <option value="agent" ${u?.role === 'agent' ? 'selected' : ''}>🛠 Agente</option>
          <option value="admin" ${u?.role === 'admin' ? 'selected' : ''}>👑 Administrador</option>
        </select>
      </div>
      <div class="fg">
        <label class="fl">Área</label>
        <select class="fs" id="um-d">
          ${depts.map(d => `<option ${u?.dept === d ? 'selected' : ''}>${escHtml(d)}</option>`).join('')}
        </select>
      </div>
    </div>
    <div class="fg">
      <label class="fl">Color de avatar</label>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        ${colors.map(c => `
          <div onclick="selColor('${c}')" id="ac${c.replace('#','')}"
            style="width:28px;height:28px;border-radius:50%;background:${c};cursor:pointer;
                   border:3px solid ${currentAvatar === c ? 'var(--ink)' : 'transparent'};
                   transition:border .15s">
          </div>`).join('')}
      </div>
      <input type="hidden" id="um-av" value="${currentAvatar}">
    </div>`;

  openModal('userModal');
}

function selColor(c) {
  document.querySelectorAll('[id^="ac"]').forEach(el => el.style.border = '3px solid transparent');
  $('ac' + c.replace('#', '')).style.border = '3px solid var(--ink)';
  $('um-av').value = c;
}

/* ══════════════════════════════════════════════
   API: GUARDAR USUARIO (CREAR O ACTUALIZAR)
   POST  /api/users.php  → crear
   PUT   /api/users.php  → actualizar
   ══════════════════════════════════════════════ */

async function saveUser() {
  const n  = $('um-n')?.value.trim();
  const e  = $('um-e')?.value.trim().toLowerCase();
  const pw = $('um-p')?.value;

  // Validación básica en cliente
  let ok = true;
  if (!n) { $('um-ne').classList.add('show'); ok = false; } else $('um-ne').classList.remove('show');
  if (!e) { $('um-ee').classList.add('show'); ok = false; } else $('um-ee').classList.remove('show');
  if (!editUserId && (!pw || pw.length < 6)) { $('um-pe').classList.add('show'); ok = false; } else $('um-pe').classList.remove('show');
  if (!ok) return;

  const payload = {
    name:   n,
    email:  e,
    role:   $('um-r').value,
    dept:   $('um-d').value,
    avatar: $('um-av').value
  };

  // Incluir contraseña solo si se proporcionó
  if (pw && pw.length >= 6) payload.password = pw;

  // Si es edición, incluir el ID
  if (editUserId) payload.id = editUserId;

  const method = editUserId ? 'PUT' : 'POST';
  const btn    = $('btnSaveUser');
  btn.disabled = true;
  btn.textContent = 'Guardando...';

  try {
    const res  = await fetch(`${API_BASE}/users.php`, {
      method,
      headers: apiHeaders(),
      body: JSON.stringify(payload)
    });
    const json = await res.json();

    if (json.success) {
      closeModal('userModal');
      showToast(editUserId ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente', 'success');
      loadUsers(); // Recargar tabla desde la API
    } else {
      showToast(json.error || 'Error al guardar usuario', 'error');
    }
  } catch {
    showToast('Error de conexión con la API', 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Guardar cambios';
  }
}

/* ══════════════════════════════════════════════
   API: ACTIVAR / DESACTIVAR USUARIO
   PUT /api/users.php  { id, active }
   ══════════════════════════════════════════════ */

async function toggleActive(id, newActive) {
  try {
    const res  = await fetch(`${API_BASE}/users.php`, {
      method:  'PUT',
      headers: apiHeaders(),
      body:    JSON.stringify({ id, active: newActive })
    });
    const json = await res.json();

    if (json.success) {
      showToast(newActive ? 'Usuario activado' : 'Usuario desactivado', newActive ? 'success' : 'warning');
      loadUsers();
    } else {
      showToast(json.error || 'Error al cambiar estado', 'error');
    }
  } catch {
    showToast('Error de conexión con la API', 'error');
  }
}

/* ══════════════════════════════════════════════
   MODAL: CONFIRMAR ELIMINACIÓN
   ══════════════════════════════════════════════ */

function askDelUser(id) {
  pendingDelUser = id;
  openModal('confirmUserDelete');
}

/* ══════════════════════════════════════════════
   API: ELIMINAR USUARIO (SOFT DELETE)
   DELETE /api/users.php?id=X
   ══════════════════════════════════════════════ */

async function confirmUserDelete() {
  if (!pendingDelUser) return;

  const btn = $('btnConfirmDel');
  btn.disabled = true;
  btn.textContent = 'Eliminando...';

  try {
    const res  = await fetch(`${API_BASE}/users.php?id=${pendingDelUser}`, {
      method:  'DELETE',
      headers: apiHeaders()
    });
    const json = await res.json();

    if (json.success) {
      closeModal('confirmUserDelete');
      showToast('Usuario eliminado', 'error');
      loadUsers();
    } else {
      showToast(json.error || 'Error al eliminar usuario', 'error');
    }
  } catch {
    showToast('Error de conexión con la API', 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Sí, eliminar';
    pendingDelUser = null;
  }
}

/* ══════════════════════════════════════════════
   MODAL: CAMBIAR CONTRASEÑA
   ══════════════════════════════════════════════ */

async function openChangePass(id) {
  // Cargar nombre del usuario para el título del modal
  try {
    const res  = await fetch(`${API_BASE}/users.php?id=${id}`, { headers: apiHeaders() });
    const json = await res.json();
    if (json.success) {
      $('cpModalTitle').textContent = 'Contraseña — ' + json.data.name;
    }
  } catch {
    $('cpModalTitle').textContent = 'Cambiar contraseña';
  }

  $('cp-uid').value     = id;
  $('cp-new').value     = '';
  $('cp-confirm').value = '';
  $('cp-err').style.display = 'none';
  openModal('changePassModal');
  setTimeout(() => $('cp-new').focus(), 100);
}

/* ══════════════════════════════════════════════
   API: GUARDAR NUEVA CONTRASEÑA
   PUT /api/users.php  { id, password }
   ══════════════════════════════════════════════ */

async function saveChangePass() {
  const id = parseInt($('cp-uid').value);
  const np = $('cp-new').value;
  const cp = $('cp-confirm').value;

  $('cp-err').style.display = 'none';

  if (!np || np.length < 6) {
    $('cp-err').textContent = 'Mínimo 6 caracteres.';
    $('cp-err').style.display = 'block';
    return;
  }
  if (np !== cp) {
    $('cp-err').textContent = 'Las contraseñas no coinciden.';
    $('cp-err').style.display = 'block';
    return;
  }

  const btn = $('btnSavePass');
  btn.disabled = true;
  btn.textContent = 'Guardando...';

  try {
    const res  = await fetch(`${API_BASE}/users.php`, {
      method:  'PUT',
      headers: apiHeaders(),
      body:    JSON.stringify({ id, password: np })
    });
    const json = await res.json();

    if (json.success) {
      closeModal('changePassModal');
      showToast('Contraseña actualizada correctamente', 'success');
    } else {
      $('cp-err').textContent = json.error || 'Error al actualizar contraseña.';
      $('cp-err').style.display = 'block';
    }
  } catch {
    $('cp-err').textContent = 'Error de conexión con la API.';
    $('cp-err').style.display = 'block';
  } finally {
    btn.disabled = false;
    btn.textContent = '🔑 Cambiar contraseña';
  }
}
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Nuevo Ticket — Decomobil</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../styles/style.css">

</head>
<body>
<nav id="mainNav">
  <a class="nav-brand" href="home.php">
    <div class="nav-logo">DC</div>
    <span class="nav-name">DECOMOBIL</span>
  </a>
  <div class="nav-sep"></div>
  <div id="navLinks">
    <a class="nav-link" href="home.php">🏠 Inicio</a>
    <a class="nav-link active" href="new.php">🎫 Nuevo Ticket</a>
    <a class="nav-link" href="company.php">🏢 Nosotros</a>
    <a class="nav-link" href="org.php">🗂 Organigrama</a>
    <a class="nav-link" href="admin.php" data-role="agent">⚙️ Administración</a>
    <a class="nav-link" href="users.php" data-role="admin">👥 Usuarios</a>
    <a class="nav-link" href="metrics.php" data-role="admin">📊 Métricas</a>
  </div>
  <div id="navRight"></div>
</nav>
<div id="detailPanel"></div>
<div class="page">
<div id="newTicketContent" style="flex:1;overflow-y:auto;padding-top:60px"></div>

</div>

<div id="toastContainer"></div>
<script>

/* ══ HELPERS ══ */
const $ = id => document.getElementById(id);
const $$ = s => document.querySelectorAll(s);

function formatDate(d) {
  if (!d) return '—';
  const dt = new Date(d + 'T12:00:00');
  const m  = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  return `${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear()}`;
}

function isOverdue(due, status) {
  if (!due || status === 'Resuelto') return false;
  return new Date(due) < new Date();
}



function priorityBadge(p) {
  const m={'Alta':['b-high','🔴'],'Media':['b-medium','🟡'],'Baja':['b-low','🟢']};
  const [c,i] = m[p]||['b-low','⚪'];
  return `<span class="badge ${c}">${i} ${p}</span>`;
}

function statusBadge(s) {
  const m={'Abierto':'b-open','En Progreso':'b-progress','En Revisión':'b-review','Pendiente':'b-pending','Resuelto':'b-resolved'};
  const dotC={'Abierto':'var(--primary)','En Progreso':'var(--amber)','En Revisión':'var(--violet)','Pendiente':'var(--ink3)','Resuelto':'var(--green)'};
  const pulse = s==='En Progreso'?' dot-pulse':'';
  return `<span class="badge ${m[s]||'b-pending'}"><span class="bd${pulse}" style="background:${dotC[s]||'var(--ink3)'}"></span>${s}</span>`;
}

function roleBadge(r) {
  const m={admin:['b-critical','👑 Admin'],agent:['b-progress','🛠 Agente'],user:['b-open','👤 Usuario']};
  const [c,l] = m[r]||['b-pending','Desconocido'];
  return `<span class="badge ${c}">${l}</span>`;
}

function showToast(msg, type='', icon='') {
  const c=$('toastContainer'); const icons={success:'✓',error:'✕',warning:''};
  const el=document.createElement('div'); el.className=`toast ${type}`;
  el.innerHTML=`<span style="font-size:15px">${icon||icons[type]||'ℹ'}</span><span>${msg}</span>`;
  c.appendChild(el);
  setTimeout(()=>{ el.style.opacity='0'; el.style.transform='translateX(20px) scale(.95)'; el.style.transition='all .3s'; setTimeout(()=>el.remove(),300); },3200);
}


function openModal(id)  { $(id).classList.add('open'); }
function closeModal(id) { $(id).classList.remove('open'); }

const getInitials = (name) => String(name||'').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();

(function initNav() {
  // Obtener sesión del nuevo sistema (sessionStorage)
  const sessionData = sessionStorage.getItem('user_session');
  const authToken = sessionStorage.getItem('auth_token');

  // Si no hay sesión válida, redirigir a login
  if (!sessionData || !authToken) {
    window.location.href = 'login.php';
    return;
  }

  const s = JSON.parse(sessionData);

  // Hide role-restricted links
  document.querySelectorAll('[data-role]').forEach(el => {
    const r = el.dataset.role;
    const ok = (r === 'agent' && (s.role==='agent'||s.role==='admin')) ||
               (r === 'admin' && s.role==='admin');
    if (!ok) el.style.display = 'none';
  });

  // User pill
  document.getElementById('navRight').innerHTML = `
    <div class="nav-user">
      <div class="avatar" style="width:28px;height:28px;background:${s.avatar};font-size:10px">${getInitials(s.name)}</div>
      <span style="font-size:13px;font-weight:600;color:rgba(255,255,255,.85)">${s.name.split(' ')[0]}</span>
    </div>
    <button class="nav-btn nb-ghost" onclick="sessionStorage.removeItem('auth_token');sessionStorage.removeItem('user_session');window.location.href='login.php'">Salir</button>
  `;
})();

// Renderizar el formulario de nuevo ticket
renderNew();


document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.getElementById('detailPanel')?.classList.remove('open');
    document.querySelectorAll('.overlay').forEach(o => o.classList.remove('open'));
  }
});


/* ══ NEW TICKET ══ */

/* ══ NEW TICKET ══ */

async function renderNew() {
  const API_BASE = new URL('../xampp_project/api', window.location.href).href;

  // Obtener sesión actual
  const sessionData = sessionStorage.getItem('user_session');
  const authToken = sessionStorage.getItem('auth_token');
  const userSession = sessionData ? JSON.parse(sessionData) : null;
  const isAgent = userSession && (userSession.role === 'agent' || userSession.role === 'admin');

  // Cargar usuarios válidos para asignación desde API
  let agents = [];
  if (authToken) {
    try {
      const response = await fetch(`${API_BASE}/users.php`, {
        headers: { 'Authorization': `Bearer ${authToken}` }
      });
      if (response.ok) {
        const result = await response.json();
        if (result.success && result.data) {
          agents = result.data.filter(u => u.role !== 'user' && u.active);
        }
      }
    } catch (error) {
      console.error('Error cargando agentes:', error);
    }
  }

  const d7 = new Date();
  d7.setDate(d7.getDate() + 7);

  $('newTicketContent').innerHTML = `
    <div style="padding:32px 28px;max-width:760px; margin:auto;">
      <div style="margin-bottom:24px"><div class="sey">Soporte Interno</div><div class="sti">Crear Nuevo Ticket</div><div class="sde">Describe tu problema o solicitud con el mayor detalle posible.</div></div>
      <div class="card" style="padding:0;overflow:hidden">
        <div style="background:var(--primary);padding:16px 20px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;background:rgba(255,255,255,.15);border-radius:9px;display:grid;place-items:center;font-size:20px">🎫</div>
          <div><div style="font-family:var(--display);font-size:14px;font-weight:700;color:#fff">Nuevo ticket de soporte</div><div style="font-size:12px;color:rgba(255,255,255,.6)">El equipo responderá a la brevedad.</div></div>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px">
          <div class="fg"><label class="fl">Título <span class="r">*</span></label><input class="fi" id="nt-title" placeholder="Menciona tu problema..." maxlength="120"><div class="fe" id="nt-te">El título es requerido.</div></div>
          <div class="fg"><label class="fl">Descripción detallada <span class="r">*</span></label><textarea class="ft" id="nt-desc" style="min-height:100px" placeholder="Describe la problematica..."></textarea><div class="fe" id="nt-de">La descripción es requerida.</div></div>
          <div class="fg3">
            <div class="fg"><label class="fl">Categoría</label><select class="fs" id="nt-cat"><option value="TI">💻 TI / Soporte</option><option value="Mantenimiento">⚙️ Mantenimiento</option></select></div>
            <div class="fg"><label class="fl">Prioridad</label><select class="fs" id="nt-prio"><option value="Baja">🟢 Baja</option><option value="Media" selected>🟡 Media</option><option value="Alta">🔴 Alta</option><option value="Critica">‼️ Crítica</option></select></div>
            <div class="fg"><label class="fl">Área solicitante</label><select class="fs" id="nt-dept"><option>Direccion</option><option>Ventas</option><option>Sistemas</option><option>Mantenimiento</option><option>RRHH</option><option>Calidad</option><option>Finanzas</option><option>Almacen General</option><option>Logistica</option></select></div>
          </div>
          <div class="fg2">
            <div class="fg"><label class="fl">Fecha límite</label><input class="fi" id="nt-due" type="date" value="${d7.toISOString().split('T')[0]}"></div>
            <div class="fg"><label class="fl">Asignar a</label><select class="fs" id="nt-ass">
              <option value="">Sin asignar</option>
              ${agents.length ? agents.map(u => `<option value="${u.id}">${u.name}</option>`).join('') : '<option disabled>No hay agentes disponibles</option>'}
            </select></div>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:10px;border-top:1px solid var(--border)">
            <button class="btn btn-ghost" onclick="$('nt-title').value='';$('nt-desc').value=''">Limpiar</button>
            <button class="btn btn-primary btn-lg" onclick="submitTicket()">✓ Enviar ticket</button>
          </div>
        </div>
      </div>
    </div>`;
}
async function submitTicket() {
  const API_BASE = new URL('../xampp_project/api', window.location.href).href;

  const title = $('nt-title')?.value.trim();
  const desc = $('nt-desc')?.value.trim();
  const dueDate = $('nt-due')?.value;

  // Limpiar mensajes de error previos
  document.querySelectorAll('.fe').forEach(el => el.classList.remove('show'));

  let missingFields = [];
  let ok = true;

  // Validar título
  if (!title) {
    $('nt-te').classList.add('show');
    $('nt-title').focus();
    missingFields.push('Título');
    ok = false;
  }

  // Validar descripción
  if (!desc) {
    $('nt-de').classList.add('show');
    if (ok) $('nt-desc').focus();
    missingFields.push('Descripción');
    ok = false;
  }

  // Validar fecha límite (opcional pero debe ser futura si se especifica)
  if (dueDate) {
    const selectedDate = new Date(dueDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
      showToast('La fecha límite debe ser hoy o en el futuro', 'error');
      $('nt-due').focus();
      return;
    }
  }

  // Si faltan campos requeridos, mostrar mensaje y no continuar
  if (!ok) {
    const fieldText = missingFields.length === 1 ? 'el campo' : 'los campos';
    const message = `Por favor complete ${fieldText} requerido${missingFields.length === 1 ? '' : 's'}: ${missingFields.join(', ')}`;
    showToast(message, 'error');

    // Hacer scroll al primer campo con error
    const firstErrorField = document.querySelector('.fe.show');
    if (firstErrorField) {
      firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return;
  }

  const authToken = sessionStorage.getItem('auth_token');
  const sessionData = sessionStorage.getItem('user_session');
  const userSession = sessionData ? JSON.parse(sessionData) : null;

  if (!authToken || !userSession) {
    showToast('Sesión expirada. Por favor, inicia sesión nuevamente.', 'error');
    window.location.href = 'login.php';
    return;
  }

  try {
    const res = await fetch(`${API_BASE}/tickets.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`
      },
      body: JSON.stringify({
        title: title,
        description: desc,
        category: $('nt-cat').value,
        priority: $('nt-prio').value,
        dept: $('nt-dept').value,
        due_date: $('nt-due').value || null,
        assignee_id: $('nt-ass') ? parseInt($('nt-ass').value) || null : null,
      })
    });

    const data = await res.json();

    if (data.success) {
      showToast(`Ticket creado exitosamente`, 'success');
      window.location.href = 'home.php';
    } else {
      showToast(`Error: ${data.error || 'No se pudo guardar'}`, 'error');
    }

  } catch (err) {
    console.error(err);
    showToast('Error de conexión con el servidor', 'error');
  }
}
</script>
</body>
</html>
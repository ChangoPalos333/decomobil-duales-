<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Inicio — Decomobil</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../styles/style.css">
<style>
  /* ═══════════════════════════════════════════
     ICONO FLOTANTE (NEWS ORB)
  ═══════════════════════════════════════════ */
  .news-orb {
    position: fixed !important;
    left: 24px !important;
    bottom: 24px !important;
    width: 60px !important;
    height: 60px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #0F52BA, #2563eb) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: white !important;
    font-size: 24px !important;
    cursor: pointer !important;
    z-index: 999999 !important;
    box-shadow: 0 8px 24px rgba(15,82,186,0.4) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
  }
  .news-orb:hover {
    transform: scale(1.08) !important;
    box-shadow: 0 16px 40px rgba(15,82,186,.45) !important;
  }

  /* ═══════════════════════════════════════════
     PANEL LATERAL
  ═══════════════════════════════════════════ */
  .news-panel {
    position: fixed !important;
    left: -420px !important;
    top: 0 !important;
    width: 400px !important;
    height: 100vh !important;
    background: #ffffff !important;
    z-index: 999998 !important;
    transition: left 0.3s ease !important;
    box-shadow: 0 0 40px rgba(0,0,0,0.2) !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
  }
  .news-panel.open { left: 0 !important; }

  /* Cabecera oscura del panel — aquí va el degradado */
  .news-header {
    padding: 24px !important;
    background: linear-gradient(135deg, #0E1117, #111827) !important;
    color: #ffffff !important;
    border-bottom: 1px solid #e5e7eb !important;
  }
  .news-header h2 { color: #ffffff !important; }
  .news-header p  { color: rgba(255,255,255,.7) !important; }

  /* Lista scrolleable */
  .news-list {
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 18px !important;
  }

  /* ═══════════════════════════════════════════
     FORMULARIO CREAR NOTICIA (solo admin)
  ═══════════════════════════════════════════ */
  .news-create {
    background: #f9fafb !important;
    border: 1.5px dashed #d1d5db !important;
    padding: 20px !important;
    margin-bottom: 24px !important;
    border-radius: 14px !important;
    color: #111827 !important;
  }
  .news-create .news-title {
    color: #111827 !important;
    font-size: 18px !important;
    margin-bottom: 12px !important;
  }
  .news-create input,
  .news-create textarea {
    width: 100% !important;
    background: #ffffff !important;
    color: #111827 !important;
    border: 1px solid #d1d5db !important;
    padding: 10px !important;
    margin-bottom: 10px !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    font-family: inherit !important;
  }
  .news-create textarea {
    resize: vertical !important;
    min-height: 100px !important;
  }
  .news-create input::placeholder,
  .news-create textarea::placeholder {
    color: #9ca3af !important;
  }

  /* ═══════════════════════════════════════════
     TARJETAS DE NOTICIAS
  ═══════════════════════════════════════════ */
  .news-card {
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 18px !important;
    padding: 18px !important;
    margin-bottom: 16px !important;
    color: #111827 !important;
    box-shadow: 0 1px 2px rgba(0,0,0,.04) !important;
    transition: transform .2s ease, box-shadow .2s ease !important;
  }
  .news-card:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,.08) !important;
  }

  /* ★ La barrita de color (lo que tu llamabas "el verde") */
  .news-accent {
    width: 100% !important;
    height: 6px !important;
    border-radius: 999px !important;
    margin-bottom: 14px !important;
  }

  .news-title       { color: #111827 !important; font-size: 16px !important; font-weight: 700 !important; margin-bottom: 8px !important; }
  .news-description { color: #374151 !important; font-size: 13px !important; line-height: 1.5 !important; }
  .news-meta        { color: #6b7280 !important; font-size: 11px !important; margin-top: 14px !important; }

  /* Botones de admin dentro de cada noticia */
  .news-admin-actions {
    display: flex !important;
    gap: 8px !important;
    margin-top: 14px !important;
  }
  .news-admin-actions button {
    border: none !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    cursor: pointer !important;
    font-weight: 600 !important;
    font-size: 12px !important;
  }
  .news-admin-actions button:hover { opacity: .9 !important; }
</style>

</head>
<body>
<nav id="mainNav">
  <a class="nav-brand" href="home.php">
    <div class="nav-logo">DC</div>
    <span class="nav-name">DECOMOBIL</span>
  </a>
  <div class="nav-sep"></div>
  <div id="navLinks">
    <a class="nav-link active" href="home.php">🏠 Inicio</a>
    <a class="nav-link" href="new.php">🎫 Nuevo Ticket</a>
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
<div id="homeContent" style="flex:1;display:flex;flex-direction:column;overflow-y:auto;padding-top:60px;margin:auto"></div>
</div>

<div id="toastContainer"></div>

<div class="news-orb" onclick="toggleNewsPanel()">
  📰
</div>

<div class="news-panel" id="newsPanel">
  <div class="news-header">
    <h2 style="margin:0;font-size:22px">Noticias</h2>
    <p style="margin-top:6px;font-size:13px;color:rgba(255,255,255,.7)">
      Comunicados internos de la empresa
    </p>
  </div>
  <div class="news-list" id="newsList"></div>
</div>
<script>
/* ══ HELPERS ══ */
/* ══ HELPERS ══ */
const $ = id => document.getElementById(id);
const $$ = s => document.querySelectorAll(s);
const API_BASE = '../xampp_project/api';
const getInitials = (name) => String(name||'').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();

function formatDate(d) {
  if (!d) return '—';
  let dateString = String(d).trim();
  
  // Soporta YYYY-MM-DD, YYYY-MM-DD HH:MM:SS y marcas de tiempo ISO
  if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(dateString)) {
    dateString = dateString.replace(' ', 'T');
  }
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
    dateString = `${dateString}T12:00:00`;
  }
  const dt = new Date(dateString);
  if (Number.isNaN(dt.getTime())) return '—';
  const m  = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  return `${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear()}`;
}

function isOverdue(due, status) {
  if (!due || status === 'Resuelto') return false;
  const dateString = String(due).trim().replace(' ', 'T');
  const dt = new Date(dateString);
  if (Number.isNaN(dt.getTime())) return false;
  return dt < new Date();
}


function priorityBadge(p) {
  const styles = {
    'Critica': { bg: 'rgba(239, 68, 68, 0.1)', border: '1px solid rgba(239, 68, 68, 0.3)', color: '#dc2626', icon: '‼️' },
    'Alta': { bg: 'rgba(245, 101, 101, 0.1)', border: '1px solid rgba(245, 101, 101, 0.3)', color: '#ea580c', icon: '🔴' },
    'Media': { bg: 'rgba(251, 191, 36, 0.1)', border: '1px solid rgba(251, 191, 36, 0.3)', color: '#d97706', icon: '🟡' },
    'Baja': { bg: 'rgba(34, 197, 94, 0.1)', border: '1px solid rgba(34, 197, 94, 0.3)', color: '#16a34a', icon: '🟢' }
  };
  const style = styles[p] || styles['Baja'];
  return `<span class="badge" style="background:${style.bg};border:${style.border};color:${style.color}">${style.icon} ${p}</span>`;
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
  const c=$('toastContainer'); const icons={success:'✓',error:'✕'};
  const el=document.createElement('div'); el.className=`toast ${type}`;
  el.innerHTML=`<span style="font-size:15px">${icon||icons[type]||'ℹ'}</span><span>${msg}</span>`;
  c.appendChild(el);
  setTimeout(()=>{ el.style.opacity='0'; el.style.transform='translateX(20px) scale(.95)'; el.style.transition='all .3s'; setTimeout(()=>el.remove(),300); },3200);
}

function openModal(id)  { $(id).classList.add('open'); }
function closeModal(id) { $(id).classList.remove('open'); }


//!gggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg
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


document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.getElementById('detailPanel')?.classList.remove('open');
    TicketChat?.close();
    document.querySelectorAll('.overlay').forEach(o => o.classList.remove('open'));
  }
});


/* ══ HOME ══ */
async function renderHome() {
  const API_BASE = new URL('../xampp_project/api', window.location.href).href;
  
  // Obtener sesión de sessionStorage  
  const sessionData = sessionStorage.getItem('user_session');
  const authToken = sessionStorage.getItem('auth_token');
  
  if (!sessionData || !authToken) {
    window.location.href = 'login.php';
    return;
  }
  
  const s = JSON.parse(sessionData);
  const isAgent = s.role === 'agent' || s.role === 'admin';
  
  try {
    // Obtener estadísticas
    const statsRes = await fetch(`${API_BASE}/tickets.php?stats=1`, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    const statsData = await statsRes.json();
    const stats = statsData.data || {};
    
    // Obtener tickets
    const ticketsRes = await fetch(`${API_BASE}/tickets.php`, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    const ticketsData = await ticketsRes.json();
    const tickets = (ticketsData.data || []).filter(t => !t.resolved_at || t.status !== 'Resuelto');
    const allTickets = ticketsData.data || [];
    const recent = allTickets.slice(0, 5);
    
    // Calcular estadísticas desde los datos
    const total = stats.total || 0;
    const open = (stats.by_status && stats.by_status['Abierto']) || 0;
    const inProgress = (stats.by_status && stats.by_status['En Progreso']) || 0;
    const resolved = (stats.by_status && stats.by_status['Resuelto']) || 0;
    const avgDays = stats.avg_resolution_days || 0;
    
    // Saludo según la hora
    const h = new Date().getHours();
    const greeting = h < 12 ? 'Buenos días' : h < 18 ? 'Buenas tardes' : 'Buenas noches';
    
    const homeContent = document.getElementById('homeContent');
    if (!homeContent) return;
    
    homeContent.innerHTML = `
      <div style="background:linear-gradient(135deg,#0E1117 0%,#0F52BA 100%);padding:32px 28px 28px;color:#fff;position:relative;overflow:hidden;flex-shrink:0">
        <div style="position:absolute;inset:0;background:radial-gradient(circle at 80% 50%,rgba(255,255,255,.05),transparent 60%);pointer-events:none"></div>
        <div style="position:relative">
          <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.45);margin-bottom:8px">${new Date().toLocaleDateString('es-MX',{weekday:'long',year:'numeric',month:'long',day:'numeric'})}</div>
          <h1 style="font-family:var(--display);font-size:26px;font-weight:800;letter-spacing:-.5px;margin-bottom:6px">${greeting}, ${s.name.split(' ')[0]} 👋</h1>
          <p style="font-size:13.5px;color:rgba(255,255,255,.6)">${s.role==='admin'?'Panel de administración completo — tienes acceso a todas las funciones.':'Gestiona tus tickets y mantén al equipo informado.'}</p>
        </div>
      </div>
      <div style="padding:20px 28px 0">
        <div class="stats-grid" style="display:grid;grid-template-columns:repeat(4 ,minmax(220px,1fr));gap:16px; width:100%;align-items:stretch">
          <div class="stat-card sc-blue"><div class="sv">${total}</div><div class="sl">Total tickets</div><div class="ss">Histórico completo</div></div>
          <div class="stat-card sc-red"><div class="sv" style="color:var(--red)">${open}</div><div class="sl">Abiertos</div><div class="ss">Esperando atención</div></div>
          <div class="stat-card sc-amber"><div class="sv" style="color:var(--amber)">${inProgress}</div><div class="sl">En progreso</div><div class="ss">Siendo atendidos</div></div>
          <div class="stat-card sc-green"><div class="sv" style="color:var(--green)">${resolved}</div><div class="sl">Resueltos</div><div class="ss">Prom: ${avgDays} días</div></div>
        </div>
      </div>
     <div 
  style="
    padding:20px 28px;
    display:grid;
    grid-template-columns:${isAgent ? '1fr 1fr' : '1fr'};
    gap:20px;
    flex:1;
    min-height:0;
    width:100%;
    box-sizing:border-box;
  "
>
      <div 
  class="card tickets-card" 
  style="
    overflow-y:auto;
    min-height:340px;
    width:100%;
    padding:24px;
    border-radius:18px;
  "
>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div class="ct" style="margin:0">Tickets activos</div>
            <button class="btn btn-sm btn-primary" onclick="navigate('new')">+ Nuevo</button>
          </div>
          ${tickets.length===0?`<div class="empty"><div class="ei">🎉</div><h3>Sin tickets activos</h3><p>No tienes pendientes.</p></div>`:
            tickets.slice(0,8).map(t=>`<div onclick="openDetail('${t.id}')" style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border);cursor:pointer;border-radius:4px;padding-left:4px;transition:background .1s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
              <div style="flex:1;min-width:0"><div style="font-size:13px;font-weight:600;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${t.title}</div><div style="font-size:11px;color:var(--ink3);margin-top:2px;font-family:var(--mono)">${t.id} · ${formatDate(t.created_at)}</div></div>
              ${statusBadge(t.status)}
            </div>`).join('')}
        </div>
      ${isAgent?`<div class="card" style="overflow-y:auto">
          <div class="ct">Actividad reciente</div>
          ${recent.map(t=>`<div onclick="openDetail('${t.id}')" style="display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid var(--border);cursor:pointer;border-radius:4px;padding-left:4px;transition:background .1s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
            <div class="avatar" style="width:26px;height:26px;border-radius:50%;display:grid;place-items:center;font-size:10px;background:${t.assignee_avatar||'#9CA3AF'};color:#fff">${t.assignee_name?getInitials(t.assignee_name):'?'}</div>
            <div style="flex:1;min-width:0"><div style="font-size:12.5px;font-weight:600;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${t.title}</div>
            <div style="display:flex;gap:6px;margin-top:3px;align-items:center">${priorityBadge(t.priority)}<span style="font-size:11px;color:var(--ink4)">${formatDate(t.created_at)}</span></div></div>
          </div>`).join('')}
        </div>`:''}

        </div>`;
      
  } catch (error) {
    console.error('Error cargando datos:', error);
    document.getElementById('homeContent').innerHTML = `
      <div style="padding:40px 28px;text-align:center;color:var(--ink3)">
        <h3>Error cargando datos</h3>
        <p>No se pudieron obtener las estadísticas de la base de datos.</p>
      </div>`;
  }
}
/* ══ DETAIL PANEL ══ */
let activeDetailId=null;

async function openDetail(id) {
  const API_BASE = new URL('../xampp_project/api', window.location.href).href;
  const authToken = sessionStorage.getItem('auth_token');
  
  console.log('openDetail', id, 'API_BASE=', API_BASE);
  
  if (!authToken) {
    console.error('No hay token de autenticación');
    return;
  }
  
  try {
    const requestUrl = `${API_BASE}/tickets.php?id=${encodeURIComponent(id)}`;
    console.log('Fetching ticket URL:', requestUrl);
    const response = await fetch(requestUrl, {
      headers: { 'Authorization': `Bearer ${authToken}` }
    });
    
    const json = await response.json();
    console.log('Ticket response', response.status, json);
    if (!response.ok || !json.success || !json.data) {
      console.error('Error al cargar ticket', response.status, json);
      const message = json?.error || `Error ${response.status} al cargar el ticket`;
      $('detailPanel').innerHTML = `<div style="padding:20px;color:var(--red);font-weight:700">${message}</div>`;
      showToast(message, 'error');
      return;
    }
    
    const t = json.data;
    activeDetailId = id;
    
    // Obtener sesión actual
    const sessionData = sessionStorage.getItem('user_session');
    const userSession = sessionData ? JSON.parse(sessionData) : null;
    const isAgent = userSession && (userSession.role === 'agent' || userSession.role === 'admin');
    
    // Datos del ticket
    const activity = Array.isArray(t.activity) ? t.activity : [];
    const assigneeName = t.assignee_name || t.assignee || null;
    const creatorName = t.creator_name || t.creator || 'Sistema';
    const createdAt = formatDate(t.created_at || t.createdAt);
    const description = t.description || t.desc || '<em style="color:var(--ink4)">Sin descripción.</em>';
    
    // Función para renderizar actividad
    const renderActivity = (a) => {
      const type = a.type || a.activity_type || 'comment';
      const user = a.user_name || a.user || a.author || 'Sistema';
      const msg = a.msg || a.message || a.text || '';
      const time = a.time || formatTime(a.created_at || a.timestamp || new Date().toISOString());
      const icon = type === 'comment' ? '💬' : type === 'status' ? '🔄' : type === 'assign' ? '👤' : '✨';
      const bg = type === 'comment' ? 'var(--primary-light)' : type === 'status' ? 'var(--amber-light)' : 'var(--green-light)';
      return `
        <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">
          <div style="width:26px;height:26px;border-radius:50%;display:grid;place-items:center;font-size:11px;flex-shrink:0;background:${bg}">${icon}</div>
          <div style="flex:1"><div style="font-size:12.5px"><strong>${user}</strong> — ${msg}</div><div style="font-size:10.5px;color:var(--ink3);font-family:var(--mono);margin-top:2px">${time}</div></div>
        </div>`;
    };
    
    // Función auxiliar para formatear tiempo
    function formatTime(dateStr) {
      if (!dateStr) return '';
      const dt = new Date(dateStr);
      if (Number.isNaN(dt.getTime())) return '';
      return dt.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
    }
    
    $('detailPanel').innerHTML = `
      <div style="padding:16px 18px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;flex-shrink:0">
        <div style="flex:1;min-width:0">
          <div style="font-family:var(--mono);font-size:11px;color:var(--ink3);margin-bottom:5px">${t.id} · ${t.category || 'General'} · ${t.dept || 'N/D'}</div>
          <div style="font-family:var(--display);font-size:15px;font-weight:700;color:var(--ink);line-height:1.35">${t.title}</div>
          <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap">${statusBadge(t.status)} ${priorityBadge(t.priority)}</div>
        </div>
        <div style="width:28px;height:28px;border-radius:6px;display:grid;place-items:center;cursor:pointer;color:var(--ink3);flex-shrink:0" onclick="$('detailPanel').classList.remove('open')" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">✕</div>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap;padding:0 18px 12px;align-items:flex-start">
        <button class="btn btn-sm btn-primary" onclick="TicketChat.open('${t.id}','${t.title.replace(/'/g,"\\'")}')">💬 Chat en vivo</button>
      </div>
      <div style="flex:1;overflow-y:auto;padding:16px 18px;display:flex;flex-direction:column;gap:16px">
        <div>
          <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:8px">Descripción</div>
          <div style="font-size:13px;color:var(--ink2);line-height:1.65;background:var(--bg);border:1px solid var(--border);border-radius:7px;padding:12px 14px">${description}</div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
          <div>
            <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:5px">Asignado a</div>
            <div style="display:flex;align-items:center;gap:7px">
              ${assigneeName ? `<div class="avatar" style="width:22px;height:22px;background:${t.assignee_avatar || '#9CA3AF'};font-size:9px">${getInitials(assigneeName)}</div>` : `<div class="avatar" style="width:22px;height:22px;background:var(--border2);border:1.5px dashed var(--border2)"></div>`}
              <span style="font-size:13px">${assigneeName || '<em style="color:var(--ink3)">Sin asignar</em>'}</span>
            </div>
          </div>
          <div>
            <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:5px">Reportado por</div>
            <div style="display:flex;align-items:center;gap:7px">
              ${creatorName !== 'Sistema' ? `<div class="avatar" style="width:22px;height:22px;background:${t.creator_avatar || '#9CA3AF'};font-size:9px">${getInitials(creatorName)}</div>` : ''}
              <span style="font-size:13px">${creatorName}</span>
            </div>
          </div>
          <div>
            <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:4px">Creado</div>
            <div style="font-size:13px">${createdAt}</div>
          </div>
          <div>
            <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:4px">Estado</div>
            <div style="font-size:13px">${t.status}</div>
          </div>
        </div>
        ${activity.length > 0 ? `
          <div>
            <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:8px">Actividad</div>
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:7px;padding:12px 14px;max-height:200px;overflow-y:auto">
              ${activity.map(renderActivity).join('')}
            </div>
          </div>
        ` : ''}
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px">
          <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:10px">Añadir comentario</div>
          <div style="display:flex;gap:8px">
            <input type="text" id="dp-cmt" class="fi" style="flex:1" placeholder="Escribe un comentario..." maxlength="200">
            <button class="btn btn-sm btn-primary" onclick="addCmt('${t.id}')">Enviar</button>
          </div>
        </div>
        ${isAgent ? `
          <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px">
            <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:10px">Acciones rápidas</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <button class="btn btn-sm btn-primary" onclick="changeStatus('${t.id}', 'En Progreso')">Marcar en progreso</button>
              <button class="btn btn-sm btn-success" onclick="changeStatus('${t.id}', 'Resuelto')">Marcar resuelto</button>
              <button class="btn btn-sm btn-ghost" onclick="assignToMe('${t.id}')">Asignarme</button>
              <button class="btn btn-sm btn-danger" onclick="deleteTicket('${t.id}')">🗑️ Eliminar</button>
            </div>
          </div>
        ` : ''}
      </div>`;
    
    $('detailPanel').classList.add('open');
    
  } catch (error) {
    console.error('Error cargando detalle del ticket:', error);
    showToast('Error al cargar el ticket', 'error');
  }
}

async function changeStatus(id, status) {
  const API_BASE = '../xampp_project/api';
  const authToken = sessionStorage.getItem('auth_token');
  
  if (!authToken) {
    console.error('No hay token de autenticación');
    return;
  }
  
  try {
    const response = await fetch(`${API_BASE}/tickets.php`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`
      },
      body: JSON.stringify({
        id: id,
        status: status
      })
    });
    
    const json = await response.json();
    if (json.success) {
      openDetail(id);
      renderHome(); // Actualizar la lista de tickets activos
      showToast(`Estado: ${status}`, 'success');
    } else {
      console.error('Error cambiando estado:', json);
      showToast('Error al cambiar estado', 'error');
    }
  } catch (error) {
    console.error('Error cambiando estado:', error);
    showToast('Error al cambiar estado', 'error');
  }
}

async function assignToMe(id) {
  const API_BASE = '../xampp_project/api';
  const authToken = sessionStorage.getItem('auth_token');
  const sessionData = sessionStorage.getItem('user_session');
  const userSession = sessionData ? JSON.parse(sessionData) : null;
  
  if (!authToken || !userSession) {
    console.error('No hay token de autenticación o sesión');
    return;
  }
  
  try {
    const response = await fetch(`${API_BASE}/tickets.php`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`
      },
      body: JSON.stringify({
        id: id,
        assignee_id: userSession.id
      })
    });
    
    const json = await response.json();
    if (json.success) {
      openDetail(id);
      renderHome(); // Actualizar la lista de tickets
      showToast('Ticket asignado a ti', 'success');
    } else {
      console.error('Error asignando ticket:', json);
      showToast('Error al asignar ticket', 'error');
    }
  } catch (error) {
    console.error('Error asignando ticket:', error);
    showToast('Error al asignar ticket', 'error');
  }
}

async function addCmt(id) {
  const txt = document.getElementById('dp-cmt')?.value.trim();
  if (!txt) return;
  
  const API_BASE = '../xampp_project/api';
  const authToken = sessionStorage.getItem('auth_token');
  const sessionData = sessionStorage.getItem('user_session');
  const userSession = sessionData ? JSON.parse(sessionData) : null;
  
  if (!authToken || !userSession) {
    console.error('No hay token de autenticación o sesión');
    return;
  }
  
  try {
    const response = await fetch(`${API_BASE}/activity.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`
      },
      body: JSON.stringify({
        ticket_id: id,
        user_name: userSession.name,
        type: 'comment',
        message: txt
      })
    });
    
    const json = await response.json();
    if (json.success) {
      document.getElementById('dp-cmt').value = ''; // Limpiar textarea
      openDetail(id); // Recargar detalles
      showToast('Comentario agregado', 'success');
    } else {
      console.error('Error agregando comentario:', json);
      showToast('Error al agregar comentario', 'error');
    }
  } catch (error) {
    console.error('Error agregando comentario:', error);
    showToast('Error al agregar comentario', 'error');
  }
}

async function deleteTicket(id) {
  // Confirmación para evitar accidentes
  if (!confirm('¿Estás seguro de que deseas eliminar este ticket?')) return;

  const API_BASE = '../xampp_project/api';
  const authToken = sessionStorage.getItem('auth_token');

  try {
    // IMPORTANTE: Tu PHP usa $_GET['id'], así que lo pasamos en la URL
    const response = await fetch(`${API_BASE}/tickets.php?id=${encodeURIComponent(id)}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });

    const json = await response.json();

    if (json.success) {
      showToast(json.message || 'Ticket eliminado', 'success');
      // Cerramos el panel de detalles
      $('detailPanel').classList.remove('open');
      // Recargamos la lista del home para que desaparezca el ticket borrado
      renderHome();
    } else {
      // Aquí mostramos el error específico que configuraste en el PHP (400, 404, etc.)
      showToast(json.error || 'Error al eliminar', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('No se pudo conectar con el servidor', 'error');
  }
}

toastContainer

const TicketChat = (() => {
  let currentTicketId    = null;
  let currentTicketTitle = '';
  let lastMessageId      = 0;
  let pollController     = null;
  let isPolling          = false;
  let chatPanel          = null;
  let currentUser        = null;
  const CHAT_API = API_BASE + '/chat.php';
  const ACT_API  = API_BASE + '/activity.php';
  
  /* ── NOTIFICACIONES PUSH ── */
  let notifPermission = (typeof Notification !== 'undefined') ? Notification.permission : 'denied';
  
  async function requestNotifPermission() {
    if (typeof Notification === 'undefined') return;
    if (notifPermission === 'granted' || notifPermission === 'denied') return;
    notifPermission = await Notification.requestPermission();
  }
  
  function fireNotif(senderName, text, ticketId, ticketTitle) {
    if (typeof Notification === 'undefined') return;
    if (Notification.permission !== 'granted') return;
    if (!document.hidden) return; // solo cuando la pestaña está en segundo plano
    
    const n = new Notification('💬 ' + senderName, {
      body:  text.length > 90 ? text.slice(0, 87) + '…' : text,
      tag:   'chat-' + ticketId,  // agrupa notificaciones del mismo ticket
      icon:  '/favicon.ico',
      badge: '/favicon.ico',
      data:  { ticketId, ticketTitle }
    });
    
    n.onclick = () => {
      window.focus();
      n.close();
      TicketChat.open(ticketId, ticketTitle);
    };
    
    setTimeout(() => n.close(), 6000);
  }
  const hdrs = () => {
    const token = sessionStorage.getItem('auth_token');
    return token ? { 'Authorization': 'Bearer ' + token } : {};
  };

  function initPanel() {
    if (document.getElementById('dc-chat-panel')) return;
    const session = sessionStorage.getItem('user_session');
    currentUser = session ? JSON.parse(session) : { name: 'Usuario', avatar: '#0F52BA' };
    const panel = document.createElement('div');
    panel.id = 'dc-chat-panel';
    panel.innerHTML = `
      <div class="dcc-drawer">
        <div class="dcc-header">
          <div class="dcc-header-left">
            <div class="dcc-icon">💬</div>
            <div>
              <div class="dcc-title" id="dcc-title">Chat del ticket</div>
              <div class="dcc-subtitle" id="dcc-ticket-id">—</div>
            </div>
          </div>
          <div class="dcc-header-right">
            <div class="dcc-status">
              <span class="dcc-dot" id="dcc-dot"></span>
              <span id="dcc-status-text">Conectando...</span>
            </div>
            <button class="dcc-close" onclick="TicketChat.close()" title="Cerrar (Esc)">✕</button>
          </div>
        </div>
        <div class="dcc-messages" id="dcc-messages">
          <div class="dcc-loading" id="dcc-loading">
            <div class="dcc-spinner"></div>
            <span>Cargando historial...</span>
          </div>
        </div>
        <div class="dcc-composer">
          <div class="dcc-composer-inner">
            <div class="dcc-avatar-mini" id="dcc-my-avatar">?</div>
            <textarea id="dcc-input" class="dcc-input" placeholder="Escribe un comentario... (Enter para enviar)" rows="1" maxlength="2000"></textarea>
            <button class="dcc-send" id="dcc-send-btn" onclick="TicketChat._send()" title="Enviar">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
          </div>
        </div>
      </div>
    `;
    const style = document.createElement('style');
    style.textContent = `
      #dc-chat-panel { position:fixed;top:0;right:0;bottom:0;z-index:8000;width:400px;max-width:100vw;pointer-events:none;font-family:var(--body,'DM Sans',sans-serif); }
      #dc-chat-panel.open { pointer-events:all }
      .dcc-drawer { position:absolute;top:0;right:0;bottom:0;left:0;background:var(--surface,#fff);border-left:1px solid var(--border,#e5e7eb);display:flex;flex-direction:column;transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);box-shadow:-6px 0 32px rgba(0,0,0,.1); }
      #dc-chat-panel.open .dcc-drawer { transform:translateX(0) }
      .dcc-header { display:flex;align-items:center;justify-content:space-between;padding:13px 15px;background:var(--primary,#0F52BA);color:#fff;flex-shrink:0; }
      .dcc-header-left { display:flex;align-items:center;gap:9px }
      .dcc-header-right { display:flex;align-items:center;gap:9px }
      .dcc-icon { width:34px;height:34px;background:rgba(255,255,255,.15);border-radius:8px;display:grid;place-items:center;font-size:16px;flex-shrink:0; }
      .dcc-title { font-weight:700;font-size:13px;line-height:1.2;max-width:190px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis }
      .dcc-subtitle { font-size:10.5px;opacity:.6;font-family:var(--mono,'JetBrains Mono',monospace) }
      .dcc-status { display:flex;align-items:center;gap:4px;font-size:11px;opacity:.8 }
      .dcc-dot { width:7px;height:7px;border-radius:50%;background:#6b7280;transition:background .3s }
      .dcc-dot.connected { background:#22c55e;animation:dcc-pulse 2s infinite }
      .dcc-dot.error { background:#ef4444 }
      @keyframes dcc-pulse { 0%,100%{ box-shadow:0 0 0 3px rgba(34,197,94,.25) }50%{ box-shadow:0 0 0 6px rgba(34,197,94,.08) }}
      .dcc-close { width:27px;height:27px;border-radius:6px;border:none;background:rgba(255,255,255,.15);color:#fff;cursor:pointer;font-size:12px;display:grid;place-items:center;transition:background .15s }
      .dcc-close:hover { background:rgba(255,255,255,.28) }
      .dcc-messages { flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:3px;background:var(--bg,#f8f9fb);scroll-behavior:smooth; }
      .dcc-messages::-webkit-scrollbar { width:4px }
      .dcc-messages::-webkit-scrollbar-thumb { background:var(--border,#e5e7eb);border-radius:4px }
      .dcc-loading { display:flex;flex-direction:column;align-items:center;gap:9px;padding:40px 20px;color:var(--ink3,#9ca3af);font-size:13px; }
      .dcc-spinner { width:22px;height:22px;border-radius:50%;border:2px solid var(--border,#e5e7eb);border-top-color:var(--primary,#0F52BA);animation:dcc-spin .8s linear infinite; }
      @keyframes dcc-spin { to{ transform:rotate(360deg) }}
      .dcc-msg { display:flex;gap:7px;align-items:flex-end;animation:dcc-fadeup .18s ease;max-width:100%; }
      @keyframes dcc-fadeup { from{ opacity:0;transform:translateY(5px) }to{ opacity:1;transform:none }}
      .dcc-msg.mine { flex-direction:row-reverse }
      .dcc-msg-avatar { width:26px;height:26px;border-radius:50%;display:grid;place-items:center;font-size:9px;font-weight:700;color:#fff;flex-shrink:0;align-self:flex-end;margin-bottom:2px; }
      .dcc-msg.mine .dcc-msg-avatar { display:none }
      .dcc-bubble-wrap { display:flex;flex-direction:column;gap:2px;max-width:265px }
      .dcc-msg.mine .dcc-bubble-wrap { align-items:flex-end }
      .dcc-sender { font-size:10px;font-weight:600;color:var(--ink3,#9ca3af);padding:0 9px }
      .dcc-bubble { padding:8px 12px;border-radius:13px;font-size:12.5px;line-height:1.55;word-break:break-word;white-space:pre-wrap; }
      .dcc-msg:not(.mine) .dcc-bubble { background:var(--surface,#fff);border:1px solid var(--border,#e5e7eb);color:var(--ink,#111);border-bottom-left-radius:4px; }
      .dcc-msg.mine .dcc-bubble { background:var(--primary,#0F52BA);color:#fff;border-bottom-right-radius:4px; }
      .dcc-time { font-size:9px;color:var(--ink3,#9ca3af);padding:0 9px }
      .dcc-msg.system { justify-content:center;margin:7px 0 }
      .dcc-msg.system .dcc-bubble { background:transparent;border:1px dashed var(--border,#e5e7eb);color:var(--ink3,#9ca3af);font-size:11px;border-radius:18px;padding:4px 13px;text-align:center; }
      .dcc-msg.system .dcc-msg-avatar,.dcc-msg.system .dcc-sender { display:none }
      .dcc-date-sep { display:flex;align-items:center;gap:8px;margin:10px 0 5px;color:var(--ink3,#9ca3af);font-size:10px; }
      .dcc-date-sep::before,.dcc-date-sep::after { content:'';flex:1;height:1px;background:var(--border,#e5e7eb); }
      .dcc-empty { display:flex;flex-direction:column;align-items:center;gap:7px;padding:45px 20px;color:var(--ink3,#9ca3af);text-align:center; }
      .dcc-empty-icon { font-size:34px;margin-bottom:4px }
      .dcc-empty h4 { font-size:13px;font-weight:600;color:var(--ink2,#6b7280);margin:0 }
      .dcc-empty p { font-size:12px;margin:0;line-height:1.5 }
      .dcc-composer { padding:11px;border-top:1px solid var(--border,#e5e7eb);background:var(--surface,#fff);flex-shrink:0; }
      .dcc-composer-inner { display:flex;align-items:flex-end;gap:8px;background:var(--bg,#f8f9fb);border:1.5px solid var(--border,#e5e7eb);border-radius:12px;padding:8px 8px 8px 10px;transition:border-color .15s; }
      .dcc-composer-inner:focus-within { border-color:var(--primary,#0F52BA);background:var(--surface,#fff); }
      .dcc-avatar-mini { width:24px;height:24px;border-radius:50%;display:grid;place-items:center;font-size:8.5px;font-weight:700;color:#fff;flex-shrink:0; }
      .dcc-input { flex:1;border:none;background:transparent;font-size:12.5px;line-height:1.5;color:var(--ink,#111);resize:none;outline:none;font-family:inherit;max-height:110px;overflow-y:auto; }
      .dcc-input::placeholder { color:var(--ink3,#9ca3af) }
      .dcc-send { width:32px;height:32px;border-radius:9px;border:none;background:var(--primary,#0F52BA);color:#fff;cursor:pointer;display:grid;place-items:center;flex-shrink:0;transition:transform .1s,opacity .15s;opacity:.45; }
      .dcc-send.active { opacity:1 }
      .dcc-send.active:hover { transform:scale(1.08) }
      .dcc-send.active:active { transform:scale(.93) }
    `;
    document.head.appendChild(style);
    document.body.appendChild(panel);
    chatPanel = panel;
    const input = document.getElementById('dcc-input');
    const sendBtn = document.getElementById('dcc-send-btn');
    input.addEventListener('input', () => {
      input.style.height = 'auto';
      input.style.height = Math.min(input.scrollHeight, 110) + 'px';
      sendBtn.classList.toggle('active', input.value.trim().length > 0);
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); TicketChat._send(); }
    });
  }

  function open(ticketId, ticketTitle) {
    initPanel();
    if (currentTicketId === ticketId && chatPanel.classList.contains('open')) return;
    stopPolling();
    currentTicketId = ticketId;
    currentTicketTitle = ticketTitle || ticketId;
    lastMessageId = 0;
    document.getElementById('dcc-title').textContent = currentTicketTitle;
    document.getElementById('dcc-ticket-id').textContent = ticketId;
    const msgContainer = document.getElementById('dcc-messages');
    msgContainer.innerHTML = `
      <div class="dcc-loading">
        <div class="dcc-spinner"></div>
        <span>Cargando historial...</span>
      </div>`;
    const session = sessionStorage.getItem('user_session');
    currentUser = session ? JSON.parse(session) : { name: 'Usuario', avatar: '#0F52BA' };
    const av = document.getElementById('dcc-my-avatar');
    if (av) { av.style.background = currentUser.avatar || '#0F52BA'; av.textContent = getInitials(currentUser.name); }
    chatPanel.classList.add('open');
    requestNotifPermission();
    loadHistory().then(() => startPolling());
  }

  function close() {
    stopPolling();
    if (chatPanel) chatPanel.classList.remove('open');
    currentTicketId = null;
  }

  async function loadHistory() {
    try {
      const res = await fetch(`${ACT_API}?ticket_id=${currentTicketId}`, { headers: hdrs() });
      const json = await res.json();
      const msgContainer = document.getElementById('dcc-messages');
      if (!json.success || !json.data || json.data.length === 0) {
        msgContainer.innerHTML = `
          <div class="dcc-empty">
            <div class="dcc-empty-icon">💬</div>
            <h4>Sin actividad aún</h4>
            <p>Sé el primero en comentar este ticket.</p>
          </div>`;
        setStatus('connected', 'En vivo');
        return;
      }
      msgContainer.innerHTML = '';
      let lastDate = '';
      json.data.forEach(msg => {
        const msgDate = fmtDateSep(msg.created_at);
        if (msgDate !== lastDate) {
          msgContainer.insertAdjacentHTML('beforeend', `<div class="dcc-date-sep">${msgDate}</div>`);
          lastDate = msgDate;
        }
        msgContainer.insertAdjacentHTML('beforeend', renderMsg(msg));
        if (parseInt(msg.id) > lastMessageId) lastMessageId = parseInt(msg.id);
      });
      scrollBottom(false);
      setStatus('connected', 'En vivo');
    } catch (err) {
      console.error('[Chat] Error historial:', err);
      setStatus('error', 'Error de conexión');
    }
  }

  function startPolling() { isPolling = true; poll(); }
  function stopPolling() { isPolling = false; if (pollController) { pollController.abort(); pollController = null; } }

  async function poll() {
    if (!isPolling || !currentTicketId) return;
    pollController = new AbortController();
    const tid = setTimeout(() => pollController.abort(), 30000);
    try {
      const res = await fetch(`${CHAT_API}?ticket_id=${currentTicketId}&last_id=${lastMessageId}`, { headers: hdrs(), signal: pollController.signal });
      clearTimeout(tid);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      if (json.success && json.messages && json.messages.length > 0) {
        const msgContainer = document.getElementById('dcc-messages');
        let lastDate = getLastDateSep();
        json.messages.forEach(msg => {
          if (msg.user_name === currentUser?.name && document.querySelector(`[data-msg-id="${msg.id}"]`)) return;
          const msgDate = fmtDateSep(msg.created_at);
          if (msgDate !== lastDate) {
            msgContainer.insertAdjacentHTML('beforeend', `<div class="dcc-date-sep">${msgDate}</div>`);
            lastDate = msgDate;
          }
          msgContainer.insertAdjacentHTML('beforeend', renderMsg(msg));
          if (parseInt(msg.id) > lastMessageId) lastMessageId = parseInt(msg.id);
          
          // Notificación push si el mensaje es de otra persona
          const isSystem = ['status','assign','resolve','create'].includes(msg.activity_type);
          if (!isSystem && msg.user_name !== currentUser?.name) {
            fireNotif(msg.user_name, msg.message, currentTicketId, currentTicketTitle);
          }
        });
        scrollBottom(true);
        setStatus('connected', 'En vivo');
      }
    } catch (err) {
      clearTimeout(tid);
      if (err.name !== 'AbortError') {
        setStatus('error', 'Reconectando...');
        await sleep(3000);
      }
    }
    if (isPolling) { await sleep(250); poll(); }
  }

  async function _send() {
    const input = document.getElementById('dcc-input');
    const sendBtn = document.getElementById('dcc-send-btn');
    const text = input?.value.trim();
    if (!text || !currentTicketId) return;
    const tempId = 'tmp-' + Date.now();
    const tempMsg = {
      id: tempId, ticket_id: currentTicketId,
      user_name: currentUser?.name || 'Tú',
      activity_type: 'comment', message: text,
      created_at: formatLocalDateTime(new Date()),
      avatar: currentUser?.avatar
    };
    const msgContainer = document.getElementById('dcc-messages');
    const empty = msgContainer.querySelector('.dcc-empty');
    if (empty) empty.remove();
    msgContainer.insertAdjacentHTML('beforeend', renderMsg(tempMsg));
    scrollBottom(true);
    input.value = '';
    input.style.height = 'auto';
    sendBtn.classList.remove('active');
    input.focus();
    try {
      const res = await fetch(CHAT_API, {
        method: 'POST',
        headers: { ...hdrs(), 'Content-Type': 'application/json' },
        body: JSON.stringify({ ticket_id: currentTicketId, message: text, type: 'comment', user_name: currentUser?.name })
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        markFailed(tempId);
        return;
      }
      if (json.data) {
        const tmpEl = document.querySelector(`[data-msg-id="${tempId}"]`);
        if (tmpEl) { tmpEl.dataset.msgId = json.data.id; lastMessageId = Math.max(lastMessageId, parseInt(json.data.id)); }
      }
    } catch (err) {
      markFailed(tempId);
    }
  }

  function markFailed(tempId) {
    const el = document.querySelector(`[data-msg-id="${tempId}"]`);
    if (el) { const b = el.querySelector('.dcc-bubble'); if (b) { b.style.opacity = '.5'; b.title = 'Error al enviar'; } }
  }

  function renderMsg(msg) {
    const isMe = msg.user_name === currentUser?.name;
    const isSystem = ['status','assign','resolve','create'].includes(msg.activity_type);
    const cssClass = isSystem ? 'system' : (isMe ? 'mine' : '');
    const avColor = msg.avatar || strColor(msg.user_name);
    const time = fmtTime(msg.created_at);
    if (isSystem) return `
      <div class="dcc-msg system" data-msg-id="${msg.id}">
        <div class="dcc-bubble-wrap"><div class="dcc-bubble">${esc(msg.message)} · <span style="opacity:.7">${time}</span></div></div>
      </div>`;
    return `
      <div class="dcc-msg ${cssClass}" data-msg-id="${msg.id}">
        ${!isMe ? `<div class="dcc-msg-avatar" style="background:${avColor}">${getInitials(msg.user_name)}</div>` : ''}
        <div class="dcc-bubble-wrap">
          ${!isMe ? `<div class="dcc-sender">${esc(msg.user_name)}</div>` : ''}
          <div class="dcc-bubble">${esc(msg.message)}</div>
          <div class="dcc-time">${time}</div>
        </div>
      </div>`;
  }

  function setStatus(state, text) {
    const dot = document.getElementById('dcc-dot');
    const lbl = document.getElementById('dcc-status-text');
    if (dot) dot.className = 'dcc-dot ' + state;
    if (lbl) lbl.textContent = text;
  }

  function scrollBottom(smooth) {
    const el = document.getElementById('dcc-messages');
    if (el) requestAnimationFrame(() => el.scrollTo({ top: el.scrollHeight, behavior: smooth ? 'smooth' : 'auto' }));
  }

  function getLastDateSep() {
    const seps = document.querySelectorAll('#dcc-messages .dcc-date-sep');
    return seps.length ? seps[seps.length-1].textContent : '';
  }

  function formatLocalDateTime(dt) {
    const pad = n => String(n).padStart(2,'0');
    return `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())} ${pad(dt.getHours())}:${pad(dt.getMinutes())}:${pad(dt.getSeconds())}`;
  }

  function parseServerDate(d) {
    if (!d) return null;
    const m = String(d).match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}):(\d{2}))?$/);
    if (m) {
      const y = +m[1], mo = +m[2]-1, day = +m[3], hh = +(m[4]||0), mm = +(m[5]||0), ss = +(m[6]||0);
      return new Date(y, mo, day, hh, mm, ss);
    }
    const dt = new Date(d);
    return isNaN(dt) ? null : dt;
  }

  function fmtTime(d) {
    if (!d) return '';
    const dt = parseServerDate(d) || new Date();
    return dt.toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit' });
  }

  function fmtDateSep(d) {
    if (!d) return '';
    const dt = parseServerDate(d) || new Date();
    const now = new Date();
    const yes = new Date(now); yes.setDate(now.getDate()-1);
    if (sameDay(dt, now)) return 'Hoy';
    if (sameDay(dt, yes)) return 'Ayer';
    return dt.toLocaleDateString('es-MX', { day:'numeric', month:'short', year:'numeric' });
  }

  function sameDay(a,b) { return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }
  function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
  function strColor(s) { const pool=['#0F52BA','#15803D','#B45309','#7C3AED','#D93025','#0D9488','#9333EA','#059669']; let h=0; for(let i=0;i<s.length;i++) h=s.charCodeAt(i)+((h<<5)-h); return pool[Math.abs(h)%pool.length]; }
  function sleep(ms) { return new Promise(r=>setTimeout(r,ms)); }

  // Global chat watcher: long-poll activity endpoint for system-wide messages
  let globalLastMsgId = 0;
  let globalPollAbort = null;
  let globalPolling = false;

  function startGlobalChatWatcher() { if (globalPolling) return; globalPolling = true; pollGlobal(); }
  function stopGlobalChatWatcher() { globalPolling = false; if (globalPollAbort) { globalPollAbort.abort(); globalPollAbort = null; } }

  async function pollGlobal() {
    if (!globalPolling) return;
    globalPollAbort = new AbortController();
    const tid = setTimeout(() => globalPollAbort.abort(), 30000);
    try {
      const res = await fetch(`${ACT_API}?last_id=${globalLastMsgId}`, { headers: hdrs(), signal: globalPollAbort.signal });
      clearTimeout(tid);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      if (json.success && Array.isArray(json.data) && json.data.length) {
        json.data.forEach(m => {
          const isSystem = ['status','assign','resolve','create'].includes(m.activity_type);
          if (!isSystem && m.user_name !== currentUser?.name) {
            fireNotif(m.user_name, m.message || m.msg || '', m.ticket_id || m.ticket || m.id, m.ticket_title || m.title || '');
          }
          const mid = parseInt(m.id || 0);
          if (mid > globalLastMsgId) globalLastMsgId = mid;
        });
      }
    } catch (err) {
      clearTimeout(tid);
      if (err.name !== 'AbortError') await sleep(2000);
    }
    if (globalPolling) { await sleep(250); pollGlobal(); }
  }

  return { open, close, _send, startGlobalChatWatcher };
})();

window.addEventListener('DOMContentLoaded', () => {
    renderHome();
    loadNews();
    setInterval(loadNews, 8000);
    if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
      Notification.requestPermission().then(permission => {
        newsNotifPermission = permission;
      });
    }
    // Start global watcher to receive chat notifications even if panel is closed
    try { TicketChat.startGlobalChatWatcher(); } catch(e) { /* ignore if not available */ }
  });

  function toggleNewsPanel() {
    const panel = $('newsPanel');
    if (panel) {
      panel.classList.toggle('open');
    }
  }

// Función de navegación
function navigate(page) {
  switch(page) {
    case 'new':
      window.location.href = 'new.php';
      break;
    case 'home':
      window.location.href = 'home.php';
      break;
    default:
      console.error('Página no reconocida:', page);
  }
}
function startGlobalRefresh() {

  setInterval(() => {

    const path = window.location.pathname;

    if(path.includes('home.php')){
      refreshHomeData();
    }

    else if(path.includes('admin.php')){
      refreshAdminData();
    }

    else if(path.includes('metrics.php')){
      refreshMetricsData();
    }

  }, 5000);

}

const NEWS_API = `${API_BASE}/news.php`;
let lastLoadedNewsIds = [];
let newsNotifPermission = (typeof Notification !== 'undefined') ? Notification.permission : 'denied';

function fireNewsNotif(newsId, newsTitle, newsDescription) {
  if (typeof Notification === 'undefined') return;
  if (Notification.permission !== 'granted') return;
  if (!document.hidden) return;
  
  const n = new Notification(newsTitle || 'Noticia', {
    body: (String(newsDescription || '').length > 90) ? String(newsDescription).slice(0, 87) + '...' : String(newsDescription || ''),
    tag: 'news-' + newsId,
    icon: '/favicon.ico',
    data: { newsId, newsTitle }
  });
  
  n.onclick = () => {
    window.focus();
    n.close();
    document.getElementById('newsPanel').classList.add('open');
  };
  
  setTimeout(() => n.close(), 6000);
}

async function loadNews() {

  const sessionData = JSON.parse(sessionStorage.getItem('user_session'));
  const token = sessionStorage.getItem('auth_token');

  // Preserve any in-progress inputs in the create-news form to avoid clearing while typing
  const prevTitleEl = document.getElementById('newsTitle');
  const prevDescEl  = document.getElementById('newsDescription');
  const prevColorEl = document.getElementById('newsColor');
  const prevTitle   = prevTitleEl ? prevTitleEl.value : null;
  const prevDesc    = prevDescEl  ? prevDescEl.value  : null;
  const prevColor   = prevColorEl ? prevColorEl.value : null;
  const prevSelStart = prevDescEl ? prevDescEl.selectionStart : null;
  const prevSelEnd   = prevDescEl ? prevDescEl.selectionEnd   : null;
  const activeId     = document.activeElement ? document.activeElement.id : null;

  const res = await fetch(NEWS_API, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  const json = await res.json();

  const news = json.data || [];
  
  const currentNewsIds = news.map(n => n.id);
  const newNews = news.filter(n => !lastLoadedNewsIds.includes(n.id));
  
  newNews.forEach(n => {
    fireNewsNotif(n.id, n.title, n.description);
  });
  
  lastLoadedNewsIds = currentNewsIds;

  const isAdmin = sessionData && sessionData.role === 'admin';

  const container = document.getElementById('newsList');

  container.innerHTML = `

    ${isAdmin ? `

      <div class="news-card news-create">

        <div class="news-title">Crear noticia</div>

        <input id="newsTitle" placeholder="Título">

        <textarea id="newsDescription" placeholder="Descripción"></textarea>

        <input type="color" id="newsColor" value="#0F52BA" style="height:50px;padding:4px">

        <button
          class="btn btn-primary"
          style="margin-top:12px;width:100%"
          onclick="createNews()"
        >
          Publicar noticia
        </button>

      </div>

    ` : ''}
    ${news.map(n => `
      <div class="news-card">
        <div class="news-accent" style="background:${n.accent_color || 'var(--primary)'}"></div>
        <div class="news-title">${n.title}</div>
        <div class="news-description">${n.description}</div>
        <div class="news-meta">
          ${n.created_by || 'Sistema'} ·
          ${formatDate(n.created_at)}
        </div>
        ${isAdmin ? `
          <div class="news-admin-actions">
            <button
              onclick="deleteNews(${n.id})"
              style="background:#fee2e2;color:#dc2626"
            >
              Eliminar
            </button>
          </div>
        ` : ''}
      </div>
    `).join('')}

  `;

  // Restore preserved inputs (if present) so user's typing is not lost
  try {
    if (prevTitle !== null) {
      const t = document.getElementById('newsTitle'); if (t) t.value = prevTitle;
    }
    if (prevDesc !== null) {
      const d = document.getElementById('newsDescription'); if (d) {
        d.value = prevDesc;
        if (prevSelStart !== null && prevSelEnd !== null) {
          d.selectionStart = prevSelStart; d.selectionEnd = prevSelEnd;
        }
      }
    }
    if (prevColor !== null) {
      const c = document.getElementById('newsColor'); if (c) c.value = prevColor;
    }
    if (activeId) {
      const ae = document.getElementById(activeId); if (ae) ae.focus();
    }
  } catch(e) { /* no-op if restore fails */ }
}


async function createNews() {

  const token = sessionStorage.getItem('auth_token');

  const title = document.getElementById('newsTitle').value.trim();
  const description = document.getElementById('newsDescription').value.trim();
  const accent_color = document.getElementById('newsColor').value;

  if (!title || !description) {
    showToast('Completa todos los campos', 'error');
    return;
  }

  const res = await fetch(NEWS_API, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      title,
      description,
      accent_color
    })
  });

  const json = await res.json();

  if (json.success) {
    showToast('Noticia publicada', 'success');
    // Clear the create form so user sees it was published
    try { document.getElementById('newsTitle').value = ''; document.getElementById('newsDescription').value = ''; } catch(e){}
    loadNews();
  } else {
    showToast(json.error || 'Error', 'error');
  }
}

async function deleteNews(id) {
  if (!confirm('¿Estás seguro de eliminar esta noticia?')) return;
  const token = sessionStorage.getItem('auth_token');
  try {
    const res = await fetch(`${NEWS_API}?id=${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const json = await res.json();
    if (json.success) {
      showToast('Noticia eliminada', 'success');
      loadNews();
    } else {
      showToast(json.error || 'Error al eliminar', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error de conexión', 'error');
  }
}
</script>
<script src="../js/script.js"></script>
</body>
</html>
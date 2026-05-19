<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Administración — Decomobil</title>
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
    <a class="nav-link" href="new.php">🎫 Nuevo Ticket</a>
    <a class="nav-link" href="company.php">🏢 Nosotros</a>
    <a class="nav-link" href="org.php">🗂 Organigrama</a>
    <a class="nav-link active" href="admin.php" data-role="agent">⚙️ Administración</a>
    <a class="nav-link" href="users.php" data-role="admin">👥 Usuarios</a>
    <a class="nav-link" href="metrics.php" data-role="admin">📊 Métricas</a>
  </div>
  <div id="navRight"></div>
</nav>
<div id="detailPanel"></div>
<div class="page">
  <div id="adminContent"></div>
</div>

<div class="overlay" id="confirmTicketDelete" onclick="if(event.target===this)closeModal('confirmTicketDelete')">
  <div class="modal" style="max-width:380px">
    <div class="mh"><span class="mt">⚠ Eliminar ticket</span><div class="mc" onclick="closeModal('confirmTicketDelete')">✕</div></div>
    <div class="mb"><p style="font-size:13.5px;color:var(--ink2);line-height:1.6">¿Confirmas eliminar este ticket? Esta acción es permanente.</p></div>
    <div class="mf">
      <button class="btn btn-ghost" onclick="closeModal('confirmTicketDelete')">Cancelar</button>
      <button class="btn btn-danger" onclick="confirmTicketDelete()">Sí, eliminar</button>
    </div>
  </div>
</div>
<div id="toastContainer"></div>

<script>
/* ══════════════════════════════════════════════════════════
   CONFIG
   ══════════════════════════════════════════════════════════ */
const API_BASE = '../xampp_project/api';

/* ══ HELPERS ══ */
const $  = id => document.getElementById(id);
const $$ = s  => document.querySelectorAll(s);
const getInitials = n => String(n||'').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();

function formatDate(d){
  if(!d) return '—';
  const raw = String(d).split(' ')[0];
  const dt = new Date(raw + 'T12:00:00');
  if(isNaN(dt)) return '—';
  const m = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  return `${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear()}`;
}
function isOverdue(due,status){
  if(!due || status==='Resuelto') return false;
  return new Date(String(due).split(' ')[0]) < new Date(new Date().toISOString().split('T')[0]);
}
function showToast(msg,type=''){
  const c=$('toastContainer');
  const icons={success:'✓',error:'✕',warning:'⚠'};
  const el=document.createElement('div'); el.className=`toast ${type}`;
  el.innerHTML=`<span style="font-size:15px">${icons[type]||'ℹ'}</span><span>${msg}</span>`;
  c.appendChild(el);
  setTimeout(()=>{el.style.opacity='0';el.style.transform='translateX(20px) scale(.95)';el.style.transition='all .3s';setTimeout(()=>el.remove(),300)},3200);
}
function openModal(id){ $(id).classList.add('open'); }
function closeModal(id){ $(id).classList.remove('open'); }

/* ══ SESIÓN ══ */
const Session = {
  get(){ try { return JSON.parse(sessionStorage.getItem('user_session')||'null'); } catch(e){ return null; } },
  token(){ return sessionStorage.getItem('auth_token'); },
  isAgent(){ const s=Session.get(); return s && (s.role==='agent'||s.role==='admin'); },
  isAdmin(){ const s=Session.get(); return s && s.role==='admin'; },
  clear(){ sessionStorage.removeItem('auth_token'); sessionStorage.removeItem('user_session'); }
};

/* ══ CLIENTE API ══ */
async function apiFetch(path, opts={}){
  const headers = { 'Content-Type':'application/json', ...(opts.headers||{}) };
  const tk = Session.token();
  if (tk) headers['Authorization'] = 'Bearer ' + tk;
  const res = await fetch(API_BASE + path, { ...opts, headers });
  let data = null;
  try { data = await res.json(); } catch(e){ data = { success:false, error:'Respuesta no válida' }; }
  if (!res.ok || data.success === false) {
    const err = (data && data.error) ? data.error : ('HTTP '+res.status);
    throw new Error(err);
  }
  return data;
}
const API = {
  listTickets: ()             => apiFetch('/tickets.php'),
  getTicket:   id             => apiFetch('/tickets.php?id=' + encodeURIComponent(id)),
  updateTicket:(id,payload)   => apiFetch('/tickets.php', { method:'PUT',    body: JSON.stringify({ id, ...payload }) }),
  deleteTicket:id             => apiFetch('/tickets.php?id=' + encodeURIComponent(id), { method:'DELETE' }),
  listUsers:   ()             => apiFetch('/users.php'),
  addActivity: payload        => apiFetch('/activity.php', { method:'POST', body: JSON.stringify(payload) }),
};

/* ══ NORMALIZACIÓN snake_case → camelCase ══ */
function normalizeTicket(t){
  return {
    id: t.id, title: t.title,
    desc: t.description ?? t.desc ?? '',
    priority: t.priority, status: t.status,
    category: t.category, dept: t.dept,
    assigneeId: t.assignee_id ?? t.assigneeId ?? null,
    createdBy:  t.created_by  ?? t.createdBy  ?? null,
    createdAt:  t.created_at  ?? t.createdAt  ?? null,
    dueDate:    t.due_date    ?? t.dueDate    ?? null,
    resolvedAt: t.resolved_at ?? t.resolvedAt ?? null,
    creatorName:  t.creator_name  ?? null,
    creatorAvatar:t.creator_avatar ?? null,
    assigneeName: t.assignee_name  ?? null,
    assigneeAvatar:t.assignee_avatar ?? null,
    activity: Array.isArray(t.activity) ? t.activity : []
  };
}

/* ══ CACHE ══ */
let TICKETS = [];
let USERS   = [];
let USERS_BY_ID = {};
function userById(id){ return USERS_BY_ID[id] || null; }

/* ══ BADGES ══ */
function priorityBadge(p){
  const m={'Crítica':['b-critical','🟣'],'Critica':['b-critical','🟣'],'Alta':['b-high','🔴'],'Media':['b-medium','🟡'],'Baja':['b-low','🟢']};
  const [c,i]=m[p]||['b-low','⚪']; return `<span class="badge ${c}">${i} ${p}</span>`;
}
function statusBadge(s){
  const m={'Abierto':'b-open','En Progreso':'b-progress','En Revisión':'b-review','En Revision':'b-review','Pendiente':'b-pending','Resuelto':'b-resolved'};
  const dotC={'Abierto':'var(--primary)','En Progreso':'var(--amber)','En Revisión':'var(--violet)','En Revision':'var(--violet)','Pendiente':'var(--ink3)','Resuelto':'var(--green)'};
  const pulse = s==='En Progreso' ? ' dot-pulse' : '';
  return `<span class="badge ${m[s]||'b-pending'}"><span class="bd${pulse}" style="background:${dotC[s]||'var(--ink3)'}"></span>${s}</span>`;
}
function avatarHTML(userId, size=24){
  const u = userById(userId);
  if(!u) return `<div class="avatar" style="width:${size}px;height:${size}px;background:var(--border2);font-size:${Math.floor(size*.38)}px">?</div>`;
  return `<div class="avatar" style="width:${size}px;height:${size}px;background:${u.avatar||'#0F52BA'};font-size:${Math.floor(size*.38)}px" title="${u.name}">${getInitials(u.name)}</div>`;
}

function computeStats(list){
  return {
    total:list.length,
    open:list.filter(t=>t.status==='Abierto').length,
    inProgress:list.filter(t=>t.status==='En Progreso').length,
    review:list.filter(t=>t.status==='En Revisión' || t.status==='En Revision').length,
    pending:list.filter(t=>t.status==='Pendiente').length,
    resolved:list.filter(t=>t.status==='Resuelto').length,
  };
}

/* ══ NAV + SESIÓN ══ */
(function initNav(){
  const s = Session.get();
  const tk = Session.token();
  if (!s || !tk) { window.location.href = 'login.php'; return; }
  document.querySelectorAll('[data-role]').forEach(el=>{
    const r = el.dataset.role;
    const ok = (r==='agent' && Session.isAgent()) || (r==='admin' && Session.isAdmin());
    if (!ok) el.style.display = 'none';
  });
  document.getElementById('navRight').innerHTML = `
    <div class="nav-user">
      <div class="avatar" style="width:28px;height:28px;background:${s.avatar||'#0F52BA'};font-size:10px">${getInitials(s.name)}</div>
      <span style="font-size:13px;font-weight:600;color:rgba(255,255,255,.85)">${(s.name||'').split(' ')[0]}</span>
    </div>
    <button class="nav-btn nb-ghost" id="logoutBtn">Salir</button>
  `;
  document.getElementById('logoutBtn').onclick = async () => {
    try { await fetch(API_BASE + '/auth.php?action=logout', { headers:{ 'Authorization':'Bearer '+Session.token() } }); } catch(e){}
    Session.clear();
    window.location.href = 'login.php';
  };
})();

document.addEventListener('keydown', e=>{
  if (e.key === 'Escape') {
    // Cerrar chat si está abierto, sino cerrar detailPanel
    if (document.getElementById('dc-chat-panel')?.classList.contains('open')) {
      TicketChat.close();
    } else {
      document.getElementById('detailPanel')?.classList.remove('open');
      document.querySelectorAll('.overlay').forEach(o=>o.classList.remove('open'));
    }
  }
});

/* ══ ADMIN PANEL ══ */
let adminTab = 'all';
let pendingDelTicket = null;

window.addEventListener('DOMContentLoaded', async () => {
  if (!Session.isAgent()) { window.location.href = 'home.php'; return; }
  renderAdminShell();
  await reloadData();
});

async function reloadData(){
  try {
    const [uRes, tRes] = await Promise.all([ API.listUsers(), API.listTickets() ]);
    USERS = uRes.data || [];
    USERS_BY_ID = {}; USERS.forEach(u => USERS_BY_ID[u.id] = u);
    TICKETS = (tRes.data || []).map(normalizeTicket);
    setTab(adminTab);
  } catch(err){ showToast('Error al cargar datos: ' + err.message, 'error'); }
}

function renderAdminShell(){
  document.getElementById('adminContent').innerHTML = `
    <div class="sh">
      <div>
        <div class="sey">Panel de Control</div>
        <div class="sti">Administración de Tickets</div>
        <div class="sde">Gestiona, asigna y da seguimiento a todos los tickets del sistema.</div>
      </div>
    </div>
    <div style="padding:8px 28px 0;display:flex;gap:2px;border-bottom:1px solid var(--border);overflow-x:auto">
      ${[['all','Todos'],['open','Abiertos'],['progress','En Progreso'],['pending','Pendientes'],['resolved','Resueltos']].map(([k,l])=>
        `<div id="atab-${k}" onclick="setTab('${k}')"
          style="padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;border-bottom:2px solid transparent;white-space:nowrap;color:var(--ink3);transition:all .15s;display:flex;align-items:center;gap:6px">
          ${l}<span id="acnt-${k}" style="font-family:var(--mono);font-size:10px;background:var(--bg2);padding:1px 6px;border-radius:8px;color:var(--ink3)">0</span>
        </div>`
      ).join('')}
    </div>
    <div id="tabContent" style="flex:1;overflow-y:auto;padding:20px 28px"></div>`;
}

function setTab(tab){
  adminTab = tab;
  const stats = computeStats(TICKETS);
  ['all','open','progress','pending','resolved'].forEach(k=>{
    const el = $(`atab-${k}`); if(!el) return;
    el.style.color = k===tab ? 'var(--primary)' : 'var(--ink3)';
    el.style.borderBottomColor = k===tab ? 'var(--primary)' : 'transparent';
  });
  const cnt = { all: TICKETS.length, open: stats.open, progress: stats.inProgress, pending: stats.pending, resolved: stats.resolved };
  Object.entries(cnt).forEach(([k,v])=>{ const el=$(`acnt-${k}`); if(el) el.textContent=v; });
  const c = $('tabContent'); if(!c) return;
  const fm = { all:null, open:'Abierto', progress:'En Progreso', pending:'Pendiente', resolved:'Resuelto' };
  const f = fm[tab];
  const list = f ? TICKETS.filter(t=>t.status===f) : TICKETS.slice();
  renderTicketTable(c, list);
}

function renderTicketTable(container, list){
  container.innerHTML = `
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap">
      <div style="position:relative;flex:1;min-width:200px;max-width:300px">
        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--ink3)">🔍</span>
        <input id="admSearch" style="width:100%;border:1.5px solid var(--border);border-radius:6px;padding:7px 10px 7px 30px;font-size:13px;outline:none;font-family:var(--body)" placeholder="Buscar tickets..." oninput="filterTTable()">
      </div>
      <select id="admPrio" style="height:34px;padding:0 10px;border:1.5px solid var(--border);border-radius:6px;font-size:12.5px;outline:none;font-family:var(--body)" onchange="filterTTable()"><option value="">Todas las prioridades</option><option>Crítica</option><option>Alta</option><option>Media</option><option>Baja</option></select>
      <select id="admCat"  style="height:34px;padding:0 10px;border:1.5px solid var(--border);border-radius:6px;font-size:12.5px;outline:none;font-family:var(--body)" onchange="filterTTable()"><option value="">Todas las categorías</option><option>TI</option><option>RRHH</option><option>OPS</option><option>CONT</option><option>VEN</option><option>INFRA</option><option>OTROS</option></select>
      <span id="tCountLabel" style="font-size:12px;color:var(--ink3);font-family:var(--mono);margin-left:auto">${list.length} tickets</span>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
      <table class="dt"><thead><tr><th>ID</th><th>TÍTULO</th><th>PRIORIDAD</th><th>ESTADO</th><th>ASIGNADO</th><th>CREADO</th><th></th></tr></thead>
      <tbody id="tBody">${tRows(list)}</tbody></table>
    </div>`;
}

function tRows(list){
  if (!list.length) return '<tr><td colspan="7"><div class="empty"><div class="ei">🎫</div><h3>Sin tickets</h3></div></td></tr>';
  return list.map(t=>{
    const ag = userById(t.assigneeId);
    return `<tr onclick="openDetail('${t.id}')">
      <td><span style="font-family:var(--mono);font-size:11px;color:var(--ink3)">${t.id}</span></td>
      <td style="max-width:300px"><div style="font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--ink)">${t.title}</div><div style="font-size:11px;color:var(--ink3)">${t.category||'—'} · ${t.dept||'—'}</div></td>
      <td>${priorityBadge(t.priority)}</td>
      <td>${statusBadge(t.status)}</td>
      <td><div style="display:flex;align-items:center;gap:6px">${t.assigneeId ? avatarHTML(t.assigneeId,20) : `<div style="width:20px;height:20px;border-radius:50%;border:1.5px dashed var(--border2)"></div>`}<span style="font-size:12px">${ag?ag.name.split(' ')[0]:(t.assigneeName?t.assigneeName.split(' ')[0]:'—')}</span></div></td>
      <td><span style="font-size:12px;color:var(--ink3)">${formatDate(t.createdAt)}</span></td>
      <td onclick="event.stopPropagation()"><button class="btn btn-sm btn-danger" onclick="askDelTicket('${t.id}')">✕</button></td>
    </tr>`;
  }).join('');
}

function filterTTable(){
  const q   = $('admSearch')?.value.toLowerCase() || '';
  const p   = $('admPrio')?.value || '';
  const cat = $('admCat')?.value  || '';
  const fm = { all:null, open:'Abierto', progress:'En Progreso', pending:'Pendiente', resolved:'Resuelto' };
  const f = fm[adminTab];
  let list = TICKETS.slice();
  if (f)   list = list.filter(t=>t.status===f);
  if (q)   list = list.filter(t => (t.title||'').toLowerCase().includes(q) || (t.id||'').toLowerCase().includes(q) || (t.dept||'').toLowerCase().includes(q));
  if (p)   list = list.filter(t=>t.priority===p);
  if (cat) list = list.filter(t=>t.category===cat);
  const b = $('tBody'); if (b) b.innerHTML = tRows(list);
  const cl = $('tCountLabel'); if (cl) cl.textContent = list.length + ' tickets';
}

/* ══ DELETE ══ */
function askDelTicket(id){ pendingDelTicket = id; openModal('confirmTicketDelete'); }
async function confirmTicketDelete(){
  if (!pendingDelTicket) return;
  const id = pendingDelTicket;
  try {
    await API.deleteTicket(id);
    $('detailPanel').classList.remove('open');
    TicketChat.close();
    closeModal('confirmTicketDelete');
    pendingDelTicket = null;
    TICKETS = TICKETS.filter(t=>t.id!==id);
    setTab(adminTab);
    showToast('Ticket eliminado', 'error');
  } catch(err){
    closeModal('confirmTicketDelete');
    pendingDelTicket = null;
    showToast('No se pudo eliminar: ' + err.message, 'error');
  }
}

/* ══ DETAIL ══ */
let activeDetailId = null;

async function openDetail(id){
  activeDetailId = id;
  try {
    const res = await API.getTicket(id);
    const t = normalizeTicket(res.data);
    renderDetail(t);
    const idx = TICKETS.findIndex(x=>x.id===id);
    if (idx >= 0) TICKETS[idx] = { ...TICKETS[idx], ...t };
  } catch(err){ showToast('No se pudo cargar el ticket: ' + err.message, 'error'); }
}

function renderDetail(t){
  const agents = USERS.filter(u => (u.role==='agent'||u.role==='admin') && String(u.active)!=='0');
  const isAgent = Session.isAgent();
  const creatorName = userById(t.createdBy)?.name || t.creatorName || 'Sistema';

  $('detailPanel').innerHTML = `
    <div style="padding:16px 18px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;flex-shrink:0">
      <div style="flex:1;min-width:0">
        <div style="font-family:var(--mono);font-size:11px;color:var(--ink3);margin-bottom:5px">${t.id} · ${t.category||'—'} · ${t.dept||'—'}</div>
        <div style="font-family:var(--display);font-size:15px;font-weight:700;color:var(--ink);line-height:1.35">${t.title}</div>
        <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap">${statusBadge(t.status)} ${priorityBadge(t.priority)}</div>
      </div>
      <div style="display:flex;gap:6px;align-items:center;flex-shrink:0">
        <!-- ✅ BOTÓN DE CHAT EN TIEMPO REAL -->
        <button
          onclick="TicketChat.open('${t.id}', '${t.title.replace(/'/g, "\\'")}')"
          style="display:flex;align-items:center;gap:5px;padding:6px 11px;background:var(--primary);color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;font-family:var(--body);transition:opacity .15s"
          onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'"
          title="Abrir chat en tiempo real"
        >
          💬 Chat en vivo
        </button>
        <div style="width:28px;height:28px;border-radius:6px;display:grid;place-items:center;cursor:pointer;color:var(--ink3)" onclick="document.getElementById('detailPanel').classList.remove('open');TicketChat.close()">✕</div>
      </div>
    </div>
    <div style="flex:1;overflow-y:auto;padding:16px 18px;display:flex;flex-direction:column;gap:16px">
      <div>
        <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:8px">Descripción</div>
        <div style="font-size:13px;color:var(--ink2);line-height:1.65;background:var(--bg);border:1px solid var(--border);border-radius:7px;padding:12px 14px">${t.desc || '<em style="color:var(--ink4)">Sin descripción.</em>'}</div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div>
          <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:5px">Asignado a</div>
          <div style="display:flex;align-items:center;gap:7px">${t.assigneeId?avatarHTML(t.assigneeId,22):`<div class="avatar" style="width:22px;height:22px;background:var(--border2);border:1.5px dashed var(--border2)"></div>`}<span style="font-size:13px">${t.assigneeId?(userById(t.assigneeId)?.name || t.assigneeName || '—'):'<em style="color:var(--ink3)">Sin asignar</em>'}</span></div>
        </div>
        <div>
          <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:5px">Reportado por</div>
          <div style="display:flex;align-items:center;gap:7px">${t.createdBy?avatarHTML(t.createdBy,22):''}<span style="font-size:13px">${creatorName}</span></div>
        </div>
        <div><div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:4px">Creado</div><div style="font-size:13px">${formatDate(t.createdAt)}</div></div>
        <div><div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:4px">Vence</div><div style="font-size:13px;color:${isOverdue(t.dueDate,t.status)?'var(--red)':'var(--ink2)'}">${formatDate(t.dueDate)}</div></div>
      </div>
      ${isAgent ? `
      <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px">
        <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:10px">Acciones rápidas</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
          ${['Abierto','En Progreso','En Revisión','Pendiente','Resuelto'].map(st =>
            `<button class="btn btn-sm ${t.status===st?'btn-primary':'btn-ghost'}" onclick="changeStatus('${t.id}','${st}')">${st}</button>`
          ).join('')}
        </div>
        <div style="display:flex;gap:8px;align-items:center">
          <select class="fs" id="dp-ass" style="flex:1;height:30px;font-size:12px;padding:0 8px">
            <option value="">Reasignar a...</option>
            ${agents.map(u=>`<option value="${u.id}" ${String(t.assigneeId)===String(u.id)?'selected':''}>${u.name}</option>`).join('')}
          </select>
          <button class="btn btn-sm btn-ghost" onclick="changeAssignee('${t.id}')">Asignar</button>
        </div>
      </div>` : ''}

      <!-- Historial de actividad (solo eventos de sistema, sin comentarios) -->
      <div>
        <div style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink3);margin-bottom:8px;display:flex;align-items:center;justify-content:space-between">
          <span>Historial (${t.activity.filter(a=>['status','assign','resolve','create'].includes(a.activity_type||a.type||'comment')).length} eventos)</span>
          <button onclick="TicketChat.open('${t.id}','${t.title.replace(/'/g,"\\'")}');this.closest('#detailPanel').classList.remove('open')" style="font-size:11px;color:var(--primary);background:none;border:none;cursor:pointer;font-family:var(--body);font-weight:600">Ver todos los comentarios →</button>
        </div>
        ${t.activity.filter(a=>['status','assign','resolve','create'].includes(a.activity_type||a.type||'comment')).slice().reverse().map(a=>{
          const type = a.activity_type || a.type || 'create';
          const msg  = a.message || a.msg || '';
          const user = a.user_name || a.user || 'Sistema';
          const when = a.created_at ? new Date(a.created_at.replace(' ','T')).toLocaleString('es-MX',{dateStyle:'short',timeStyle:'short'}) : (a.time||'');
          const bg   = type==='status'?'var(--amber-light)': type==='assign'?'var(--green-light)':'var(--primary-light)';
          const ic   = type==='status'?'🔄': type==='assign'?'👤': type==='resolve'?'✅':'🎫';
          return `<div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">
            <div style="width:26px;height:26px;border-radius:50%;display:grid;place-items:center;font-size:11px;flex-shrink:0;background:${bg}">${ic}</div>
            <div style="flex:1"><div style="font-size:12.5px"><strong>${user}</strong> — ${msg}</div><div style="font-size:10.5px;color:var(--ink3);font-family:var(--mono);margin-top:2px">${when}</div></div>
          </div>`;
        }).join('')}
        ${t.activity.filter(a=>['status','assign','resolve','create'].includes(a.activity_type||a.type||'comment')).length === 0
          ? `<div style="font-size:12.5px;color:var(--ink3);padding:8px 0">Sin eventos de sistema aún.</div>`
          : ''}
      </div>

      ${isAgent ? `<div style="display:flex;gap:8px"><button class="btn btn-sm btn-danger" onclick="askDelTicket('${t.id}')">🗑 Eliminar ticket</button></div>` : ''}
    </div>

    <!-- Footer: acceso rápido al chat -->
    <div style="padding:12px 18px;border-top:1px solid var(--border);background:var(--surface2);flex-shrink:0">
      <button
        onclick="TicketChat.open('${t.id}','${t.title.replace(/'/g,"\\'")}');document.getElementById('detailPanel').classList.remove('open')"
        style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:9px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--body);transition:opacity .15s"
        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'"
      >
        💬 Abrir chat en tiempo real
      </button>
    </div>`;

  $('detailPanel').classList.add('open');

  // ✅ Abrir el chat automáticamente al lado del panel de detalle
  TicketChat.open(t.id, t.title);
}

/* ══ ACCIONES ══ */
async function changeStatus(id, status){
  const s = Session.get();
  try {
    await API.updateTicket(id, { status, user_name: s?.name || 'Sistema' });
    showToast(`Estado: ${status}`, 'success');
    const res = await API.getTicket(id);
    const t = normalizeTicket(res.data);
    const idx = TICKETS.findIndex(x=>x.id===id); if (idx>=0) TICKETS[idx] = t;
    renderDetail(t);
    setTab(adminTab);
  } catch(err){ showToast('Error: ' + err.message, 'error'); }
}

async function changeAssignee(id){
  const sel = $('dp-ass'); if (!sel || !sel.value) return;
  const s = Session.get();
  const ag = userById(parseInt(sel.value));
  try {
    await API.updateTicket(id, { assignee_id: parseInt(sel.value), user_name: s?.name || 'Sistema' });
    showToast(`Asignado a ${ag?.name || 'usuario'}`, 'success');
    const res = await API.getTicket(id);
    const t = normalizeTicket(res.data);
    const idx = TICKETS.findIndex(x=>x.id===id); if (idx>=0) TICKETS[idx] = t;
    renderDetail(t);
    setTab(adminTab);
  } catch(err){ showToast('Error: ' + err.message, 'error'); }
}

/* ══════════════════════════════════════════════════════════
   CHAT EN TIEMPO REAL — TicketChat
   Panel lateral con long polling. Usa la misma tabla
   ticket_activity que ya existe en la BD.
   ══════════════════════════════════════════════════════════ */
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

  const token   = () => Session.token() || '';
  const hdrs    = () => ({ 'Authorization': 'Bearer ' + token() });

  /* — Init panel (se crea una sola vez en el DOM) — */
  function initPanel() {
    if (document.getElementById('dc-chat-panel')) return;

    const session = sessionStorage.getItem('user_session');
    currentUser = session ? JSON.parse(session) : { name: 'Usuario', avatar: '#0F52BA', role: 'user' };

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
            <textarea id="dcc-input" class="dcc-input"
              placeholder="Escribe un comentario... (Enter para enviar)"
              rows="1" maxlength="2000"></textarea>
            <button class="dcc-send" id="dcc-send-btn" onclick="TicketChat._send()" title="Enviar">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
          </div>
        </div>
      </div>
    `;

    /* Estilos del chat */
    const style = document.createElement('style');
    style.textContent = `
      #dc-chat-panel {
        position:fixed; top:0; right:0; bottom:0; z-index:8000;
        width:400px; max-width:100vw;
        pointer-events:none;
        font-family:var(--body,'DM Sans',sans-serif);
      }
      #dc-chat-panel.open { pointer-events:all }

      .dcc-drawer {
        position:absolute; top:0; right:0; bottom:0; left:0;
        background:var(--surface,#fff);
        border-left:1px solid var(--border,#e5e7eb);
        display:flex; flex-direction:column;
        transform:translateX(100%);
        transition:transform .3s cubic-bezier(.4,0,.2,1);
        box-shadow:-6px 0 32px rgba(0,0,0,.1);
      }
      #dc-chat-panel.open .dcc-drawer { transform:translateX(0) }

      .dcc-header {
        display:flex; align-items:center; justify-content:space-between;
        padding:13px 15px;
        background:var(--primary,#0F52BA); color:#fff;
        flex-shrink:0;
      }
      .dcc-header-left  { display:flex; align-items:center; gap:9px }
      .dcc-header-right { display:flex; align-items:center; gap:9px }
      .dcc-icon {
        width:34px; height:34px; background:rgba(255,255,255,.15);
        border-radius:8px; display:grid; place-items:center;
        font-size:16px; flex-shrink:0;
      }
      .dcc-title    { font-weight:700; font-size:13px; line-height:1.2; max-width:190px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
      .dcc-subtitle { font-size:10.5px; opacity:.6; font-family:var(--mono,'JetBrains Mono',monospace) }
      .dcc-status   { display:flex; align-items:center; gap:4px; font-size:11px; opacity:.8 }
      .dcc-dot {
        width:7px; height:7px; border-radius:50%; background:#6b7280;
        transition:background .3s;
      }
      .dcc-dot.connected { background:#22c55e; animation:dcc-pulse 2s infinite }
      .dcc-dot.error     { background:#ef4444 }
      @keyframes dcc-pulse {
        0%,100%{ box-shadow:0 0 0 3px rgba(34,197,94,.25) }
        50%    { box-shadow:0 0 0 6px rgba(34,197,94,.08) }
      }
      .dcc-close {
        width:27px; height:27px; border-radius:6px; border:none;
        background:rgba(255,255,255,.15); color:#fff;
        cursor:pointer; font-size:12px; display:grid; place-items:center;
        transition:background .15s;
      }
      .dcc-close:hover { background:rgba(255,255,255,.28) }

      .dcc-messages {
        flex:1; overflow-y:auto; padding:14px;
        display:flex; flex-direction:column; gap:3px;
        background:var(--bg,#f8f9fb);
        scroll-behavior:smooth;
      }
      .dcc-messages::-webkit-scrollbar { width:4px }
      .dcc-messages::-webkit-scrollbar-thumb { background:var(--border,#e5e7eb); border-radius:4px }

      .dcc-loading {
        display:flex; flex-direction:column; align-items:center;
        gap:9px; padding:40px 20px;
        color:var(--ink3,#9ca3af); font-size:13px;
      }
      .dcc-spinner {
        width:22px; height:22px; border-radius:50%;
        border:2px solid var(--border,#e5e7eb);
        border-top-color:var(--primary,#0F52BA);
        animation:dcc-spin .8s linear infinite;
      }
      @keyframes dcc-spin { to{ transform:rotate(360deg) } }

      .dcc-msg {
        display:flex; gap:7px; align-items:flex-end;
        animation:dcc-fadeup .18s ease;
        max-width:100%;
      }
      @keyframes dcc-fadeup { from{ opacity:0; transform:translateY(5px) } to{ opacity:1; transform:none } }
      .dcc-msg.mine { flex-direction:row-reverse }

      .dcc-msg-avatar {
        width:26px; height:26px; border-radius:50%;
        display:grid; place-items:center;
        font-size:9px; font-weight:700; color:#fff;
        flex-shrink:0; align-self:flex-end; margin-bottom:2px;
      }
      .dcc-msg.mine .dcc-msg-avatar { display:none }

      .dcc-bubble-wrap { display:flex; flex-direction:column; gap:2px; max-width:265px }
      .dcc-msg.mine .dcc-bubble-wrap { align-items:flex-end }

      .dcc-sender { font-size:10px; font-weight:600; color:var(--ink3,#9ca3af); padding:0 9px }

      .dcc-bubble {
        padding:8px 12px; border-radius:13px;
        font-size:12.5px; line-height:1.55;
        word-break:break-word; white-space:pre-wrap;
      }
      .dcc-msg:not(.mine) .dcc-bubble {
        background:var(--surface,#fff);
        border:1px solid var(--border,#e5e7eb);
        color:var(--ink,#111);
        border-bottom-left-radius:4px;
      }
      .dcc-msg.mine .dcc-bubble {
        background:var(--primary,#0F52BA);
        color:#fff;
        border-bottom-right-radius:4px;
      }
      .dcc-time { font-size:9px; color:var(--ink3,#9ca3af); padding:0 9px }

      .dcc-msg.system { justify-content:center; margin:7px 0 }
      .dcc-msg.system .dcc-bubble {
        background:transparent; border:1px dashed var(--border,#e5e7eb);
        color:var(--ink3,#9ca3af); font-size:11px;
        border-radius:18px; padding:4px 13px; text-align:center;
      }
      .dcc-msg.system .dcc-msg-avatar,
      .dcc-msg.system .dcc-sender { display:none }

      .dcc-date-sep {
        display:flex; align-items:center; gap:8px;
        margin:10px 0 5px; color:var(--ink3,#9ca3af); font-size:10px;
      }
      .dcc-date-sep::before,.dcc-date-sep::after {
        content:''; flex:1; height:1px; background:var(--border,#e5e7eb);
      }

      .dcc-empty {
        display:flex; flex-direction:column; align-items:center;
        gap:7px; padding:45px 20px;
        color:var(--ink3,#9ca3af); text-align:center;
      }
      .dcc-empty-icon { font-size:34px; margin-bottom:4px }
      .dcc-empty h4 { font-size:13px; font-weight:600; color:var(--ink2,#6b7280); margin:0 }
      .dcc-empty p  { font-size:12px; margin:0; line-height:1.5 }

      .dcc-composer {
        padding:11px;
        border-top:1px solid var(--border,#e5e7eb);
        background:var(--surface,#fff);
        flex-shrink:0;
      }
      .dcc-composer-inner {
        display:flex; align-items:flex-end; gap:8px;
        background:var(--bg,#f8f9fb);
        border:1.5px solid var(--border,#e5e7eb);
        border-radius:12px; padding:8px 8px 8px 10px;
        transition:border-color .15s;
      }
      .dcc-composer-inner:focus-within {
        border-color:var(--primary,#0F52BA);
        background:var(--surface,#fff);
      }
      .dcc-avatar-mini {
        width:24px; height:24px; border-radius:50%;
        display:grid; place-items:center;
        font-size:8.5px; font-weight:700; color:#fff; flex-shrink:0;
      }
      .dcc-input {
        flex:1; border:none; background:transparent;
        font-size:12.5px; line-height:1.5;
        color:var(--ink,#111); resize:none; outline:none;
        font-family:inherit; max-height:110px; overflow-y:auto;
      }
      .dcc-input::placeholder { color:var(--ink3,#9ca3af) }
      .dcc-send {
        width:32px; height:32px; border-radius:9px; border:none;
        background:var(--primary,#0F52BA); color:#fff;
        cursor:pointer; display:grid; place-items:center;
        flex-shrink:0; transition:transform .1s,opacity .15s; opacity:.45;
      }
      .dcc-send.active { opacity:1 }
      .dcc-send.active:hover  { transform:scale(1.08) }
      .dcc-send.active:active { transform:scale(.93) }
    `;
    document.head.appendChild(style);
    document.body.appendChild(panel);
    chatPanel = panel;

    /* Auto-resize textarea */
    const input   = document.getElementById('dcc-input');
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

  /* — Abrir chat para un ticket — */
  function open(ticketId, ticketTitle) {
    initPanel();
    if (currentTicketId === ticketId && chatPanel.classList.contains('open')) return;

    stopPolling();
    currentTicketId    = ticketId;
    currentTicketTitle = ticketTitle || ticketId;
    lastMessageId      = 0;

    document.getElementById('dcc-title').textContent    = currentTicketTitle;
    document.getElementById('dcc-ticket-id').textContent = ticketId;

    const msgContainer = document.getElementById('dcc-messages');
    msgContainer.innerHTML = `
      <div class="dcc-loading">
        <div class="dcc-spinner"></div>
        <span>Cargando historial...</span>
      </div>`;

    /* Avatar del usuario */
    const session = sessionStorage.getItem('user_session');
    currentUser = session ? JSON.parse(session) : { name: 'Usuario', avatar: '#0F52BA' };
    const av = document.getElementById('dcc-my-avatar');
    if (av) { av.style.background = currentUser.avatar || '#0F52BA'; av.textContent = getInitials(currentUser.name); }

    chatPanel.classList.add('open');
    loadHistory().then(() => startPolling());
  }

  /* — Cerrar — */
  function close() {
    stopPolling();
    if (chatPanel) chatPanel.classList.remove('open');
    currentTicketId = null;
  }

  /* — Cargar historial completo desde activity.php — */
  async function loadHistory() {
    try {
      const res  = await fetch(`${ACT_API}?ticket_id=${currentTicketId}`, { headers: hdrs() });
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
    } catch(err) {
      console.error('[Chat] Error historial:', err);
      setStatus('error', 'Error de conexión');
    }
  }

  /* — Long polling — */
  function startPolling() { isPolling = true; poll(); }
  function stopPolling()  { isPolling = false; if (pollController) { pollController.abort(); pollController = null; } }

  async function poll() {
    if (!isPolling || !currentTicketId) return;
    pollController = new AbortController();
    const tid = setTimeout(() => pollController.abort(), 30000);
    try {
      const res  = await fetch(`${CHAT_API}?ticket_id=${currentTicketId}&last_id=${lastMessageId}`, { headers: hdrs(), signal: pollController.signal });
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
        });
        scrollBottom(true);
        setStatus('connected', 'En vivo');
      }
    } catch(err) {
      clearTimeout(tid);
      if (err.name !== 'AbortError') {
        setStatus('error', 'Reconectando...');
        await sleep(3000);
      }
    }
    if (isPolling) { await sleep(250); poll(); }
  }

  /* — Enviar mensaje — */
  async function _send() {
    const input   = document.getElementById('dcc-input');
    const sendBtn = document.getElementById('dcc-send-btn');
    const text    = input?.value.trim();
    if (!text || !currentTicketId) return;

    /* Inserción optimista */
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

    input.value = ''; input.style.height = 'auto';
    sendBtn.classList.remove('active');
    input.focus();

    try {
      const res  = await fetch(CHAT_API, {
        method:'POST',
        headers:{ ...hdrs(), 'Content-Type':'application/json' },
        body: JSON.stringify({ ticket_id: currentTicketId, message: text, type:'comment', user_name: currentUser?.name })
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
    } catch(err) {
      markFailed(tempId);
    }
  }

  function markFailed(tempId) {
    const el = document.querySelector(`[data-msg-id="${tempId}"]`);
    if (el) { const b = el.querySelector('.dcc-bubble'); if(b){ b.style.opacity='.5'; b.title='Error al enviar'; } }
  }

  /* — Render de burbuja — */
  function renderMsg(msg) {
    const isMe     = msg.user_name === currentUser?.name;
    const isSystem = ['status','assign','resolve','create'].includes(msg.activity_type);
    const cssClass = isSystem ? 'system' : (isMe ? 'mine' : '');
    const avColor  = msg.avatar || strColor(msg.user_name);
    const time     = fmtTime(msg.created_at);
    const sysIcon  = { create:'🎫', status:'🔄', assign:'👤', resolve:'✅' }[msg.activity_type] || '📌';
    if (isSystem) return `
      <div class="dcc-msg system" data-msg-id="${msg.id}">
        <div class="dcc-bubble-wrap"><div class="dcc-bubble">${sysIcon} ${esc(msg.message)} · <span style="opacity:.7">${time}</span></div></div>
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

  /* — Utilidades — */
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
  function strColor(s) {
    const pool=['#0F52BA','#15803D','#B45309','#7C3AED','#D93025','#0D9488','#9333EA','#059669'];
    let h=0; for(let i=0;i<s.length;i++) h=s.charCodeAt(i)+((h<<5)-h);
    return pool[Math.abs(h)%pool.length];
  }
  function sleep(ms) { return new Promise(r=>setTimeout(r,ms)); }

  return { open, close, _send };
})();
</script>
</body>
</html>

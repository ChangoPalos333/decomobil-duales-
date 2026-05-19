<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Métricas — Decomobil</title>
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
    <a class="nav-link" href="admin.php" data-role="agent">⚙️ Administración</a>
    <a class="nav-link" href="users.php" data-role="admin">👥 Usuarios</a>
    <a class="nav-link active" href="metrics.php" data-role="admin">📊 Métricas</a>
  </div>
  <div id="navRight"></div>
</nav>

<div class="page">
  <div id="metricsContent" style="flex:1;overflow-y:auto;padding-top:60px;margin:auto;width:100%">
    <div class="empty" style="padding-top:100px">
      <div class="ei">⏳</div>
      <h3>Cargando métricas...</h3>
      <p>Estamos analizando los datos del sistema.</p>
    </div>
  </div>
</div>

<div id="toastContainer"></div>

<script>
/* ══════════════════════════════════════════════
   DECOMOBIL — metrics.php
   Análisis de datos vía API real
   ══════════════════════════════════════════════ */

const API_BASE = '../xampp_project/api';

const $ = id => document.getElementById(id);
const getInitials = name => String(name || '').split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();

function showToast(msg, type = '') {
  const c = $('toastContainer');
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<span>✕</span><span>${msg}</span>`;
  c.appendChild(el);
  setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3200);
}

function getAuthToken() { return sessionStorage.getItem('auth_token') || ''; }
function apiHeaders() { return { 'Authorization': 'Bearer ' + getAuthToken() }; }

(function initNav() {
  const sessionData = sessionStorage.getItem('user_session');
  const authToken   = sessionStorage.getItem('auth_token');
  if (!sessionData || !authToken) { window.location.href = 'login.php'; return; }
  const s = JSON.parse(sessionData);
  document.querySelectorAll('[data-role]').forEach(el => {
    const r = el.dataset.role;
    const ok = (r === 'agent' && (s.role === 'agent' || s.role === 'admin')) || (r === 'admin' && s.role === 'admin');
    if (!ok) el.style.display = 'none';
  });
  $('navRight').innerHTML = `
    <div class="nav-user">
      <div class="avatar" style="width:28px;height:28px;background:${s.avatar};font-size:10px">${getInitials(s.name)}</div>
      <span style="font-size:13px;font-weight:600;color:rgba(255,255,255,.85)">${s.name.split(' ')[0]}</span>
    </div>
    <button class="nav-btn nb-ghost" onclick="sessionStorage.clear();window.location.href='login.php'">Salir</button>
  `;
})();

window.addEventListener('DOMContentLoaded', () => {
  const sessionData = sessionStorage.getItem('user_session');
  if (!sessionData) return;
  const s = JSON.parse(sessionData);
  if (s.role !== 'admin') { window.location.href = 'home.php'; return; }
  loadMetrics();
});

async function loadMetrics() {
  try {
    const resStats = await fetch(`${API_BASE}/tickets.php?stats=1`, { headers: apiHeaders() });
    const jsonStats = await resStats.json();
    
    const resTickets = await fetch(`${API_BASE}/tickets.php`, { headers: apiHeaders() });
    const jsonTickets = await resTickets.json();

    if (jsonStats.success && jsonTickets.success) {
      renderMetrics(jsonStats.data, jsonTickets.data);
    } else {
      showToast('Error al cargar datos de la API', 'error');
    }
  } catch (err) {
    showToast('Error de conexión con el servidor', 'error');
  }
}

function renderMetrics(stats, allTickets) {
  const byCategory = allTickets.reduce((acc, t) => { acc[t.category] = (acc[t.category] || 0) + 1; return acc; }, {});
  const byDept = allTickets.reduce((acc, t) => { acc[t.dept] = (acc[t.dept] || 0) + 1; return acc; }, {});
  
  // Mapeo robusto de estados (soporta singular/plural y variaciones de la API)
  const getCount = (keys) => {
    return keys.reduce((sum, key) => sum + (stats.by_status[key] || 0), 0);
  };

  const s = {
    total: stats.total,
    open: getCount(['Abierto', 'Abiertos']),
    inProgress: getCount(['En Progreso']),
    review: getCount(['En Revisión', 'En Revision', 'En revisión', 'En revision']),
    pending: getCount(['Pendiente', 'Pendientes']),
    resolved: getCount(['Resuelto', 'Resueltos']),
    resolution: stats.avg_resolution_days || 0,
    byPriority: stats.by_priority || {},
    byCategory,
    byDept
  };

  $('metricsContent').innerHTML = `
    <div class="sh">
      <div>
        <div class="sey">Administración</div>
        <div class="sti">Métricas del Sistema</div>
        <div class="sde">Análisis visual del estado de tickets, categorías y desempeño del equipo.</div>
      </div>
      <div class="sa">
        <span style="font-size:12px;color:var(--ink3);font-family:var(--mono)">${s.total} tickets · actualizado ahora</span>
      </div>
    </div>
    <div style="padding:20px 28px 28px;display:flex;flex-direction:column;gap:20px">
      <!-- KPI row -->
      <div class="stats-grid">
        <div class="stat-card sc-blue"><div class="sv">${s.total}</div><div class="sl">Total tickets</div><div class="ss">Histórico completo</div></div>
        <div class="stat-card sc-red"><div class="sv" style="color:var(--red)">${s.open}</div><div class="sl">Abiertos</div><div class="ss">Sin atender</div></div>
        <div class="stat-card sc-amber"><div class="sv" style="color:var(--amber)">${s.inProgress}</div><div class="sl">En Progreso</div><div class="ss">Siendo atendidos</div></div>
        <div class="stat-card sc-green"><div class="sv" style="color:var(--green)">${s.resolved}</div><div class="sl">Resueltos</div><div class="ss">Prom: ${s.resolution} días</div></div>
        <div class="stat-card sc-violet"><div class="sv" style="color:var(--violet)">${s.review}</div><div class="sl">En Revisión</div><div class="ss">Pendiente aprobación</div></div>
        <div class="stat-card"><div class="sv" style="color:var(--ink3)">${s.pending}</div><div class="sl">Pendientes</div><div class="ss">En espera</div></div>
      </div>

      <!-- Row 2: status progress + category bars -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div class="card">
          <div class="ct">Estado de tickets</div>
          ${[['Total',s.total,'#0F52BA'],['Abiertos',s.open,'#C0392B'],['En Progreso',s.inProgress,'#B45309'],['En Revisión',s.review,'var(--violet)'],['Pendientes',s.pending,'#9CA3AF'],['Resueltos',s.resolved,'#15803D']].map(([l,v,col])=>{
            const p = s.total > 0 ? Math.round(v/s.total*100) : 0;
            return `<div style="margin-bottom:12px">
              <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:4px">
                <span style="color:var(--ink2)">${l}</span>
                <span style="font-weight:700;font-family:var(--mono)">${v} <span style="color:var(--ink3);font-weight:400">${p}%</span></span>
              </div>
              <div style="height:7px;background:var(--bg2);border-radius:4px;overflow:hidden">
                <div style="height:100%;width:${p}%;background:${col};border-radius:4px;transition:width .7s ease"></div>
              </div>
            </div>`;
          }).join('')}
        </div>
        <div class="card">
          <div class="ct">Tickets por Categoría</div>
          ${Object.entries(s.byCategory).sort((a,b)=>b[1]-a[1]).map(([cat,count])=>{
            const p = s.total > 0 ? (count/s.total*100).toFixed(1) : 0;
            const col = {TI:'#0F52BA',RRHH:'#15803D',OPS:'#B45309',CONT:'var(--violet)',VEN:'#D93025',INFRA:'#0D9488',OTROS:'#9CA3AF'}[cat]||'#9CA3AF';
            return `<div style="display:flex;align-items:center;gap:8px;margin-bottom:11px;font-size:12.5px">
              <div style="width:10px;height:10px;border-radius:3px;background:${col};flex-shrink:0"></div>
              <span style="min-width:60px;color:var(--ink2)">${cat}</span>
              <div style="flex:1;height:9px;background:var(--bg2);border-radius:5px;overflow:hidden">
                <div style="height:100%;width:${p}%;background:${col};border-radius:5px;transition:width .7s ease"></div>
              </div>
              <span style="font-family:var(--mono);font-weight:700;min-width:22px;text-align:right">${count}</span>
            </div>`;
          }).join('')}
        </div>
      </div>

      <!-- Row 3: frequency table -->
      <div class="card">
        <div class="ct">Tabla de Problemas por Frecuencia</div>
        <table class="dt">
          <thead><tr><th style="width:36px">#</th><th>CATEGORÍA</th><th style="width:90px">FRECUENCIA</th><th style="width:80px">% TOTAL</th><th style="width:200px">MEDICIÓN VISUAL</th><th style="width:130px">PRIORIDAD DOM.</th></tr></thead>
          <tbody>
            ${Object.entries(s.byCategory).sort((a,b)=>b[1]-a[1]).map(([cat,count],i)=>{
              const p = (s.total > 0 ? count/s.total*100 : 0).toFixed(1);
              const catT = allTickets.filter(t=>t.category===cat);
              const pr = catT.reduce((a,t)=>{a[t.priority]=(a[t.priority]||0)+1;return a;},{});
              const dom = Object.entries(pr).sort((a,b)=>b[1]-a[1])[0]?.[0]||'—';
              const col = {TI:'#0F52BA',RRHH:'#15803D',OPS:'#B45309',CONT:'var(--violet)',VEN:'#D93025',INFRA:'#0D9488',OTROS:'#9CA3AF'}[cat]||'#9CA3AF';
              return `<tr>
                <td style="font-family:var(--mono);font-weight:700;color:var(--ink3)">${String(i+1).padStart(2,'0')}</td>
                <td style="font-weight:600;color:var(--ink)">${cat}</td>
                <td style="font-family:var(--mono);font-weight:700;font-size:15px">${count}</td>
                <td style="font-family:var(--mono)">${p}%</td>
                <td><div style="display:flex;align-items:center;gap:7px">
                  <div style="flex:1;height:8px;background:var(--bg2);border-radius:4px;overflow:hidden">
                    <div style="height:100%;width:${p}%;background:${col};border-radius:4px;transition:width .7s"></div>
                  </div>
                  <span style="font-size:10.5px;color:var(--ink3);font-family:var(--mono);min-width:38px">${p}%</span>
                </div></td>
                <td><span class="badge ${priorityBadgeClass(dom)}">${dom}</span></td>
              </tr>`;
            }).join('')}
          </tbody>
        </table>
      </div>

      <!-- Row 4: by dept + donut -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div class="card">
          <div class="ct">Tickets por Departamento</div>
          ${Object.entries(s.byDept).sort((a,b)=>b[1]-a[1]).map(([dept,count])=>{
            const p = s.total > 0 ? Math.round(count/s.total*100) : 0;
            return `<div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;font-size:12.5px">
              <span style="min-width:96px;color:var(--ink2)">${dept}</span>
              <div style="flex:1;height:10px;background:var(--bg2);border-radius:5px;overflow:hidden">
                <div style="height:100%;width:${p}%;background:var(--primary);border-radius:5px;opacity:${Math.max(0.3,p/100)}"></div>
              </div>
              <span style="font-family:var(--mono);font-weight:700;min-width:24px;text-align:right">${count}</span>
            </div>`;
          }).join('')}
        </div>
        <div class="card">
          <div class="ct">Distribución por Prioridad</div>
          <div id="metricsDonut" style="display:flex;justify-content:center;margin-bottom:18px"></div>
          ${Object.entries(s.byPriority).map(([p,count])=>{
            const pct = s.total > 0 ? (count/s.total*100).toFixed(1) : 0;
            const col = {'Crítica': 'var(--magenta)', 'Alta':'#C0392B','Media':'#B45309','Baja':'#15803D'}[p]||'#ccc';
            return `<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;font-size:12.5px">
              <div style="width:10px;height:10px;border-radius:3px;background:${col};flex-shrink:0"></div>
              <span style="min-width:65px;color:var(--ink2)">${p}</span>
              <div style="flex:1;height:8px;background:var(--bg2);border-radius:4px;overflow:hidden">
                <div style="height:100%;width:${pct}%;background:${col};border-radius:4px;transition:width .7s"></div>
              </div>
              <span style="font-family:var(--mono);font-weight:700;min-width:28px">${count}</span>
              <span style="font-size:11px;color:var(--ink3)">${pct}%</span>
            </div>`;
          }).join('')}
        </div>
      </div>
    </div>
  `;
  drawDonut(s.byPriority, s.total, 'metricsDonut');
}

function priorityBadgeClass(p) {
  return {'Crítica':'b-critical','Alta':'b-high','Media':'b-medium','Baja':'b-low'}[p]||'b-low';
}

function drawDonut(data, total, targetId) {
  const el=$(targetId); if(!el) return;
  const colors={'Crítica': '#D946EF', 'Alta':'#C0392B','Media':'#B45309','Baja':'#15803D'};
  const r=44, circ=2*Math.PI*r, size=120;
  let paths='', offset=0;
  Object.entries(data).forEach(([p,count])=>{
    const pct=total>0?count/total:0, dash=pct*circ, gap=circ-dash, color=colors[p]||'#ccc';
    paths+=`<circle cx="60" cy="60" r="${r}" fill="none" stroke="${color}" stroke-width="16" stroke-dasharray="${dash.toFixed(2)} ${gap.toFixed(2)}" stroke-dashoffset="${(circ/4-offset).toFixed(2)}"></circle>`;
    offset+=dash;
  });
  el.innerHTML=`<div style="position:relative;display:inline-block">
    <svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}"><circle cx="60" cy="60" r="${r}" fill="none" stroke="var(--bg2)" stroke-width="16"/>${paths}</svg>
    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center">
      <div style="font-family:var(--display);font-size:22px;font-weight:800">${total}</div>
      <div style="font-size:10px;color:var(--ink3)">tickets</div>
    </div>
  </div>`;
}
</script>
</body>
</html>

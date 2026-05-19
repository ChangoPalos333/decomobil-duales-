<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Nosotros — Decomobil</title>
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
    <a class="nav-link active" href="company.php">🏢 Nosotros</a>
    <a class="nav-link" href="org.php">🗂 Organigrama</a>
    <a class="nav-link" href="admin.php" data-role="agent">⚙️ Administración</a>
    <a class="nav-link" href="users.php" data-role="admin">👥 Usuarios</a>
    <a class="nav-link" href="metrics.php" data-role="admin">📊 Métricas</a>
  </div>
  <div id="navRight"></div>
</nav>
<div id="detailPanel"></div>
<div class="page">
<div style="padding-top:60px">
    <div class="company-hero">
      <div style="position:relative;max-width:720px; margin:auto;">
        <div style="  font-size:10px;font-weight:700;letter-spacing:20px;text-transform:uppercase;color:rgba(255, 255, 255, 0.929);margin-bottom:10px">DECOMOBIL</div>
        <h1 style="font-family:var(--display);font-size:36px;font-weight:800;color:#fff;letter-spacing:-.5px;line-height:1.2;margin-bottom:16px">Politica de Calidad</h1>
        <p style="font-size:20px;color:rgba(255,255,255,.7);line-height:1.7;max-width:580px">
        Nos comprometemos a diseñar y producir mobiliario que supere las expectativas de nuestros clientes, mediante un control de calidad riguroso en cada etapa de la produccion y la capacitacion continua de nuestros colaboradores,
        asegurandonos de que todas las piezas cumplan con los estandares de calidad antes de su comercializacion.
        La atencion al clientes es fundamental, ofrecemos un servicio al cliente agil y resolutivo,
        estableciendo un sistema de retroalimentacion para mejorar nuestros pasos.
        Ademas, priorizamos el respeto por el medio ambiente y el impacto social,
        reduciendo nuestra huella ambiental utilizando materiales sostenibles y apoyando iniciativas locales que reflejen nuestros valores de integridad y disciplina
        </p>
      </div>
    </div>

    <div class="pillar-grid" style="background:var(--bg)">
      <!-- Misión -->
        <div class="pillar-card">
            <div style="width:44px;height:44px;background:var(--primary-light);border-radius:10px;display:grid;place-items:center;font-size:22px;margin-bottom:14px">🎯</div>
            <div style="font-family:var(--display);font-size:26px;font-weight:700;color:var(--primary);margin-bottom:8px;letter-spacing:-.2px">Misión</div>
            <p style="font-size:20px;color:var(--ink);line-height:1.7">
            Diseñar, producir y comercializar mobiliario practico, funcional, seguro y atractivo para todos nuestros clientes, a traves de los canales digitales y fisicos necesarios que faciliten su adquisicion, con una buena relacion costo-beneficio y un excelente servicio de atencion al cliente.
            </p>
        </div>
        <!-- Visión -->
        <div class="pillar-card">
            <div style="width:44px;height:44px;background:#EDE9FE;border-radius:10px;display:grid;place-items:center;font-size:22px;margin-bottom:14px">🔭</div>
            <div style="font-family:var(--display);font-size:26px;font-weight:700;color:var(--violet);margin-bottom:8px;letter-spacing:-.2px">Visión</div>
            <p style="font-size:20px;color:var(--ink);line-height:1.7">
            Ser una empresa estructuralmente fuerte en todas sus areas operativas, tener presencia de marca y comercializacion nacional e internacional a traves de cnales de venta externos y propios con un catalogo amplio y que cubra todas las necesidades de mobiliario de nuestros clientes.
            </p>
        </div>
        <!-- Valores -->
        <div class="pillar-card">
            <div style="width:44px;height:44px;background:var(--green-light);border-radius:10px;display:grid;place-items:center;font-size:22px;margin-bottom:14px">⭐</div>
            <div style="font-family:var(--display);font-size:26px;font-weight:700;color:var(--green);margin-bottom:8px;letter-spacing:-.2px">Valores</div>
            <div style="display:flex;flex-direction:column;gap:7px">
            <p style="font-size:20px;color:var(--ink);line-height:1.7">
                El respeto, la integridad y la disciplina son los valores que mueven a todos nuestros colaboradores para realizar con seguridad, pasion y calidad todos nuestros productos, cuidando siempre la sustentabilidad y el impacto social de nuestras acciones.
            </p>
            <div style="display:flex;gap:8px;align-items:flex-start">
            </div>
        </div>
        </div>

    <!-- Team -->
   
    </div>
</div>
</div>

<div id="toastContainer"></div>
<script>
/* ══ HELPERS ══ */
const $ = id => document.getElementById(id);
const $$ = s => document.querySelectorAll(s);
const getInitials = (name) => String(name||'').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();

//Esto detecta la seción
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
    document.querySelectorAll('.overlay').forEach(o => o.classList.remove('open'));
  }
});

window.addEventListener("DOMContentLoaded", ()=>{});
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Organigrama — Decomobil</title>
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
    <a class="nav-link active" href="org.php">🗂 Organigrama</a>
    <a class="nav-link" href="admin.php" data-role="agent">⚙️ Administración</a>
    <a class="nav-link" href="users.php" data-role="admin">👥 Usuarios</a>
    <a class="nav-link" href="metrics.php" data-role="admin">📊 Métricas</a>
  </div>
  <div id="navRight"></div>
</nav>
<div id="detailPanel"></div>
<div class="page">
 <div style="width:100%; height:100vh; display:flex; justify-content:center; align-items:center;">
  <img src="../rc/organigrama.png" style="
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  ">
</div>

<div id="homeContent" style="flex:1;display:flex;flex-direction:column;overflow-y:auto;padding-top:60px;margin:auto"></div>

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
</script>
</body>
</html>
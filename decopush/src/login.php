<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Iniciar sesión — Decomobil</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* Login is fixed overlay, no flash prevention needed */
/* ═══════════════════ TOKENS ═══════════════════ */
:root {
  --ink:#0E1117; --ink2:#374151; --ink3:#6B7280; --ink4:#9CA3AF;
  --bg:#F7F8FA; --bg2:#EDEEF1;
  --surface:#FFFFFF; --surface2:#F9FAFB;
  --border:#E5E7EB; --border2:#D1D5DB;
  --primary:#0F52BA; --primary-dark:#0A3D8F; --primary-light:#EBF0FB;
  --red:#C0392B; --red-light:#FEF2F2;
  --amber:#B45309; --amber-light:#FFFBEB;
  --green:#15803D; --green-light:#F0FDF4;
  --violet:#6D28D9; --violet-light:#F5F3FF;
  --teal:#0D9488;
  --display:'Arial'; --body:'Arial'; --mono:'Arial' ;
  --radius:10px; --radius-sm:6px; --radius-lg:16px;
  --shadow-sm:0 1px 4px rgba(0,0,0,.08); --shadow:0 4px 16px rgba(0,0,0,.10); --shadow-lg:0 20px 48px rgba(0,0,0,.15);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--body);background:var(--bg);color:var(--ink);-webkit-font-smoothing:antialiased;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:var(--body);cursor:pointer;border:none;background:none}
input,select,textarea{font-family:var(--body)}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-thumb{background:var(--border2);border-radius:3px}

/* ═══ NAVBAR ═══ */
#mainNav{position:fixed;top:0;left:0;right:0;height:60px;background:#0E1117;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;padding:0 24px;z-index:1000;gap:0}
.nav-brand{display:flex;align-items:center;gap:10px;color:#fff;flex-shrink:0;text-decoration:none}
.nav-logo{width:32px;height:32px;background:var(--primary);border-radius:8px;display:grid;place-items:center;font-family:var(--display);font-size:13px;font-weight:800;color:#fff}
.nav-name{font-family:var(--display);font-size:18px;font-weight:700;color:#fff;letter-spacing:-.3px}
.nav-sep{width:1px;height:22px;background:rgba(255,255,255,.1);margin:0 18px}
#navLinks{display:flex;align-items:center;gap:2px;flex:1}
.nav-link{display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:7px;font-size:13px;font-weight:500;color:rgba(255,255,255,.55);cursor:pointer;transition:all .15s;white-space:nowrap;border:1px solid transparent}
.nav-link:hover{color:#fff;background:rgba(255,255,255,.07)}
.nav-link.active{color:#fff;background:rgba(255,255,255,.10);border-color:rgba(255,255,255,.12);font-weight:600}
#navRight{display:flex;align-items:center;gap:8px;margin-left:auto}
.nav-user{display:flex;align-items:center;gap:8px;padding:4px 12px 4px 4px;border-radius:20px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);cursor:pointer}
.nav-user:hover{background:rgba(255,255,255,.12)}
.nav-avatar{width:28px;height:28px;border-radius:50%;display:grid;place-items:center;font-size:10px;font-weight:700;color:#fff}
.nav-uname{font-size:12.5px;font-weight:500;color:rgba(255,255,255,.8)}
.nav-btn{height:32px;padding:0 14px;border-radius:7px;font-size:12.5px;font-weight:600;display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:all .15s}
.nb-primary{background:var(--primary);color:#fff}
.nb-primary:hover{background:var(--primary-dark)}
.nb-ghost{border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.65)}
.nb-ghost:hover{background:rgba(255,255,255,.08);color:#fff}

/* ═══ PAGE ═══ */
.page{padding-top:60px;min-height:100vh;display:flex;flex-direction:column}
.section{display:none;flex:1;flex-direction:column}
.section.active{display:flex}

/* ═══ BUTTONS ═══ */
.btn{display:inline-flex;align-items:center;gap:7px;height:36px;padding:0 18px;border-radius:var(--radius-sm);font-size:13.5px;font-weight:600;cursor:pointer;transition:all .15s;border:1.5px solid transparent;white-space:nowrap;font-family:var(--body)}
.btn-sm{height:30px;padding:0 12px;font-size:12px}
.btn-lg{height:44px;padding:0 28px;font-size:14px;border-radius:8px}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-primary:hover{background:var(--primary-dark)}
.btn-ghost{border-color:var(--border2);color:var(--ink2);background:var(--surface)}
.btn-ghost:hover{background:var(--bg)}
.btn-danger{background:var(--red);color:#fff}
.btn-danger:hover{background:#a93226}
.btn-success{background:var(--green);color:#fff}

/* ═══ FIELDS ═══ */
.fg{display:flex;flex-direction:column;gap:5px}
.fl{font-size:12px;font-weight:600;color:var(--ink2)}
.fl .r{color:var(--red)}
.fi,.fs,.ft{width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 11px;font-size:13.5px;color:var(--ink);background:var(--surface);outline:none;transition:border-color .15s,box-shadow .15s;font-family:var(--body)}
.fi:focus,.fs:focus,.ft:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(15,82,186,.12)}
.ft{resize:vertical;min-height:80px;line-height:1.5}
.fe{font-size:11.5px;color:var(--red);display:none}
.fe.show{display:block}
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.fg3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}

/* ═══ BADGES ═══ */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:5px;font-size:11.5px;font-weight:600;white-space:nowrap}
.bd{width:5px;height:5px;border-radius:50%;flex-shrink:0}
.b-open{background:var(--primary-light);color:var(--primary)}
.b-progress{background:var(--amber-light);color:var(--amber)}
.b-review{background:var(--violet-light);color:var(--violet)}
.b-pending{background:var(--bg2);color:var(--ink3);border:1px solid var(--border)}
.b-resolved{background:var(--green-light);color:var(--green)}
.b-red{background:var(--red-light);color:var(--red)}
.b-critical{background:#2D1B6B;color:#C4B5FD}
.b-high{background:var(--red-light);color:var(--red)}
.b-medium{background:var(--amber-light);color:var(--amber)}
.b-low{background:var(--green-light);color:var(--green)}

/* ═══ CARDS ═══ */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px}
.ct{font-family:var(--display);font-size:15px;font-weight:700;color:var(--ink);margin-bottom:14px}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px;display:flex;flex-direction:column;gap:6px;position:relative;overflow:hidden;transition:box-shadow .2s,transform .15s}
.stat-card:hover{box-shadow:var(--shadow);transform:translateY(-1px)}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.sc-blue::before{background:var(--primary)} .sc-red::before{background:var(--red)}
.sc-amber::before{background:var(--amber)} .sc-green::before{background:var(--green)}
.sc-violet::before{background:var(--violet)}
.sc-violet::before{background:var(--violet)}
.sv{font-family:var(--display);font-size:30px;font-weight:800;color:var(--ink);line-height:1;letter-spacing:-1px}
.sl{font-size:12px;color:var(--ink3);font-weight:500}
.ss{font-size:11px;color:var(--ink4)}

/* ═══ MODAL ═══ */
.overlay{position:fixed;inset:0;background:rgba(14,17,23,.55);backdrop-filter:blur(3px);z-index:2000;display:none;align-items:center;justify-content:center;padding:20px}
.overlay.open{display:flex;animation:fade-in .2s ease}
@keyframes fade-in{from{opacity:0}to{opacity:1}}
.modal{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);width:100%;max-width:560px;max-height:calc(100vh - 40px);display:flex;flex-direction:column;overflow:hidden;animation:modal-in .25s cubic-bezier(.34,1.4,.64,1)}
.modal-wide{max-width:680px}
@keyframes modal-in{from{opacity:0;transform:scale(.94) translateY(8px)}to{opacity:1;transform:scale(1) translateY(0)}}
.mh{padding:18px 20px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.mt{font-family:var(--display);font-size:15px;font-weight:700;color:var(--ink)}
.mc{width:28px;height:28px;border-radius:6px;display:grid;place-items:center;color:var(--ink3);cursor:pointer;transition:all .12s}
.mc:hover{background:var(--bg)}
.mb{padding:20px;overflow-y:auto;display:flex;flex-direction:column;gap:14px;flex:1}
.mf{padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;background:var(--surface2);flex-shrink:0}

/* ═══ TOAST ═══ */
#toastContainer{position:fixed;bottom:20px;right:20px;display:flex;flex-direction:column-reverse;gap:8px;z-index:9999;pointer-events:none}
.toast{display:flex;align-items:center;gap:10px;padding:11px 16px;border-radius:var(--radius-sm);font-size:13.5px;font-weight:500;color:#fff;box-shadow:var(--shadow);min-width:240px;max-width:340px;animation:toast-in .3s cubic-bezier(.34,1.4,.64,1);pointer-events:auto;background:var(--ink)}
.toast.success{background:#166534} .toast.error{background:#991b1b} .toast.warning{background:#92400e}
@keyframes toast-in{from{opacity:0;transform:translateX(20px) scale(.95)}to{opacity:1;transform:translateX(0) scale(1)}}

/* ═══ TABLE ═══ */
.dt{width:100%;border-collapse:collapse}
.dt th{padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--ink3);background:var(--surface2);border-bottom:1px solid var(--border);white-space:nowrap}
.dt td{padding:11px 14px;font-size:13px;color:var(--ink2);border-bottom:1px solid var(--border);vertical-align:middle}
.dt tr:last-child td{border-bottom:none}
.dt tr:hover td{background:var(--surface2)}
.dt tr{transition:background .1s}

/* ═══ SIDE PANEL ═══ */
#detailPanel{position:fixed;top:60px;right:0;bottom:0;width:460px;background:var(--surface);border-left:1px solid var(--border);box-shadow:-8px 0 32px rgba(0,0,0,.08);z-index:900;display:none;flex-direction:column;overflow:hidden}
#detailPanel.open{display:flex;animation:panel-in .22s ease}
@keyframes panel-in{from{transform:translateX(20px);opacity:0}to{transform:translateX(0);opacity:1}}

/* ═══ AVATAR ═══ */
.avatar{border-radius:50%;display:grid;place-items:center;font-weight:700;color:#fff;flex-shrink:0}

/* ═══ LOGIN ═══ */
/* ══ LOGIN SCREEN (fullscreen fixed, hides app) ══ */
#section-login{position:fixed;inset:0;z-index:9999;background:#0E1117;display:none;grid-template-columns:1fr 1fr}
#section-login.active{display:grid}
.ll{background:linear-gradient(160deg,#0E1117 0%,#0c2d6b 55%,#0F52BA 100%);display:flex;flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden}
.ll::before{content:'';position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(15,82,186,.35) 0%,transparent 70%);top:-100px;right:-100px;pointer-events:none}
.ll::after{content:'';position:absolute;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(15,82,186,.2) 0%,transparent 70%);bottom:-60px;left:60px;pointer-events:none}
.lgrid{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none}
.lr{background:#fff;display:flex;align-items:center;justify-content:center;padding:48px 40px}
.lbox{width:100%;max-width:380px;animation:lappear .5s cubic-bezier(.34,1.2,.64,1) both}
@keyframes lappear{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.di{display:flex;align-items:center;justify-content:space-between;padding:9px 12px;background:#fff;border:1.5px solid var(--border);border-radius:7px;cursor:pointer;transition:border-color .15s,box-shadow .12s;margin-bottom:6px}
.di:last-child{margin-bottom:0}
.di:hover{border-color:var(--primary);box-shadow:0 0 0 3px rgba(15,82,186,.08)}
.lbtn{width:100%;height:46px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-family:var(--display);font-size:15px;font-weight:700;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 16px rgba(15,82,186,.3);margin-bottom:24px}
.lbtn:hover{background:var(--primary-dark);box-shadow:0 6px 20px rgba(15,82,186,.4)}
.lbtn:active{transform:scale(.98)}
#loginError{display:none;background:var(--red-light);color:var(--red);font-size:13px;padding:10px 14px;border-radius:7px;border-left:3px solid var(--red);margin-bottom:16px;font-weight:500}
#loginError.vis{display:block;animation:lshake .3s ease}
@keyframes lshake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@media(max-width:768px){#section-login{grid-template-columns:1fr}.ll{display:none}}

/* ═══ COMPANY PAGE ═══ */
.company-hero{background:linear-gradient(135deg,#0E1117,#0F52BA);padding:56px 48px;color:#fff;position:relative;overflow:hidden}
.company-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 70% 50%,rgba(255,255,255,.06),transparent 60%)}
.pillar-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;padding:32px 48px}
.pillar-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:24px;transition:box-shadow .2s,transform .15s}
.pillar-card:hover{box-shadow:var(--shadow);transform:translateY(-2px)}

/* ═══ ORG CHART ═══ */
.org-section{padding:32px 48px}
.org-tree{display:flex;flex-direction:column;align-items:center;gap:0;padding:24px 0}
.org-level{display:flex;justify-content:center;gap:24px;position:relative}
.org-node{background:var(--surface);border:1.5px solid var(--border);border-radius:10px;padding:14px 18px;text-align:center;min-width:130px;box-shadow:var(--shadow-sm);transition:box-shadow .2s,transform .15s;cursor:default}
.org-node:hover{box-shadow:var(--shadow);transform:translateY(-2px)}
.org-node.top{border-color:var(--primary);background:var(--primary-light)}
.org-node.mid{border-color:var(--border)}
.org-connector{width:2px;height:28px;background:var(--border);margin:0 auto}
.org-h-line{height:2px;background:var(--border);margin:0 -12px}
.org-level-wrap{display:flex;flex-direction:column;align-items:center}
.org-name{font-family:var(--display);font-size:12.5px;font-weight:700;color:var(--ink)}
.org-role{font-size:11px;color:var(--ink3);margin-top:2px}
.org-dept{font-size:10px;font-weight:600;color:var(--primary);background:var(--primary-light);padding:2px 7px;border-radius:4px;margin-top:5px;display:inline-block}

/* ═══ SECTION HEADER ═══ */
.sh{padding:24px 28px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap}
.sey{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--primary);margin-bottom:4px}
.sti{font-family:var(--display);font-size:22px;font-weight:800;color:var(--ink);letter-spacing:-.5px}
.sde{font-size:13.5px;color:var(--ink3);margin-top:4px}
.sa{display:flex;gap:8px;align-items:center;flex-wrap:wrap}

/* ═══ HOME ═══ */
#homeContent{flex:1;display:flex;flex-direction:column;overflow-y:auto}
#newTicketContent{flex:1;overflow-y:auto}
#adminContent{flex:1;display:flex;flex-direction:column;overflow-y:auto}

/* ═══ PULSE ANIMATION ═══ */
@keyframes dot-pulse{0%,100%{box-shadow:0 0 0 0 rgba(180,83,9,.5)}50%{box-shadow:0 0 0 4px rgba(180,83,9,0)}}
.dot-pulse{animation:dot-pulse 2s infinite}

/* ═══ EMPTY ═══ */
.empty{text-align:center;padding:48px 20px;color:var(--ink3)}
.empty .ei{font-size:36px;margin-bottom:12px}
.empty h3{font-size:15px;color:var(--ink2);font-weight:600;margin-bottom:5px}

/* ═══ MISC ═══ */
.divider{height:1px;background:var(--border)}
</style>
<style>
#section-login{position:fixed;inset:0;z-index:9999;background:#0E1117;display:grid;grid-template-columns:1fr 1fr}
@media(max-width:768px){#section-login{grid-template-columns:1fr}.ll{display:none}}
</style>
</head>
<body style="display:grid;grid-template-columns:1fr 1fr;min-height:100vh;padding-top:0;background:#0E1117">
<div class="ll">
      <div class="lgrid"></div>
      <div style="position:relative;z-index:1;display:flex;align-items:center;gap:12px">
        <div style="width:40px;height:40px;background:var(--primary);border-radius:10px;display:grid;place-items:center;font-family:var(--display);font-size:16px;font-weight:800;color:#fff;box-shadow:0 4px 16px rgba(15,82,186,.5)">DC</div>
        <div><div style="font-family:var(--display);font-size:20px;font-weight:800;color:#fff;letter-spacing:-.3px">DECOMOBIL</div><div style="font-size:11px;color:rgba(255,255,255,.4);font-weight:600;letter-spacing:1.5px;text-transform:uppercase">Sistema de Tickets</div></div>
      </div>
      <div style="position:relative;z-index:1">
        <div style="font-size:11px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:16px">Soporte Interno</div>
        <h1 style="font-family:var(--display);font-size:42px;font-weight:800;color:#fff;line-height:1.15;letter-spacing:-1.5px;margin-bottom:20px">Gestión de<br>tickets <span style="color:rgba(255, 255, 255, 0.913)">sin</span><br>complicaciones.</h1>
        <p style="font-size:15px;color:rgba(255,255,255,.55);line-height:1.7;max-width:340px">Reporta problemas, asigna solicitudes y da seguimiento — todo en un solo lugar.</p>
      </div>
      <div style="position:relative;z-index:1;display:flex;gap:32px">
        <div><div style="font-family:var(--display);font-size:26px;font-weight:800;color:#fff;letter-spacing:-1px">100%</div><div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:2px">En tu navegador</div></div>
        <div><div style="font-family:var(--display);font-size:26px;font-weight:800;color:#fff;letter-spacing:-1px">3</div><div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:2px">Niveles de acceso</div></div>
        <div><div style="font-family:var(--display);font-size:26px;font-weight:800;color:#fff;letter-spacing:-1px">∞</div><div style="font-size:12px;color:rgba(255,255,255,.4);margin-top:2px">Historial</div></div>
      </div>
    </div>
    <div class="lr">
      <div class="lbox">
        <div style="font-family:var(--display);font-size:26px;font-weight:800;color:var(--ink);letter-spacing:-.5px;margin-bottom:6px">Bienvenido</div>
        <div style="font-size:14px;color:var(--ink3);margin-bottom:32px">Inicia sesión para acceder al sistema.</div>
        <div class="fg" style="margin-bottom:16px">
          <label class="fl">Usuario</label>
          <input class="fi" id="loginInitials" type="text" placeholder="ej: FUribe (Francisco Uribe)" style="height:44px;font-size:14px" maxlength="30" onkeydown="if(event.key==='Enter')document.getElementById('loginPass').focus()">
        </div>
        <div class="fg" style="margin-bottom:16px">
          <label class="fl">Contraseña</label>
          <input class="fi" id="loginPass" type="password" placeholder="••••••••" style="height:44px;font-size:14px" onkeydown="if(event.key==='Enter')tryLogin()">
        </div>
        <div id="loginError"></div>
        <button class="lbtn" id="loginBtn" onclick="tryLogin()">
          <span id="loginBtnText">Ingresar al sistema</span>
          <svg id="loginBtnIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 16 16"><line x1="3" y1="8" x2="13" y2="8"/><polyline points="9,4 13,8 9,12"/></svg>
        </button>

        </div>
      </div>
    </div>
  </div>
<div id="toastContainer"></div>
<script>
/* ══ CONSTANTS ══ */
const API_BASE = '../xampp_project/api';

/* ══ SESSION MANAGER ══ */
const Session = {
  set: (user, token) => {
    sessionStorage.setItem('auth_token', token);
    sessionStorage.setItem('user_session', JSON.stringify(user));
  },
  get: () => JSON.parse(sessionStorage.getItem('user_session') || 'null'),
  getToken: () => sessionStorage.getItem('auth_token'),
  clear: () => {
    sessionStorage.removeItem('auth_token');
    sessionStorage.removeItem('user_session');
  },
  isAuthenticated: () => {
    return !!sessionStorage.getItem('auth_token') && !!sessionStorage.getItem('user_session');
  }
};

/* ══ HELPERS ══ */
const $ = id => document.getElementById(id);
const $$ = s => document.querySelectorAll(s);

function formatDate(d) {
  if (!d) return '—';
  const dt = new Date(d + 'T12:00:00');
  const m  = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  return `${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear()}`;
}

function showToast(msg, type='', icon='') {
  const c=$('toastContainer'); const icons={success:'✓',error:'✕',warning:''};
  const el=document.createElement('div'); el.className=`toast ${type}`;
  el.innerHTML=`<span style="font-size:15px">${icon||icons[type]||'ℹ'}</span><span>${msg}</span>`;
  c.appendChild(el);
  setTimeout(()=>{ el.style.opacity='0'; el.style.transform='translateX(20px) scale(.95)'; el.style.transition='all .3s'; setTimeout(()=>el.remove(),300); },3200);
}

function initials(name) {
  // Formato: primera letra del primer nombre + apellido completo
  // Ejemplo: "Francisco Uribe" -> "FUribe"
  const words = name.trim().split(/\s+/);
  if (words.length === 0) return '';
  return words[0][0].toUpperCase() + (words[1] || '');
}

function getAvatarColor(role) {
  const colors = {
    'admin': '#0F52BA',
    'agent': '#15803D',
    'user': '#6D28D9'
  };
  return colors[role] || '#9CA3AF';
}

/* ══ LOAD USERS FROM API ══ */
async function loadDemoUsers() {
  try {
    const response = await fetch(`${API_BASE}/users.php`);
    if (!response.ok) throw new Error('Error cargando usuarios');
    
    const result = await response.json();
    
    // Verificar que la respuesta sea exitosa
    if (!result.success || !result.data) {
      throw new Error('Respuesta inválida de la API');
    }
    
    const users = result.data;
    const container = document.getElementById('demoUsers');
    
    // Limitar a 3 usuarios de demostración
    const demoUsers = users.slice(0, 3);
    
    let html = '<div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--ink4);margin-bottom:10px">Usuarios disponibles</div>';
    
    demoUsers.forEach(user => {
      const roleEmojis = {'admin': '👑', 'agent': '🛠', 'user': '👤'};
      const emoji = roleEmojis[user.role] || '👤';
      const userInitials = initials(user.name);
      html += `<div class="di" onclick="fillLogin('${userInitials}','')"><span style="font-size:12.5px;font-weight:600;color:var(--ink)">${emoji} ${user.name}</span><span style="font-size:11px;color:var(--ink4);font-family:monospace">${userInitials}</span></div>`;
    });
    
    container.innerHTML = html;
  } catch (error) {
    console.error('Error:', error);
    const container = document.getElementById('demoUsers');
    container.innerHTML = '<div style="font-size:11px;color:var(--red);padding:10px">No se pudieron cargar los usuarios</div>';
  }
}

/* ══ LOGIN FUNCTION ══ */
async function tryLogin() {
  const initials = document.getElementById('loginInitials').value.trim();
  const password = document.getElementById('loginPass').value;
  const errorDiv = document.getElementById('loginError');
  const loginBtn = document.getElementById('loginBtn');
  const loginBtnText = document.getElementById('loginBtnText');
  
  // Validar campos
  errorDiv.classList.remove('vis');
  if (!initials || !password) {
    errorDiv.textContent = 'Ingresa tu usuario y contraseña.';
    errorDiv.classList.add('vis');
    return;
  }
  
  try {
    // Desabilitar botón durante la petición
    loginBtn.disabled = true;
    loginBtnText.textContent = 'Iniciando sesión...';
    
    // Hacer petición POST a la API
    const response = await fetch(`${API_BASE}/auth.php?action=login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        initials: initials,
        password: password
      })
    });
    
    const data = await response.json();
    
    if (data.success && data.user && data.token) {
      // Guardar sesión y usuario
      Session.set(data.user, data.token);
      
      // Mostrar toast de éxito
      showToast(`¡Bienvenido ${data.user.name}!`, 'success', '✓');
      
      // Redirigir después de 600ms
      document.body.style.transition = 'opacity .35s ease';
      document.body.style.opacity = '0';
      setTimeout(() => {
        window.location.href = 'home.php';
      }, 370);
    } else {
      // Error de login
      errorDiv.textContent = data.error || 'Error al iniciar sesión';
      errorDiv.classList.add('vis');
      document.getElementById('loginPass').value = '';
      loginBtn.disabled = false;
      loginBtnText.textContent = 'Ingresar al sistema';
    }
  } catch (error) {
    console.error('Error:', error);
    errorDiv.textContent = 'Error de conexión. Verifica que la API esté disponible.';
    errorDiv.classList.add('vis');
    loginBtn.disabled = false;
    loginBtnText.textContent = 'Ingresar al sistema';
  }
}

/* ══ FILL LOGIN (Demo Users) ══ */
function fillLogin(userInitials, password) {
  document.getElementById('loginInitials').value = userInitials;
  document.getElementById('loginPass').value = password;
  document.getElementById('loginPass').focus();
  document.getElementById('loginError').classList.remove('vis');
}

/* ══ INITIALIZATION ══ */
window.addEventListener('DOMContentLoaded', () => {
  // Si ya hay sesión activa, redirigir a home
  if (Session.isAuthenticated()) {
    window.location.href = 'home.php';
    return;
  }
  
  // Enfocar en el campo de email
  document.getElementById('loginEmail').focus();
  
  // Cargar usuarios disponibles
  loadDemoUsers();
});


</script>
</body>
</html>
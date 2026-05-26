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
    <a class="nav-link" href="org.php">🗂 Organigrama</a>
    <a class="nav-link" href="admin.php" data-role="agent">⚙️ Administración</a>
    <a class="nav-link" href="users.php" data-role="admin">👥 Usuarios</a>
    <a class="nav-link" href="metrics.php" data-role="admin">📊 Métricas</a>
  </div>
  <div id="navRight"></div>
</nav>
<div id="detailPanel"></div>
<div class="page">
  <div class="org-marc">
    <h1 id="org0">ORGANIGRAMA DECOMOBIL</h1>
    
   <div class="org-tree-wrap">
   <div class="org-tree">

  <ul>
    <li>
      <div class="org-tag main">
        <div class="org-name">Jorge Santiago Solis</div>
        <div class="org-desc">Dirección General</div>
      </div>

      <ul>

        <!-- SUBDIRECCION -->
        <li>
          <div class="org-tag">
            <div class="org-name">Elizabeth Uribe Avelar</div>
            <div class="org-desc">Subdirección</div>
          </div>

          <ul>

            <!-- RH -->
            <li>
              <div class="org-tag">
                <div class="org-name">Monserrat Barajas Gutierrez</div>
                <div class="org-desc">Gerente RH</div>
              </div>
            </li>

            <!-- FINANZAS -->
            <li>
              <div class="org-tag">
                <div class="org-name">Sergio Eduardo Garcia Ocampo</div>
                <div class="org-desc">Gerente Finanzas</div>
              </div>
            </li>

            <!-- COMPRAS -->
            <li>
              <div class="org-tag">
                <div class="org-name">Genesis Mariangel Guerrero</div>
                <div class="org-desc">Compras</div>
              </div>
            </li>

            <!-- OPERACIONES -->
            <li>
              <div class="org-tag operations">
                <div class="org-name">Enrique Antonio Dueñas</div>
                <div class="org-desc">Gerente Operaciones</div>
              </div>

              <ul>

                <li>
                  <div class="org-sec">Producción</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Sergio Manuel Rios
                      </div>
                    </li>

                    <li>
                      <div class="org-tag small">
                        Monica Amezquita
                      </div>
                    </li>

                    <li>
                      <div class="org-tag small">
                        Yadira Rodriguez
                      </div>
                    </li>
                  </ul>
                </li>

                <li>
                  <div class="org-sec">Mantenimiento</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Francisco Javier Uribe
                      </div>
                    </li>
                  </ul>
                </li>

                <li>
                  <div class="org-sec">Sistemas</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Omar Alejandro Uribe
                      </div>
                    </li>
                  </ul>
                </li>

                <li>
                  <div class="org-sec">Almacén PT</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Alberto Carrillo
                      </div>
                    </li>
                  </ul>
                </li>

                <li>
                  <div class="org-sec">Logística</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Francisco Javier Contreras
                      </div>
                    </li>
                  </ul>
                </li>

              </ul>
            </li>

            <!-- VENTAS -->
            <li>
              <div class="org-tag">
                <div class="org-name">Juan Rubi Avila</div>
                <div class="org-desc">Gerente Ventas</div>
              </div>

              <ul>
                <li>
                  <div class="org-sec">Ingeniería y Diseño</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Aline Andre Yanez
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>

            <!-- CALIDAD -->
            <li>
              <div class="org-tag">
                <div class="org-name">Juan Pablo Santiago Solis</div>
                <div class="org-desc">Gestión y Calidad</div>
              </div>

              <ul>

                <li>
                  <div class="org-sec">Sistemas de Gestión</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Yesica Isabel Orozco
                      </div>
                    </li>
                  </ul>
                </li>

                <li>
                  <div class="org-sec">Calidad</div>

                  <ul>
                    <li>
                      <div class="org-tag small">
                        Luis Diego Miramontes
                      </div>
                    </li>
                  </ul>
                </li>

              </ul>

            </li>

          </ul>

        </li>

      </ul>

    </li>
  </ul>

</div><!-- /org-tree -->
  </div><!-- /org-tree-wrap -->
</div>

<div id="toastContainer"></div>
<script>
  /* ══ HELPERS ══ */
  const $ = id => document.getElementById(id);
  const $$ = s => document.querySelectorAll(s);
  const getInitials = (name) => String(name||'').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();


  //!Esto detecta la seción =========================================================
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
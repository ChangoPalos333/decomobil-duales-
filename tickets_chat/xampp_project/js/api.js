/**
 * =============================================
 * DECOMOBIL - Cliente API JavaScript
 * =============================================
 * 
 * Este archivo reemplaza las funciones de localStorage con llamadas
 * a la API PHP/MySQL del backend. Proporciona una interfaz compatible
 * con el codigo existente para facilitar la migracion.
 * 
 * USO:
 * 1. Incluir este archivo en tus HTML:
 *    <script src="js/api.js"></script>
 * 
 * 2. Usar el objeto DB igual que antes, pero con async/await:
 *    const user = await DB.login('email@empresa.com', 'password');
 *    const tickets = await DB.tickets();
 * 
 * FUNCIONES DISPONIBLES:
 * - DB.login(email, password)    - Iniciar sesion
 * - DB.logout()                  - Cerrar sesion
 * - DB.session()                 - Obtener sesion actual (sincrono)
 * - DB.getSession()              - Verificar sesion con API (async)
 * - DB.users()                   - Listar usuarios
 * - DB.userById(id)              - Obtener usuario por ID
 * - DB.saveUser(data)            - Crear/actualizar usuario
 * - DB.deleteUser(id)            - Eliminar usuario
 * - DB.tickets()                 - Listar tickets
 * - DB.ticketById(id)            - Obtener ticket por ID
 * - DB.saveTicket(data)          - Crear/actualizar ticket
 * - DB.deleteTicket(id)          - Eliminar ticket
 * - DB.addActivity(...)          - Agregar comentario a ticket
 * - DB.stats()                   - Obtener estadisticas
 * 
 * REQUISITOS:
 * - Navegador moderno con soporte para fetch y async/await
 * - API PHP corriendo en XAMPP
 */

// =============================================
// CONFIGURACION
// =============================================

/**
 * URL base de la API
 * Cambiar si la API esta en otra ubicacion
 * Ejemplo: 'http://localhost:8080/decomobil/api' si usas otro puerto
 */
const API_BASE = 'http://localhost/decomobil/api';


// =============================================
// FUNCIONES DE AUTENTICACION (HELPERS)
// =============================================

/**
 * Obtener el token de sesion guardado en localStorage
 * El token se guarda al hacer login y se usa para autenticar peticiones
 * 
 * @returns {string|null} Token de sesion o null si no hay sesion
 */
function getToken() {
    return localStorage.getItem('df_token');
}

/**
 * Guardar el token de sesion en localStorage
 * Se llama automaticamente despues de un login exitoso
 * 
 * @param {string} token - Token de sesion del servidor
 */
function setToken(token) {
    localStorage.setItem('df_token', token);
}

/**
 * Obtener los headers comunes para todas las peticiones
 * Incluye Content-Type y Authorization si hay token
 * 
 * @returns {Object} Headers para fetch
 */
function getHeaders() {
    const headers = {
        'Content-Type': 'application/json'  // Todas las peticiones son JSON
    };
    
    // Si hay token de sesion, agregarlo al header Authorization
    const token = getToken();
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
    }
    
    return headers;
}


// =============================================
// OBJETO DB - API PRINCIPAL
// =============================================
/**
 * Objeto principal que expone todas las funciones de la API
 * Usa el patron Module (IIFE) para encapsular la implementacion
 * 
 * Uso:
 * const user = await DB.login('email', 'pass');
 * const tickets = await DB.tickets();
 */
const DB = (() => {
    
    // Claves de localStorage para compatibilidad con codigo existente
    const K = { 
        u: 'df_users',      // Cache de usuarios (opcional)
        t: 'df_tickets',    // Cache de tickets (opcional)
        s: 'df_session',    // Datos de sesion del usuario
        cfg: 'df_cfg'       // Configuracion (opcional)
    };

    // =============================================
    // INICIALIZACION
    // =============================================
    
    /**
     * Inicializar el cliente API
     * Verifica si hay una sesion activa al cargar la pagina
     * 
     * @returns {Object|null} Datos del usuario si hay sesion, null si no
     * 
     * Ejemplo:
     * const session = await DB.init();
     * if (session) {
     *   console.log('Bienvenido', session.name);
     * }
     */
    async function init() {
        // Verificar sesion existente con el servidor
        const session = await getSession();
        
        // Si hay sesion valida, guardarla en localStorage
        if (session) {
            localStorage.setItem(K.s, JSON.stringify(session));
        }
        
        return session;
    }

    // =============================================
    // AUTENTICACION
    // =============================================
    
    /**
     * Iniciar sesion con email y contrasena
     * 
     * @param {string} email    - Email del usuario
     * @param {string} password - Contrasena
     * @returns {Object|null} Datos del usuario si login exitoso, null si fallo
     * 
     * Ejemplo:
     * const user = await DB.login('admin@empresa.com', 'password');
     * if (user) {
     *   console.log('Login exitoso:', user.name);
     *   window.location.href = 'home.html';
     * } else {
     *   alert('Credenciales incorrectas');
     * }
     */
    async function login(email, password) {
        try {
            // Hacer peticion POST al endpoint de login
            const res = await fetch(`${API_BASE}/auth.php?action=login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            
            // Parsear respuesta JSON
            const data = await res.json();
            
            // Si login exitoso
            if (data.success) {
                // Guardar token para futuras peticiones
                setToken(data.token);
                // Guardar datos del usuario en localStorage
                localStorage.setItem(K.s, JSON.stringify(data.user));
                // Retornar datos del usuario
                return data.user;
            }
            
            // Si fallo, retornar null
            return null;
            
        } catch (e) {
            // Si hay error de red o similar
            console.error('Error en login:', e);
            return null;
        }
    }

    /**
     * Cerrar sesion del usuario actual
     * Limpia el token y datos de sesion
     * 
     * Ejemplo:
     * DB.logout();
     * window.location.href = 'login.html';
     */
    function logout() {
        // Notificar al servidor (no esperamos respuesta)
        fetch(`${API_BASE}/auth.php?action=logout`, {
            headers: getHeaders()
        }).catch(() => {});  // Ignorar errores
        
        // Limpiar localStorage
        localStorage.removeItem(K.s);
        localStorage.removeItem('df_token');
    }

    /**
     * Verificar sesion con el servidor (async)
     * Util para validar que la sesion sigue activa
     * 
     * @returns {Object|null} Datos del usuario si sesion valida, null si no
     * 
     * Ejemplo:
     * const session = await DB.getSession();
     * if (!session) {
     *   window.location.href = 'login.html';
     * }
     */
    async function getSession() {
        try {
            const res = await fetch(`${API_BASE}/auth.php?action=session`, {
                headers: getHeaders()
            });
            const data = await res.json();
            return data.success ? data.user : null;
        } catch (e) {
            return null;
        }
    }

    /**
     * Obtener datos de sesion de localStorage (sincrono)
     * Usa los datos guardados, no verifica con el servidor
     * 
     * @returns {Object|null} Datos del usuario o null
     * 
     * Ejemplo:
     * const user = DB.session();
     * if (user) {
     *   document.getElementById('userName').textContent = user.name;
     * }
     */
    function session() {
        const s = localStorage.getItem(K.s);
        return s ? JSON.parse(s) : null;
    }

    // =============================================
    // USUARIOS
    // =============================================
    
    /**
     * Obtener lista de todos los usuarios
     * 
     * @param {Object} filters - Filtros opcionales {role, dept, active}
     * @returns {Array} Lista de usuarios
     * 
     * Ejemplo:
     * const allUsers = await DB.users();
     * const agents = await DB.users({ role: 'agent' });
     */
    async function users(filters = {}) {
        try {
            // Construir query string con filtros
            const params = new URLSearchParams(filters).toString();
            const url = `${API_BASE}/users.php${params ? '?' + params : ''}`;
            
            const res = await fetch(url, {
                headers: getHeaders()
            });
            const data = await res.json();
            
            return data.success ? data.data : [];
        } catch (e) {
            console.error('Error obteniendo usuarios:', e);
            return [];
        }
    }

    /**
     * Obtener un usuario por su ID
     * 
     * @param {number} id - ID del usuario
     * @returns {Object|null} Datos del usuario o null si no existe
     * 
     * Ejemplo:
     * const user = await DB.userById(2);
     * console.log(user.name, user.email);
     */
    async function userById(id) {
        try {
            const res = await fetch(`${API_BASE}/users.php?id=${id}`, {
                headers: getHeaders()
            });
            const data = await res.json();
            return data.success ? data.data : null;
        } catch (e) {
            return null;
        }
    }

    /**
     * Crear o actualizar un usuario
     * Si userData tiene id, actualiza; si no, crea nuevo
     * 
     * @param {Object} userData - Datos del usuario
     * @returns {boolean} true si exito, false si fallo
     * 
     * Ejemplo crear:
     * await DB.saveUser({
     *   name: 'Juan Perez',
     *   email: 'juan@empresa.com',
     *   password: 'secreto123',
     *   role: 'user'
     * });
     * 
     * Ejemplo actualizar:
     * await DB.saveUser({
     *   id: 5,
     *   name: 'Juan P. Gomez'  // Solo actualiza el nombre
     * });
     */
    async function saveUser(userData) {
        try {
            // PUT si tiene id (actualizar), POST si no (crear)
            const method = userData.id ? 'PUT' : 'POST';
            
            const res = await fetch(`${API_BASE}/users.php`, {
                method,
                headers: getHeaders(),
                body: JSON.stringify(userData)
            });
            const data = await res.json();
            
            return data.success;
        } catch (e) {
            console.error('Error guardando usuario:', e);
            return false;
        }
    }

    /**
     * Eliminar o desactivar un usuario
     * 
     * @param {number} id   - ID del usuario
     * @param {boolean} hard - true=eliminar permanente, false=desactivar
     * @returns {boolean} true si exito
     * 
     * Ejemplo:
     * await DB.deleteUser(5);        // Soft delete (desactivar)
     * await DB.deleteUser(5, true);  // Hard delete (eliminar)
     */
    async function deleteUser(id, hard = false) {
        try {
            const res = await fetch(`${API_BASE}/users.php?id=${id}&hard=${hard}`, {
                method: 'DELETE',
                headers: getHeaders()
            });
            const data = await res.json();
            return data.success;
        } catch (e) {
            return false;
        }
    }

    // =============================================
    // TICKETS
    // =============================================
    
    /**
     * Obtener lista de tickets con filtros opcionales
     * 
     * @param {Object} filters - Filtros opcionales
     * @returns {Array} Lista de tickets
     * 
     * Filtros disponibles:
     * - status: 'Abierto', 'En Progreso', 'Resuelto', etc.
     * - priority: 'Critica', 'Alta', 'Media', 'Baja'
     * - assignee_id: ID del usuario asignado
     * - created_by: ID del creador
     * - dept: Departamento
     * - search: Buscar en titulo/descripcion
     * - limit: Cantidad de resultados
     * - offset: Desde donde empezar
     * 
     * Ejemplo:
     * const allTickets = await DB.tickets();
     * const openTickets = await DB.tickets({ status: 'Abierto' });
     * const myTickets = await DB.tickets({ assignee_id: 2 });
     */
    async function tickets(filters = {}) {
        try {
            // Construir query string con filtros
            const params = new URLSearchParams(filters).toString();
            const url = `${API_BASE}/tickets.php${params ? '?' + params : ''}`;
            
            const res = await fetch(url, {
                headers: getHeaders()
            });
            const data = await res.json();
            
            return data.success ? data.data : [];
        } catch (e) {
            console.error('Error obteniendo tickets:', e);
            return [];
        }
    }

    /**
     * Obtener un ticket por su ID con toda su actividad
     * 
     * @param {string} id - ID del ticket (ej: 'TK-001')
     * @returns {Object|null} Ticket con actividad o null
     * 
     * Ejemplo:
     * const ticket = await DB.ticketById('TK-001');
     * console.log(ticket.title);
     * console.log(ticket.activity);  // Array de comentarios
     */
    async function ticketById(id) {
        try {
            const res = await fetch(`${API_BASE}/tickets.php?id=${id}`, {
                headers: getHeaders()
            });
            const data = await res.json();
            return data.success ? data.data : null;
        } catch (e) {
            return null;
        }
    }

    /**
     * Crear o actualizar un ticket
     * Si tiene id existente, actualiza; si no, crea nuevo
     * 
     * @param {Object} ticketData - Datos del ticket
     * @returns {string|boolean} ID del ticket creado, o true/false
     * 
     * Campos soportados:
     * - title: Titulo del ticket
     * - description / desc: Descripcion
     * - priority: Prioridad
     * - status: Estado
     * - category: Categoria
     * - dept: Departamento
     * - assignee_id / assigneeId: ID del asignado
     * - created_by / createdBy: ID del creador (requerido para crear)
     * - due_date / dueDate: Fecha limite
     * 
     * Ejemplo crear:
     * const ticketId = await DB.saveTicket({
     *   title: 'Mi nuevo ticket',
     *   description: 'Descripcion del problema',
     *   priority: 'Alta',
     *   created_by: 4
     * });
     * console.log('Ticket creado:', ticketId);  // 'TK-007'
     * 
     * Ejemplo actualizar:
     * await DB.saveTicket({
     *   id: 'TK-001',
     *   status: 'Resuelto'
     * });
     */
    async function saveTicket(ticketData) {
        try {
            // Determinar si es crear o actualizar
            // Si el id empieza con 'TK-NEW', es nuevo
            const method = ticketData.id && !ticketData.id.startsWith('TK-NEW') ? 'PUT' : 'POST';
            
            // Mapear campos para compatibilidad con codigo existente
            // Acepta tanto camelCase como snake_case
            const payload = {
                ...ticketData,
                description: ticketData.desc || ticketData.description,
                assignee_id: ticketData.assigneeId || ticketData.assignee_id,
                created_by: ticketData.createdBy || ticketData.created_by,
                due_date: ticketData.dueDate || ticketData.due_date
            };

            const res = await fetch(`${API_BASE}/tickets.php`, {
                method,
                headers: getHeaders(),
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            // Si se creo, retornar el nuevo ID
            if (data.success && data.data?.id) {
                return data.data.id;
            }
            
            return data.success;
        } catch (e) {
            console.error('Error guardando ticket:', e);
            return false;
        }
    }

    /**
     * Eliminar un ticket permanentemente
     * 
     * @param {string} id - ID del ticket
     * @returns {boolean} true si exito
     * 
     * Ejemplo:
     * if (confirm('Eliminar ticket?')) {
     *   await DB.deleteTicket('TK-001');
     * }
     */
    async function deleteTicket(id) {
        try {
            const res = await fetch(`${API_BASE}/tickets.php?id=${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            });
            const data = await res.json();
            return data.success;
        } catch (e) {
            return false;
        }
    }

    /**
     * Agregar comentario o actividad a un ticket
     * 
     * @param {string} ticketId - ID del ticket
     * @param {string} userName - Nombre del usuario
     * @param {string} type     - Tipo: 'comment', 'status', 'assign', 'resolve'
     * @param {string} message  - Mensaje o comentario
     * @returns {boolean} true si exito
     * 
     * Ejemplo:
     * await DB.addActivity('TK-001', 'Ana Garcia', 'comment', 'Revisando el problema...');
     */
    async function addActivity(ticketId, userName, type, message) {
        try {
            const res = await fetch(`${API_BASE}/activity.php`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify({
                    ticket_id: ticketId,
                    user_name: userName,
                    type,
                    message
                })
            });
            const data = await res.json();
            return data.success;
        } catch (e) {
            return false;
        }
    }

    // =============================================
    // ESTADISTICAS
    // =============================================
    
    /**
     * Obtener estadisticas generales de tickets
     * 
     * @returns {Object|null} Estadisticas o null si error
     * 
     * Retorna:
     * - total: Total de tickets
     * - by_status: Conteo por estado
     * - by_priority: Conteo por prioridad
     * - overdue: Tickets vencidos
     * - resolved_this_month: Resueltos este mes
     * - avg_resolution_days: Dias promedio de resolucion
     * 
     * Ejemplo:
     * const stats = await DB.stats();
     * console.log('Total tickets:', stats.total);
     * console.log('Abiertos:', stats.by_status['Abierto']);
     */
    async function stats() {
        try {
            const res = await fetch(`${API_BASE}/tickets.php?stats=1`, {
                headers: getHeaders()
            });
            const data = await res.json();
            return data.success ? data.data : null;
        } catch (e) {
            return null;
        }
    }

    // =============================================
    // HELPERS
    // =============================================
    
    /**
     * Generar un ID temporal para nuevos tickets
     * Este ID se reemplaza por uno real al guardar
     * 
     * @param {string} prefix - Prefijo del ID (default: 'TK')
     * @returns {string} ID temporal unico
     * 
     * Ejemplo:
     * const tempId = DB.nextId();  // 'TK-NEW-1709856000000'
     */
    function nextId(prefix = 'TK') {
        return `${prefix}-NEW-${Date.now()}`;
    }

    // =============================================
    // API PUBLICA
    // =============================================
    // Estas son las funciones que se exponen al exterior
    // Compatible con el codigo existente que usa DB.funcion()
    
    return {
        init,           // Inicializacion
        
        // Autenticacion
        login,          // Iniciar sesion
        logout,         // Cerrar sesion
        session,        // Obtener sesion local (sincrono)
        getSession,     // Verificar sesion con servidor (async)
        
        // Usuarios
        users,          // Listar usuarios
        userById,       // Obtener usuario por ID
        saveUser,       // Crear/actualizar usuario
        deleteUser,     // Eliminar usuario
        
        // Tickets
        tickets,        // Listar tickets
        ticketById,     // Obtener ticket por ID
        saveTicket,     // Crear/actualizar ticket
        deleteTicket,   // Eliminar ticket
        addActivity,    // Agregar comentario
        
        // Estadisticas
        stats,          // Obtener estadisticas
        
        // Helpers
        nextId          // Generar ID temporal
    };
})();


// =============================================
// FUNCIONES DE UTILIDAD
// =============================================

/**
 * Mostrar notificacion toast
 * Si no existe la funcion toast, la creamos
 * 
 * @param {string} msg  - Mensaje a mostrar
 * @param {string} type - Tipo: 'success', 'error', 'warning', ''
 */
if (typeof toast === 'undefined') {
    function toast(msg, type = '') {
        // Buscar o crear contenedor de toasts
        const container = document.getElementById('toastContainer') || createToastContainer();
        
        // Crear elemento toast
        const t = document.createElement('div');
        t.className = 'toast ' + type;
        t.innerHTML = `<span>${msg}</span>`;
        
        // Agregar al contenedor
        container.appendChild(t);
        
        // Remover despues de 4 segundos
        setTimeout(() => t.remove(), 4000);
    }

    /**
     * Crear contenedor de toasts si no existe
     * @returns {HTMLElement} Contenedor de toasts
     */
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;';
        document.body.appendChild(container);
        return container;
    }
}


// =============================================
// INICIALIZACION AUTOMATICA
// =============================================

/**
 * Cuando el DOM este listo, inicializar el cliente API
 * Intenta restaurar la sesion si existe
 * Dispara evento 'db-ready' cuando esta listo
 */
document.addEventListener('DOMContentLoaded', async () => {
    // Intentar restaurar sesion existente
    const session = await DB.init();
    
    // Disparar evento personalizado para que otros scripts sepan que esta listo
    // Uso: window.addEventListener('db-ready', (e) => { console.log(e.detail.session); });
    window.dispatchEvent(new CustomEvent('db-ready', { detail: { session } }));
});

// Mensaje de confirmacion en consola
console.log('DECOMOBIL API Client cargado. Usa DB.login(), DB.tickets(), etc.');

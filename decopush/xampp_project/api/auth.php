<?php
/**
 * =============================================
 * DECOMOBIL - API de Autenticacion
 * =============================================
 * 
 * Este archivo maneja todas las operaciones de autenticacion:
 * - Login (iniciar sesion)
 * - Logout (cerrar sesion)
 * - Session (verificar sesion activa)
 * - Register (crear nuevo usuario)
 * 
 * ENDPOINTS:
 * POST   /api/auth.php?action=login    - Iniciar sesion
 * GET    /api/auth.php?action=logout   - Cerrar sesion
 * GET    /api/auth.php?action=session  - Verificar sesion
 * POST   /api/auth.php?action=register - Registrar usuario
 * 
 * SEGURIDAD:
 * - Las contrasenas se hashean con bcrypt (password_hash)
 * - Los tokens de sesion son aleatorios de 64 caracteres
 * - Las sesiones expiran en 24 horas
 * - Compatible con contrasenas legacy en base64
 */

// Incluir configuracion de base de datos
require_once __DIR__ . '/../config/database.php';

// Iniciar sesion PHP para manejar cookies de sesion
session_start();

// =============================================
// ROUTER - Determinar que accion ejecutar
// =============================================

// Obtener metodo HTTP (GET, POST, etc.)
$method = $_SERVER['REQUEST_METHOD'];

// Obtener accion del parametro ?action=
// Si no hay accion, sera string vacio
$action = $_GET['action'] ?? '';

// Ejecutar la funcion correspondiente segun la accion
switch ($action) {
    case 'login':
        handleLogin();      // Procesar inicio de sesion
        break;
    case 'logout':
        handleLogout();     // Procesar cierre de sesion
        break;
    case 'session':
        getSession();       // Verificar si hay sesion activa
        break;
    case 'register':
        handleRegister();   // Registrar nuevo usuario
        break;
    default:
        // Si la accion no es valida, retornar error 400 (Bad Request)
        jsonResponse(['success' => false, 'error' => 'Accion no valida bitch'], 400);
}


// =============================================
// FUNCION: handleLogin()
// =============================================
/**
 * Procesa el inicio de sesion de un usuario
 * 
 * FLUJO:
 * 1. Validar que sea peticion POST
 * 2. Obtener iniciales y password del body JSON
 * 3. Buscar usuario en la base de datos cuyas iniciales coincidan
 * 4. Verificar contrasena (bcrypt o base64 legacy)
 * 5. Crear token de sesion
 * 6. Guardar sesion en BD y PHP
 * 7. Retornar datos del usuario
 * 
 * REQUEST (POST):
 * {
 *   "initials": "FUribe",    <- Primera letra nombre + apellido completo
 *   "password": "micontrasena"
 * }
 * 
 * RESPONSE (exito):
 * {
 *   "success": true,
 *   "user": { id, name, email, role, ... },
 *   "token": "abc123..."
 * }
 */
function handleLogin() {
    // Solo aceptar peticiones POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['success' => false, 'error' => 'Metodo no permitido'], 405);
    }

    // Leer el cuerpo de la peticion (JSON)
    // php://input contiene el body raw de la peticion
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Extraer identificador y password, limpiando espacios
    // El identificador es: primera letra del nombre + apellido completo
    // Ejemplo: "Francisco Uribe" -> "FUribe"
    $identifier = trim($data['initials'] ?? '');
    $password = $data['password'] ?? '';

    // Validar que ambos campos esten presentes
    if (empty($identifier) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Identificador y contraseña son requeridos'], 400);
    }

    try {
        // Obtener conexion a la base de datos
        $db = getDB();
        
        // Buscar todos los usuarios activos
        $stmt = $db->prepare("SELECT * FROM users WHERE active = 1");
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Función para calcular el identificador a partir del nombre
        // Formato: primera letra del primer nombre + apellido completo (case-insensitive)
        // Ejemplo: "Francisco Uribe"       -> "FUribe"
        //          "Ana Maria Garcia"      -> "AGarcia"
        //          "Carlos Lopez Mendoza"  -> "CLopez"
        $calculateIdentifier = function($name) {
            $words = array_values(array_filter(explode(' ', trim($name))));
            if (empty($words)) return '';
            $firstLetter = strtoupper($words[0][0]);       // Primera letra del primer nombre
            $lastName = isset($words[1]) ? $words[1] : ''; // Primer apellido completo
            return $firstLetter . $lastName;
        };

        // Buscar usuario cuyo identificador coincida (sin importar mayusculas/minusculas)
        $user = null;
        foreach ($users as $u) {
            if (strcasecmp($calculateIdentifier($u['name']), $identifier) === 0) {
                $user = $u;
                break;
            }
        }

        // Si no encontramos usuario con ese identificador, retornar error
        if (!$user) {
            jsonResponse(['success' => false, 'error' => 'Usuario no encontrado o inactivo'], 401);
        }

        // =============================================
        // VERIFICACION DE CONTRASENA
        // =============================================
        // Soportamos dos formatos:
        // 1. bcrypt (password_hash) - Formato actual y seguro
        // 2. base64 - Formato legacy del sistema anterior
        
        $validPassword = false;
        
        // Intentar verificar con password_hash (bcrypt)
        // password_verify compara el password plano con el hash
        if (password_verify($password, $user['password'])) {
            $validPassword = true;
        }
        // Fallback: verificar con base64 (sistema anterior)
        // base64_decode convierte el hash guardado a texto plano
        elseif (base64_decode($user['password']) === $password) {
            $validPassword = true;
            
            // Si la contrasena estaba en base64, actualizarla a bcrypt
            // Esto migra automaticamente las contrasenas legacy
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->execute([$newHash, $user['id']]);
        }

        // Si la contrasena no coincide, retornar error
        if (!$validPassword) {
            jsonResponse(['success' => false, 'error' => 'Contraseña incorrecta'], 401);
        }

        // =============================================
        // CREAR SESION
        // =============================================
        
        // Generar token aleatorio de 64 caracteres hexadecimales
        // random_bytes genera bytes criptograficamente seguros
        // bin2hex los convierte a string hexadecimal
        $sessionToken = bin2hex(random_bytes(32));
        
        // La sesion expira en 24 horas
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Guardar sesion en la base de datos
        $stmt = $db->prepare("INSERT INTO sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $sessionToken, $expiresAt]);

        // Guardar en sesion PHP (para cookies)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['session_token'] = $sessionToken;

        // Remover contrasena de la respuesta por seguridad
        // Nunca enviamos contrasenas al cliente
        unset($user['password']);

        // Retornar respuesta exitosa con datos del usuario y token
        jsonResponse([
            'success' => true,
            'user' => $user,
            'token' => $sessionToken
        ]);

    } catch (PDOException $e) {
        // Si hay error de base de datos, retornar error 500
        jsonResponse(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()], 500);
    }
}


// =============================================
// FUNCION: handleLogout()
// =============================================
/**
 * Cierra la sesion del usuario actual
 * 
 * FLUJO:
 * 1. Eliminar sesion de la base de datos
 * 2. Destruir sesion PHP
 * 3. Retornar confirmacion
 * 
 * REQUEST: GET /api/auth.php?action=logout
 * 
 * RESPONSE:
 * { "success": true, "message": "Sesion cerrada" }
 */
function handleLogout() {
    try {
        $db = getDB();
        
        // Si hay token de sesion guardado, eliminarlo de la BD
        if (isset($_SESSION['session_token'])) {
            $stmt = $db->prepare("DELETE FROM sessions WHERE session_token = ?");
            $stmt->execute([$_SESSION['session_token']]);
        }

        // Destruir la sesion PHP (elimina cookies)
        session_destroy();

        // Confirmar cierre de sesion
        jsonResponse(['success' => true, 'message' => 'Sesion cerrada']);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al cerrar sesion'], 500);
    }
}


// =============================================
// FUNCION: getSession()
// =============================================
/**
 * Verifica si hay una sesion activa y valida
 * Retorna los datos del usuario si la sesion es valida
 * 
 * FLUJO:
 * 1. Obtener token del header Authorization o sesion PHP
 * 2. Buscar sesion en BD que no haya expirado
 * 3. Retornar datos del usuario si es valida
 * 
 * REQUEST: GET /api/auth.php?action=session
 * Headers: Authorization: Bearer <token>
 * 
 * RESPONSE (sesion valida):
 * { "success": true, "authenticated": true, "user": {...} }
 * 
 * RESPONSE (sin sesion):
 * { "success": false, "authenticated": false }
 */
function getSession() {
    // Buscar token en el header Authorization o en la sesion PHP
    // El header viene como "Bearer <token>"
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SESSION['session_token'] ?? null;
    
    // Si el token tiene prefijo "Bearer ", quitarlo
    if ($token && strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);  // Quitar "Bearer " (7 caracteres)
    }

    // Si no hay token, no hay sesion
    if (!$token) {
        jsonResponse(['success' => false, 'authenticated' => false], 401);
    }

    try {
        $db = getDB();
        
        // Buscar sesion valida (no expirada) y usuario activo
        // JOIN une la tabla sessions con users
        // WHERE verifica: token correcto, no expirado, usuario activo
        $stmt = $db->prepare("
            SELECT u.* FROM users u
            INNER JOIN sessions s ON u.id = s.user_id
            WHERE s.session_token = ? AND s.expires_at > NOW() AND u.active = 1
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        // Si no hay resultado, la sesion no es valida
        if (!$user) {
            jsonResponse(['success' => false, 'authenticated' => false], 401);
        }

        // Quitar contrasena de la respuesta
        unset($user['password']);
        
        // Retornar usuario autenticado
        jsonResponse([
            'success' => true,
            'authenticated' => true,
            'user' => $user
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error de base de datos'], 500);
    }
}


// =============================================
// FUNCION: handleRegister()
// =============================================
/**
 * Registra un nuevo usuario en el sistema
 * 
 * FLUJO:
 * 1. Validar metodo POST
 * 2. Extraer y validar datos del usuario
 * 3. Verificar que el email no exista
 * 4. Hashear contrasena con bcrypt
 * 5. Insertar en la base de datos
 * 6. Retornar confirmacion
 * 
 * REQUEST (POST):
 * {
 *   "name": "Juan Perez",
 *   "email": "juan@empresa.com",
 *   "password": "micontrasena",
 *   "role": "user",        // opcional, default: user
 *   "dept": "Ventas",      // opcional
 *   "avatar": "#FF5733"    // opcional, default: #0F52BA
 * }
 * 
 * RESPONSE:
 * { "success": true, "message": "Usuario creado", "user_id": 6 }
 */
function handleRegister() {
    // Solo aceptar peticiones POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['success' => false, 'error' => 'Metodo no permitido'], 405);
    }

    // Leer datos del body JSON
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Extraer campos, usando valores por defecto donde aplique
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'user';           // Por defecto: usuario normal
    $dept = $data['dept'] ?? null;              // Departamento opcional
    $avatar = $data['avatar'] ?? '#0F52BA';     // Color azul por defecto

    // Validar campos requeridos
    if (empty($name) || empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Nombre, email y contrasena son requeridos'], 400);
    }

    try {
        $db = getDB();
        
        // Verificar que el email no este registrado
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'error' => 'El email ya esta registrado'], 400);
        }

        // Hashear contrasena con bcrypt
        // PASSWORD_DEFAULT usa el algoritmo mas seguro disponible
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role, dept, avatar, active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 1, CURDATE())
        ");
        $stmt->execute([$name, $email, $hashedPassword, $role, $dept, $avatar]);

        // Obtener el ID del usuario recien creado
        $userId = $db->lastInsertId();

        // Retornar respuesta exitosa
        jsonResponse([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'user_id' => $userId
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al crear usuario: ' . $e->getMessage()], 500);
    }
}
?>
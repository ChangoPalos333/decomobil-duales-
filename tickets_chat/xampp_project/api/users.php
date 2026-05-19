<?php
/**
 * =============================================
 * DECOMOBIL - API de Usuarios
 * =============================================
 * 
 * Este archivo maneja el CRUD completo de usuarios:
 * - GET    -> Listar usuarios o obtener uno por ID
 * - POST   -> Crear nuevo usuario
 * - PUT    -> Actualizar usuario existente
 * - DELETE -> Eliminar/desactivar usuario
 * 
 * ENDPOINTS:
 * GET    /api/users.php           - Listar todos los usuarios
 * GET    /api/users.php?id=1      - Obtener usuario con ID 1
 * GET    /api/users.php?role=admin - Filtrar por rol
 * GET    /api/users.php?dept=TI   - Filtrar por departamento
 * POST   /api/users.php           - Crear nuevo usuario
 * PUT    /api/users.php           - Actualizar usuario
 * DELETE /api/users.php?id=1      - Desactivar usuario (soft delete)
 * DELETE /api/users.php?id=1&hard=true - Eliminar permanentemente
 * 
 * SEGURIDAD:
 * - Las contrasenas siempre se hashean con bcrypt
 * - Por defecto se usa soft delete (desactivar, no eliminar)
 * - Los emails son unicos para evitar duplicados
 */

// Incluir configuracion de base de datos
require_once __DIR__ . '/../config/database.php';

// =============================================
// ROUTER - Determinar que operacion ejecutar
// =============================================

// Obtener metodo HTTP de la peticion
$method = $_SERVER['REQUEST_METHOD'];

// Ejecutar la funcion correspondiente segun el metodo HTTP
switch ($method) {
    case 'GET':
        // Si hay ?id=X, obtener usuario especifico
        // Si no, listar todos los usuarios
        if (isset($_GET['id'])) {
            getUser($_GET['id']);
        } else {
            getUsers();
        }
        break;
    case 'POST':
        createUser();    // Crear nuevo usuario
        break;
    case 'PUT':
        updateUser();    // Actualizar usuario existente
        break;
    case 'DELETE':
        deleteUser();    // Eliminar/desactivar usuario
        break;
    default:
        // Si el metodo no esta soportado, retornar error 405
        jsonResponse(['success' => false, 'error' => 'Metodo no permitido'], 405);
}


// =============================================
// FUNCION: getUsers()
// =============================================
/**
 * Lista todos los usuarios con filtros opcionales
 * 
 * FILTROS DISPONIBLES (via query string):
 * - ?role=admin    -> Solo administradores
 * - ?role=agent    -> Solo agentes
 * - ?role=user     -> Solo usuarios normales
 * - ?dept=TI       -> Solo del departamento TI
 * - ?active=1      -> Solo activos
 * - ?active=0      -> Solo inactivos
 * 
 * Puedes combinar filtros: ?role=agent&dept=TI&active=1
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "data": [
 *     { "id": 1, "name": "Admin", "email": "...", ... },
 *     { "id": 2, "name": "Ana", "email": "...", ... }
 *   ],
 *   "total": 2
 * }
 */
function getUsers() {
    try {
        $db = getDB();
        
        // Construir la clausula WHERE dinamicamente
        // Empezamos con "WHERE 1=1" para poder agregar ANDs facilmente
        $where = "WHERE 1=1";
        $params = [];  // Array para los valores de los filtros

        // Filtro por rol (admin, agent, user)
        if (isset($_GET['role'])) {
            $where .= " AND role = ?";
            $params[] = $_GET['role'];
        }

        // Filtro por departamento
        if (isset($_GET['dept'])) {
            $where .= " AND dept = ?";
            $params[] = $_GET['dept'];
        }

        // Filtro por estado (activo/inactivo)
        if (isset($_GET['active'])) {
            $where .= " AND active = ?";
            $params[] = $_GET['active'];
        }

        // Ejecutar consulta
        // NOTA: No incluimos 'password' en el SELECT por seguridad
        $stmt = $db->prepare("
            SELECT id, name, email, role, dept, avatar, active, created_at 
            FROM users $where 
            ORDER BY created_at DESC
        ");
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        // Retornar lista de usuarios
        jsonResponse([
            'success' => true,
            'data' => $users,
            'total' => count($users)
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al obtener usuarios'], 500);
    }
}


// =============================================
// FUNCION: getUser($id)
// =============================================
/**
 * Obtiene un usuario especifico por su ID
 * 
 * @param int $id ID del usuario a buscar
 * 
 * REQUEST: GET /api/users.php?id=1
 * 
 * RESPONSE (exito):
 * {
 *   "success": true,
 *   "data": { "id": 1, "name": "Admin", ... }
 * }
 * 
 * RESPONSE (no encontrado):
 * { "success": false, "error": "Usuario no encontrado" }
 */
function getUser($id) {
    try {
        $db = getDB();
        
        // Buscar usuario por ID (sin incluir password)
        $stmt = $db->prepare("
            SELECT id, name, email, role, dept, avatar, active, created_at 
            FROM users WHERE id = ?
        ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        // Si no existe, retornar error 404
        if (!$user) {
            jsonResponse(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        // Retornar datos del usuario
        jsonResponse([
            'success' => true,
            'data' => $user
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al obtener usuario'], 500);
    }
}


// =============================================
// FUNCION: createUser()
// =============================================
/**
 * Crea un nuevo usuario en el sistema
 * 
 * REQUEST (POST):
 * {
 *   "name": "Juan Perez",        // Requerido
 *   "email": "juan@empresa.com", // Requerido, debe ser unico
 *   "password": "secreto123",    // Requerido, se hasheara
 *   "role": "user",              // Opcional, default: user
 *   "dept": "Ventas",            // Opcional
 *   "avatar": "#FF5733"          // Opcional, default: #0F52BA
 * }
 * 
 * RESPONSE (exito):
 * {
 *   "success": true,
 *   "message": "Usuario creado",
 *   "data": { "id": 6, "name": "Juan Perez", ... }
 * }
 */
function createUser() {
    // Leer datos JSON del body de la peticion
    $data = json_decode(file_get_contents('php://input'), true);

    // Extraer campos con valores por defecto
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'user';
    $dept = $data['dept'] ?? null;
    $avatar = $data['avatar'] ?? '#0F52BA';

    // Validar campos requeridos
    if (empty($name) || empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Campos requeridos: name, email, password'], 400);
    }

    try {
        $db = getDB();

        // Verificar que el email no este registrado
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'error' => 'El email ya existe'], 400);
        }

        // Hashear contrasena con bcrypt para almacenarla segura
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role, dept, avatar, active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 1, CURDATE())
        ");
        $stmt->execute([$name, $email, $hashedPassword, $role, $dept, $avatar]);

        // Obtener ID del usuario creado
        $userId = $db->lastInsertId();

        // Retornar respuesta con codigo 201 (Created)
        jsonResponse([
            'success' => true,
            'message' => 'Usuario creado',
            'data' => [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'dept' => $dept,
                'avatar' => $avatar
            ]
        ], 201);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al crear usuario: ' . $e->getMessage()], 500);
    }
}


// =============================================
// FUNCION: updateUser()
// =============================================
/**
 * Actualiza los datos de un usuario existente
 * Solo actualiza los campos que se envian (actualizacion parcial)
 * 
 * REQUEST (PUT):
 * {
 *   "id": 1,                     // Requerido
 *   "name": "Nuevo Nombre",      // Opcional
 *   "email": "nuevo@email.com",  // Opcional, se verifica duplicado
 *   "password": "nuevapass",     // Opcional, se hasheara
 *   "role": "admin",             // Opcional
 *   "dept": "TI",                // Opcional
 *   "avatar": "#FF0000",         // Opcional
 *   "active": false              // Opcional
 * }
 * 
 * RESPONSE:
 * { "success": true, "message": "Usuario actualizado" }
 */
function updateUser() {
    // Leer datos del body
    $data = json_decode(file_get_contents('php://input'), true);
    
    // El ID puede venir en el body o en la URL
    $id = $data['id'] ?? $_GET['id'] ?? null;

    // Validar que haya ID
    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'ID de usuario requerido'], 400);
    }

    try {
        $db = getDB();

        // Verificar que el usuario existe
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            jsonResponse(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        // =============================================
        // CONSTRUIR QUERY DINAMICO
        // =============================================
        // Solo actualizamos los campos que se enviaron
        $fields = [];   // Campos a actualizar: ["name = ?", "email = ?"]
        $params = [];   // Valores correspondientes: ["Juan", "juan@email.com"]

        // Actualizar nombre si se envio
        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
        }
        
        // Actualizar email si se envio (verificando duplicados)
        if (isset($data['email'])) {
            // Verificar que el nuevo email no pertenezca a otro usuario
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$data['email'], $id]);
            if ($stmt->fetch()) {
                jsonResponse(['success' => false, 'error' => 'El email ya existe'], 400);
            }
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        
        // Actualizar password si se envio (hasheando)
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Actualizar rol si se envio
        if (isset($data['role'])) {
            $fields[] = "role = ?";
            $params[] = $data['role'];
        }
        
        // Actualizar departamento si se envio
        if (isset($data['dept'])) {
            $fields[] = "dept = ?";
            $params[] = $data['dept'];
        }
        
        // Actualizar avatar si se envio
        if (isset($data['avatar'])) {
            $fields[] = "avatar = ?";
            $params[] = $data['avatar'];
        }
        
        // Actualizar estado activo si se envio
        if (isset($data['active'])) {
            $fields[] = "active = ?";
            $params[] = $data['active'] ? 1 : 0;  // Convertir boolean a 1/0
        }

        // Si no hay campos para actualizar, retornar error
        if (empty($fields)) {
            jsonResponse(['success' => false, 'error' => 'No hay campos para actualizar'], 400);
        }

        // Agregar el ID al final de los parametros para el WHERE
        $params[] = $id;
        
        // Construir y ejecutar el query
        // Ejemplo: UPDATE users SET name = ?, email = ? WHERE id = ?
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Confirmar actualizacion
        jsonResponse([
            'success' => true,
            'message' => 'Usuario actualizado'
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al actualizar: ' . $e->getMessage()], 500);
    }
}


// =============================================
// FUNCION: deleteUser()
// =============================================
/**
 * Elimina o desactiva un usuario
 * 
 * Por defecto hace "soft delete" (desactivar usuario).
 * Esto permite mantener el historial y datos relacionados.
 * 
 * Para eliminacion permanente, agregar ?hard=true
 * 
 * REQUEST:
 * DELETE /api/users.php?id=1           -> Soft delete (desactivar)
 * DELETE /api/users.php?id=1&hard=true -> Hard delete (eliminar permanente)
 * 
 * RESPONSE:
 * { "success": true, "message": "Usuario desactivado" }
 * { "success": true, "message": "Usuario eliminado permanentemente" }
 */
function deleteUser() {
    // Obtener ID del usuario a eliminar
    $id = $_GET['id'] ?? null;
    
    // Verificar si se solicita eliminacion permanente
    $hardDelete = isset($_GET['hard']) && $_GET['hard'] === 'true';

    // Validar que haya ID
    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'ID de usuario requerido'], 400);
    }

    try {
        $db = getDB();

        if ($hardDelete) {
            // =============================================
            // HARD DELETE - Eliminacion permanente
            // =============================================
            // CUIDADO: Esto elimina el usuario y puede afectar
            // tickets asignados o creados por este usuario
            // (segun las reglas de FK definidas en el SQL)
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        } else {
            // =============================================
            // SOFT DELETE - Solo desactivar
            // =============================================
            // El usuario ya no puede iniciar sesion pero
            // sus datos se mantienen para historial
            $stmt = $db->prepare("UPDATE users SET active = 0 WHERE id = ?");
        }

        // Ejecutar la operacion
        $stmt->execute([$id]);

        // Verificar si se afecto algun registro
        if ($stmt->rowCount() === 0) {
            jsonResponse(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        // Confirmar operacion
        jsonResponse([
            'success' => true,
            'message' => $hardDelete ? 'Usuario eliminado permanentemente' : 'Usuario desactivado'
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al eliminar usuario'], 500);
    }
}
?>

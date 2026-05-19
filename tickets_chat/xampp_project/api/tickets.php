<?php
/**
 * =============================================
 * DECOMOBIL - API de Tickets
 * =============================================
 * 
 * Este archivo maneja el CRUD completo de tickets de soporte:
 * - GET    -> Listar tickets, obtener uno, o estadisticas
 * - POST   -> Crear nuevo ticket
 * - PUT    -> Actualizar ticket existente
 * - DELETE -> Eliminar ticket
 * 
 * ENDPOINTS:
 * GET    /api/tickets.php                    - Listar todos los tickets
 * GET    /api/tickets.php?id=TK-001          - Obtener ticket especifico
 * GET    /api/tickets.php?stats=1            - Obtener estadisticas
 * GET    /api/tickets.php?status=Abierto     - Filtrar por estado
 * GET    /api/tickets.php?priority=Alta      - Filtrar por prioridad
 * GET    /api/tickets.php?assignee_id=2      - Tickets asignados a usuario
 * GET    /api/tickets.php?search=VPN         - Buscar en titulo/descripcion
 * POST   /api/tickets.php                    - Crear nuevo ticket
 * PUT    /api/tickets.php                    - Actualizar ticket
 * DELETE /api/tickets.php?id=TK-001          - Eliminar ticket
 * 
 * ESTADOS DE TICKET:
 * - Abierto:      Ticket recien creado, sin atender
 * - En Progreso:  Se esta trabajando en el ticket
 * - En Revision:  Esperando validacion o aprobacion
 * - Pendiente:    En espera de informacion adicional
 * - Resuelto:     Ticket completado y cerrado
 * 
 * PRIORIDADES:
 * - Critica: Requiere atencion inmediata (sistema caido, etc.)
 * - Alta:    Importante, resolver lo antes posible
 * - Media:   Normal, seguir orden de llegada
 * - Baja:    Puede esperar, sin urgencia
 */

// Incluir helpers de autenticacion y autorizacion
require_once __DIR__ . '/auth_helpers.php';

// =============================================
// ROUTER - Determinar que operacion ejecutar
// =============================================

// Obtener metodo HTTP de la peticion
$method = $_SERVER['REQUEST_METHOD'];

// Ejecutar la funcion correspondiente segun el metodo
switch ($method) {
    case 'GET':
        // Determinar que tipo de GET es:
        // - ?id=X      -> Obtener un ticket
        // - ?stats=1   -> Obtener estadisticas
        // - (sin id)   -> Listar todos
        if (isset($_GET['id'])) {
            getTicket($_GET['id']);
        } elseif (isset($_GET['stats'])) {
            getStats();
        } else {
            getTickets();
        }
        break;
    case 'POST':
        createTicket();    // Crear nuevo ticket
        break;
    case 'PUT':
        updateTicket();    // Actualizar ticket
        break;
    case 'DELETE':
        deleteTicket();    // Eliminar ticket
        break;
    default:
        jsonResponse(['success' => false, 'error' => 'Metodo no permitido'], 405);
}


// =============================================
// FUNCION: getTickets()
// =============================================
/**
 * Lista todos los tickets con filtros, ordenamiento y paginacion
 * 
 * FILTROS DISPONIBLES (via query string):
 * - ?status=Abierto        -> Solo tickets abiertos
 * - ?priority=Alta         -> Solo tickets de alta prioridad
 * - ?assignee_id=2         -> Tickets asignados a usuario ID 2
 * - ?created_by=5          -> Tickets creados por usuario ID 5
 * - ?dept=Ventas           -> Tickets del departamento Ventas
 * - ?category=TI           -> Tickets de categoria TI
 * - ?search=VPN            -> Buscar "VPN" en titulo, descripcion o ID
 * 
 * ORDENAMIENTO:
 * - ?sort=created_at&dir=DESC  -> Ordenar por fecha (mas recientes primero)
 * - ?sort=due_date&dir=ASC     -> Ordenar por fecha limite (proximos primero)
 * - ?sort=priority             -> Ordenar por prioridad
 * 
 * PAGINACION:
 * - ?limit=10&offset=0     -> Primeros 10 tickets
 * - ?limit=10&offset=10    -> Siguientes 10 tickets
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "data": [ { ticket1 }, { ticket2 }, ... ],
 *   "total": 25  // Total sin paginacion (para calcular paginas)
 * }
 */
function getTickets() {
    try {
        $db = getDB();
        $user = requireAuth();

        // Construir clausula WHERE dinamica
        $where = "WHERE 1=1";
        $params = [];

        // Solo ver tickets propios o asignados
        if ($user['role'] === 'user') {
            $where .= " AND t.created_by = ?";
            $params[] = $user['id'];
        } else {
            $where .= " AND t.assignee_id = ?";
            $params[] = $user['id'];
        }

        // =============================================
        // APLICAR FILTROS
        // =============================================
        
        // Filtro por estado
        if (isset($_GET['status'])) {
            $where .= " AND t.status = ?";
            $params[] = $_GET['status'];
        }
        
        // Filtro por prioridad
        if (isset($_GET['priority'])) {
            $where .= " AND t.priority = ?";
            $params[] = $_GET['priority'];
        }
        
        // Filtro por usuario asignado
        if (isset($_GET['assignee_id'])) {
            $where .= " AND t.assignee_id = ?";
            $params[] = $_GET['assignee_id'];
        }
        
        // Filtro por creador
        if (isset($_GET['created_by'])) {
            $where .= " AND t.created_by = ?";
            $params[] = $_GET['created_by'];
        }
        
        // Filtro por departamento
        if (isset($_GET['dept'])) {
            $where .= " AND t.dept = ?";
            $params[] = $_GET['dept'];
        }
        
        // Filtro por categoria
        if (isset($_GET['category'])) {
            $where .= " AND t.category = ?";
            $params[] = $_GET['category'];
        }
        
        // Busqueda en titulo, descripcion e ID
        if (isset($_GET['search'])) {
            // LIKE %texto% busca el texto en cualquier parte del campo
            $where .= " AND (t.title LIKE ? OR t.description LIKE ? OR t.id LIKE ?)";
            $searchTerm = '%' . $_GET['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // =============================================
        // ORDENAMIENTO
        // =============================================
        $orderBy = "ORDER BY t.created_at DESC";  // Por defecto: mas recientes primero
        
        if (isset($_GET['sort'])) {
            // Solo permitir campos seguros para evitar SQL injection
            $allowedSorts = ['created_at', 'due_date', 'priority', 'status'];
            if (in_array($_GET['sort'], $allowedSorts)) {
                // Direccion: ASC (ascendente) o DESC (descendente)
                $dir = isset($_GET['dir']) && strtoupper($_GET['dir']) === 'ASC' ? 'ASC' : 'DESC';
                $orderBy = "ORDER BY t." . $_GET['sort'] . " " . $dir;
            }
        }

        // =============================================
        // PAGINACION
        // =============================================
        $limit = "";
        if (isset($_GET['limit'])) {
            $limitVal = (int)$_GET['limit'];   // Cantidad de registros
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;  // Desde donde empezar
            $limit = "LIMIT $limitVal OFFSET $offset";
        }

        // =============================================
        // QUERY PRINCIPAL
        // =============================================
        // Usamos LEFT JOIN para incluir informacion del creador y asignado
        // Esto nos permite mostrar nombres en lugar de solo IDs
        $sql = "
            SELECT 
                t.*,
                creator.name as creator_name,
                creator.avatar as creator_avatar,
                assignee.name as assignee_name,
                assignee.avatar as assignee_avatar
            FROM tickets t
            LEFT JOIN users creator ON t.created_by = creator.id
            LEFT JOIN users assignee ON t.assignee_id = assignee.id
            $where
            $orderBy
            $limit
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll();

        // =============================================
        // CONTAR TOTAL (para paginacion)
        // =============================================
        // Contamos sin LIMIT para saber cuantos tickets hay en total
        $countSql = "SELECT COUNT(*) as total FROM tickets t $where";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // Retornar tickets y total
        jsonResponse([
            'success' => true,
            'data' => $tickets,
            'total' => $total
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al obtener tickets: ' . $e->getMessage()], 500);
    }
}


// =============================================
// FUNCION: getTicket($id)
// =============================================
/**
 * Obtiene un ticket especifico con toda su informacion y actividad
 * 
 * @param string $id ID del ticket (ej: "TK-001")
 * 
 * REQUEST: GET /api/tickets.php?id=TK-001
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "data": {
 *     "id": "TK-001",
 *     "title": "Falla en VPN",
 *     "description": "...",
 *     "priority": "Critica",
 *     "status": "En Progreso",
 *     "creator_name": "Pedro Ruiz",
 *     "assignee_name": "Ana Garcia",
 *     "activity": [
 *       { "id": 1, "user_name": "Sistema", "message": "Ticket creado", ... },
 *       { "id": 2, "user_name": "Ana", "message": "Revisando...", ... }
 *     ]
 *   }
 * }
 */
function getTicket($id) {
    try {
        $db = getDB();
        $user = requireAuth();
        
        // Obtener ticket con informacion de creador y asignado
        $stmt = $db->prepare("
            SELECT 
                t.*,
                creator.name as creator_name,
                creator.avatar as creator_avatar,
                assignee.name as assignee_name,
                assignee.avatar as assignee_avatar
            FROM tickets t
            LEFT JOIN users creator ON t.created_by = creator.id
            LEFT JOIN users assignee ON t.assignee_id = assignee.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch();

        // Si no existe el ticket, retornar 404
        if (!$ticket) {
            jsonResponse(['success' => false, 'error' => 'Ticket no encontrado'], 404);
        }

        if (!userCanAccessTicket($user, $ticket)) {
            jsonResponse(['success' => false, 'error' => 'Acceso denegado al ticket'], 403);
        }

        // Obtener historial de actividad del ticket
        // Ordenado cronologicamente (mas antiguo primero)
        $stmt = $db->prepare("
            SELECT * FROM ticket_activity 
            WHERE ticket_id = ? 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$id]);
        $activity = $stmt->fetchAll();

        // Agregar actividad al ticket
        $ticket['activity'] = $activity;

        // Retornar ticket completo
        jsonResponse([
            'success' => true,
            'data' => $ticket
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al obtener ticket'], 500);
    }
}


// =============================================
// FUNCION: createTicket()
// =============================================
/**
 * Crea un nuevo ticket de soporte
 * 
 * REQUEST (POST):
 * {
 *   "title": "Falla en impresora",    // Requerido
 *   "description": "No imprime...",   // Opcional
 *   "priority": "Alta",               // Opcional, default: Media
 *   "category": "TI",                 // Opcional, default: TI
 *   "dept": "Contabilidad",           // Opcional
 *   "assignee_id": 2,                 // Opcional (ID del agente)
 *   "due_date": "2025-03-15"          // Opcional
 * }
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "message": "Ticket creado",
 *   "data": { "id": "TK-007" }
 * }
 */
function createTicket() {
    // Leer datos del body JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Extraer campos
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $priority = $data['priority'] ?? 'Media';
    $category = $data['category'] ?? 'TI';
    $dept = $data['dept'] ?? null;
    $dueDate = $data['due_date'] ?? null;
    $assigneeId = $data['assignee_id'] ?? null;
    $user = requireAuth();
    $createdBy = $user['id'];

    // Validar campos requeridos
    if (empty($title)) {
        jsonResponse(['success' => false, 'error' => 'Titulo es requerido'], 400);
    }

    try {
        $db = getDB();

        // =============================================
        // GENERAR ID UNICO DEL TICKET
        // =============================================
        // Formato: TK-001, TK-002, TK-003, etc.
        // Contamos tickets existentes y sumamos 1
        $stmt = $db->query("SELECT COUNT(*) as count FROM tickets");
        $count = $stmt->fetch()['count'] + 1;
        $ticketId = 'TK-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Verificar que el ID no exista (por si hay gaps por eliminaciones)
        // Si existe, incrementar hasta encontrar uno libre
        $stmt = $db->prepare("SELECT id FROM tickets WHERE id = ?");
        $stmt->execute([$ticketId]);
        while ($stmt->fetch()) {
            $count++;
            $ticketId = 'TK-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            $stmt->execute([$ticketId]);
        }

        // Insertar el nuevo ticket
        $stmt = $db->prepare("
            INSERT INTO tickets (id, title, description, priority, status, category, dept, assignee_id, created_by, due_date)
            VALUES (?, ?, ?, ?, 'Abierto', ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$ticketId, $title, $description, $priority, $category, $dept, $assigneeId, $createdBy, $dueDate]);

        // Registrar actividad de creacion
        $stmt = $db->prepare("
            INSERT INTO ticket_activity (ticket_id, user_name, activity_type, message)
            VALUES (?, 'Sistema', 'create', 'Ticket creado')
        ");
        $stmt->execute([$ticketId]);

        // Retornar respuesta exitosa con el ID
        jsonResponse([
            'success' => true,
            'message' => 'Ticket creado',
            'data' => ['id' => $ticketId]
        ], 201);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al crear ticket: ' . $e->getMessage()], 500);
    }
}


// =============================================
// FUNCION: updateTicket()
// =============================================
/**
 * Actualiza un ticket existente
 * Solo actualiza los campos enviados (actualizacion parcial)
 * Registra automaticamente los cambios importantes en el historial
 * 
 * REQUEST (PUT):
 * {
 *   "id": "TK-001",              // Requerido
 *   "title": "Nuevo titulo",     // Opcional
 *   "description": "...",        // Opcional
 *   "priority": "Critica",       // Opcional (registra cambio)
 *   "status": "Resuelto",        // Opcional (registra cambio)
 *   "category": "CONT",          // Opcional
 *   "dept": "Finanzas",          // Opcional
 *   "assignee_id": 3,            // Opcional (registra cambio)
 *   "due_date": "2025-03-20",    // Opcional
 *   "user_name": "Ana Garcia"    // Opcional (para registrar quien hizo el cambio)
 * }
 * 
 * RESPONSE:
 * { "success": true, "message": "Ticket actualizado" }
 */
function updateTicket() {
    // Leer datos del body
    $data = json_decode(file_get_contents('php://input'), true);
    
    // ID puede venir en body o URL
    $id = $data['id'] ?? $_GET['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'ID de ticket requerido'], 400);
    }

    try {
        $db = getDB();

        // Verificar que el ticket existe
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch();

        if (!$ticket) {
            jsonResponse(['success' => false, 'error' => 'Ticket no encontrado'], 404);
        }

        $user = requireAuth();
        if (!userCanAccessTicket($user, $ticket)) {
            jsonResponse(['success' => false, 'error' => 'Acceso denegado al ticket'], 403);
        }

        // Arrays para construir query dinamico
        $fields = [];
        $params = [];
        $activityMessages = [];  // Cambios a registrar en el historial

        // =============================================
        // PROCESAR CADA CAMPO
        // =============================================
        
        // Actualizar titulo
        if (isset($data['title'])) {
            $fields[] = "title = ?";
            $params[] = $data['title'];
        }
        
        // Actualizar descripcion
        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $params[] = $data['description'];
        }
        
        // Actualizar prioridad (registrar si cambio)
        if (isset($data['priority'])) {
            $fields[] = "priority = ?";
            $params[] = $data['priority'];
            // Si la prioridad cambio, registrar en historial
            if ($data['priority'] !== $ticket['priority']) {
                $activityMessages[] = ['type' => 'status', 'msg' => "Prioridad cambiada a {$data['priority']}"];
            }
        }
        
        // Actualizar estado (registrar si cambio)
        if (isset($data['status'])) {
            $fields[] = "status = ?";
            $params[] = $data['status'];
            // Si el estado cambio, registrar en historial
            if ($data['status'] !== $ticket['status']) {
                $activityMessages[] = ['type' => 'status', 'msg' => "Estado cambiado a {$data['status']}"];
            }
            // Si se marco como Resuelto, guardar fecha de resolucion
            if ($data['status'] === 'Resuelto' && $ticket['status'] !== 'Resuelto') {
                $fields[] = "resolved_at = NOW()";
            }
        }
        
        // Actualizar categoria
        if (isset($data['category'])) {
            $fields[] = "category = ?";
            $params[] = $data['category'];
        }
        
        // Actualizar departamento
        if (isset($data['dept'])) {
            $fields[] = "dept = ?";
            $params[] = $data['dept'];
        }
        
        // Actualizar asignado (registrar si cambio)
        if (isset($data['assignee_id'])) {
            $fields[] = "assignee_id = ?";
            $params[] = $data['assignee_id'] ?: null;  // null si es vacio
            
            // Si el asignado cambio, registrar en historial
            if ($data['assignee_id'] != $ticket['assignee_id']) {
                if ($data['assignee_id']) {
                    // Obtener nombre del nuevo asignado
                    $stmt = $db->prepare("SELECT name FROM users WHERE id = ?");
                    $stmt->execute([$data['assignee_id']]);
                    $assignee = $stmt->fetch();
                    $activityMessages[] = ['type' => 'assign', 'msg' => "Asignado a " . ($assignee['name'] ?? 'usuario')];
                } else {
                    $activityMessages[] = ['type' => 'assign', 'msg' => "Ticket desasignado"];
                }
            }
        }
        
        // Actualizar fecha limite
        if (isset($data['due_date'])) {
            $fields[] = "due_date = ?";
            $params[] = $data['due_date'];
        }

        // Si no hay campos para actualizar, error
        if (empty($fields)) {
            jsonResponse(['success' => false, 'error' => 'No hay campos para actualizar'], 400);
        }

        // Agregar ID al final para el WHERE
        $params[] = $id;
        
        // Ejecutar UPDATE
        $sql = "UPDATE tickets SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // =============================================
        // REGISTRAR ACTIVIDADES EN EL HISTORIAL
        // =============================================
        $userName = $data['user_name'] ?? 'Sistema';
        foreach ($activityMessages as $activity) {
            $stmt = $db->prepare("
                INSERT INTO ticket_activity (ticket_id, user_name, activity_type, message)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$id, $userName, $activity['type'], $activity['msg']]);
        }

        // Confirmar actualizacion
        jsonResponse([
            'success' => true,
            'message' => 'Ticket actualizado'
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al actualizar: ' . $e->getMessage()], 500);
    }
}


// =============================================
// FUNCION: deleteTicket()
// =============================================
/**
 * Elimina un ticket permanentemente
 * NOTA: Esto tambien elimina toda la actividad del ticket (CASCADE)
 * 
 * REQUEST: DELETE /api/tickets.php?id=TK-001
 * 
 * RESPONSE:
 * { "success": true, "message": "Ticket eliminado" }
 */
function deleteTicket() {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'ID de ticket requerido'], 400);
    }

    try {
        $db = getDB();
        $user = requireAuth();

        // Verificar que el ticket existe y que el usuario tiene acceso
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch();

        if (!$ticket) {
            jsonResponse(['success' => false, 'error' => 'Ticket no encontrado'], 404);
        }

        if (!userCanAccessTicket($user, $ticket)) {
            jsonResponse(['success' => false, 'error' => 'Acceso denegado al ticket'], 403);
        }

        // Eliminar ticket (la actividad se elimina automaticamente por CASCADE)
        $stmt = $db->prepare("DELETE FROM tickets WHERE id = ?");
        $stmt->execute([$id]);

        // Verificar si se elimino algo
        if ($stmt->rowCount() === 0) {
            jsonResponse(['success' => false, 'error' => 'Ticket no encontrado'], 404);
        }

        // Confirmar eliminacion
        jsonResponse([
            'success' => true,
            'message' => 'Ticket eliminado'
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al eliminar ticket'], 500);
    }
}


// =============================================
// FUNCION: getStats()
// =============================================
/**
 * Obtiene estadisticas generales de los tickets
 * Util para dashboards y reportes
 * 
 * REQUEST: GET /api/tickets.php?stats=1
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "data": {
 *     "total": 25,                    // Total de tickets
 *     "by_status": {                  // Conteo por estado
 *       "Abierto": 5,
 *       "En Progreso": 8,
 *       "Resuelto": 12
 *     },
 *     "by_priority": {                // Conteo por prioridad
 *       "Critica": 2,
 *       "Alta": 7,
 *       "Media": 10,
 *       "Baja": 6
 *     },
 *     "overdue": 3,                   // Tickets vencidos
 *     "resolved_this_month": 8,       // Resueltos este mes
 *     "avg_resolution_days": 2.5      // Dias promedio de resolucion
 *   }
 * }
 */
function getStats() {
    try {
        $db = getDB();
        $user = requireAuth();

        $ticketWhere = '';
        $ticketParams = [];
        if ($user['role'] === 'user') {
            $ticketWhere = 'WHERE created_by = ?';
            $ticketParams[] = $user['id'];
        } else {
            $ticketWhere = 'WHERE assignee_id = ?';
            $ticketParams[] = $user['id'];
        }

        // =============================================
        // CONTEO POR ESTADO
        // =============================================
        $stmt = $db->prepare("
            SELECT status, COUNT(*) as count 
            FROM tickets 
            $ticketWhere 
            GROUP BY status
        ");
        $stmt->execute($ticketParams);
        // FETCH_KEY_PAIR retorna: ['Abierto' => 5, 'Resuelto' => 12, ...]
        $byStatus = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // =============================================
        // CONTEO POR PRIORIDAD
        // =============================================
        $stmt = $db->prepare("
            SELECT priority, COUNT(*) as count 
            FROM tickets 
            $ticketWhere 
            GROUP BY priority
        ");
        $stmt->execute($ticketParams);
        $byPriority = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // =============================================
        // TICKETS VENCIDOS (due_date pasada y no resueltos)
        // =============================================
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM tickets 
            $ticketWhere 
            AND due_date < CURDATE() AND status NOT IN ('Resuelto')
        ");
        $stmt->execute($ticketParams);
        $overdue = $stmt->fetch()['count'];

        // =============================================
        // TOTAL DE TICKETS
        // =============================================
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM tickets $ticketWhere");
        $stmt->execute($ticketParams);
        $total = $stmt->fetch()['total'];

        // =============================================
        // RESUELTOS ESTE MES
        // =============================================
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM tickets 
            $ticketWhere 
            AND status = 'Resuelto' 
            AND MONTH(resolved_at) = MONTH(CURDATE())
            AND YEAR(resolved_at) = YEAR(CURDATE())
        ");
        $stmt->execute($ticketParams);
        $resolvedThisMonth = $stmt->fetch()['count'];

        // =============================================
        // TIEMPO PROMEDIO DE RESOLUCION (en dias)
        // =============================================
        // DATEDIFF calcula la diferencia en dias entre dos fechas
        $stmt = $db->prepare(
            "SELECT AVG(DATEDIFF(resolved_at, created_at)) as avg_days
            FROM tickets 
            $ticketWhere 
            AND status = 'Resuelto' AND resolved_at IS NOT NULL
        ");
        $stmt->execute($ticketParams);
        $avgResolutionDays = round($stmt->fetch()['avg_days'] ?? 0, 1);

        // Retornar todas las estadisticas
        jsonResponse([
            'success' => true,
            'data' => [
                'total' => $total,
                'by_status' => $byStatus,
                'by_priority' => $byPriority,
                'overdue' => $overdue,
                'resolved_this_month' => $resolvedThisMonth,
                'avg_resolution_days' => $avgResolutionDays
            ]
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al obtener estadisticas'], 500);
    }
}
?>

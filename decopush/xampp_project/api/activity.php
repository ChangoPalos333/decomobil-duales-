<?php
/**
 * =============================================
 * DECOMOBIL - API de Actividad de Tickets
 * =============================================
 * 
 * Este archivo maneja el historial de actividad de los tickets.
 * Permite agregar comentarios y ver el historial de cambios.
 * 
 * La actividad incluye:
 * - Creacion del ticket
 * - Comentarios de usuarios
 * - Cambios de estado
 * - Asignaciones y reasignaciones
 * - Resoluciones
 * 
 * ENDPOINTS:
 * GET  /api/activity.php?ticket_id=TK-001  - Ver actividad de un ticket
 * POST /api/activity.php                    - Agregar comentario/actividad
 * 
 * TIPOS DE ACTIVIDAD:
 * - create:  Ticket fue creado
 * - comment: Comentario de un usuario
 * - status:  Se cambio el estado o prioridad
 * - assign:  Se asigno o reasigno el ticket
 * - resolve: Se marco como resuelto
 */

// Incluir helpers de autenticacion y autorizacion
require_once __DIR__ . '/auth_helpers.php';

// =============================================
// ROUTER - Determinar que operacion ejecutar
// =============================================

// Obtener metodo HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Ejecutar funcion segun el metodo
switch ($method) {
    case 'GET':
        getActivity();    // Obtener actividad de un ticket
        break;
    case 'POST':
        addActivity();    // Agregar nueva actividad/comentario
        break;
    default:
        jsonResponse(['success' => false, 'error' => 'Metodo no permitido'], 405);
}


// =============================================
// FUNCION: getActivity()
// =============================================
/**
 * Obtiene toda la actividad/historial de un ticket especifico
 * 
 * REQUEST: GET /api/activity.php?ticket_id=TK-001
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "data": [
 *     {
 *       "id": 1,
 *       "ticket_id": "TK-001",
 *       "user_name": "Sistema",
 *       "activity_type": "create",
 *       "message": "Ticket creado",
 *       "created_at": "2025-03-01 10:00:00"
 *     },
 *     {
 *       "id": 2,
 *       "ticket_id": "TK-001",
 *       "user_name": "Ana Garcia",
 *       "activity_type": "comment",
 *       "message": "Revisando el problema...",
 *       "created_at": "2025-03-01 10:30:00"
 *     }
 *   ]
 * }
 */
function getActivity() {
    // Obtener ID del ticket del query string
    $ticketId = $_GET['ticket_id'] ?? null;

    // Validar que se proporciono el ID
    if (!$ticketId) {
        jsonResponse(['success' => false, 'error' => 'ticket_id es requerido'], 400);
    }

    try {
        $db = getDB();
        
        // Obtener toda la actividad del ticket
        // Ordenado cronologicamente (mas antiguo primero)
        // Esto permite mostrar el historial como una linea de tiempo
        $stmt = $db->prepare("
            SELECT * FROM ticket_activity 
            WHERE ticket_id = ? 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$ticketId]);
        $activity = $stmt->fetchAll();

        // Retornar la lista de actividades
        jsonResponse([
            'success' => true,
            'data' => $activity
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al obtener actividad'], 500);
    }
}


// =============================================
// FUNCION: addActivity()
// =============================================
/**
 * Agrega una nueva entrada al historial de actividad de un ticket
 * Se usa principalmente para agregar comentarios
 * 
 * Los cambios de estado y asignaciones normalmente se registran
 * automaticamente desde la API de tickets (updateTicket)
 * 
 * REQUEST (POST):
 * {
 *   "ticket_id": "TK-001",        // Requerido
 *   "user_name": "Ana Garcia",    // Opcional, default: Sistema
 *   "type": "comment",            // Opcional, default: comment
 *   "message": "Revisando..."     // Requerido
 * }
 * 
 * TIPOS VALIDOS:
 * - create:  Ticket fue creado (normalmente solo lo usa el sistema)
 * - comment: Comentario de un usuario
 * - status:  Cambio de estado (normalmente automatico)
 * - assign:  Asignacion/reasignacion (normalmente automatico)
 * - resolve: Resolucion del ticket
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "message": "Actividad agregada",
 *   "data": {
 *     "id": 5,
 *     "ticket_id": "TK-001",
 *     "user_name": "Ana Garcia",
 *     "activity_type": "comment",
 *     "message": "Revisando..."
 *   }
 * }
 */
function addActivity() {
    $data = json_decode(file_get_contents('php://input'), true);

    $ticketId = $data['ticket_id'] ?? null;
    $type = $data['type'] ?? 'comment';
    $message = trim($data['message'] ?? '');

    if (!$ticketId || empty($message)) {
        jsonResponse(['success' => false, 'error' => 'ticket_id y message son requeridos'], 400);
    }

    $user = requireAuth();
    $ticket = getTicketById($ticketId);
    if (!$ticket) {
        jsonResponse(['success' => false, 'error' => 'Ticket no encontrado'], 404);
    }

    if (!userCanAccessTicket($user, $ticket)) {
        jsonResponse(['success' => false, 'error' => 'Acceso denegado al ticket'], 403);
    }

    $userName = $user['name'];
    $allowedTypes = ['create', 'comment', 'status', 'assign', 'resolve'];
    if (!in_array($type, $allowedTypes)) {
        $type = 'comment';
    }

    try {
        $db = getDB();

        // =============================================
        // VERIFICAR QUE EL TICKET EXISTE
        // =============================================
        // No queremos agregar actividad a tickets inexistentes
        $stmt = $db->prepare("SELECT id FROM tickets WHERE id = ?");
        $stmt->execute([$ticketId]);
        if (!$stmt->fetch()) {
            jsonResponse(['success' => false, 'error' => 'Ticket no encontrado'], 404);
        }

        // =============================================
        // INSERTAR LA ACTIVIDAD
        // =============================================
        $stmt = $db->prepare("
            INSERT INTO ticket_activity (ticket_id, user_name, activity_type, message)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$ticketId, $userName, $type, $message]);

        // Obtener el ID de la actividad recien creada
        $activityId = $db->lastInsertId();

        // Retornar respuesta exitosa con los datos de la actividad
        jsonResponse([
            'success' => true,
            'message' => 'Actividad agregada',
            'data' => [
                'id' => $activityId,
                'ticket_id' => $ticketId,
                'user_name' => $userName,
                'activity_type' => $type,
                'message' => $message
            ]
        ], 201);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al agregar actividad: ' . $e->getMessage()], 500);
    }
}
?>
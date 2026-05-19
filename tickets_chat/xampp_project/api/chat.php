<?php
/**
 * =============================================
 * DECOMOBIL - API de Chat en Tiempo Real
 * Long Polling para comentarios de tickets
 * =============================================
 *
 * ENDPOINTS:
 * GET  /api/chat.php?ticket_id=TK-001              -> Poll: espera mensajes nuevos
 * GET  /api/chat.php?ticket_id=TK-001&last_id=42   -> Poll desde mensaje #42
 * POST /api/chat.php                                -> Enviar mensaje
 *
 * COMO FUNCIONA EL LONG POLLING:
 * 1. El cliente hace GET con el último ID que conoce
 * 2. El servidor espera hasta 1 segundos buscando mensajes nuevos
 * 3. Si hay mensajes nuevos, responde de inmediato
 * 4. Si no hay nada en 25 seg, responde vacío (timeout)
 * 5. El cliente repite el ciclo inmediatamente
 */

require_once __DIR__ . '/auth_helpers.php';

// Tiempo máximo de espera en segundos para long polling
define('POLL_TIMEOUT', 1);
// Intervalo de revisión en la base de datos (microsegundos = 1.5 seg)
define('POLL_INTERVAL', 1500000);

// Aumentar tiempo de ejecución PHP para long polling
set_time_limit(60);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        pollMessages();
        break;
    case 'POST':
        sendMessage();
        break;
    default:
        jsonResponse(['success' => false, 'error' => 'Método no permitido'], 405);
}


/**
 * Long polling: espera mensajes nuevos y responde cuando hay
 */
function pollMessages() {
    $ticketId = $_GET['ticket_id'] ?? null;
    $lastId   = intval($_GET['last_id'] ?? 0);

    // Evitar caches y mantener la conexión limpia
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    ignore_user_abort(true);

    if (!$ticketId) {
        jsonResponse(['success' => false, 'error' => 'ticket_id requerido'], 400);
    }

    $user = requireAuth();
    $ticket = getTicketById($ticketId);
    if (!$ticket) {
        jsonResponse(['success' => false, 'error' => 'Ticket no encontrado'], 404);
    }
    if (!userCanAccessTicket($user, $ticket)) {
        jsonResponse(['success' => false, 'error' => 'Acceso denegado al ticket'], 403);
    }

    $db = getDB();
    $deadline = time() + POLL_TIMEOUT;

    // Bucle de espera: revisa la BD cada 1.5 segundos
    while (time() < $deadline) {
        $stmt = $db->prepare("
            SELECT 
                id,
                ticket_id,
                user_name,
                activity_type,
                message,
                created_at
            FROM ticket_activity
            WHERE ticket_id = ?
              AND id > ?
              AND activity_type IN ('comment', 'create', 'status', 'assign', 'resolve')
            ORDER BY id ASC
            LIMIT 50
        ");
        $stmt->execute([$ticketId, $lastId]);
        $messages = $stmt->fetchAll();

        if (!empty($messages)) {
            // Hay mensajes nuevos — responder de inmediato
            jsonResponse([
                'success'  => true,
                'messages' => $messages,
                'last_id'  => end($messages)['id']
            ]);
        }

        // No hay mensajes aún, esperar y reintentar
        usleep(POLL_INTERVAL);

        // Verificar que la conexión del cliente sigue activa
        if (connection_aborted()) {
            exit;
        }
    }

    // Timeout — responder vacío para que el cliente reinicie el poll
    jsonResponse([
        'success'  => true,
        'messages' => [],
        'last_id'  => $lastId,
        'timeout'  => true
    ]);
}


/**
 * Enviar un nuevo mensaje/comentario en un ticket
 */
function sendMessage() {
    // Leer datos del body JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Extraer campos
    $ticketId = $data['ticket_id'] ?? null;
    $userName = $data['user_name'] ?? 'Sistema';    // Por defecto: Sistema
    $type = $data['type'] ?? 'comment';              // Por defecto: comentario
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

        // Insertar el mensaje
        $stmt = $db->prepare("
            INSERT INTO ticket_activity (ticket_id, user_name, activity_type, message)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$ticketId, $userName, $type, $message]);
        $newId = $db->lastInsertId();

        jsonResponse([
            'success' => true,
            'message' => 'Mensaje enviado',
            'data' => [
                'id'            => $newId,
                'ticket_id'     => $ticketId,
                'user_name'     => $userName,
                'activity_type' => $type,
                'message'       => $message,
                'created_at'    => date('Y-m-d H:i:s')
            ]
        ], 201);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Error al enviar mensaje'], 500);
    }
}


?>

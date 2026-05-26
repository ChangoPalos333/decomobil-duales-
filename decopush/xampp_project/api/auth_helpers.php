<?php
/**
 * Helpers compartidos para autenticacion y autorizacion de tickets.
 */

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getBearerToken() {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (!$authHeader && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (!empty($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (!empty($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        }
    }

    if (!$authHeader) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    }

    if (strpos($authHeader, 'Bearer ') === 0) {
        return substr($authHeader, 7);
    }

    return null;
}

function getCurrentUser() {
    $token = getBearerToken() ?: $_SESSION['session_token'] ?? null;
    if (!$token) {
        return null;
    }

    try {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT u.* FROM users u
             INNER JOIN sessions s ON u.id = s.user_id
             WHERE s.session_token = ?
               AND s.expires_at > NOW()
               AND u.active = 1"
        );
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        unset($user['password']);
        return $user;
    } catch (PDOException $e) {
        return null;
    }
}

function requireAuth() {
    $user = getCurrentUser();
    if (!$user) {
        jsonResponse(['success' => false, 'error' => 'No autorizado'], 401);
    }
    return $user;
}

function getTicketById($ticketId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$ticketId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function userCanAccessTicket(array $user, array $ticket) {
    if (!$ticket) {
        return false;
    }

    if (isset($user['role']) && $user['role'] === 'user') {
        return (int) $ticket['created_by'] === (int) $user['id'];
    }

    return (int) $ticket['assignee_id'] === (int) $user['id'];
}
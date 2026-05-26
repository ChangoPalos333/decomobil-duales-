<?php
header('Content-Type: application/json');

require_once '../config/database.php';
require_once 'auth_helpers.php';

$db = getDB();

$user = requireAuth();
$method = $_SERVER['REQUEST_METHOD'];

try {

    // =========================
    // GET - OBTENER NOTICIAS
    // =========================
    if ($method === 'GET') {

        $query = "
            SELECT *
            FROM news
            ORDER BY created_at DESC
        ";

        $stmt = $db->prepare($query);
        $stmt->execute();

        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $news
        ]);

        exit;
    }


    // =========================
    // POST - CREAR
    // =========================
    if ($method === 'POST') {

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'Solo administradores'
            ]);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $accent = trim($data['accent_color'] ?? '#0F52BA');

        if (!$title || !$description) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Faltan campos'
            ]);
            exit;
        }

        $query = "
            INSERT INTO news
            (title, description, accent_color, created_by)
            VALUES
            (:title, :description, :accent, :created_by)
        ";

        $stmt = $db->prepare($query);

        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':accent' => $accent,
            ':created_by' => $user['name']
        ]);

        echo json_encode([
            'success' => true
        ]);

        exit;
    }


    // =========================
    // PUT - EDITAR
    // =========================
    if ($method === 'PUT') {

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'Solo administradores'
            ]);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $query = "
            UPDATE news
            SET
                title = :title,
                description = :description,
                accent_color = :accent
            WHERE id = :id
        ";

        $stmt = $db->prepare($query);

        $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':accent' => $data['accent_color']
        ]);

        echo json_encode([
            'success' => true
        ]);

        exit;
    }

    // =========================
    // DELETE
    // =========================
    if ($method === 'DELETE') {

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'Solo administradores'
            ]);
            exit;
        }

        $id = $_GET['id'] ?? null;

        $stmt = $db->prepare('DELETE FROM news WHERE id = :id');
        $stmt->execute([':id' => $id]);

        echo json_encode([
            'success' => true
        ]);

        exit;
    }

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}